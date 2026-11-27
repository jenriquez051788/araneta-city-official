<?php
declare(strict_types=1);

namespace App\Test\TestCase\Template;

use App\View\AppView;
use Cake\Http\ServerRequest;
use Cake\TestSuite\TestCase;
use ReflectionMethod;

/**
 * Property tests for the homepage popup modal template block.
 *
 * No PBT library is in composer.json, so properties are checked with a
 * hand-rolled randomized loop (seeded for reproducibility).
 *
 * Rendering approach: templates/Mainpages/home.php is too coupled to the
 * full page (API data, controller vars) to render with arbitrary config, so
 * the test extracts the popup section of the real template source (everything
 * from the top of the file through the popup's closing `endif`, which is
 * the line before the `<!-- wrapper-->` marker), replaces the `$popupModal = [...]`
 * config literal with a generated one, and renders that through a real
 * AppView. Production code is untouched.
 */
class PopupModalPropertyTest extends TestCase
{
    private const ITERATIONS = 150;

    private string $tmpFile;

    protected function setUp(): void
    {
        parent::setUp();
        $this->tmpFile = TMP . 'popup_modal_property_' . uniqid('', true) . '.php';
    }

    protected function tearDown(): void
    {
        if (is_file($this->tmpFile)) {
            unlink($this->tmpFile);
        }
        parent::tearDown();
    }

    /**
     * Build the popup-only template source with a given config injected.
     *
     * @param array<string, mixed> $config Popup config.
     * @return string
     */
    private function buildSource(array $config): string
    {
        $source = (string)file_get_contents(ROOT . DS . 'templates' . DS . 'Mainpages' . DS . 'home.php');

        $marker = '<!-- wrapper-->';
        $pos = strpos($source, $marker);
        $this->assertNotFalse($pos, 'Could not find end of popup block in home.php');
        $source = substr($source, 0, $pos);

        $count = 0;
        $literal = var_export($config, true);
        $source = preg_replace_callback(
            '/\$popupModal\s*=\s*\[.*?\n\];/s',
            fn() => '$popupModal = ' . $literal . ';',
            $source,
            1,
            $count
        );
        $this->assertSame(1, $count, 'Could not locate $popupModal config literal in home.php');

        return $source;
    }

    /**
     * @param array<string, mixed> $config Popup config.
     * @return string
     */
    private function render(array $config): string
    {
        file_put_contents($this->tmpFile, $this->buildSource($config));

        $view = new AppView(new ServerRequest());
        $method = new ReflectionMethod($view, '_evaluate');

        return (string)$method->invoke($view, $this->tmpFile, []);
    }

    /**
     * @param bool $enabled Enabled flag.
     * @return array<string, mixed>
     */
    private function randomConfig(bool $enabled): array
    {
        $pick = static fn(array $options) => $options[mt_rand(0, count($options) - 1)];
        $text = static function (): string {
            $chars = ['a', 'B', ' ', '<', '>', '&', '"', "'", '1', '-'];
            $len = mt_rand(0, 40);
            $s = '';
            for ($i = 0; $i < $len; $i++) {
                $s .= $chars[mt_rand(0, count($chars) - 1)];
            }

            return $s;
        };

        return [
            'enabled' => $enabled,
            'title' => $pick([$text(), '', 'Welcome']),
            'message' => $pick([$text(), '', 'Hello there']),
            'image' => $pick([null, '', 'logo.png', 'banner/' . mt_rand(1, 99) . '.jpg']),
            'ctaLabel' => $pick([null, '', '   ', $text(), 'Click']),
            'ctaUrl' => $pick([null, '', '  ', '/happenings', 'https://example.com/' . mt_rand(), 'javascript:alert(1)', 'ftp://x']),
        ];
    }

    /**
     * Feature: homepage-popup-modal, Property 7: Enabled flag exactly controls markup and script presence
     *
     * Validates: Requirements 1.8, 6.2, 6.3
     *
     * @return void
     */
    public function testEnabledFlagExactlyControlsMarkupAndScript(): void
    {
        mt_srand(7);
        $markers = [
            'id="popup-modal-overlay"',
            'id="popup-modal"',
            'id="popup-modal-title"',
            'id="popup-modal-close"',
            'popupModalDismissed',
            '<script',
            '<style',
            'popup-modal',
        ];

        for ($i = 0; $i < self::ITERATIONS; $i++) {
            $enabled = $i % 2 === 0;
            $config = $this->randomConfig($enabled);
            $output = $this->render($config);
            $context = 'iteration ' . $i . ' config ' . json_encode($config);

            if ($enabled) {
                $this->assertStringContainsString('id="popup-modal-overlay"', $output, $context);
                $this->assertStringContainsString('id="popup-modal"', $output, $context);
                $this->assertStringContainsString('id="popup-modal-title"', $output, $context);
                $this->assertStringContainsString('id="popup-modal-close"', $output, $context);
                $this->assertStringContainsString('<script', $output, $context);
                $this->assertStringContainsString('popupModalDismissed', $output, $context);
            } else {
                foreach ($markers as $marker) {
                    $this->assertStringNotContainsString($marker, $output, $context);
                }
                $this->assertSame('', trim($output), $context);
            }
        }
    }
}
