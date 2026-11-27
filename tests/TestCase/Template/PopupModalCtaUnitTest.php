<?php
declare(strict_types=1);

namespace App\Test\TestCase\Template;

use App\View\AppView;
use Cake\Http\ServerRequest;
use Cake\TestSuite\TestCase;
use ReflectionMethod;

/**
 * Unit tests for popupModalIsSafeUrl and CTA omission rules in home.php.
 *
 * Validates: Requirements 6.5, 6.6
 */
class PopupModalCtaUnitTest extends TestCase
{
    private string $tmpFile;

    protected function setUp(): void
    {
        parent::setUp();
        $this->tmpFile = TMP . 'popup_modal_cta_unit_' . uniqid('', true) . '.php';
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
    private function render(array $config): string
    {
        $source = (string)file_get_contents(ROOT . DS . 'templates' . DS . 'Mainpages' . DS . 'home.php');
        $pos = strpos($source, '<!-- wrapper-->');
        $this->assertNotFalse($pos);
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
        $this->assertSame(1, $count);

        file_put_contents($this->tmpFile, $source);
        $view = new AppView(new ServerRequest());
        $method = new ReflectionMethod($view, '_evaluate');

        return (string)$method->invoke($view, $this->tmpFile, []);
    }

    /**
     * @param string $label CTA label.
     * @param string $url CTA URL.
     * @return string
     */
    private function renderCta(string $label, string $url): string
    {
        return $this->render([
            'enabled' => true,
            'title' => 'Unit Title',
            'message' => 'Unit Message',
            'image' => null,
            'ctaLabel' => $label,
            'ctaUrl' => $url,
        ]);
    }

    /**
     * @return void
     */
    public function testIsSafeUrlAcceptsHttpsAndRootRelative(): void
    {
        $this->renderCta('Go', '/ok');
        $this->assertTrue(popupModalIsSafeUrl('https://example.com'));
        $this->assertTrue(popupModalIsSafeUrl('/happenings'));
    }

    /**
     * @return void
     */
    public function testIsSafeUrlRejectsJavascriptEmptyAndWhitespace(): void
    {
        $this->renderCta('Go', '/ok');
        $this->assertFalse(popupModalIsSafeUrl('javascript:alert(1)'));
        $this->assertFalse(popupModalIsSafeUrl(''));
        $this->assertFalse(popupModalIsSafeUrl('   '));
        $this->assertFalse(popupModalIsSafeUrl(null));
    }

    /**
     * @return void
     */
    public function testCtaRendersForSafeUrlAndLabel(): void
    {
        $output = $this->renderCta('See More', 'https://example.com');
        $this->assertStringContainsString('id="popup-modal-cta"', $output);
        $this->assertStringContainsString('See More', $output);
    }

    /**
     * @return void
     */
    public function testCtaOmittedForUnsafeOrBlankUrlButTitleAndMessageRender(): void
    {
        foreach (['javascript:alert(1)', '', '   '] as $url) {
            $output = $this->renderCta('See More', $url);
            $this->assertStringNotContainsString('id="popup-modal-cta"', $output, $url);
            $this->assertStringContainsString('Unit Title', $output, $url);
            $this->assertStringContainsString('Unit Message', $output, $url);
        }
    }

    /**
     * @return void
     */
    public function testCtaOmittedForBlankLabelButTitleAndMessageRender(): void
    {
        foreach (['', '   '] as $label) {
            $output = $this->renderCta($label, '/happenings');
            $this->assertStringNotContainsString('id="popup-modal-cta"', $output);
            $this->assertStringContainsString('Unit Title', $output);
            $this->assertStringContainsString('Unit Message', $output);
        }
    }
}
