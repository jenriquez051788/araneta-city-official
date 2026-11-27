<?php
declare(strict_types=1);

/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link      https://cakephp.org CakePHP(tm) Project
 * @since     0.2.9
 * @license   https://opensource.org/licenses/mit-license.php MIT License
 */
namespace App\Controller;

use Cake\Cache\Cache;
use Cake\Controller\Controller;
use Cake\Event\EventInterface;
use Cake\Http\Client;
use Cake\Log\Log;

/**
 * Application Controller
 *
 * @link https://book.cakephp.org/5/en/controllers.html#the-app-controller
 */
class AppController extends Controller
{
    /**
     * Name of the cache config used for (short-lived) API responses.
     */
    private const API_CACHE_CONFIG = '_cake_api_';

    /**
     * "Last known good" copy of API responses (long-lived). Only served when
     * the live API call fails, so a transient CMS error never shows an empty
     * "No hotels found" page. Defined in config/app.php.
     */
    private const API_STALE_CONFIG = '_cake_api_stale_';

    /**
     * Shared CMS bearer token (one login for ALL visitors instead of one per
     * session). Defined in config/app.php.
     */
    private const API_TOKEN_CONFIG = '_cake_api_token_';
    private const API_TOKEN_KEY = 'cms_api_token';

    /**
     * Never write a response larger than this many bytes to the cache.
     */
    private const API_CACHE_MAX_BYTES = 8 * 1024 * 1024;

    /**
     * Cache files older than this many seconds are deleted by the pruner
     * (only the short-lived _cake_api_ folder; the stale folder is never pruned).
     * Keep above the 'duration' of _cake_api_ (600s).
     */
    private const API_CACHE_MAX_AGE = 900;

    /**
     * Minimum seconds between two prune runs.
     */
    private const API_CACHE_PRUNE_INTERVAL = 600;

    /**
     * Endpoints that are cached automatically (10 min) even when the calling
     * code passes $cacheSeconds = 0. Matched against the path part of the
     * endpoint (query string ignored). This protects pages that previously
     * called the API uncached (malls, parking, transport, attractions, ...).
     */
    private const AUTO_CACHE_SECONDS = 600;
    private const AUTO_CACHE_ENDPOINTS = [
        '/api/buildingslist',
        '/api/parkinglist',
        '/api/transportslist',
        '/api/attractionslist',
        '/api/events_upcoming',
        '/api/events_week',
        '/api/events_today',
        '/api/establishments_by_building',
        '/api/hotelslist',
        '/api/offices',
        '/api/residences',
        '/api/happeningslist',
    ];

    /**
     * True when the last callApiFresh() attempt ended because of a timeout.
     */
    private bool $lastApiFailureWasTimeout = false;

    /**
     * Initialization hook method.
     *
     * @return void
     */
    public function initialize(): void
    {
        parent::initialize();

        $this->loadComponent('Flash');

        // requireIdentity is disabled globally so the public frontend stays
        // open. Admin controllers opt back in.
        $this->loadComponent('Authentication.Authentication', [
            'requireIdentity' => false,
        ]);
        $this->loadComponent('Authorization.Authorization');
    }

    /**
     * Runs before every action.
     *
     * @param \Cake\Event\EventInterface $event The beforeFilter event.
     * @return void
     */
    public function beforeFilter(EventInterface $event): void
    {
        parent::beforeFilter($event);
        $this->response = $this->response
        ->withHeader('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
        ->withHeader('Pragma', 'no-cache')
        ->withHeader('Expires', '0');
        $this->Authorization->skipAuthorization();
    }

    /**
     * Runs before the view is rendered.
     *
     * Makes the buildings list available to every view (header search bar).
     *
     * @param \Cake\Event\EventInterface $event The beforeRender event.
     * @return void
     */
    public function beforeRender(EventInterface $event): void
    {
        parent::beforeRender($event);

        // Housekeeping: remove expired API cache files (throttled, cheap).
        $this->pruneApiCache();

        $headerBuildings = Cache::read('header_buildings');
        if (!is_array($headerBuildings) || empty($headerBuildings)) {
            $headerBuildings = [];
            // buildingslist is auto-cached (+ stale fallback) in callApi().
            $buildingsResponse = $this->callApi('/api/buildingslist');
            if (!empty($buildingsResponse['success']) && !empty($buildingsResponse['data'])) {
                foreach ($buildingsResponse['data'] as $b) {
                    $bid = $b['id'] ?? '';
                    if ($bid !== '') {
                        $headerBuildings[$bid] = $b['building_name'] ?? ($b['title'] ?? ($b['name'] ?? ''));
                    }
                }
            }
            // Only cache when we actually got results.
            if (!empty($headerBuildings)) {
                Cache::write('header_buildings', $headerBuildings);
            }
        }

        $this->set('headerBuildings', $headerBuildings);
    }

    /**
     * Convenience: the currently logged-in User entity, or null when guest.
     *
     * @return \App\Model\Entity\User|null
     */
    protected function currentUser(): ?\App\Model\Entity\User
    {
        $identity = $this->Authentication->getIdentity();
        if ($identity === null) {
            return null;
        }

        /** @var \App\Model\Entity\User $user */
        $user = $identity->getOriginalData();

        return $user;
    }

    /**
     * Checks whether the current user's role matches one of the given slugs.
     *
     * @param list<string> $roleSlugs Allowed role slugs.
     * @return bool
     */
    protected function userHasRole(array $roleSlugs): bool
    {
        $user = $this->currentUser();
        if ($user === null || empty($user->role)) {
            return false;
        }

        return in_array($user->role->slug, $roleSlugs, true);
    }

    // ------------------------------------------------------------------
    // Safe cache helpers (never throw if a cache config is missing)
    // ------------------------------------------------------------------

    /**
     * Runs a cache operation while turning file-engine warnings into log
     * entries. On Windows, FileEngine can fail to open a cache file that
     * another request is deleting ("Permission denied"); in debug mode that
     * warning is printed before headers are sent and breaks the page.
     *
     * @param callable $operation Cache operation to run.
     * @return mixed The operation result, or false if it raised a warning.
     */
    private function withoutCacheWarnings(callable $operation): mixed
    {
        $failed = false;
        set_error_handler(static function (int $errno, string $errstr) use (&$failed): bool {
            $failed = true;
            Log::warning('Cache warning: ' . $errstr);

            return true;
        }, E_WARNING | E_USER_WARNING);
        try {
            $result = $operation();
        } finally {
            restore_error_handler();
        }

        return $failed ? false : $result;
    }

    /**
     * @param string $key Cache key.
     * @param string $config Cache config name.
     * @return array|null The cached array, or null when missing/unavailable.
     */
    private function cacheReadArray(string $key, string $config): ?array
    {
        try {
            $value = $this->withoutCacheWarnings(fn() => Cache::read($key, $config));

            return is_array($value) ? $value : null;
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * @param string $key Cache key.
     * @param mixed $value Value to store.
     * @param string $config Cache config name.
     * @return void
     */
    private function cacheWriteSafe(string $key, mixed $value, string $config): void
    {
        try {
            $this->withoutCacheWarnings(fn() => Cache::write($key, $value, $config));
        } catch (\Throwable $e) {
            Log::warning("Cache write failed ({$config}): " . $e->getMessage());
        }
    }

    /**
     * Returns the automatic cache duration for an endpoint (0 = none).
     *
     * @param string $endpoint API endpoint, possibly with a query string.
     * @return int
     */
    private function autoCacheSeconds(string $endpoint): int
    {
        $path = (string)strtok($endpoint, '?');

        return in_array($path, self::AUTO_CACHE_ENDPOINTS, true) ? self::AUTO_CACHE_SECONDS : 0;
    }

    /**
     * Builds the cache key for an API request. Query is normalized so
     * equivalent requests share one cache entry.
     *
     * @param string $endpoint API endpoint.
     * @param array<string, mixed> $query Query parameters.
     * @return string
     */
    private function buildApiCacheKey(string $endpoint, array $query): string
    {
        $normalized = array_filter(
            $query,
            static fn($v) => $v !== null && $v !== ''
        );
        ksort($normalized);

        return 'api_' . md5($endpoint . '?' . http_build_query($normalized));
    }

    /**
     * Writes an API response to the short-lived AND the stale cache, unless it
     * is empty or too large.
     *
     * @param string $cacheKey Key from buildApiCacheKey().
     * @param array $data Decoded response data.
     * @param int $rawBytes Size of the raw response body in bytes.
     * @return void
     */
    private function writeApiCache(string $cacheKey, array $data, int $rawBytes): void
    {
        if (empty($data)) {
            return;
        }

        if ($rawBytes > self::API_CACHE_MAX_BYTES) {
            Log::debug("API cache skipped for {$cacheKey}: {$rawBytes} bytes exceeds limit");

            return;
        }

        $this->writeFresh($cacheKey, $data);
        $this->cacheWriteSafe($cacheKey, $data, self::API_STALE_CONFIG);
    }

    /**
     * Stores data in the short-lived cache together with a timestamp, so the
     * per-call $cacheSeconds can be honoured on read (the file itself lives
     * for the config duration, but is treated as expired sooner if asked).
     *
     * @param string $cacheKey Cache key.
     * @param array $data Data to store.
     * @return void
     */
    private function writeFresh(string $cacheKey, array $data): void
    {
        $this->cacheWriteSafe($cacheKey, ['_t' => time(), '_d' => $data], self::API_CACHE_CONFIG);
    }

    /**
     * Reads the short-lived cache, returning data only if it is younger than
     * $ttl seconds. Old-format entries (no timestamp) count as expired.
     *
     * @param string $cacheKey Cache key.
     * @param int $ttl Maximum age in seconds.
     * @return array|null
     */
    private function readFresh(string $cacheKey, int $ttl): ?array
    {
        $entry = $this->cacheReadArray($cacheKey, self::API_CACHE_CONFIG);
        if ($entry === null || !isset($entry['_t'], $entry['_d']) || !is_array($entry['_d'])) {
            return null;
        }

        return (time() - (int)$entry['_t']) <= $ttl ? $entry['_d'] : null;
    }

    /**
     * Returns the "last known good" copy for a key, if any. When found it is
     * also re-written to the short-lived cache so a failing upstream isn't
     * hammered (and made to time out) on every single request.
     *
     * @param string $cacheKey Key from buildApiCacheKey().
     * @param string $endpoint Endpoint (for logging).
     * @return array|null
     */
    private function serveStale(string $cacheKey, string $endpoint): ?array
    {
        $stale = $this->cacheReadArray($cacheKey, self::API_STALE_CONFIG);
        if ($stale === null || empty($stale)) {
            return null;
        }

        Log::warning("Serving STALE cache for {$endpoint} because the live API call failed");
        $this->writeFresh($cacheKey, $stale);

        return $stale;
    }

    /**
     * Deletes expired short-lived API cache files (never the stale folder).
     *
     * @return void
     */
    private function pruneApiCache(): void
    {
        try {
            $engine = Cache::pool(self::API_CACHE_CONFIG);
            if (!method_exists($engine, 'getConfig')) {
                return;
            }
            $config = $engine->getConfig();
        } catch (\Throwable $e) {
            return;
        }

        $path = $config['path'] ?? null;
        if (!is_string($path) || $path === '' || !is_dir($path)) {
            return;
        }
        $path = rtrim($path, '/\\') . DIRECTORY_SEPARATOR;

        $now = time();
        $marker = $path . '.api_prune_marker';
        if (is_file($marker) && ($now - (int)filemtime($marker)) < self::API_CACHE_PRUNE_INTERVAL) {
            return;
        }
        @touch($marker);

        $prefix = (string)($config['prefix'] ?? '');
        $files = glob($path . $prefix . 'api_*');
        if (!is_array($files)) {
            return;
        }

        $removed = 0;
        foreach ($files as $file) {
            if (is_file($file) && ($now - (int)filemtime($file)) > self::API_CACHE_MAX_AGE) {
                if (@unlink($file)) {
                    $removed++;
                }
            }
        }

        if ($removed > 0) {
            Log::debug("API cache pruned: removed {$removed} expired file(s)");
        }
    }

    // ------------------------------------------------------------------
    // Token handling
    // ------------------------------------------------------------------

    /**
     * Returns a valid bearer token for the CMS API. Order of lookup:
     * shared cache (all visitors) -> session -> fresh login.
     *
     * @return string|null The bearer token, or null if login failed.
     */
    private function getApiToken(): ?string
    {
        // 1. Shared token (one login for every visitor).
        try {
            $shared = $this->withoutCacheWarnings(fn() => Cache::read(self::API_TOKEN_KEY, self::API_TOKEN_CONFIG));
            if (is_string($shared) && $shared !== '') {
                return $shared;
            }
        } catch (\Throwable $e) {
            // Token cache not configured: fall through to session.
        }

        // 2. Per-session token (also used when the shared cache is unavailable).
        $session = $this->request->getSession();
        $token = $session->read('api_token');
        if ($token) {
            return $token;
        }

        // 3. Log in.
        $baseUrl = rtrim(trim(env('API_BASE_URL', 'https://aci-cms.aranetacity.com/')), '/');

        try {
            $http = new Client([
                'timeout' => 10,
                'connectTimeout' => 5,
            ]);

            $loginResponse = $http->post(
                $baseUrl . '/api/user_login',
                [
                    'email' => env('API_EMAIL', ''),
                    'password' => env('API_PASSWORD', ''),
                ],
                [
                    'type' => 'json',
                    'headers' => ['Accept' => 'application/json'],
                ]
            );

            if (!$loginResponse->isOk()) {
                Log::error('Login failed: HTTP ' . $loginResponse->getStatusCode());

                return null;
            }

            $loginData = $loginResponse->getJson();
            $token = $loginData['token'] ?? null;

            if (!$token) {
                Log::error('No token in login response');

                return null;
            }

            $session->write('api_token', $token);
            $this->cacheWriteSafe(self::API_TOKEN_KEY, $token, self::API_TOKEN_CONFIG);

            return $token;
        } catch (\Exception $e) {
            Log::error('API login exception: ' . $e->getMessage());

            return null;
        }
    }

    /**
     * Forgets the CMS token everywhere (used after a 401).
     *
     * @return void
     */
    private function clearApiToken(): void
    {
        $this->request->getSession()->delete('api_token');
        try {
            $this->withoutCacheWarnings(fn() => Cache::delete(self::API_TOKEN_KEY, self::API_TOKEN_CONFIG));
        } catch (\Throwable $e) {
            // Token cache not configured; nothing to clear.
        }
    }

    // ------------------------------------------------------------------
    // API calls
    // ------------------------------------------------------------------

    /**
     * Centralized API call method.
     *
     * - Serves a fresh cached copy when available.
     * - Otherwise calls the API (with retries).
     * - If the live call fails, serves the last known good copy instead of an
     *   empty result, so pages don't show "No ... found" on a transient error.
     *
     * @param string $endpoint API endpoint (e.g., '/api/newslistallnow')
     * @param array $query Query parameters
     * @param int $cacheSeconds When > 0, cache the response (real lifetime is the
     *   _cake_api_ config duration). Endpoints in AUTO_CACHE_ENDPOINTS are cached
     *   even when 0 is passed.
     * @return array API response data
     */
    protected function callApi(string $endpoint, array $query = [], int $cacheSeconds = 0): array
    {
        if ($cacheSeconds <= 0) {
            $cacheSeconds = $this->autoCacheSeconds($endpoint);
        }

        $cacheKey = null;
        if ($cacheSeconds > 0) {
            $cacheKey = $this->buildApiCacheKey($endpoint, $query);
            $cached = $this->readFresh($cacheKey, $cacheSeconds);
            if ($cached !== null) {
                return $cached;
            }
        }

        $result = $this->callApiFresh($endpoint, $query, $cacheKey, false);
        if (!empty($result) || $cacheKey === null) {
            return $result;
        }

        // Live call failed. Prefer the last known good copy.
        $stale = $this->serveStale($cacheKey, $endpoint);
        if ($stale !== null) {
            return $stale;
        }

        // Nothing to fall back on. If the failure was a timeout, give it one
        // more (shorter) try before showing an empty result.
        if ($this->lastApiFailureWasTimeout) {
            return $this->callApiFresh($endpoint, $query, $cacheKey, true);
        }

        return $result;
    }

    /**
     * Performs the real HTTP call (with retries) and writes the cache on success.
     *
     * @param string $endpoint API endpoint.
     * @param array $query Query parameters.
     * @param string|null $cacheKey Cache key to write on success (null = don't cache).
     * @param bool $allowTimeoutRetry Retry once (with a shorter timeout) after a timeout.
     * @return array Decoded response, or [] on failure.
     */
    private function callApiFresh(string $endpoint, array $query, ?string $cacheKey, bool $allowTimeoutRetry): array
    {
        $this->lastApiFailureWasTimeout = false;

        $baseUrl = rtrim(trim(env('API_BASE_URL', 'https://aci-cms.aranetacity.com/')), '/');
        $url = $baseUrl . $endpoint;

        $maxAttempts = 3;
        $tokenResetOnce = false;
        $timeoutRetried = false;
        $readTimeout = $allowTimeoutRetry ? 15 : 25;

        for ($attempt = 1; $attempt <= $maxAttempts; $attempt++) {
            try {
                $token = $this->getApiToken();
                if (!$token) {
                    if ($attempt < $maxAttempts) {
                        usleep(200000 * $attempt);
                        continue;
                    }

                    return [];
                }

                $http = new Client([
                    'timeout' => $readTimeout,
                    'connectTimeout' => 5,
                ]);

                Log::debug("API Call URL: {$url} (attempt {$attempt})");

                $response = $http->get(
                    $url,
                    $query,
                    [
                        'headers' => [
                            'Authorization' => 'Bearer ' . $token,
                            'Accept' => 'application/json',
                        ],
                    ]
                );

                $statusCode = $response->getStatusCode();
                Log::debug("API Response Status: {$statusCode}");

                // 401: token expired/invalidated. Clear it everywhere, retry once.
                if ($statusCode === 401) {
                    $this->clearApiToken();
                    if (!$tokenResetOnce) {
                        $tokenResetOnce = true;
                        $attempt--; // token refresh must not consume a retry slot
                        continue;
                    }

                    return [];
                }

                // 5xx and 429 (rate limit) are transient -> retry with backoff.
                if ($statusCode >= 500 || $statusCode === 429) {
                    Log::error("API request to {$endpoint} returned HTTP {$statusCode} (attempt {$attempt})");
                    if ($attempt < $maxAttempts) {
                        usleep(($statusCode === 429 ? 500000 : 200000) * $attempt);
                        continue;
                    }

                    return [];
                }

                // Other 4xx are not retryable.
                if (!$response->isOk()) {
                    Log::error("API request to {$endpoint} failed with HTTP {$statusCode}");

                    return [];
                }

                $data = $response->getJson();

                if ($cacheKey !== null && is_array($data) && !empty($data)) {
                    $this->writeApiCache($cacheKey, $data, strlen($response->getStringBody()));
                }

                return is_array($data) ? $data : [];
            } catch (\Exception $e) {
                $message = $e->getMessage();
                Log::error("API Exception (attempt {$attempt}) for {$endpoint}: " . $message);

                $isTimeout = stripos($message, 'timed out') !== false || stripos($message, 'timeout') !== false;
                if ($isTimeout) {
                    $this->lastApiFailureWasTimeout = true;

                    // Only one (shorter) timeout retry, and only when explicitly
                    // allowed; otherwise bail out so waits never stack up.
                    if ($allowTimeoutRetry && !$timeoutRetried && $attempt < $maxAttempts) {
                        $timeoutRetried = true;
                        $readTimeout = 10;
                        usleep(200000);
                        continue;
                    }

                    return [];
                }

                // Other transient errors (DNS/TLS/connection reset): brief retry.
                if ($attempt < $maxAttempts) {
                    usleep(200000 * $attempt);
                    continue;
                }

                return [];
            }
        }

        return [];
    }

    /**
     * Fire multiple GET requests against the same API endpoint concurrently.
     * Same auth / caching / stale-fallback semantics as callApi().
     *
     * @param string $endpoint API endpoint.
     * @param array<int|string, array<string, mixed>> $queryList Map of key => query params.
     * @param int $cacheSeconds Same meaning as in callApi().
     * @return array<int|string, array> Map of the same keys => decoded API response
     *   (or [] for any request that failed and has no stale copy).
     */
    protected function callApiMulti(string $endpoint, array $queryList, int $cacheSeconds = 0): array
    {
        if (empty($queryList)) {
            return [];
        }

        if ($cacheSeconds <= 0) {
            $cacheSeconds = $this->autoCacheSeconds($endpoint);
        }

        $results = [];
        $toFetch = [];

        // Serve whatever we can from cache first.
        foreach ($queryList as $key => $query) {
            $results[$key] = [];
            if ($cacheSeconds > 0) {
                $cacheKey = $this->buildApiCacheKey($endpoint, $query);
                $cached = $this->readFresh($cacheKey, $cacheSeconds);
                if ($cached !== null) {
                    $results[$key] = $cached;
                    continue;
                }
            }
            $toFetch[$key] = $query;
        }

        if (empty($toFetch)) {
            return $results;
        }

        // No curl_multi: fall back to the sequential path (has its own stale logic).
        if (!function_exists('curl_multi_init')) {
            foreach ($toFetch as $key => $query) {
                $results[$key] = $this->callApi($endpoint, $query, $cacheSeconds);
            }

            return $results;
        }

        $token = $this->getApiToken();
        if (!$token) {
            // Login failed: serve stale copies where we have them.
            return $this->applyStaleToMissing($endpoint, $toFetch, $results, $cacheSeconds);
        }

        $baseUrl = rtrim(trim(env('API_BASE_URL', 'https://aci-cms.aranetacity.com/')), '/');

        $multiHandle = curl_multi_init();
        $curlHandles = [];

        foreach ($toFetch as $key => $query) {
            $url = $baseUrl . $endpoint;
            if (!empty($query)) {
                $url .= '?' . http_build_query($query);
            }

            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTPHEADER => [
                    'Authorization: Bearer ' . $token,
                    'Accept: application/json',
                ],
                CURLOPT_TIMEOUT => 15,
                CURLOPT_CONNECTTIMEOUT => 5,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_MAXREDIRS => 5,
                CURLOPT_SSL_VERIFYPEER => !\Cake\Core\Configure::read('debug'),
                CURLOPT_SSL_VERIFYHOST => \Cake\Core\Configure::read('debug') ? 0 : 2,
            ]);
            curl_multi_add_handle($multiHandle, $ch);
            $curlHandles[$key] = $ch;

            Log::debug("API Multi Call URL: {$url}");
        }

        $stillRunning = null;
        do {
            $status = curl_multi_exec($multiHandle, $stillRunning);
            if ($stillRunning) {
                curl_multi_select($multiHandle);
            }
        } while ($stillRunning && $status === CURLM_OK);

        $needsTokenReset = false;
        $retryKeys = [];
        $failedKeys = [];

        foreach ($curlHandles as $key => $ch) {
            $statusCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $body = curl_multi_getcontent($ch);
            $curlError = curl_error($ch);

            Log::debug("API Multi Response Status ({$key}): {$statusCode}");

            if ($statusCode === 401) {
                $needsTokenReset = true;
                $retryKeys[] = $key;
            } elseif ($statusCode >= 200 && $statusCode < 300 && $curlError === '') {
                $data = json_decode((string)$body, true);
                if (is_array($data)) {
                    $results[$key] = $data;
                    if ($cacheSeconds > 0 && !empty($data)) {
                        $cacheKey = $this->buildApiCacheKey($endpoint, $toFetch[$key]);
                        $this->writeApiCache($cacheKey, $data, strlen((string)$body));
                    }
                } else {
                    $failedKeys[] = $key;
                }
            } else {
                Log::error("API multi-request to {$endpoint} (key {$key}) failed with HTTP {$statusCode}" . ($curlError !== '' ? " ({$curlError})" : ''));
                $failedKeys[] = $key;
            }

            curl_multi_remove_handle($multiHandle, $ch);
            curl_close($ch);
        }

        curl_multi_close($multiHandle);

        if ($needsTokenReset) {
            $this->clearApiToken();
            foreach ($retryKeys as $key) {
                // callApi() re-logs in and has its own retry + stale fallback.
                $results[$key] = $this->callApi($endpoint, $toFetch[$key], $cacheSeconds);
            }
        }

        // Failed requests: serve stale; if there is none, retry sequentially
        // (capped, so a total outage can't stack up many slow calls).
        if (!empty($failedKeys)) {
            $noStale = [];
            foreach ($failedKeys as $key) {
                $stale = null;
                if ($cacheSeconds > 0) {
                    $stale = $this->serveStale($this->buildApiCacheKey($endpoint, $toFetch[$key]), $endpoint);
                }
                if ($stale !== null) {
                    $results[$key] = $stale;
                } else {
                    $noStale[] = $key;
                }
            }

            if (count($noStale) <= 3) {
                foreach ($noStale as $key) {
                    $results[$key] = $this->callApi($endpoint, $toFetch[$key], $cacheSeconds);
                }
            }
        }

        return $results;
    }

    /**
     * Fills empty results with stale copies (used when login failed entirely).
     *
     * @param string $endpoint API endpoint.
     * @param array $toFetch Queries that were not served from cache.
     * @param array $results Results so far.
     * @param int $cacheSeconds Cache flag.
     * @return array
     */
    private function applyStaleToMissing(string $endpoint, array $toFetch, array $results, int $cacheSeconds): array
    {
        if ($cacheSeconds <= 0) {
            return $results;
        }

        foreach ($toFetch as $key => $query) {
            if (empty($results[$key])) {
                $stale = $this->serveStale($this->buildApiCacheKey($endpoint, $query), $endpoint);
                if ($stale !== null) {
                    $results[$key] = $stale;
                }
            }
        }

        return $results;
    }
}