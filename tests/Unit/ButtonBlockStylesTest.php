<?php
/**
 * ボタンブロックの表示スタイルに関する回帰テスト
 */

namespace Cocoon\Tests\Unit;

use Cocoon\Tests\TestCase;

class ButtonBlockStylesTest extends TestCase
{
    /** @var string[] _extension.scssを取り込む配布用CSS */
    private const GENERATED_STYLESHEETS = [
        'style.css',
        'amp.css',
        'css/admin.css',
        'css/entry-content.css',
        'css/gutenberg-editor.css',
    ];

    public function test_大サイズの通常ボタンは装飾をまたぐ改行を保持する(): void
    {
        $theme_root = dirname(__DIR__, 2);

        foreach (self::GENERATED_STYLESHEETS as $relative_path) {
            $stylesheet = $theme_root . '/' . $relative_path;
            $css = file_get_contents($stylesheet);

            $this->assertNotFalse($css, "{$relative_path}を読み込めません");

            // 管理画面CSSの親セレクタを許容しつつ、通常ボタン限定のblock指定を検証する
            $this->assertMatchesRegularExpression(
                '/\.button-block > \[class~=btn-l\]\s*\{[^}]*display:\s*block;/s',
                $css,
                "{$relative_path}に通常ボタン限定の改行修正が生成されていません"
            );

            // flex解除後も右端の矢印がボタン全体の縦中央に配置されることを検証する
            $this->assertMatchesRegularExpression(
                '/\.button-block > \[class~=btn-l\]::after\s*\{[^}]*top:\s*50%;[^}]*transform:\s*translateY\(-50%\);/s',
                $css,
                "{$relative_path}に右矢印の縦中央配置が生成されていません"
            );

            // Sassの@extendでランキングリンクがblock指定へ混入していないことを検証する
            $this->assertDoesNotMatchRegularExpression(
                '/\.ranking-item-link-buttons[^{]*\s+a(?=[\s.:#\[>,{])[^{]*\{[^}]*display:\s*block;/s',
                $css,
                "{$relative_path}でランキングリンクへ通常ボタン用のblock指定が波及しています"
            );

            // 囲みボタンのリンクがblock指定へ巻き込まれていないことを検証する
            $this->assertDoesNotMatchRegularExpression(
                '/\.btn-wrap[^{]*(?:>\s*|\s+)a(?=[\s.:#\[>,{])[^{]*\{[^}]*display:\s*block;/s',
                $css,
                "{$relative_path}で囲みボタンへ通常ボタン用のblock指定が波及しています"
            );

            // 大サイズの囲みボタンが従来どおりflex表示を維持することを検証する
            $this->assertMatchesRegularExpression(
                '/\.btn-wrap\.btn-wrap-l\s*>\s*a[^{]*\{[^}]*display:\s*flex;/s',
                $css,
                "{$relative_path}で大サイズの囲みボタンがflex表示を維持していません"
            );

            if ($relative_path !== 'css/gutenberg-editor.css') {
                // ランキングを収録するCSSではリンクが従来どおりflex表示を維持することを検証する
                $this->assertMatchesRegularExpression(
                    '/\.ranking-item-link-buttons[^{]*\s+a(?=[\s.:#\[>,{])[^{]*\{[^}]*display:\s*flex;/s',
                    $css,
                    "{$relative_path}でランキングリンクがflex表示を維持していません"
                );
            }
        }
    }

    public function test_ライトニングスキンは通常ボタンの矢印位置を維持する(): void
    {
        $skin_css = file_get_contents(dirname(__DIR__, 2) . '/skins/lightning-skin/style.css');

        $this->assertNotFalse($skin_css, 'LightningスキンのCSSを読み込めません');

        // 通常ボタンだけに新しい中央配置と釣り合う補正値が設定されていることを検証する
        $this->assertMatchesRegularExpression(
            '/\.button-block\s*>\s*\.btn\.btn-l::after\s*\{[^}]*margin-top:\s*-2\.5px;/s',
            $skin_css,
            'Lightningスキンで通常ボタンの矢印位置補正が設定されていません'
        );

        // 通常ボタン以外へ適用される従来の補正値が変更されていないことを検証する
        $this->assertMatchesRegularExpression(
            '/\.btn\.btn-l:after[^\{]*\{[^}]*margin-top:\s*-5px;/s',
            $skin_css,
            'Lightningスキンで単独の大サイズボタンの矢印位置が変更されています'
        );
        $this->assertMatchesRegularExpression(
            '/\.ranking-item-link-buttons\s+a:after[^\{]*\{[^}]*margin-top:\s*-5px;/s',
            $skin_css,
            'Lightningスキンでランキングリンクの矢印位置が変更されています'
        );
    }
}
