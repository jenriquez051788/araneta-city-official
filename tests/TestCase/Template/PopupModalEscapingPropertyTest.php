<?php
declare(strict_types=1);

namespace App\Test\TestCase\Template;

use App\View\AppView;
use Cake\Http\ServerRequest;
use Cake\TestSuite\TestCase;
use DOMDocument;
use DOMXPath;
use ReflectionMethod;

/**
 * Property test for HTML escaping in the homepage popup modal template.
 *
 * Uses the same rendering approach as PopupModalPropertyTest (popup section of
 * the real home.php rendered through AppView with a generated config) and a
 * hand-rolled seeded randomized loop, since no PBT library is installed.
 */
class PopupModalEscapingPropertyTest extends TestCase
{
    private const ITERATIONS = 150;

    private string $tmpFile;

    protected function setUp(): void
    {
        parent::setUp();
        $this->tmpFile = TMP . 'popup_modal_escaping_' . uniqid('', true) . '.php';
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
     * Random text rich in the special characters, bounded by non-space
     * letters so whitespace normalization by the parser cannot interfere.
     *
     * @return string
     */
    private function specialText(): string
    {
        $chars = ['<', '>', '&', '"', "'", '&amp;', '&lt;', '&#39;', '<b>', 'a', 'Z', ' ', '1', '=', '/', 'é'];
        $len = mt_rand(0, 40);
        $s = '';
        for ($i = 0; $i < $len; $i++) {
            $s .= $chars[mt_rand(0, count($chars) - 1)];
        }

        return 'x' . $s . 'y';
    }

    /**
     * Inner HTML of the first match of a regex must contain no raw special
     * characters once valid entities are removed.
     *
     * @param string $html Fragment (inner HTML or attribute value).
     * @param string $context Failure context.
     * @return void
     */
    private function assertNoRawSpecials(string $html, string $context): void
    {
        $stripped = preg_replace('/&(?:amp|lt|gt|quot|#0?39);/', '', $html);
        $this->assertSame(0, preg_match('/[<>&"\']/', (string)$stripped), $context . ' raw special in: ' . $html);
    }

    /**
     * Feature: homepage-popup-modal, Property 8: HTML-escaping is total over the special-character set
     *
     * Validates: Requirements 6.4
     *
     * @return void
     */
    public function testEscapingIsTotalOverSpecialCharacters(): void
    {
        mt_srand(8);

        for ($i = 0; $i < self::ITERATIONS; $i++) {
            $title = $this->specialText();
            $message = $this->specialText();
            $label = $this->specialText();
            $url = '/' . $this->specialText();

            $output = $this->render([
                'enabled' => true,
                'title' => $title,
                'message' => $message,
                'image' => null,
                'ctaLabel' => $label,
                'ctaUrl' => $url,
            ]);
            $context = 'iteration ' . $i . ' ' . json_encode([$title, $message, $label, $url]);

            // Raw output: escaped in text and attribute positions.
            $this->assertSame(1, preg_match('#<h2 id="popup-modal-title"[^>]*>(.*?)</h2>#s', $output, $m), $context);
            $this->assertNoRawSpecials($m[1], $context . ' title');
            $this->assertSame(1, preg_match('#<p class="popup-modal-message">(.*?)</p>#s', $output, $m), $context);
            $this->assertNoRawSpecials($m[1], $context . ' message');
            $this->assertSame(
                1,
                preg_match('#<a id="popup-modal-cta"[^>]*href="([^"]*)"[^>]*>(.*?)</a>#s', $output, $m),
                $context
            );
            $this->assertNoRawSpecials($m[1], $context . ' href');
            $this->assertNoRawSpecials($m[2], $context . ' label');

            // Parsed output round-trips to the original strings.
            $dom = new DOMDocument();
            $previous = libxml_use_internal_errors(true);
            $dom->loadHTML('<?xml encoding="UTF-8">' . $output);
            libxml_clear_errors();
            libxml_use_internal_errors($previous);
            $xpath = new DOMXPath($dom);

            $text = static function (string $id) use ($xpath): ?string {
                $node = $xpath->query('//*[@id="' . $id . '"]')->item(0);

                return $node === null ? null : $node->textContent;
            };

            $this->assertSame($title, $text('popup-modal-title'), $context);
            $this->assertSame($label, $text('popup-modal-cta'), $context);
            $paragraph = $xpath->query('//p[@class="popup-modal-message"]')->item(0);
            $this->assertNotNull($paragraph, $context);
            $this->assertSame($message, $paragraph->textContent, $context);
            $cta = $xpath->query('//*[@id="popup-modal-cta"]')->item(0);
            $this->assertSame($url, $cta->getAttribute('href'), $context);
        }
    }
}
