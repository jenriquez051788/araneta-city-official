<?php
declare(strict_types=1);

namespace App\Test\TestCase\Controller;

use Cake\Cache\Cache;
use Cake\Database\Schema\TableSchema;
use Cake\Datasource\ConnectionManager;
use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;

/**
 * Integration tests for the homepage popup modal render.
 *
 * Assumes `$popupModal['enabled']` is true in templates/Mainpages/home.php.
 */
class MainpagesControllerPopupTest extends TestCase
{
    use IntegrationTestTrait;

    /**
     * @var array<string>
     */
    protected array $fixtures = ['app.Mainpages'];

    private mixed $previousApiBaseUrl = null;

    /**
     * Cache configs replaced with in-memory engines during each test.
     */
    private const ISOLATED_CACHES = ['default', '_cake_api_', '_cake_api_stale_', '_cake_api_token_'];

    /**
     * @var array<string, array|null>
     */
    private array $savedCacheConfigs = [];

    /**
     * The test database is built from migrations, which do not include the
     * `mainpages` table that MainpagesController::home() queries. Create it
     * before fixtures are loaded.
     *
     * @return void
     */
    protected function setUp(): void
    {
        $connection = ConnectionManager::get('test');
        $tables = $connection->getSchemaCollection()->listTables();
        if (!in_array('mainpages', $tables, true)) {
            $schema = new TableSchema('mainpages');
            $schema
                ->addColumn('id', ['type' => 'integer', 'autoIncrement' => true])
                ->addColumn('title', ['type' => 'string', 'null' => true])
                ->addColumn('slug', ['type' => 'string', 'null' => true])
                ->addColumn('hero_heading', ['type' => 'string', 'null' => true])
                ->addColumn('hero_image', ['type' => 'string', 'null' => true])
                ->addColumn('hero_image_dir', ['type' => 'string', 'null' => true])
                ->addColumn('hero_image_size', ['type' => 'integer', 'null' => true])
                ->addColumn('hero_image_type', ['type' => 'string', 'null' => true])
                ->addColumn('content', ['type' => 'text', 'null' => true])
                ->addColumn('meta_title', ['type' => 'string', 'null' => true])
                ->addColumn('meta_description', ['type' => 'string', 'null' => true])
                ->addColumn('meta_keywords', ['type' => 'string', 'null' => true])
                ->addColumn('status', ['type' => 'integer', 'null' => true])
                ->addColumn('created', ['type' => 'datetime', 'null' => true])
                ->addColumn('modified', ['type' => 'datetime', 'null' => true])
                ->addConstraint('primary', ['type' => 'primary', 'columns' => ['id']]);
            foreach ($schema->createSql($connection) as $sql) {
                $connection->execute($sql);
            }
        }

        parent::setUp();

        // Keep the tests offline: point the CMS API at a closed local port so
        // calls fail immediately, and pre-seed the TicketNet caches so
        // home() never reaches the external feeds.
        $this->previousApiBaseUrl = env('API_BASE_URL');
        putenv('API_BASE_URL=http://127.0.0.1:9');
        $_ENV['API_BASE_URL'] = 'http://127.0.0.1:9';
        $_SERVER['API_BASE_URL'] = 'http://127.0.0.1:9';
        // Swap the API caches for in-memory ones so the test never touches
        // the running site's files under tmp/cache (on Windows, deleting a
        // cache file another process has open breaks that process).
        foreach (self::ISOLATED_CACHES as $name) {
            $this->savedCacheConfigs[$name] = Cache::getConfig($name);
            Cache::drop($name);
            Cache::setConfig($name, ['className' => 'Array']);
        }
        Cache::write('home_ticketnet_movies', [], '_cake_api_');
        Cache::write('home_ticketnet_events', [], '_cake_api_');
    }

    /**
     * @return void
     */
    protected function tearDown(): void
    {
        foreach ($this->savedCacheConfigs as $name => $config) {
            Cache::drop($name);
            if ($config !== null) {
                Cache::setConfig($name, $config);
            }
        }
        $this->savedCacheConfigs = [];
        if ($this->previousApiBaseUrl === null) {
            putenv('API_BASE_URL');
            unset($_ENV['API_BASE_URL'], $_SERVER['API_BASE_URL']);
        } else {
            putenv('API_BASE_URL=' . $this->previousApiBaseUrl);
            $_ENV['API_BASE_URL'] = $_SERVER['API_BASE_URL'] = $this->previousApiBaseUrl;
        }

        parent::tearDown();
    }

    /**
     * GET / returns 200 with the modal markup.
     *
     * Validates: Requirements 7.3
     *
     * @return void
     */
    public function testHomeReturns200WithModalMarkup(): void
    {
        $this->get('/');

        $this->assertResponseCode(200);
        $this->assertResponseContains('id="popup-modal-overlay"');
        $this->assertResponseContains('id="popup-modal"');
        $this->assertResponseContains('id="popup-modal-title"');
    }

    /**
     * Dialog ARIA attributes and the Close button label.
     *
     * Validates: Requirements 4.1, 4.6
     *
     * @return void
     */
    public function testModalHasAriaAttributesAndCloseLabel(): void
    {
        $this->get('/');
        $this->assertResponseOk();
        $body = (string)$this->_response->getBody();

        $this->assertMatchesRegularExpression(
            '/<div[^>]*id="popup-modal"[^>]*role="dialog"[^>]*aria-modal="true"[^>]*aria-labelledby="popup-modal-title"[^>]*>/',
            $body
        );
        $this->assertMatchesRegularExpression('/<h2[^>]*id="popup-modal-title"/', $body);
        $this->assertMatchesRegularExpression(
            '/<button[^>]*id="popup-modal-close"[^>]*aria-label="Close"[^>]*>/',
            $body
        );
    }

    /**
     * The CTA anchor opens in the same tab.
     *
     * Validates: Requirements 2.5
     *
     * @return void
     */
    public function testCtaLinkDoesNotOpenInNewTab(): void
    {
        $this->get('/');
        $this->assertResponseOk();
        $body = (string)$this->_response->getBody();

        if (preg_match('/<div[^>]*class="[^"]*popup-modal--image-only/', $body)) {
            // Poster-only mode: no message or CTA is rendered.
            $this->assertStringNotContainsString('id="popup-modal-cta"', $body);
            $this->assertStringNotContainsString('class="popup-modal-message"', $body);

            return;
        }

        $this->assertSame(
            1,
            preg_match('/<a[^>]*id="popup-modal-cta"[^>]*>/', $body, $m),
            'CTA anchor should be rendered'
        );
        $this->assertStringNotContainsString('target=', $m[0]);
    }

    /**
     * Other pages that use layout/default.php do not include the modal.
     *
     * Validates: Requirements 1.7
     *
     * @return void
     */
    public function testOtherPageDoesNotContainModal(): void
    {
        $this->get('/privacy-policy');

        $this->assertResponseOk();
        $this->assertResponseNotContains('popup-modal');
    }
}
