<?php
declare(strict_types=1);

namespace App\Test\TestCase\Template;

use App\View\AppView;
use Cake\Http\ServerRequest;
use Cake\TestSuite\TestCase;
use ReflectionMethod;

/**
 * Property test for CTA URL validity in the homepage popup modal template.
 *
 * Renders the popup section of the real home.php through AppView with a
 * generated config, using a seeded randomized loop (no PBT library installed).
 */
class PopupModalCtaUrlPropertyTest extends TestCase
{
    private const ITERATIONS = 150;

    private string $tmpFile;

    protected function setUp(): void
    {
        parent::setUp();
        $this->tmpFile = TMP . 'popup_modal_cta_url_' . uniqid('', true) . '.php';
    }

    protected function tearDown(): void
    {
        if (is_file($this->tmpFile)) {
            unlink($this->tmpFile);
        }
        parent::tearDown();
    }

    /**
     * @param array<string, mixed> $config Popup config.
     * @return string
     */
    private function buildSource(array $config): string
    {
        $source = (string)file_get_contents(ROOT . DS . 'templates' . DS . 'Mainpages' . DS . 'home.php');

        $pos = strpos($source, '<!-- wrapper-->');
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
     * Independent oracle for the allowed-prefix rule.
     *
     * @param string|null $url URL.
     * @return bool
     */
    private function expectedSafe(?string $url): bool
    {
        if ($url === null || trim($url) === '') {
            return false;
        }
        $lower = strtolower($url);

        return str_starts_with($lower, 'http://')
            || str_starts_with($lower, 'https://')
            || str_starts_with($lower, '/');
    }

    /**
     * @return string
     */
    private function randomUrl(): string
    {
        $prefixes = [
            '', ' ', "\t", "\n", '   ', 'javascript:', 'JavaScript:', 'ftp://', 'data:', 'data:text/html,',
            'mailto:', '//', 'http://', 'https://', 'HTTP://', 'HtTpS://', 'http:/', 'http//', 'https:',
            '/', '/happenings', ' /', ' http://', 'www.', 'vbscript:', 'file://', 'happenings/', '#', '?',
        ];
        $tail = ['', 'a', 'example.com', '/x', '?q=1', '#frag', 'alert(1)', ' ', 'é'];

        $url = $prefixes[mt_rand(0, count($prefixes) - 1)];
        if (mt_rand(0, 3) > 0) {
            $url .= $tail[mt_rand(0, count($tail) - 1)];
        }

        return $url;
    }

    /**
     * Feature: homepage-popup-modal, Property 9: CTA URL validity is determined solely by allowed prefixes
     *
     * Validates: Requirements 6.5, 6.6
     *
     * @return void
     */
    public function testCtaUrlValidityDeterminedByAllowedPrefixes(): void
    {
        mt_srand(9);
        $labels = ['See Happenings', '', '   ', "\t\n", 'Go', 'x'];

        for ($i = 0; $i < self::ITERATIONS; $i++) {
            $url = $this->randomUrl();
            $label = $labels[mt_rand(0, count($labels) - 1)];
            $context = 'iteration ' . $i . ' ' . json_encode([$url, $label]);

            $output = $this->render([
                'enabled' => true,
                'title' => 'Title',
                'message' => 'Message',
                'image' => null,
                'ctaLabel' => $label,
                'ctaUrl' => $url,
            ]);

            $expectedSafe = $this->expectedSafe($url);
            $this->assertTrue(function_exists('popupModalIsSafeUrl'), $context);
            $this->assertSame($expectedSafe, popupModalIsSafeUrl($url), $context);

            $expectedAnchor = $expectedSafe && trim($label) !== '';
            $this->assertSame($expectedAnchor, str_contains($output, 'id="popup-modal-cta"'), $context);
        }
    }

    /**
     * @return void
     */
    public function testNullAndExplicitUnsafeExamples(): void
    {
        $this->render([
            'enabled' => true, 'title' => 'T', 'message' => 'M', 'image' => null,
            'ctaLabel' => 'L', 'ctaUrl' => '/ok',
        ]);
        $this->assertFalse(popupModalIsSafeUrl(null));
        foreach (['', '   ', 'javascript:alert(1)', 'ftp://x', 'data:text/html,x'] as $url) {
            $this->assertFalse(popupModalIsSafeUrl($url), $url);
        }
        foreach (['/a', 'http://a', 'https://a', 'HTTPS://a'] as $url) {
            $this->assertTrue(popupModalIsSafeUrl($url), $url);
        }
    }
}
