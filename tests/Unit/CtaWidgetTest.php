<?php
/**
 * CTAウィジェットの保存処理に関する回帰テスト
 */

namespace Cocoon\Tests\Unit;

use Brain\Monkey\Functions;
use Cocoon\Tests\TestCase;
use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;

// CTAウィジェット読み込み用の最小限のWP_Widget代替クラス
class CtaWidgetWpWidgetStub
{
    public string $id_base = 'cta_box';
}

#[RunTestsInSeparateProcesses]
#[PreserveGlobalState(false)]
class CtaWidgetTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        if (!class_exists('WP_Widget', false)) {
            class_alias(CtaWidgetWpWidgetStub::class, 'WP_Widget', false);
        }

        require_once dirname(__DIR__, 2) . '/lib/shortcodes.php';
        require_once dirname(__DIR__, 2) . '/lib/widgets/cta-box.php';
    }

    /**
     * CTAウィジェット保存時にURL、本文、固定クラスが用途別に正規化されることを検証
     */
    public function test_CTAウィジェットの保存値を用途別に正規化する(): void
    {
        Functions\when('wp_kses_post')->justReturn('<strong>許可された本文</strong>');

        $reflection = new \ReflectionClass('CTABoxWidgetItem');
        /** @var \CTABoxWidgetItem $widget */
        $widget = $reflection->newInstanceWithoutConstructor();
        $result = $widget->update(
            [
                'title' => '<script>alert(1)</script>タイトル',
                'heading' => '<img src=x onerror=alert(1)>見出し',
                'layout' => 'cta-left-and-right" onmouseover="alert(1)',
                'image_url' => 'https://example.com/image.png',
                'message' => '<strong>許可された本文</strong><script>alert(1)</script>',
                'filter' => false,
                'button_text' => '<b>ボタン</b>',
                'button_url' => 'javascript:alert(1)',
                'button_color_class' => 'btn-red" onclick="alert(1)',
            ],
            ['既存キー' => '保持']
        );

        $this->assertSame('保持', $result['既存キー']);
        $this->assertStringNotContainsString('<script', $result['title']);
        $this->assertStringNotContainsString('<img', $result['heading']);
        $this->assertSame('cta-top-and-bottom', $result['layout']);
        $this->assertSame(esc_url_raw('https://example.com/image.png'), $result['image_url']);
        $this->assertSame('<strong>許可された本文</strong>', $result['message']);
        $this->assertFalse($result['filter']);
        $this->assertSame('ボタン', $result['button_text']);
        $this->assertSame('', $result['button_url']);
        $this->assertSame('btn-red', $result['button_color_class']);

        $spaced_classes = $widget->update(
            [
                'layout' => ' cta-left-and-right ',
                'button_color_class' => ' btn-blue ',
            ],
            []
        );
        $this->assertSame('cta-left-and-right', $spaced_classes['layout']);
        $this->assertSame('btn-blue', $spaced_classes['button_color_class']);
    }

    /**
     * CTAウィジェット表示時に文字列の0が既定ボタン文字列へ置き換わらないことを検証
     */
    public function test_CTAウィジェット表示で文字列の0を維持する(): void
    {
        Functions\expect('get_cta_tag')
            ->once()
            ->with(
                \Mockery::on(static function (array $atts): bool {
                    return $atts['button_text'] === '0';
                }),
                ''
            )
            ->andReturn('<div class="cta-box">0</div>');

        $reflection = new \ReflectionClass('CTABoxWidgetItem');
        /** @var \CTABoxWidgetItem $widget */
        $widget = $reflection->newInstanceWithoutConstructor();

        ob_start();
        $widget->widget(
            [
                'before_widget' => '<aside>',
                'after_widget' => '</aside>',
                'before_title' => '<h2>',
                'after_title' => '</h2>',
            ],
            [
                'button_text' => '0',
            ]
        );
        $output = ob_get_clean();

        $this->assertSame('<aside><div class="cta-box">0</div></aside>', $output);
    }
}
