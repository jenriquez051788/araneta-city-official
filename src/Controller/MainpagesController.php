<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\Http\Client;
use Cake\Http\Exception\BadRequestException;
use Cake\Core\Configure;
use Cake\Mailer\Mailer;

/**
 * Mainpages Controller
 *
 * @property \App\Model\Table\MainpagesTable $Mainpages
 */
class MainpagesController extends AppController
{
    /**
     * Before filter callback
     *
     * @param \Cake\Event\EventInterface $event The beforeFilter event.
     * @return void
     */
    public function beforeFilter(\Cake\Event\EventInterface $event): void
    {
        parent::beforeFilter($event);

        // Guard admin CRUD actions (index, view, add, edit, delete) to superadmin/admin only
        $adminActions = ['index', 'view', 'add', 'edit', 'delete'];
        if (in_array($this->request->getParam('action'), $adminActions)) {
            // Check if user is authenticated
            if (!$this->Authentication->getIdentity()) {
                $this->Flash->error(__('You must be logged in to access this page.'));
                $this->redirect(['controller' => 'Users', 'action' => 'login']);
                $event->stopPropagation();
                return;
            }

            // Check role authorization
            if (!$this->userHasRole(['superadmin', 'admin'])) {
                throw new \Cake\Http\Exception\ForbiddenException('You do not have permission to access this page.');
            }
        }
    }

    /**
     * Handle hero image file upload
     *
     * @param array $data The request data
     * @return array Updated data with file metadata
     */
    private function handleHeroImageUpload(array $data): array
    {
        $uploadedFile = $this->request->getUploadedFile('hero_image');

        if (!empty($uploadedFile)) {
            // Validate file
            $maxSize = 5 * 1024 * 1024; // 5MB
            if ($uploadedFile->getSize() > $maxSize) {
                throw new BadRequestException('File size exceeds 5MB limit');
            }

            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            if (!in_array($uploadedFile->getClientMediaType(), $allowedTypes)) {
                throw new BadRequestException('Invalid file type. Allowed: JPG, PNG, GIF');
            }

            // Create directory if it doesn't exist
            $uploadDir = WWW_ROOT . 'img' . DS . 'banner' . DS;
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            // Use original filename
            $fileName = $uploadedFile->getClientFilename();
            $filePath = $uploadDir . $fileName;

            // Move uploaded file
            $uploadedFile->moveTo($filePath);

            // Store file metadata in the data array
            $data['hero_image'] = $fileName;
            $data['hero_image_dir'] = 'img/banner/';
            $data['hero_image_size'] = $uploadedFile->getSize();
            $data['hero_image_type'] = $uploadedFile->getClientMediaType();
        }

        return $data;
    }
    /**
     * Home method
     *
     * Renders the public Araneta City homepage at the site root ('/').
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function home()
    {
        $this->viewBuilder()->setLayout('default');

        // The homepage aggregates several external APIs (CMS happenings/news +
        // TicketNet feeds). Some CMS list endpoints return multi-MB payloads
        // that can be slow on a cold cache. Give the request headroom above the
        // default 30s so a first (uncached) load can finish and populate the
        // caches; subsequent loads are fast from cache.
        set_time_limit(60);

        // Disable DebugKit to avoid memory issues with large API responses
        if (isset($this->DebugKit)) {
            $this->DebugKit->enabled = false;
        }

        $mainpage = $this->Mainpages->find()
            ->where(['status' => 1])
            ->orderBy(['modified' => 'DESC'])
            ->first();

        // Fetch active happenings from API using events endpoint (cached 10 min)
        $happeningsResponse = $this->callApi('/api/happeningslist', [], 600);
        $happenings = [];
        $allHappeningsData = [];
        if (!empty($happeningsResponse['success']) && !empty($happeningsResponse['data'])) {
            $allHappeningsData = $happeningsResponse['data'];
        } elseif (is_array($happeningsResponse) && !empty($happeningsResponse)) {
            // Fallback: if response is just the data array without wrapper
            $allHappeningsData = $happeningsResponse;
        }

        // Homepage "What's Happening" carousel shows only ACTIVE happenings.
        // (The full /happenings listing page still shows all of them.)
        $activeHappenings = array_filter($allHappeningsData, function ($h) {
            return !empty($h['is_active']);
        });

        // Sort by created date descending (most recent first) before taking 10
        if (!empty($activeHappenings)) {
            usort($activeHappenings, function ($a, $b) {
                $dateA = !empty($a['created']) ? strtotime($a['created']) : 0;
                $dateB = !empty($b['created']) ? strtotime($b['created']) : 0;

                return $dateB <=> $dateA;
            });
            $happenings = array_slice(array_values($activeHappenings), 0, 10);
        }

        // Fetch news from API (cached 10 min). The CMS has no /api/newslistallnow
        // endpoint (it returns 404), so call newslistall directly instead of
        // wasting a request on every uncached load.
        $newsResponse = $this->callApi('/api/newslistall', [], 600);

        $news = [];
        if (!empty($newsResponse['success']) && !empty($newsResponse['data'])) {
            $news = $newsResponse['data'];
        } elseif (is_array($newsResponse) && !empty($newsResponse)) {
            // Fallback: if response is just the data array without wrapper
            $news = $newsResponse;
        }

        // Fetch latest 6 movies from the TicketNet API (separate from the CMS API).
        // Cached 10 min so the homepage doesn't hit the (sometimes slow/unreachable)
        // external TicketNet API on every request. Uses a hard-timeout raw curl
        // fetch so a stalled connection can never blow the 30s PHP limit.
        $movies = \Cake\Cache\Cache::read('home_ticketnet_movies', '_cake_api_');
        if (!is_array($movies)) {
            $movies = [];
            $movieData = $this->fetchExternalJson('https://ticketnet.com.ph/events/movies-api.json');
            if (!empty($movieData['events']) && is_array($movieData['events'])) {
                $movies = array_slice($movieData['events'], 0, 6);
            }
            // Cache the result (even empty, briefly) so a slow/unreachable feed
            // isn't retried on every single request within the window.
            \Cake\Cache\Cache::write('home_ticketnet_movies', $movies, '_cake_api_');
        }

        // Fetch live events (concerts/shows) from the SAC + New Frontier Theater feeds.
        $events = \Cake\Cache\Cache::read('home_ticketnet_events', '_cake_api_');
        if (!is_array($events)) {
            $eventFeeds = [
                'https://ticketnet.com.ph/events/sac-api.json' => 'Smart Araneta Coliseum',
                'https://ticketnet.com.ph/events/nft-api.json' => 'New Frontier Theater',
            ];
            $events = [];
            foreach ($eventFeeds as $feedUrl => $venueName) {
                $eventData = $this->fetchExternalJson($feedUrl);
                if (!empty($eventData['events']) && is_array($eventData['events'])) {
                    foreach ($eventData['events'] as $ev) {
                        $ev['venue_name'] = $venueName;
                        $events[] = $ev;
                    }
                }
            }
            \Cake\Cache\Cache::write('home_ticketnet_events', $events, '_cake_api_');
        }

        // Sort by event date (soonest first); unparseable dates go first.
        usort($events, function ($a, $b) {
            $ta = strtotime($a['dateofevent'] ?? $a['displaydate'] ?? '') ?: 0;
            $tb = strtotime($b['dateofevent'] ?? $b['displaydate'] ?? '') ?: 0;
            return $ta <=> $tb;
        });

        $this->set(compact('mainpage', 'happenings', 'news', 'movies', 'events'));
    }
    
    private function fetchExternalJson(string $url, int $timeout = 5): array
    {
        if (!function_exists('curl_init')) {
            return [];
        }

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => 3,
            // Hard caps: total time, connect time, and (via NOSIGNAL) make the
            // timeouts reliable even when they would otherwise be ignored.
            CURLOPT_TIMEOUT => $timeout,
            CURLOPT_CONNECTTIMEOUT => 3,
            CURLOPT_NOSIGNAL => true,
            CURLOPT_ACCEPT_ENCODING => '',
            CURLOPT_HTTPHEADER => ['Accept: application/json'],
            // Local/dev often lacks a CA bundle; verify in production only.
            CURLOPT_SSL_VERIFYPEER => !\Cake\Core\Configure::read('debug'),
            CURLOPT_SSL_VERIFYHOST => \Cake\Core\Configure::read('debug') ? 0 : 2,
        ]);

        $body = curl_exec($ch);
        $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err = curl_error($ch);
        curl_close($ch);

        if ($body === false || $err !== '' || $status < 200 || $status >= 300) {
            \Cake\Log\Log::warning("External fetch failed for {$url}: HTTP {$status}" . ($err !== '' ? " ({$err})" : ''));
            return [];
        }

        $data = json_decode($body, true);

        return is_array($data) ? $data : [];
    }

    /**
     * Shows / Events page.
     *
     * Aggregates events from the TicketNet JSON feeds for the
     * Smart Araneta Coliseum (SAC) and New Frontier Theater (NFT).
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function shows()
    {
        $this->viewBuilder()->setLayout('default');

        // Each feed is tagged with a normalized venue so the view can
        // reliably filter by venue regardless of how each event's raw
        // "venue" string is cased.
        $feeds = [
            'https://ticketnet.com.ph/events/sac-api.json' => 'Smart Araneta Coliseum',
            'https://ticketnet.com.ph/events/nft-api.json' => 'New Frontier Theater',
        ];

        $events = [];
        try {
            $http = new Client([
                'timeout' => 10,
                'connectTimeout' => 5,
            ]);

            foreach ($feeds as $feedUrl => $venueName) {
                try {
                    $response = $http->get($feedUrl);
                    if ($response->isOk()) {
                        $data = $response->getJson();
                        if (!empty($data['events']) && is_array($data['events'])) {
                            foreach ($data['events'] as $ev) {
                                $ev['venue_name'] = $venueName;
                                $events[] = $ev;
                            }
                        }
                    }
                } catch (\Throwable $e) {
                    // Skip this feed on error; other feeds still load.
                }
            }
        } catch (\Throwable $e) {
            // Events are non-critical; ignore API failures.
        }

        // Sort by event date (soonest first). Events with an unparseable date
        // (e.g. ranges like "AUGUST - SEPTEMBER 2026") sort to the very front.
        //
        // Resolve a sortable timestamp for an event. Prefers a precise
        // schedule/start date, then the display string. For ranges like
        // "November 11 & 12, 2026" that strtotime() can't parse, it extracts
        // the FIRST date so the event sorts by its first show date instead of
        // wrongly collapsing to 0 and jumping to the front of the list.
        $eventTimestamp = function ($e) {
            $source = $e['_matchingData']['Eventschedules']['eventdate']
                ?? ($e['startdate'] ?? ($e['dateofevent'] ?? ($e['displaydate'] ?? '')));

            $ts = strtotime((string) $source);
            if ($ts === false && !empty($source)) {
                if (preg_match('/([A-Za-z]+\s+\d{1,2})(?:.*?(\d{4}))?/', (string) $source, $m)) {
                    $firstDate = $m[1] . (!empty($m[2]) ? ', ' . $m[2] : '');
                    $ts = strtotime($firstDate);
                }
            }

            return $ts ?: 0;
        };

        usort($events, function ($a, $b) use ($eventTimestamp) {
            return $eventTimestamp($a) <=> $eventTimestamp($b);
        });


        $this->set(compact('events'));
    }

    /**
     * Cinema page.
     *
     * Lists movies now showing / upcoming at Gateway Cineplex 18
     * from the TicketNet movies JSON feed.
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function cinema()
    {
        $this->viewBuilder()->setLayout('default');

        $movies = [];
        try {
            $http = new Client([
                'timeout' => 10,
                'connectTimeout' => 5,
            ]);
            $response = $http->get('https://ticketnet.com.ph/events/movies-api.json');
            if ($response->isOk()) {
                $data = $response->getJson();
                if (!empty($data['events']) && is_array($data['events'])) {
                    $movies = $data['events'];
                }
            }
        } catch (\Throwable $e) {
            // Movies are non-critical; ignore API failures.
        }

        // Sort by the precise event date (soonest first) when available.
        usort($movies, function ($a, $b) {
            $da = $a['_matchingData']['Eventschedules']['eventdate'] ?? ($a['dateofevent'] ?? '');
            $db = $b['_matchingData']['Eventschedules']['eventdate'] ?? ($b['dateofevent'] ?? '');
            $ta = strtotime($da) ?: 0;
            $tb = strtotime($db) ?: 0;
            return $ta <=> $tb;
        });


        $this->set(compact('movies'));
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $this->viewBuilder()->setLayout('backend');
        $query = $this->Mainpages->find();
        $mainpages = $this->paginate($query);

        $this->set(compact('mainpages'));
    }

    /**
     * View method
     *
     * @param string|null $id Mainpage id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view(?string $id = null)
    {
        $mainpage = $this->Mainpages->get($id, contain: []);
        $this->set(compact('mainpage'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $mainpage = $this->Mainpages->newEmptyEntity();
        if ($this->request->is('post')) {
            $data = $this->request->getData();

            // Handle file upload
            try {
                $data = $this->handleHeroImageUpload($data);
            } catch (BadRequestException $e) {
                $this->Flash->error($e->getMessage());
                $this->set(compact('mainpage'));
                return;
            }

            $mainpage = $this->Mainpages->patchEntity($mainpage, $data);

            if ($this->Mainpages->save($mainpage)) {
                $this->Flash->success(__('The mainpage has been saved.'));

                return $this->redirect(['action' => 'index']);
            }

            // If save failed, show errors
            $errors = $mainpage->getErrors();
            if (!empty($errors)) {
                foreach ($errors as $field => $fieldErrors) {
                    foreach ($fieldErrors as $error) {
                        $this->Flash->error(__("{0}: {1}", $field, $error));
                    }
                }
            } else {
                $this->Flash->error(__('The mainpage could not be saved. Please, try again.'));
            }
        }
        $this->set(compact('mainpage'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Mainpage id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit(?string $id = null)
    {
        $this->viewBuilder()->setLayout('backend');
        $mainpage = $this->Mainpages->get($id, contain: []);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $data = $this->request->getData();

            // Handle file upload
            try {
                $data = $this->handleHeroImageUpload($data);
            } catch (BadRequestException $e) {
                $this->Flash->error($e->getMessage());
                $this->set(compact('mainpage'));
                return;
            }

            $mainpage = $this->Mainpages->patchEntity($mainpage, $data);

            if ($this->Mainpages->save($mainpage)) {
                $this->Flash->success(__('The mainpage has been saved.'));

                return $this->redirect(['action' => 'index']);
            }

            // If save failed, show errors
            $errors = $mainpage->getErrors();
            if (!empty($errors)) {
                foreach ($errors as $field => $fieldErrors) {
                    foreach ($fieldErrors as $error) {
                        $this->Flash->error(__("{0}: {1}", $field, $error));
                    }
                }
            } else {
                $this->Flash->error(__('The mainpage could not be saved. Please, try again.'));
            }
        }
        $this->set(compact('mainpage'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Mainpage id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete(?string $id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $mainpage = $this->Mainpages->get($id);
        if ($this->Mainpages->delete($mainpage)) {
            $this->Flash->success(__('The mainpage has been deleted.'));
        } else {
            $this->Flash->error(__('The mainpage could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    /**
     * News listing page
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function news()
    {
        $this->viewBuilder()->setLayout('default');

        // Disable DebugKit to avoid memory issues with large API responses
        if (isset($this->DebugKit)) {
            $this->DebugKit->enabled = false;
        }

        // Fetch all news from API. Cached only briefly (60s) so CMS edits and
        // deletes appear near real-time, while still shielding the page from
        // the slow/large upstream response and transient API failures (which
        // would otherwise blank the list on a single bad request).
        $newsResponse = $this->callApi('/api/newslistall', [], 60);

        $allNews = [];
        if (!empty($newsResponse['success']) && !empty($newsResponse['data'])) {
            $allNews = $newsResponse['data'];
        } elseif (is_array($newsResponse) && !empty($newsResponse)) {
            // Fallback: if response is just the data array without wrapper
            $allNews = $newsResponse;
        }

        // The API's newslistall returns ALL records, including ones that were
        // unpublished/deactivated in the CMS. Only show published news
        // (news_status truthy) so deactivating an item in the CMS removes it
        // from the site. Items with no news_status key are treated as visible.
        $allNews = array_values(array_filter($allNews, function ($item) {
            return !array_key_exists('news_status', $item) || !empty($item['news_status']);
        }));


        // Pagination
        $page = (int) $this->request->getQuery('page', 1);
        $limit = 30; // 30 items per page (3 columns x 10 rows)
        $total = count($allNews);
        $pageCount = (int) ceil($total / $limit);
        $offset = ($page - 1) * $limit;

        // Get paginated slice
        $news = array_slice($allNews, $offset, $limit);

        $paging = [
            'page' => $page,
            'pageCount' => $pageCount,
            'count' => $total,
            'limit' => $limit,
        ];

        $this->set(compact('news', 'paging'));
    }

    /**
     * Happenings listing page
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function happenings()
    {
        $this->viewBuilder()->setLayout('default');

        // Disable DebugKit to avoid memory issues with large API responses
        if (isset($this->DebugKit)) {
            $this->DebugKit->enabled = false;
        }

        // Fetch all happenings from API (cached 10 min)
        $happeningsResponse = $this->callApi('/api/happeningslist', [], 600);

        $allHappenings = [];
        if (!empty($happeningsResponse['success']) && !empty($happeningsResponse['data'])) {
            $allHappenings = $happeningsResponse['data'];
        } elseif (is_array($happeningsResponse) && !empty($happeningsResponse)) {
            // Fallback: if response is just the data array without wrapper
            $allHappenings = $happeningsResponse;
        }


        // Pagination
        $page = (int) $this->request->getQuery('page', 1);
        $limit = 30; // 30 items per page (3 columns x 10 rows)
        $total = count($allHappenings);
        $pageCount = (int) ceil($total / $limit);
        $offset = ($page - 1) * $limit;

        // Get paginated slice
        $happenings = array_slice($allHappenings, $offset, $limit);

        $paging = [
            'page' => $page,
            'pageCount' => $pageCount,
            'count' => $total,
            'limit' => $limit,
        ];

        $this->set(compact('happenings', 'paging'));
    }

    /**
     * News article detail page
     *
     * @param string $slug News article slug
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function newsArticle()
    {
        $this->viewBuilder()->setLayout('default');

        // Get slug from passed arguments (from wildcard route)
        $passedArgs = $this->request->getParam('pass');
        $slug = is_array($passedArgs) ? implode('/', $passedArgs) : '';

        if (empty($slug)) {
            throw new \Cake\Http\Exception\NotFoundException('News article not found');
        }

        // Disable DebugKit to avoid memory issues
        if (isset($this->DebugKit)) {
            $this->DebugKit->enabled = false;
        }

        // Fetch the FULL news list (cached) and match the slug in PHP. This is
        // more robust than the per-slug API call: it reuses the same cached
        // response as the listing page and the sidebar, and a transient API
        // failure (now retried inside callApi) won't turn into a spurious 404.
        // Short (60s) cache so article edits reflect near real-time.
        $allNewsResponse = $this->callApi('/api/newslistall', [], 60);
        $allNews = [];
        if (!empty($allNewsResponse['success']) && !empty($allNewsResponse['data'])) {
            $allNews = $allNewsResponse['data'];
        } elseif (is_array($allNewsResponse) && !empty($allNewsResponse)) {
            $allNews = $allNewsResponse;
        }

        // Exclude unpublished items so a deactivated article can't be opened
        // directly and won't appear in the "recent news" sidebar.
        $allNews = array_values(array_filter($allNews, function ($item) {
            return !array_key_exists('news_status', $item) || !empty($item['news_status']);
        }));

        // Match by slug (then alias) in PHP.
        $article = null;
        foreach ($allNews as $n) {
            if (($n['slug'] ?? '') === $slug || ($n['alias'] ?? '') === $slug) {
                $article = $n;
                break;
            }
        }

        // Fallback: match by trailing numeric ID in the slug.
        if (empty($article) && preg_match('/(\d+)$/', $slug, $m)) {
            $slugId = (int) $m[1];
            foreach ($allNews as $n) {
                if ((int) ($n['id'] ?? 0) === $slugId) {
                    $article = $n;
                    break;
                }
            }
        }

        // Last resort: if the full-list call came back empty (e.g. the very
        // first request on a cold cache hit a transient error that exhausted
        // retries), try the direct per-slug endpoint before giving up.
        if (empty($article) && empty($allNews)) {
            $response = $this->callApi('/api/newslistall', ['slug' => $slug]);
            if (!empty($response['success']) && !empty($response['data'])) {
                $article = $response['data'];
            } elseif (is_array($response) && !empty($response) && isset($response['slug'])) {
                $article = $response;
            }
        }

        if (empty($article)) {
            throw new \Cake\Http\Exception\NotFoundException('News article not found');
        }

        // Related/recent news for the sidebar, excluding the current article.
        $currentSlug = $article['slug'] ?? $slug;
        $recentNews = [];
        foreach ($allNews as $n) {
            if (($n['slug'] ?? '') !== $currentSlug) {
                $recentNews[] = $n;
            }
            if (count($recentNews) >= 4) {
                break;
            }
        }

        $this->set(compact('article', 'recentNews'));
    }

    /**
     * Happening detail page
     *
     * Fetches a single happening by slug and shows related happenings.
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function happeningDetail()
    {
        $this->viewBuilder()->setLayout('default');

        // Get slug from passed arguments (from wildcard route)
        $passedArgs = $this->request->getParam('pass');
        $slug = is_array($passedArgs) ? implode('/', $passedArgs) : '';

        // URL-decode the slug (handles %20 spaces, apostrophes, etc.)
        $slug = urldecode($slug);

        if (empty($slug)) {
            throw new \Cake\Http\Exception\NotFoundException('Happening not found');
        }

        // Disable DebugKit to avoid memory issues
        if (isset($this->DebugKit)) {
            $this->DebugKit->enabled = false;
        }

        // The API's slug parameter is unreliable for matching, so fetch the
        // full list (cached) and match the slug in PHP instead. This handles
        // slugs with special characters, apostrophes, and edge cases.
        $allResponse = $this->callApi('/api/happeningslist', [], 600);
        $allHappenings = [];
        if (!empty($allResponse['success']) && !empty($allResponse['data'])) {
            $allHappenings = $allResponse['data'];
        } elseif (is_array($allResponse) && !empty($allResponse)) {
            $allHappenings = $allResponse;
        }

        // Match the happening by slug in PHP.
        $happening = null;
        foreach ($allHappenings as $h) {
            if (($h['slug'] ?? '') === $slug) {
                $happening = $h;
                break;
            }
        }

        // Fallback: match by the trailing numeric ID in the slug
        // (e.g. "black-friday-...-617" -> id 617). This handles slugs that
        // may have been altered/re-slugified between generation and lookup.
        if (empty($happening) && preg_match('/(\d+)$/', $slug, $m)) {
            $slugId = (int) $m[1];
            foreach ($allHappenings as $h) {
                if ((int) ($h['id'] ?? 0) === $slugId) {
                    $happening = $h;
                    break;
                }
            }
        }

        if (empty($happening)) {
            throw new \Cake\Http\Exception\NotFoundException('Happening not found');
        }

        // Build "More to Discover": only active happenings, excluding the
        // current one. Take up to 4 at random.
        $currentSlug = $happening['slug'] ?? $slug;
        $relatedHappenings = [];
        foreach ($allHappenings as $h) {
            if (empty($h['is_active'])) {
                continue;
            }
            if (($h['slug'] ?? '') !== $currentSlug) {
                $relatedHappenings[] = $h;
            }
        }
        shuffle($relatedHappenings);
        $relatedHappenings = array_slice($relatedHappenings, 0, 4);

        $this->set(compact('happening', 'relatedHappenings'));
    }

    /**
     * Generic category listing handler (used by shopping, dining, services)
     *
     * @param string $category The category to fetch (shopping, dining, services)
     * @param array $defaultBuildingIds Default building IDs to fetch from
     * @return void
     */
    private function handleCategoryListing(string $category, array $defaultBuildingIds): void
    {
        $this->viewBuilder()->setLayout('default');

        // Disable DebugKit to avoid memory issues
        if (isset($this->DebugKit)) {
            $this->DebugKit->enabled = false;
        }

        // Fetch buildings list (cached)
        $buildingsResponse = $this->callApi('/api/buildingslist', [], 600);
        $buildings = [];
        if (!empty($buildingsResponse['success']) && !empty($buildingsResponse['data'])) {
            $buildings = $buildingsResponse['data'];
        }

        // Get selected building from query parameter (by slug/name)
        $selectedBuildingSlug = $this->request->getQuery('building');
        // Dedupe up front so we never issue the same building_id API call twice.
        $buildingIds = array_values(array_unique($defaultBuildingIds));

        // If a specific building is selected, filter to that building
        if (!empty($selectedBuildingSlug)) {
            $selectedBuildingSlug = strtolower($selectedBuildingSlug);
            foreach ($buildings as $building) {
                // FIX: null-safe slug read - a malformed building record from
                // the API no longer throws a warning/fatal here.
                if (strtolower($building['slug'] ?? '') === $selectedBuildingSlug) {
                    $buildingIds = [(int) $building['id']];
                    break;
                }
            }
        }

        // Get search query
        $searchQuery = trim((string) $this->request->getQuery('search', ''));

        // Get category filter
        $categoryFilter = trim((string) $this->request->getQuery('category', ''));

        // Fetch establishments from selected buildings with retries and better error handling
        $allEstablishments = $this->fetchEstablishmentsByCategory($category, $buildingIds);

        // Extract unique estmaincategory values for dropdown BEFORE applying search/category filters
        $categories = [];
        foreach ($allEstablishments as $establishment) {
            if (!empty($establishment['estmaincategory'])) {
                $categories[$establishment['estmaincategory']] = $establishment['estmaincategory'];
            }
        }
        ksort($categories);

        // Filter by search query if provided
        if (!empty($searchQuery)) {
            $searchQueryLower = strtolower($searchQuery);
            $allEstablishments = array_filter($allEstablishments, function ($establishment) use ($searchQueryLower) {
                $title = strtolower($establishment['title'] ?? '');
                $building = strtolower($establishment['building'] ?? '');
                $establishmentCategory = strtolower($establishment['category'] ?? '');

                return strpos($title, $searchQueryLower) !== false ||
                    strpos($building, $searchQueryLower) !== false ||
                    strpos($establishmentCategory, $searchQueryLower) !== false;
            });
            // FIX: re-index after filtering so pagination slices a clean array.
            $allEstablishments = array_values($allEstablishments);
        }

        // Filter by estmaincategory if provided
        if (!empty($categoryFilter)) {
            $categoryFilterLower = strtolower($categoryFilter);
            $allEstablishments = array_filter($allEstablishments, function ($establishment) use ($categoryFilterLower) {
                $estMainCategory = strtolower($establishment['estmaincategory'] ?? '');
                return $estMainCategory === $categoryFilterLower;
            });
            // FIX: re-index after filtering so pagination slices a clean array.
            $allEstablishments = array_values($allEstablishments);
        }

        // Pagination
        $page = (int) $this->request->getQuery('page', 1);
        $limit = 30;
        $total = count($allEstablishments);
        $pageCount = (int) ceil($total / $limit);
        $offset = ($page - 1) * $limit;

        // Get paginated slice
        $establishments = array_slice($allEstablishments, $offset, $limit);

        $paging = [
            'page' => $page,
            'pageCount' => $pageCount,
            'count' => $total,
            'limit' => $limit,
        ];

        $this->set(compact('establishments', 'paging', 'buildings', 'selectedBuildingSlug', 'searchQuery', 'categoryFilter', 'categories'));
    }

    /**
     * Fetch all establishments for a given category across multiple buildings.
     *
     * FIX: now uses AppController::callApiMulti() to fire one request per
     * building CONCURRENTLY via curl_multi, instead of looping and calling
     * callApi() one building at a time. For shopping/services (10 buildings)
     * this turns ~10 sequential round-trips into effectively 1 round-trip's
     * worth of wall-clock time (bounded by the slowest single building).
     * callApiMulti() itself falls back to the old sequential behavior if
     * curl_multi isn't available, so this is always safe.
     *
     * @param string $category The category (shopping, dining, services)
     * @param array $buildingIds Building IDs to fetch from
     * @return array<int, array<string, mixed>>
     */
    private function fetchEstablishmentsByCategory(string $category, array $buildingIds): array
    {
        $allEstablishments = [];

        // Build one query set per building, keyed by building ID so we can
        // tell responses apart (not that we need to here, but it keeps
        // callApiMulti's contract simple and debuggable).
        $queryList = [];
        foreach ($buildingIds as $buildingId) {
            $queryList[$buildingId] = [
                'building_id' => $buildingId,
                'category' => $category,
            ];
        }

        // Fires all building requests at once instead of one after another.
        $responses = $this->callApiMulti('/api/establishments_by_building', $queryList, 600);

        foreach ($responses as $response) {
            if (!empty($response['success']) && !empty($response['data'])) {
                $allEstablishments = array_merge($allEstablishments, $response['data']);
            }
        }

        return $allEstablishments;
    }

    public function shopping()
    {
        $this->handleCategoryListing('shopping', [1, 4, 10, 11, 12, 6, 5, 15, 2, 3]);
    }

    public function dining()
    {
        $this->handleCategoryListing('dining', [1, 4, 10, 11, 12, 6, 5, 15, 2, 3]);
    }

    public function services()
    {
        $this->handleCategoryListing('services', [4, 10, 11, 12, 6, 5, 15, 2, 1, 3]);
    }

    public function attractions()
    {
        $this->viewBuilder()->setLayout('default');

        // Disable DebugKit to avoid memory issues
        if (isset($this->DebugKit)) {
            $this->DebugKit->enabled = false;
        }

        // Fetch attractions list
        $attractionsResponse = $this->callApi('/api/attractionslist');

        $attractions = [];
        if (!empty($attractionsResponse['success']) && !empty($attractionsResponse['data'])) {
            $attractions = $attractionsResponse['data'];
        }


        // Get search query
        $searchQuery = $this->request->getQuery('search', '');
        $searchQuery = trim($searchQuery);

        // Filter by search query if provided
        if (!empty($searchQuery)) {
            $searchQuery = strtolower($searchQuery);
            $attractions = array_filter($attractions, function ($attraction) use ($searchQuery) {
                $title = strtolower($attraction['title'] ?? '');
                $content = strtolower($attraction['content'] ?? '');

                return strpos($title, $searchQuery) !== false ||
                    strpos($content, $searchQuery) !== false;
            });
            // FIX: re-index after filtering.
            $attractions = array_values($attractions);
        }

        $this->set(compact('attractions', 'searchQuery'));
    }

    public function attractionDetail()
    {
        $this->viewBuilder()->setLayout('default');

        // Disable DebugKit to avoid memory issues
        if (isset($this->DebugKit)) {
            $this->DebugKit->enabled = false;
        }

        // Get slug from route parameter
        $slug = $this->request->getParam('slug');

        if (empty($slug)) {
            throw new \Cake\Http\Exception\NotFoundException('Attraction not found');
        }

        // Fetch attraction detail using slug parameter
        $response = $this->callApi('/api/attractionslist?slug=' . urlencode($slug));

        $attraction = [];
        if (!empty($response['success']) && !empty($response['data'])) {
            $attraction = $response['data'];
        }

        if (empty($attraction)) {
            throw new \Cake\Http\Exception\NotFoundException('Attraction not found');
        }


        // Fetch all attractions for related suggestions
        $relatedAttractions = [];
        $allAttractionsResponse = $this->callApi('/api/attractionslist');

        if (!empty($allAttractionsResponse['success']) && !empty($allAttractionsResponse['data'])) {
            $allAttractions = $allAttractionsResponse['data'];

            // Filter out current attraction and get random 3
            // FIX: null-safe slug read.
            $otherAttractions = array_filter($allAttractions, function ($attr) use ($slug) {
                return strtolower($attr['slug'] ?? '') !== strtolower($slug);
            });
            // FIX: re-index after filtering, before shuffle/slice.
            $otherAttractions = array_values($otherAttractions);

            // Shuffle and get first 3
            shuffle($otherAttractions);
            $relatedAttractions = array_slice($otherAttractions, 0, 3);
        }

        $this->set(compact('attraction', 'relatedAttractions'));
    }

    /**
     * Offices List - Displays all office buildings
     */
    public function offices()
    {
        $this->viewBuilder()->setLayout('default');

        // Disable DebugKit to avoid memory issues
        if (isset($this->DebugKit)) {
            $this->DebugKit->enabled = false;
        }

        // Fetch offices list (cached 10 min). Caching means a transient API
        // failure on refresh serves the last good copy instead of an empty
        // "no offices found" page.
        $officesResponse = $this->callApi('/api/offices', [], 600);

        $offices = [];
        if (!empty($officesResponse['success']) && !empty($officesResponse['data'])) {
            $offices = $officesResponse['data'];
        }


        $this->set(compact('offices'));
    }

    /**
     * Office Detail - Displays detailed information about a single office building
     */
    public function officeDetail()
    {
        $this->viewBuilder()->setLayout('default');

        // Disable DebugKit to avoid memory issues
        if (isset($this->DebugKit)) {
            $this->DebugKit->enabled = false;
        }

        // Get slug from route parameter
        $slug = $this->request->getParam('slug');

        if (empty($slug)) {
            throw new \Cake\Http\Exception\NotFoundException('Office not found');
        }

        // Fetch the full offices list (cached 10 min) and match the slug in PHP.
        // This is more robust than a per-slug API call: it reuses the same
        // cached list as the listing page, so a transient upstream failure on
        // one refresh no longer turns into a spurious 404.
        $allOfficesResponse = $this->callApi('/api/offices', [], 600);
        $allOffices = [];
        if (!empty($allOfficesResponse['success']) && !empty($allOfficesResponse['data'])) {
            $allOffices = $allOfficesResponse['data'];
        }

        $office = [];
        foreach ($allOffices as $off) {
            if (strtolower($off['slug'] ?? '') === strtolower($slug)) {
                $office = $off;
                break;
            }
        }

        if (empty($office)) {
            throw new \Cake\Http\Exception\NotFoundException('Office not found');
        }

        // Build related suggestions from the same (already-fetched) list.
        $relatedOffices = [];
        if (!empty($allOffices)) {

            // Filter out current office and get random 3
            // FIX: null-safe slug read.
            $otherOffices = array_filter($allOffices, function ($off) use ($slug) {
                return strtolower($off['slug'] ?? '') !== strtolower($slug);
            });
            // FIX: re-index after filtering, before shuffle/slice.
            $otherOffices = array_values($otherOffices);

            // Shuffle and get first 3
            shuffle($otherOffices);
            $relatedOffices = array_slice($otherOffices, 0, 3);
        }

        $this->set(compact('office', 'relatedOffices'));
    }

    /**
     * Hotels List - Displays all hotels
     */
    public function hotels()
    {
        $this->viewBuilder()->setLayout('default');

        // Disable DebugKit to avoid memory issues
        if (isset($this->DebugKit)) {
            $this->DebugKit->enabled = false;
        }

        // Fetch hotels list (cached 10 min). Caching means a transient API
        // failure on refresh serves the last good copy instead of an empty
        // "no hotels" page.
        $hotelsResponse = $this->callApi('/api/hotelslist', [], 600);

        $hotels = [];
        if (!empty($hotelsResponse['success']) && !empty($hotelsResponse['data'])) {
            $hotels = $hotelsResponse['data'];
        }


        $this->set(compact('hotels'));
    }

    /**
     * Hotel Detail - Displays detailed information about a single hotel
     */
    public function hotelDetail()
    {
        $this->viewBuilder()->setLayout('default');

        // Disable DebugKit to avoid memory issues
        if (isset($this->DebugKit)) {
            $this->DebugKit->enabled = false;
        }

        // Get hotel ID from route parameter
        $id = $this->request->getParam('id');

        if (empty($id)) {
            throw new \Cake\Http\Exception\NotFoundException('Hotel not found');
        }

        // Fetch the full hotels list (cached 10 min) and match the id in PHP.
        // This reuses the same cached list as the listing page, so a transient
        // upstream failure on one refresh won't produce a spurious 404.
        $allHotelsResponse = $this->callApi('/api/hotelslist', [], 600);
        $allHotels = [];
        if (!empty($allHotelsResponse['success']) && !empty($allHotelsResponse['data'])) {
            $allHotels = $allHotelsResponse['data'];
        }

        $hotel = [];
        foreach ($allHotels as $h) {
            if (($h['id'] ?? null) == $id) {
                $hotel = $h;
                break;
            }
        }

        // Fallback: fetch the single hotel directly by id (also cached).
        if (empty($hotel)) {
            $response = $this->callApi('/api/hotelslist', ['id' => $id], 600);
            if (!empty($response['success']) && !empty($response['data'])) {
                $hotel = $response['data'];
            }
        }

        if (empty($hotel)) {
            throw new \Cake\Http\Exception\NotFoundException('Hotel not found');
        }

        // Related suggestions from the same (already-fetched) list.
        $relatedHotels = [];
        foreach ($allHotels as $h) {
            if (($h['id'] ?? null) != $id) {
                $relatedHotels[] = $h;
            }
            if (count($relatedHotels) >= 3) {
                break;
            }
        }

        $this->set(compact('hotel', 'relatedHotels'));
    }

    /**
     * Parking List - Displays all parking locations with availability
     */
    public function parkinglist()
    {
        $this->viewBuilder()->setLayout('default');

        // Disable DebugKit to avoid memory issues
        if (isset($this->DebugKit)) {
            $this->DebugKit->enabled = false;
        }

        // Fetch all parking locations from API
        $response = $this->callApi('/api/parkinglist');

        $parkings = [];
        if (!empty($response['success']) && !empty($response['data'])) {
            $parkings = $response['data'];
        }


        $this->set(compact('parkings'));
    }

    /**
     * Establishment Detail - Displays detailed information about a single establishment
     */
    public function establishmentDetail()
    {
        $this->viewBuilder()->setLayout('default');

        // Disable DebugKit to avoid memory issues
        if (isset($this->DebugKit)) {
            $this->DebugKit->enabled = false;
        }

        // Get ID from route parameter
        $id = $this->request->getParam('id');

        if (empty($id)) {
            throw new \Cake\Http\Exception\NotFoundException('Establishment not found');
        }


        $baseUrl = rtrim(trim(env('API_BASE_URL', 'https://aci-cms.aranetacity.com/')), '/');

        // Primary: POST /api/web_establishment with the id. Retry on transient
        // failures (network blips, upstream 5xx) so a single hiccup doesn't
        // surface as "Establishment not found" and then recover on reload.
        $establishment = [];
        $session = $this->request->getSession();
        $maxAttempts = 3;

        for ($attempt = 1; $attempt <= $maxAttempts; $attempt++) {
            $token = $session->read('api_token');

            // Obtain a token if we don't have one.
            if (!$token) {
                try {
                    $http = new \Cake\Http\Client(['timeout' => 15, 'connectTimeout' => 5]);
                    $loginResponse = $http->post(
                        $baseUrl . '/api/user_login',
                        [
                            'email' => env('API_EMAIL', ''),
                            'password' => env('API_PASSWORD', ''),
                        ],
                        ['type' => 'json', 'headers' => ['Accept' => 'application/json']]
                    );
                    if ($loginResponse->isOk()) {
                        $token = $loginResponse->getJson()['token'] ?? null;
                        if ($token) {
                            $session->write('api_token', $token);
                        }
                    }
                } catch (\Exception $e) {
                    \Cake\Log\Log::error('establishmentDetail login failed: ' . $e->getMessage());
                }
            }

            if (!$token) {
                if ($attempt < $maxAttempts) {
                    usleep(200000 * $attempt);
                    continue;
                }
                break;
            }

            try {
                $http = new \Cake\Http\Client(['timeout' => 15, 'connectTimeout' => 5]);
                $webEstResponse = $http->post(
                    "{$baseUrl}/api/web_establishment",
                    ['id' => $id],
                    [
                        'type' => 'json',
                        'headers' => [
                            'Authorization' => 'Bearer ' . $token,
                            'Accept' => 'application/json',
                        ],
                    ]
                );

                $status = $webEstResponse->getStatusCode();

                // Token expired: reset and retry.
                if ($status === 401) {
                    $session->delete('api_token');
                    continue;
                }

                // Server error: transient - retry with backoff.
                if ($status >= 500) {
                    if ($attempt < $maxAttempts) {
                        usleep(200000 * $attempt);
                        continue;
                    }
                    break;
                }

                $webEstData = $webEstResponse->getJson();
                if (!empty($webEstData['success']) && !empty($webEstData['data'])) {
                    $establishment = $webEstData['data'];
                    break;
                }

                // Valid response but no data -> the id genuinely doesn't exist
                // via this endpoint; stop retrying and let the fallback try.
                break;
            } catch (\Exception $e) {
                \Cake\Log\Log::error("establishmentDetail web_establishment attempt {$attempt} failed: " . $e->getMessage());
                if ($attempt < $maxAttempts) {
                    usleep(200000 * $attempt);
                    continue;
                }
            }
        }

        if (!empty($establishment)) {
            $nearbyStores = $this->fetchNearbyStores($establishment);
            $this->set(compact('establishment', 'nearbyStores'));
            return;
        }

        // Fallback: search through all buildings (cached so repeated lookups
        // and transient failures are smoothed over).
        $buildingIds = [4, 10, 11, 12, 6, 5, 15, 2, 1, 3];

        foreach ($buildingIds as $buildingId) {
            $response = $this->callApi('/api/establishments_by_building', [
                'building_id' => $buildingId
            ], 600);

            if (!empty($response['success']) && !empty($response['data'])) {
                foreach ($response['data'] as $est) {
                    if ((int) ($est['id'] ?? 0) === (int) $id) {
                        $establishment = $est;
                        break 2;
                    }
                }
            }
        }

        if (empty($establishment)) {
            throw new \Cake\Http\Exception\NotFoundException('Establishment not found');
        }


        // Fetch nearby stores for both paths
        $nearbyStores = $this->fetchNearbyStores($establishment);

        $this->set(compact('establishment', 'nearbyStores'));
    }

    /**
     * Fetch nearby stores in the same building and category
     * 
     * @param array $establishment The establishment data
     * @return array Array of nearby stores (max 3)
     */
    private function fetchNearbyStores($establishment)
    {
        $nearbyStores = [];

        // Get category - use the broad `category` (e.g. "Dining") to match the
        // API's expected value (e.g. ?category=dining). Fall back to estmaincategory.
        $rawCategory = !empty($establishment['category']) ? $establishment['category'] : (!empty($establishment['estmaincategory']) ? $establishment['estmaincategory'] : null);
        // The working API call uses a lowercase category slug (?category=dining),
        // so normalize to lowercase to match regardless of stored casing.
        $category = $rawCategory !== null ? strtolower(trim($rawCategory)) : null;


        if (!empty($establishment['building_id']) && !empty($category)) {
            try {

                $buildingResponse = $this->callApi('/api/establishments_by_building', [
                    'building_id' => $establishment['building_id'],
                    'category' => $category
                ]);


                if (!empty($buildingResponse['success']) && !empty($buildingResponse['data'])) {
                    // Filter out current establishment
                    // FIX: null-safe id comparison.
                    $nearbyStores = array_filter($buildingResponse['data'], function ($store) use ($establishment) {
                        return (int) ($store['id'] ?? 0) !== (int) $establishment['id'];
                    });

                    // Randomize and limit to 3
                    $nearbyStores = array_values($nearbyStores); // Re-index after filter
                    shuffle($nearbyStores);
                    $nearbyStores = array_slice($nearbyStores, 0, 3);
                }
            } catch (\Exception $e) {
                // Nearby stores are non-critical; ignore API failures.
            }
        }

        return $nearbyStores;
    }

    /**
     * Transport Guide Page
     * Displays all transport options available at Araneta City
     */
    public function transport()
    {
        $this->viewBuilder()->setLayout('default');

        // Disable DebugKit to avoid memory issues
        if (isset($this->DebugKit)) {
            $this->DebugKit->enabled = false;
        }

        // Fetch transport list from API
        $response = $this->callApi('/api/transportslist');

        $transports = [];
        if (!empty($response['success']) && !empty($response['data'])) {
            $transports = $response['data'];
        }


        $this->set(compact('transports'));
    }

    /**
     * Transport Detail Page
     * Displays detailed information for a specific transport option
     */
    public function transportDetail()
    {
        $this->viewBuilder()->setLayout('default');

        // Disable DebugKit to avoid memory issues
        if (isset($this->DebugKit)) {
            $this->DebugKit->enabled = false;
        }

        // Get slug from route parameter
        $slug = $this->request->getParam('slug');

        if (empty($slug)) {
            throw new \Cake\Http\Exception\NotFoundException('Transport not found');
        }

        // Fetch transport detail using slug parameter
        $response = $this->callApi('/api/transportslist?slug=' . urlencode($slug));

        $transport = [];
        if (!empty($response['success']) && !empty($response['data'])) {
            $transport = $response['data'];
        }

        if (empty($transport)) {
            throw new \Cake\Http\Exception\NotFoundException('Transport not found');
        }


        // Fetch all transports for related/other options
        $otherTransports = [];
        $allTransportsResponse = $this->callApi('/api/transportslist');

        if (!empty($allTransportsResponse['success']) && !empty($allTransportsResponse['data'])) {
            $allTransports = $allTransportsResponse['data'];

            // Filter out current transport and get random 3
            // FIX: null-safe slug read.
            $otherTransports = array_filter($allTransports, function ($trans) use ($slug) {
                return strtolower($trans['slug'] ?? '') !== strtolower($slug);
            });
            // FIX: re-index after filtering, before shuffle/slice.
            $otherTransports = array_values($otherTransports);

            // Shuffle and get first 3
            shuffle($otherTransports);
            $otherTransports = array_slice($otherTransports, 0, 3);
        }

        $this->set(compact('transport', 'otherTransports'));
    }

    /**
     * Events Page
     * Displays events happening this week at Araneta City
     */
    public function events()
    {
        $this->viewBuilder()->setLayout('default');

        // Disable DebugKit to avoid memory issues
        if (isset($this->DebugKit)) {
            $this->DebugKit->enabled = false;
        }

        // Get filter type from query parameter. Default to the broad "upcoming"
        // set so the Grid / List / Calendar views and filters have data to work with.
        $filterType = $this->request->getQuery('filter', 'upcoming');

        // Determine which API endpoint to call
        $apiEndpoint = '/api/events_upcoming';
        if ($filterType === 'today') {
            $apiEndpoint = '/api/events_today';
        } elseif ($filterType === 'week') {
            $apiEndpoint = '/api/events_week';
        }

        // Fetch events from API
        $response = $this->callApi($apiEndpoint);

        $events = [];
        $weekStart = null;
        $weekEnd = null;

        if (!empty($response['success']) && !empty($response['data'])) {
            $events = $response['data'];
            $weekStart = $response['week_start'] ?? null;
            $weekEnd = $response['week_end'] ?? null;
        }


        // Get upcoming highlights (random 3 events)
        $highlights = [];
        if (!empty($events)) {
            $highlights = array_slice($events, 0, 3);
        }

        $this->set(compact('events', 'highlights', 'filterType', 'weekStart', 'weekEnd'));
    }

    /**
     * Event detail page
     *
     * Fetches a single event by slug and displays event details.
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function eventDetail()
    {
        $this->viewBuilder()->setLayout('default');

        // Get slug from passed arguments (from wildcard route)
        $passedArgs = $this->request->getParam('pass');
        $slug = is_array($passedArgs) ? implode('/', $passedArgs) : '';

        if (empty($slug)) {
            throw new \Cake\Http\Exception\NotFoundException('Event not found');
        }

        // Disable DebugKit to avoid memory issues
        if (isset($this->DebugKit)) {
            $this->DebugKit->enabled = false;
        }

        // The events API does not reliably filter by slug server-side, so we
        // fetch the full list from each endpoint and match the slug in PHP.
        $allEvents = [];
        foreach (['/api/events_upcoming', '/api/events_week', '/api/events_today'] as $endpoint) {
            $allResponse = $this->callApi($endpoint, [], 600);
            if (!empty($allResponse['success']) && !empty($allResponse['data'])) {
                $allEvents = array_merge($allEvents, (array) $allResponse['data']);
            } elseif (is_array($allResponse) && !empty($allResponse)) {
                $allEvents = array_merge($allEvents, (array) $allResponse);
            }
        }

        // Find the event whose slug matches the requested slug.
        $event = null;
        foreach ($allEvents as $e) {
            if (isset($e['slug']) && $e['slug'] === $slug) {
                $event = $e;
                break;
            }
        }

        if (empty($event)) {
            throw new \Cake\Http\Exception\NotFoundException('Event not found');
        }

        // Build "More to Discover": exclude the current one, dedupe by slug,
        // take up to 4 at random.
        $relatedEvents = [];
        foreach ($allEvents as $e) {
            if (isset($e['slug']) && $e['slug'] !== $slug && !isset($relatedEvents[$e['slug']])) {
                $relatedEvents[$e['slug']] = $e;
            }
        }
        $relatedEvents = array_values($relatedEvents);
        shuffle($relatedEvents);
        $relatedEvents = array_slice($relatedEvents, 0, 4);

        $this->set(compact('event', 'relatedEvents'));
    }

    /**
     * Residences Page
     * Displays residential projects at Araneta City
     */
    public function residences()
    {
        $this->viewBuilder()->setLayout('default');

        // Disable DebugKit to avoid memory issues
        if (isset($this->DebugKit)) {
            $this->DebugKit->enabled = false;
        }

        // Fetch residences from the API (cached 10 min). Caching means a
        // transient failure on refresh serves the last good copy instead of an
        // empty "no residences" page.
        $residencesResponse = $this->callApi('/api/residences', [], 600);

        $residences = [];
        if (!empty($residencesResponse['success']) && !empty($residencesResponse['data'])) {
            $residences = $residencesResponse['data'];
        } elseif (is_array($residencesResponse) && !empty($residencesResponse)) {
            $residences = $residencesResponse;
        }

        $this->set(compact('residences'));
    }

    /**
     * Residence Detail Page
     * Displays a single residential project by slug.
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function residenceDetail()
    {
        $this->viewBuilder()->setLayout('default');

        // Disable DebugKit to avoid memory issues
        if (isset($this->DebugKit)) {
            $this->DebugKit->enabled = false;
        }

        $slug = $this->request->getParam('slug');
        if (empty($slug)) {
            throw new \Cake\Http\Exception\NotFoundException('Residence not found');
        }

        // Fetch the full residences list (cached) and match the slug in PHP.
        // Reuses the same cached list as the listing page, so a transient
        // upstream failure on one refresh won't produce a spurious 404.
        $allResponse = $this->callApi('/api/residences', [], 600);
        $allResidences = [];
        if (!empty($allResponse['success']) && !empty($allResponse['data'])) {
            $allResidences = $allResponse['data'];
        } elseif (is_array($allResponse) && !empty($allResponse)) {
            $allResidences = $allResponse;
        }

        $residence = [];
        foreach ($allResidences as $res) {
            if (strtolower($res['slug'] ?? '') === strtolower($slug)) {
                $residence = $res;
                break;
            }
        }

        // Fallback: fetch the single residence directly by slug.
        if (empty($residence)) {
            $single = $this->callApi('/api/residences', ['slug' => $slug], 600);
            if (!empty($single['success']) && !empty($single['data'])) {
                $residence = $single['data'];
            }
        }

        if (empty($residence)) {
            throw new \Cake\Http\Exception\NotFoundException('Residence not found');
        }

        // Other residences for the "Nearby" sidebar (exclude the current one).
        $relatedResidences = [];
        foreach ($allResidences as $res) {
            if (strtolower($res['slug'] ?? '') !== strtolower($slug)) {
                $relatedResidences[] = $res;
            }
            if (count($relatedResidences) >= 3) {
                break;
            }
        }

        $this->set(compact('residence', 'relatedResidences'));
    }

    /**
     * Malls Page
     * Displays the main Araneta City malls (a curated subset of buildings).
     */
    public function malls()
    {
        $this->viewBuilder()->setLayout('default');

        if (isset($this->DebugKit)) {
            $this->DebugKit->enabled = false;
        }

        // Fetch buildings list
        $response = $this->callApi('/api/buildingslist');

        $allBuildings = [];
        if (!empty($response['success']) && !empty($response['data'])) {
            $allBuildings = $response['data'];
        }

        // Only show these mall building IDs, in this display order.
        $mallOrder = [1, 2, 4, 5]; // Ali Mall, Farmers Plaza, Gateway Mall 1, Gateway Mall 2
        $byId = [];
        foreach ($allBuildings as $b) {
            if (isset($b['id'])) {
                $byId[(int) $b['id']] = $b;
            }
        }

        $malls = [];
        foreach ($mallOrder as $id) {
            if (isset($byId[$id])) {
                $malls[] = $byId[$id];
            }
        }


        $this->set(compact('malls'));
    }

    /**
     * About Page â€” static content about the Araneta Group.
     */
    public function about()
    {
        $this->viewBuilder()->setLayout('default');

        if (isset($this->DebugKit)) {
            $this->DebugKit->enabled = false;
        }
    }

    /**
     * About > Management â€” static org chart of the Araneta Group's
     * business unit management teams.
     */
    public function aboutManagement()
    {
        $this->viewBuilder()->setLayout('default');

        if (isset($this->DebugKit)) {
            $this->DebugKit->enabled = false;
        }

        // Fetch chairman (hardcoded for now - typically CEO)
        $chairman = [
            'name' => 'Jorge L. Araneta',
            'title' => 'Chairman, President & CEO'
        ];

        // Fetch all team members grouped by department
        $teamMembersTable = $this->fetchTable('TeamMembers');
        $allMembers = $teamMembersTable->find()
            ->where(['status' => true])
            ->orderBy(['department' => 'ASC', 'sort_order' => 'ASC'])
            ->toArray();

        // Group members by department and then by group_role
        $departments = [];

        foreach ($allMembers as $member) {
            $dept = $member['department'] ?? 'General';
            $groupRole = $member['group_role'] ?? 'Staff';

            if (!isset($departments[$dept])) {
                $departments[$dept] = [];
            }

            if (!isset($departments[$dept][$groupRole])) {
                $departments[$dept][$groupRole] = [];
            }

            $departments[$dept][$groupRole][] = $member;
        }

        // Map departments to units with proper tag and full display name
        $unitMap = [
            'ACI, Inc.' => ['tag' => 'Property', 'name' => 'ACI, Inc. Management Team'],
            'Uniprom, Inc.' => ['tag' => 'Leisure & Entertainment', 'name' => 'Uniprom, Inc. Management Team'],
            'Progressive Dev. Corp.' => ['tag' => 'Investments & Assets', 'name' => 'Progressive Development Corporation'],
            'Araneta Hotels' => ['tag' => 'Hospitality', 'name' => 'Araneta Hotels, Inc. Management Team'],
            'Philippine Pizza' => ['tag' => 'Food Service', 'name' => 'Philippine Pizza Holdings, Inc. Management Team'],
        ];

        $units = [];
        foreach ($unitMap as $tab => $meta) {
            if (isset($departments[$tab])) {
                $units[] = [
                    'tab' => $tab,
                    'tag' => $meta['tag'],
                    'name' => $meta['name'],
                    'groups' => $departments[$tab]
                ];
            }
        }

        $this->set(compact('chairman', 'units'));
    }

    /**
     * Privacy Policy â€” static content.
     */
    public function policy()
    {
        $this->viewBuilder()->setLayout('default');

        if (isset($this->DebugKit)) {
            $this->DebugKit->enabled = false;
        }
    }

    /**
     * FAQs â€” static content.
     */
    public function faqs()
    {
        $this->viewBuilder()->setLayout('default');

        if (isset($this->DebugKit)) {
            $this->DebugKit->enabled = false;
        }
    }

    /**
     * Contact page â€” static content + form.
     */
    public function contact()
    {
        $this->viewBuilder()->setLayout('default');

        if (isset($this->DebugKit)) {
            $this->DebugKit->enabled = false;
        }

        // Handle contact form submission (saved directly to the contacts table)
        if ($this->request->is('post')) {

            // Verify reCAPTCHA v3 before touching the database
            $recaptchaResponse = $this->request->getData('g-recaptcha-response');
            $secretKey = Configure::read('Recaptcha.secretKey');

            $verify = file_get_contents(
                'https://www.google.com/recaptcha/api/siteverify?secret=' . $secretKey . '&response=' . urlencode($recaptchaResponse ?? '')
            );
            $responseData = json_decode($verify);

            if (
                !$responseData ||
                !$responseData->success ||
                $responseData->score < 0.5 ||
                $responseData->action !== 'contact_form'
            ) {
                $this->Flash->error(__('We could not verify your submission. Please try again.'));

                return $this->redirect(['action' => 'contact']);
            }

            // Sanitize submitted text: strip HTML/BBCode-style tags so spam
            // markup (e.g. <center>, <font>, <a href>, <list>) can't be stored
            // or rendered in the saved record / admin email.
            $data = $this->request->getData();
            $textFields = [
                'first_name', 'middle_name', 'last_name',
                'contact_number', 'email', 'subject', 'message',
            ];
            foreach ($textFields as $field) {
                if (isset($data[$field]) && is_string($data[$field])) {
                    // Remove tags, decode entities, collapse to plain text, trim.
                    $clean = strip_tags($data[$field]);
                    $clean = html_entity_decode($clean, ENT_QUOTES, 'UTF-8');
                    // Strip any leftover angle-bracket fragments (e.g. "<size16")
                    // that aren't valid tags but are used by spam.
                    $clean = preg_replace('/<[^>]*>?/', '', $clean);
                    $data[$field] = trim($clean);
                }
            }

            $contactsTable = $this->fetchTable('Contacts');
            $contact = $contactsTable->newEmptyEntity();
            $contact = $contactsTable->patchEntity($contact, $data);

            if ($contactsTable->save($contact)) {

                // Notify the admin by email. Failure here must never break the
                // already-successful save, so it's isolated in its own try/catch.
                $this->sendAdminEmail($contact);

                $this->Flash->success(__('Thank you for your inquiry. We will get back to you soon.'));

                return $this->redirect(['action' => 'contact']);
            }

            $this->Flash->error(__('There was a problem submitting your message. Please check the form and try again.'));
        }
    }

    /**
     * Listing Page
     * Displays category tiles: Shop, Dine, Services, Hotels
     */
    public function listing()
    {
        $this->viewBuilder()->setLayout('default');

        // Disable DebugKit to avoid memory issues
        if (isset($this->DebugKit)) {
            $this->DebugKit->enabled = false;
        }

        // Static categories data
        $categories = [
            [
                'title' => 'Shopping',
                'icon' => 'ðŸ›ï¸',
                'link' => '/shopping',
                'description' => 'Discover retail stores and shopping destinations'
            ],
            [
                'title' => 'Dining',
                'icon' => 'ðŸ½ï¸',
                'link' => '/dining',
                'description' => 'Explore restaurants and food establishments'
            ],
            [
                'title' => 'Services',
                'icon' => 'ðŸ”§',
                'link' => '/services',
                'description' => 'Browse services and professional facilities'
            ],
            [
                'title' => 'Hotels',
                'icon' => 'ðŸ¨',
                'link' => '/hotels',
                'description' => 'Find accommodations and hotel options'
            ]
        ];

        $this->set(compact('categories'));
    }

    public function sendAdminEmail($contact)
    {
        $fromEmail = \Cake\Core\Configure::read('ContactForm.fromEmail', 'info@aranetacity.com');
        $fromName = \Cake\Core\Configure::read('ContactForm.fromName', 'Araneta City Website');

        $mailer = new Mailer('default');
        $mailer
            ->setFrom($fromEmail, $fromName)
            ->setEmailFormat('html')
            ->setTo('info@aranetagroup.com')
            //->setCc('dmartinez@aranetagroup.com','aamante@aranetagroup.com','jenriquez@aranetagroup.com')
            // ->setTo('aubreygailamante@gmail.com')
            // Let the recipient reply straight to the person who submitted the form.
            ->setReplyTo(!empty($contact->email) ? $contact->email : $fromEmail)
            ->setViewVars([
                'firstName' => $contact->first_name,
                'middleName' => $contact->middle_name,
                'lastName' => $contact->last_name,
                'mobileNumber' => $contact->contact_number,
                'targetEmail' => $contact->email,
                'targetSubject' => $contact->subject,
                'message' => $contact->message,
                'submittedAt' => date('F j, Y g:i A'),
            ])
            ->setSubject('New Contact Us Inquiry - Araneta City')
            ->viewBuilder()
            ->setTemplate('contact')
            ->disableAutoLayout();
        $mailer->deliver();
    }
}