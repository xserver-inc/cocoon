<?php
/**
 * Cocoon設定画面の表示契約テスト
 *
 * デザイン変更によって、保存フォームとタブ切替の契約が変わらないことを検証する。
 */

namespace Cocoon\Tests\Unit;

use Cocoon\Tests\TestCase;

class CocoonSettingsDesignContractTest extends TestCase
{
    private string $themeRoot;

    protected function setUp(): void
    {
        parent::setUp();
        $this->themeRoot = dirname(__DIR__, 2);
    }

    /**
     * nonce、フォーム、隠しフィールド、保存ボタンの契約を検証する。
     */
    public function test_設定フォームの保存契約が維持されている(): void
    {
        $source = $this->readThemeFile('lib/page-settings/_top-page.php');
        $formOpen = '<form name="form1" method="post" action="<?php echo add_query_arg(array(\'reset\' => null)); ?>" class="admin-settings">';
        $nonceInput = '<input type="hidden" name="<?php echo HIDDEN_FIELD_NAME; ?>" value="<?php echo wp_create_nonce(\'settings\');?>">';

        // 保存項目がすべて同じ既存フォーム内に残っていることを確認する。
        $this->assertSame(1, substr_count($source, $formOpen));
        $formStart = strpos($source, $formOpen);
        $this->assertIsInt($formStart);
        $formEnd = strpos($source, '</form>', $formStart);
        $this->assertIsInt($formEnd);
        $form = substr($source, $formStart, $formEnd + strlen('</form>') - $formStart);

        // select_indexの種別・ID・name・値が同じinput要素に残っていることを確認する。
        $selectIndexPattern = <<<'REGEX'
~<input\s+type="hidden"\s+id="<\?php echo SELECT_INDEX_NAME; \?>"\s+name="<\?php echo SELECT_INDEX_NAME; \?>"\s+value="<\?php echo \(\$_POST\[SELECT_INDEX_NAME\] \?\? 0\);\s*(?://[^\r\n]*)?\s*\?>">~s
REGEX;

        $this->assertStringContainsString("wp_verify_nonce(\$_POST[HIDDEN_FIELD_NAME], 'settings')", $source);
        $this->assertSame(1, substr_count($form, $nonceInput));
        $this->assertSame(1, preg_match_all($selectIndexPattern, $form));
        $this->assertSame(1, substr_count($form, 'id="<?php echo SELECT_INDEX_NAME; ?>"'));
        $this->assertSame(1, substr_count($form, 'name="<?php echo SELECT_INDEX_NAME; ?>"'));
        $this->assertSame(2, substr_count($form, "submit_button(__( '変更をまとめて保存', THEME_NAME )"));
        $this->assertStringContainsString('"submit-2"', $form);
        $this->assertStringContainsString("do_action('cocoon_settings_before_save')", $source);
        $this->assertStringContainsString("do_action('cocoon_settings_after_save')", $source);
    }

    /**
     * 38組のradio、label、contentが同じID基底で対応することを検証する。
     */
    public function test_全タブのradioとlabelとcontentが対応している(): void
    {
        $source = $this->readThemeFile('lib/page-settings/_top-page.php');

        // タブradioだけを抽出し、表示条件付きのAMPとPWAも含めて数える。
        preg_match_all(
            '/<input id="(tab-[^"]+-input)" value="([^"]+)" class="tab-input" type="radio" name="tab-input"[^>]*>/',
            $source,
            $matches
        );

        $this->assertCount(38, $matches[1]);
        $this->assertSame(38, count(array_unique($matches[1])));
        $this->assertCount(38, $matches[2]);
        $this->assertSame(
            [
                'tab-skin-input',
                'tab-all-input',
                'tab-theme-header-input',
                'tab-ads-input',
                'tab-title-input',
                'tab-seo-input',
                'tab-ogp-input',
                'tab-analytics-input',
                'tab-column-input',
                'tab-index-page-input',
                'tab-single-page-input',
                'tab-page-page-input',
                'tab-content-page-input',
                'tab-toc-page-input',
                'tab-sns-share-input',
                'tab-sns-follow-input',
                'tab-image-input',
                'tab-blog-card-input',
                'tab-code-highlight-input',
                'tab-comment-input',
                'tab-notice-area-input',
                'tab-appeal-area-input',
                'tab-recommended-input',
                'tab-carousel-input',
                'tab-footer-input',
                'tab-buttons-input',
                'tab-mobile-buttons-input',
                'tab-page-404-input',
                'tab-amp-input',
                'tab-pwa-input',
                'tab-admin-input',
                'tab-widget-input',
                'tab-widget-area-input',
                'tab-editor-input',
                'tab-apis-input',
                'tab-others-input',
                'tab-reset-input',
                'tab-about-input',
            ],
            $matches[1],
            '従来タブの順番を変更してはいけません。'
        );

        // 各radioの基底IDから対応するlabelとcontentを導き、位置関係も固定する。
        foreach ($matches[1] as $index => $inputId) {
            $baseId = substr($inputId, 0, -strlen('-input'));
            $labelId = $baseId . '-label';
            $contentId = $baseId . '-content';
            $inputPosition = strpos($source, 'id="' . $inputId . '"');
            $contentPosition = strpos($source, 'id="' . $contentId . '"');
            $adjacentLabelPattern = '/<input id="' . preg_quote($inputId, '/') . '"[^>]*>\s*<label for="'
                . preg_quote($inputId, '/') . '" id="' . preg_quote($labelId, '/') . '"/';

            $this->assertSame($inputId, $matches[2][$index]);
            $this->assertMatchesRegularExpression($adjacentLabelPattern, $source);
            $this->assertStringContainsString('for="' . $inputId . '" id="' . $labelId . '"', $source);
            $this->assertStringContainsString('id="' . $contentId . '"', $source);
            $this->assertTagHasClass($this->findTagById($source, 'label', $labelId), 'tab-label');
            $this->assertTagHasClass($this->findTagById($source, 'div', $contentId), 'metabox-holder');
            $this->assertSame(1, substr_count($source, 'id="' . $labelId . '"'));
            $this->assertSame(1, substr_count($source, 'id="' . $contentId . '"'));
            $this->assertIsInt($inputPosition);
            $this->assertIsInt($contentPosition);
            $this->assertLessThan($contentPosition, $inputPosition);
        }

        $this->assertSame(1, substr_count($source, 'name="tab-input" checked="checked"'));
        $this->assertStringContainsString('cocoonSelectedTab.checked = true;', $source);
    }

    /**
     * タブ切替がリンクやフォーム送信ではなく、同一ページ内のCSS操作であることを検証する。
     */
    public function test_タブ切替がページ遷移を起こさないCSS方式である(): void
    {
        $source = $this->readThemeFile('lib/page-settings/_top-page.php');
        $adminScss = $this->readThemeFile('scss/admin.scss');
        $tabsStart = strpos($source, '<div id="tabs" class="tabs" data-navigation-initial-mode=');
        $firstContentStart = strpos($source, '<div id="tab-skin-content"', $tabsStart);

        $this->assertIsInt($tabsStart);
        $this->assertIsInt($firstContentStart);

        // タブ操作領域だけを抽出し、遷移や送信を起こす要素・属性が混入していないことを確認する。
        $tabControls = substr($source, $tabsStart, $firstContentStart - $tabsStart);
        preg_match_all(
            '/<label\b[^>]*\bclass="[^"]*\btab-label\b[^"]*"[^>]*>/',
            $tabControls,
            $labelMatches
        );

        $this->assertCount(38, $labelMatches[0]);
        $this->assertStringNotContainsString('<a ', $tabControls);
        $this->assertStringNotContainsString('<button', $tabControls);
        $this->assertDoesNotMatchRegularExpression('/\b(?:href|formaction|onclick)\s*=/i', $tabControls);

        // 各radioのchecked状態だけで、対応するcontentが同一DOM内に表示されることを確認する。
        preg_match_all(
            '/<input id="(tab-[^"]+-input)"[^>]*\btype="radio"[^>]*\bname="tab-input"[^>]*>/',
            $tabControls,
            $inputMatches
        );

        $this->assertCount(38, $inputMatches[1]);
        $this->assertMatchesRegularExpression(
            '/#tabs\s*>\s*\.metabox-holder\s*\{\s*display:\s*none;/s',
            $adminScss
        );

        foreach ($inputMatches[1] as $inputId) {
            $contentId = substr($inputId, 0, -strlen('-input')) . '-content';
            $this->assertStringContainsString(
                '#' . $inputId . ':checked ~ #' . $contentId,
                $adminScss
            );
        }
    }

    /**
     * 初期描画では誤った横型タブを隠し、失敗時に従来UIへ戻れることを検証する。
     */
    public function test_レスポンシブナビゲーションの初期描画ちらつきを防止する(): void
    {
        $topPage = $this->readThemeFile('lib/page-settings/_top-page.php');
        $navigationScript = $this->readThemeFile('js/cocoon-settings-navigation.js');
        $partial = $this->readThemeFile('scss/_cocoon-settings-modern.scss');

        // 許可済み表示モードだけを受動的なdata属性へ出し、フォーム値を追加しない。
        $this->assertStringContainsString('cocoon_get_settings_navigation_mode()', $topPage);
        $this->assertStringContainsString(
            'data-navigation-initial-mode="<?php echo esc_attr( $cocoon_settings_initial_navigation_mode ); ?>"',
            $topPage
        );
        $this->assertStringContainsString("if ( 'responsive' === \$cocoon_settings_initial_navigation_mode )", $topPage);
        $this->assertStringContainsString("tabs.classList.add( 'is-navigation-booting' );", $topPage);
        $this->assertStringContainsString(
            "document.addEventListener( 'DOMContentLoaded', revealFallbackTabs, { once: true } );",
            $topPage
        );
        $this->assertStringContainsString('window.setTimeout( revealFallbackTabs, 10000 );', $topPage);

        // visibilityで幅を保ち、可視性判定より前に同期解除してスキン制御を維持する。
        $this->assertStringContainsString('#tabs.is-navigation-booting *::before,', $partial);
        $this->assertStringContainsString('#tabs.is-navigation-booting *::after {', $partial);
        $this->assertMatchesRegularExpression(
            '/#tabs\.is-navigation-booting[^}]*visibility:\s*hidden !important;/s',
            $partial
        );
        $this->assertMatchesRegularExpression(
            '/#tabs\.is-navigation-booting\s*\{\s*opacity:\s*0;\s*\}/s',
            $partial
        );
        $this->assertLessThan(
            strpos($navigationScript, 'if ( ! cocoonBuildSettingsNavigation( tabs, settings, inputs, panels ) )'),
            strpos($navigationScript, "tabs.classList.remove( 'is-navigation-booting' );")
        );
        $this->assertStringContainsString(
            '! cocoonInitializeSettingsNavigation()',
            $navigationScript
        );
        $this->assertStringContainsString(
            "tabs.setAttribute( stateAttribute, 'ready' );",
            $navigationScript
        );
        $this->assertStringContainsString(
            'cocoonRestoreSettingsNavigation( tabs, snapshot );',
            $navigationScript
        );
    }

    /**
     * 表示ナビゲーションと専用モード保存がCocoon本体の設定保存から分離されることを検証する。
     */
    public function test_レスポンシブナビゲーションが保存処理から分離されている(): void
    {
        $topPage = $this->readThemeFile('lib/page-settings/_top-page.php');
        $adminSource = $this->readThemeFile('lib/admin.php');
        $navigationScript = $this->readThemeFile('js/cocoon-settings-navigation.js');

        preg_match_all(
            '/<input id="(tab-[^"]+-input)"[^>]*\btype="radio"[^>]*\bname="tab-input"[^>]*>/',
            $topPage,
            $radioMatches
        );
        preg_match_all("/'(tab-[^']+-input)'/", $adminSource, $navigationMatches);

        $radioIds = array_values(array_unique($radioMatches[1]));
        $navigationIds = array_values(array_unique($navigationMatches[1]));
        sort($radioIds);
        sort($navigationIds);

        // PHP分類が、条件付きAMP・PWAを含む既存38タブと過不足なく対応することを固定する。
        $this->assertCount(38, $radioIds);
        $this->assertSame($radioIds, $navigationIds);
        $this->assertStringContainsString("if (is_admin_php_page())", $adminSource);
        $this->assertStringContainsString("'/js/cocoon-settings-navigation.js'", $adminSource);
        $this->assertStringContainsString("'cocoonSettingsNavigationData'", $adminSource);
        $this->assertStringContainsString('filemtime( $settings_navigation_js_path )', $adminSource);
        $this->assertStringContainsString("'wp_ajax_cocoon_settings_save_navigation_mode'", $adminSource);
        $this->assertStringContainsString("check_ajax_referer( 'cocoon_settings_navigation_mode', 'nonce' )", $adminSource);
        $this->assertStringContainsString("current_user_can( 'manage_options' )", $adminSource);
        $this->assertStringContainsString('update_user_option( $user_id, COCOON_SETTINGS_NAVIGATION_MODE_OPTION, $mode, false )', $adminSource);
        $this->assertStringContainsString(
            'おすすめ表示では、画面の広さに応じて設定メニューを自動で見やすく切り替えます。設定内容には影響しません。',
            $adminSource
        );
        $this->assertStringContainsString("'responsiveModeLabel' => __( 'おすすめ表示'", $adminSource);
        $this->assertStringContainsString("'tabsModeLabel' => __( '従来の表示'", $adminSource);
        $this->assertStringNotContainsString("'recommendedLabel'", $adminSource);
        $this->assertStringNotContainsString('set_theme_mod(', substr($adminSource, 0, strpos($adminSource, '//管理画面に読み込むリソースの設定')));

        // 新UIの操作要素がフォーム送信コントロールにならないことを確認する。
        $this->assertStringContainsString("navigationItem.type = 'button';", $navigationScript);
        $this->assertStringContainsString("button.type = 'button';", $navigationScript);
        $this->assertStringContainsString("mobileSelect.setAttribute( 'aria-label'", $navigationScript);
        $this->assertStringContainsString("navigationItem.setAttribute( 'aria-pressed', String( isActive ) );", $navigationScript);
        $this->assertStringContainsString("viewModeControl.setAttribute( 'role', 'radiogroup' );", $navigationScript);
        $this->assertStringContainsString("button.setAttribute( 'role', 'radio' );", $navigationScript);
        $this->assertStringContainsString("button.setAttribute( 'aria-checked', 'false' );", $navigationScript);
        $this->assertStringContainsString('button.tabIndex = isSelected ? 0 : -1;', $navigationScript);
        $this->assertStringContainsString("[ 'ArrowRight', 'ArrowDown' ]", $navigationScript);
        $this->assertStringContainsString("[ 'ArrowLeft', 'ArrowUp' ]", $navigationScript);
        $this->assertStringNotContainsString('recommendedBadge', $navigationScript);
        $this->assertStringNotContainsString('cocoon-settings-view-mode-recommended', $navigationScript);
        $this->assertStringNotContainsString("aria-current', 'page'", $navigationScript);
        $this->assertStringNotContainsString('navigationItem.name', $navigationScript);
        $this->assertStringNotContainsString('mobileSelect.name', $navigationScript);
        $this->assertStringNotContainsString('search.name', $navigationScript);
        $this->assertStringNotContainsString('.checked =', $navigationScript);
        $this->assertStringContainsString('item.input.click();', $navigationScript);
        $this->assertStringContainsString('selectedInput.click();', $navigationScript);
        $this->assertStringContainsString("input.addEventListener( 'change', synchronizeNavigation )", $navigationScript);
        $this->assertStringContainsString('const activeElement = document.activeElement;', $navigationScript);
        $this->assertStringContainsString('focusTarget.focus();', $navigationScript);
        $this->assertStringContainsString('const navigationInputs = inputs.filter(', $navigationScript);
        $this->assertStringContainsString('isElementVisible( labelElementsByInputId.get( input.id ) )', $navigationScript);
        $this->assertLessThan(
            strpos($navigationScript, "tabs.classList.add( 'is-navigation-enhanced' )"),
            strpos($navigationScript, 'const navigationInputs = inputs.filter(')
        );
        $this->assertStringContainsString("'is-navigation-mode-responsive'", $navigationScript);
        $this->assertStringContainsString("'is-navigation-mode-tabs'", $navigationScript);
        $this->assertStringContainsString("'is-navigation-wide', tabs.clientWidth >= 1040", $navigationScript);
        $this->assertStringContainsString("[ 'ArrowUp', 'ArrowDown', 'Home', 'End' ]", $navigationScript);
        $this->assertStringContainsString("panel.scrollIntoView( { block: 'start' } );", $navigationScript);
        $this->assertStringContainsString('let isSavingNavigationMode = false;', $navigationScript);
        $this->assertStringContainsString("button.setAttribute( 'aria-disabled', 'true' );", $navigationScript);
        $this->assertStringContainsString("button.removeAttribute( 'aria-disabled' );", $navigationScript);
        $this->assertStringNotContainsString('button.disabled', $navigationScript);
        $this->assertStringNotContainsString('tabs.append( input', $navigationScript);
        $this->assertStringNotContainsString('tabs.prepend( input', $navigationScript);

        // 通信は表示モード専用AJAXの1回だけ許可し、フォーム・URL・Storage操作を禁止する。
        $this->assertSame(1, substr_count($navigationScript, 'fetch( settings.ajaxUrl'));
        $this->assertStringContainsString("action: 'cocoon_settings_save_navigation_mode'", $navigationScript);
        $this->assertStringContainsString("credentials: 'same-origin'", $navigationScript);
        $this->assertStringContainsString('const abortController = new AbortController();', $navigationScript);
        $this->assertStringContainsString('signal: abortController.signal,', $navigationScript);
        $this->assertStringContainsString('}, 15000 );', $navigationScript);
        $this->assertStringContainsString('settings.modeNonceError || settings.modeSaveError', $navigationScript);
        $this->assertStringContainsString('settings.modeTimeoutError || settings.modeSaveError', $navigationScript);
        $this->assertLessThan(
            strpos($navigationScript, 'const requestBody = new URLSearchParams('),
            strpos($navigationScript, 'try {')
        );
        $this->assertDoesNotMatchRegularExpression(
            '/\b(?:XMLHttpRequest|sendBeacon|FormData|localStorage|sessionStorage|requestSubmit)\b'
                . '|(?:window\.)?location\b|\bhistory\s*\.|\.submit\s*\(/',
            $navigationScript
        );
    }

    /**
     * 広い、中程度、狭い設定領域の3種類が表示幅に応じて切り替わることを検証する。
     */
    public function test_ナビゲーションが3種類のレスポンシブ表示を持つ(): void
    {
        $partial = $this->readThemeFile('scss/_cocoon-settings-modern.scss');

        // 広い領域はResizeObserverが付けるクラスで縦型2カラムへ切り替え、fixed UIの基準を変えない。
        $this->assertStringNotContainsString('container-name:', $partial);
        $this->assertStringNotContainsString('container-type:', $partial);
        $this->assertStringNotContainsString('@container', $partial);
        $this->assertStringContainsString(
            '#tabs.is-navigation-enhanced.is-navigation-mode-responsive.is-navigation-wide',
            $partial
        );
        $this->assertStringContainsString(
            'grid-template-columns: 232px minmax(0, 1fr);',
            $partial
        );
        $this->assertStringContainsString('max-block-size: calc(100vh - 64px);', $partial);
        $this->assertStringContainsString('scroll-margin-block-start: 64px;', $partial);
        $this->assertStringContainsString('scroll-margin-block-start: 152px;', $partial);

        // タブレットでは既存ラベルが既定表示となり、追加ナビゲーションは初期状態で非表示になる。
        $this->assertMatchesRegularExpression(
            '/:where\(#tabs > \.cocoon-settings-navigation\)\s*\{\s*display:\s*none;/',
            str_replace(["\r\n", "\r"], "\n", $partial)
        );
        $this->assertStringContainsString('#tabs > .tab-label {', $partial);

        // モバイルは既存ラベルを隠し、44px以上のネイティブselectだけを表示する。
        $this->assertMatchesRegularExpression(
            '/@media screen and \(width <= 782px\).*?'
                . '#tabs\.is-navigation-enhanced\.is-navigation-mode-responsive > \.tab-label\s*\{'
                . '[^}]*display:\s*none !important;.*?'
                . '\.cocoon-settings-mobile-select\).*?\{'
                . '[^}]*min-block-size:\s*44px;/s',
            $partial
        );
        $this->assertMatchesRegularExpression(
            '/#tabs\.is-navigation-enhanced\.is-navigation-mode-tabs > \.tab-label\s*\{'
                . '[^}]*min-block-size:\s*0;'
                . '[^}]*margin:\s*0 3px 4px 0 !important;'
                . '[^}]*padding:\s*4px 9px 3px !important;/s',
            $partial
        );
        $this->assertMatchesRegularExpression(
            '/@media screen and \(width <= 782px\).*?'
                . '#tabs\.is-navigation-enhanced\.is-navigation-mode-tabs > \.tab-label\s*\{'
                . '[^}]*min-block-size:\s*44px;'
                . '[^}]*margin:\s*0 4px 6px 0 !important;'
                . '[^}]*padding:\s*9px 11px !important;/s',
            $partial
        );
        $this->assertStringNotContainsString('grid-template-columns: minmax(0, 1fr);', $partial);
        $this->assertMatchesRegularExpression(
            '/@media \(pointer: coarse\).*?'
                . '#tabs\.is-navigation-enhanced\.is-navigation-mode-tabs > \.tab-label\s*\{'
                . '[^}]*min-block-size:\s*44px;/s',
            $partial
        );
        $this->assertStringContainsString(
            ':where(#tabs.is-navigation-enhanced.is-navigation-mode-responsive > .cocoon-settings-navigation)',
            $partial
        );
        $this->assertStringContainsString('top: 46px;', $partial);
        $this->assertStringContainsString('z-index: 20;', $partial);
    }

    /**
     * 新SCSSがCocoon設定画面だけを起点にし、生成CSSへ反映されることを検証する。
     */
    public function test_モダンUIスタイルが設定画面だけにスコープされている(): void
    {
        $partial = $this->readThemeFile('scss/_cocoon-settings-modern.scss');
        $settingsScss = $this->readThemeFile('scss/cocoon-settings.scss');
        $adminScss = $this->readThemeFile('scss/admin.scss');
        $settingsCss = str_replace(["\r\n", "\r"], "\n", $this->readThemeFile('css/cocoon-settings.css'));
        $adminCss = str_replace(["\r\n", "\r"], "\n", $this->readThemeFile('css/admin.css'));
        $adminSource = $this->readThemeFile('lib/admin.php');
        $scope = '.toplevel_page_theme-settings .wrap.admin-settings';

        $cssMarker = "/**\n * Cocoon設定画面専用のモダンUI";
        $cssPosition = strpos($settingsCss, $cssMarker);

        $this->assertIsInt($cssPosition);
        $modernCss = substr($settingsCss, $cssPosition);

        // partialと生成CSSの全トップレベル規則が、同じ設定画面スコープから始まることを確認する。
        $this->assertAllModernStyleBlocksAreScoped($partial, $scope, true);
        $this->assertAllModernStyleBlocksAreScoped($modernCss, $scope, false);
        $this->assertStringNotContainsString('@at-root', $partial);
        $this->assertStringContainsString("@import 'cocoon-settings-modern';", $settingsScss);
        $this->assertStringNotContainsString("@import 'cocoon-settings-modern';", $adminScss);
        $this->assertStringNotContainsString($scope, $adminCss);
        $this->assertStringNotContainsString('--cocoon-settings-surface', $adminCss);
        $this->assertStringContainsString($scope, $settingsCss);
        $this->assertStringContainsString('--cocoon-settings-surface', $settingsCss);
        $this->assertStringContainsString('form.admin-settings > .submit:last-of-type', $settingsCss);
        $this->assertStringContainsString("'/css/cocoon-settings.css'", $adminSource);
        $this->assertStringContainsString("'cocoon-settings',", $adminSource);
        $this->assertStringContainsString('filemtime( $settings_navigation_css_path )', $adminSource);
        $this->assertStringNotContainsString('container-type:', $settingsCss);
        $this->assertStringNotContainsString('@container', $settingsCss);

        // 選択タブは細い境界線と文字色で示し、強い下線を再導入しない。
        $this->assertMatchesRegularExpression(
            '/#tabs > \.tab-input:checked \+ \.tab-label\s*\{[^}]*box-shadow:\s*none;/s',
            $partial
        );
        $this->assertStringNotContainsString(
            'box-shadow: inset 0 -3px 0 var(--cocoon-settings-accent);',
            $partial
        );
        $this->assertStringNotContainsString(
            'border-inline-start: 3px solid var(--cocoon-settings-accent);',
            $partial
        );

        // 表示方式を単一の2択セグメントとして示し、選択中の面だけを境界・背景・太字で判別できることを固定する。
        $this->assertMatchesRegularExpression(
            '/:where\(\.cocoon-settings-view-mode-control\)\s*\{'
                . '[^}]*gap:\s*0;'
                . '[^}]*grid-template-columns:\s*repeat\(2, max-content\);/s',
            $partial
        );
        $this->assertMatchesRegularExpression(
            '/:where\(\.cocoon-settings-view-mode-button\)\s*\{'
                . '[^}]*border:\s*1px solid transparent;'
                . '[^}]*background:\s*transparent;'
                . '[^}]*box-shadow:\s*none;/s',
            $partial
        );
        $this->assertMatchesRegularExpression(
            "/:where\\(\\.cocoon-settings-view-mode-button\\[aria-checked='true'\\]\\)\\s*\\{"
                . '[^}]*border-color:\s*var\(--cocoon-settings-accent\);'
                . '[^}]*background:\s*var\(--cocoon-settings-surface\);'
                . '[^}]*font-weight:\s*600;/s',
            $partial
        );
        $this->assertStringNotContainsString(':where(.cocoon-settings-view-mode-button)::before {', $partial);
        $this->assertStringNotContainsString('.cocoon-settings-view-mode-recommended', $partial);
        $this->assertMatchesRegularExpression(
            "/@media \\(forced-colors: active\\).*?"
                . "\.cocoon-settings-view-mode-button\\[aria-checked='true'\\]\\).*?\\{"
                . '[^}]*border:\s*2px solid Highlight;/s',
            $partial
        );

        // 重要ルールの再生成漏れを検出するため、主要なSCSS契約が生成CSSにも存在することを確認する。
        $this->assertStringContainsString(
            $scope . ' #tabs > .tab-label {',
            $modernCss
        );
        $this->assertStringContainsString(
            $scope . ' :where(#tabs > .metabox-holder > .postbox, #tabs > .metabox-holder > .metabox-holder > .postbox) {',
            $modernCss
        );
        $this->assertStringContainsString(
            $scope . ' :where(#tab-reset-content > .postbox, #tab-reset-content > .metabox-holder > .postbox) {',
            $modernCss
        );
        $this->assertMatchesRegularExpression(
            '/' . preg_quote($scope, '/')
                . '[^{]*\.tooltip \.tip-content[^{]*\{[^}]*width: auto !important;'
                . '[^}]*max-inline-size: calc\(100vw - 24px\) !important;/s',
            $modernCss
        );
    }

    /**
     * :where()内のカンマを壊さず、外側のセレクター枝だけを分割できることを検証する。
     */
    public function test_CSSセレクターリストの全枝を個別に検査できる(): void
    {
        $scope = '.toplevel_page_theme-settings .wrap.admin-settings';
        $header = $scope . ' :where(.first, .second), ' . $scope . ' .third';

        $this->assertSame(
            [
                $scope . ' :where(.first, .second)',
                $scope . ' .third',
            ],
            $this->splitTopLevelSelectors($header)
        );
    }

    /**
     * 直下型と入れ子型のカード、リセット、モバイル保護を同時に検証する。
     */
    public function test_既存のカード構造とモバイル保護がスタイル対象に含まれている(): void
    {
        $allForms = $this->readThemeFile('lib/page-settings/all-forms.php');
        $adsForms = $this->readThemeFile('lib/page-settings/ads-forms.php');
        $resetForms = $this->readThemeFile('lib/page-settings/reset-forms.php');
        $topPage = $this->readThemeFile('lib/page-settings/_top-page.php');
        $partial = $this->readThemeFile('scss/_cocoon-settings-modern.scss');
        $settingsCss = $this->readThemeFile('css/cocoon-settings.css');

        // 各フォーム断片の最初の出力要素を固定し、別ラッパーの混入も検出する。
        $this->assertMatchesRegularExpression(
            '/\A<\?php\b.*?\?>\s*(?:<!--.*?-->\s*)*<div class="metabox-holder">\s*'
                . '(?:<!--.*?-->\s*)*<div id="all" class="postbox">/s',
            $allForms
        );
        $this->assertDoesNotMatchRegularExpression('/class="[^"]*\bmetabox-holder\b[^"]*"/', $adsForms);
        $this->assertMatchesRegularExpression(
            '/\A<\?php\b.*?\?>\s*(?:<!--.*?-->\s*)*<div id="ads" class="postbox">/s',
            $adsForms
        );
        $this->assertMatchesRegularExpression(
            '/\A<\?php\b.*?\?>\s*(?:<!--.*?-->\s*)*<div class="metabox-holder">\s*'
                . '(?:<!--.*?-->\s*)*<div id="reset" class="postbox">/s',
            $resetForms
        );

        // 外側のタブcontentと対応するフォーム断片の読み込み位置を一体で保護する。
        foreach ([
            'tab-all-content' => 'all-forms.php',
            'tab-ads-content' => 'ads-forms.php',
            'tab-reset-content' => 'reset-forms.php',
        ] as $contentId => $fileName) {
            $include = "<?php require_once abspath(__FILE__).'{$fileName}'; ?>";
            $includePattern = '/<div id="' . preg_quote($contentId, '/')
                . '" class="[^"]*\bmetabox-holder\b[^"]*">\s*'
                . preg_quote($include, '/') . '/';

            $this->assertSame(1, preg_match($includePattern, $topPage));
        }

        $this->assertStringContainsString('#tabs > .metabox-holder > .postbox', $partial);
        $this->assertStringContainsString('#tabs > .metabox-holder > .metabox-holder > .postbox', $partial);
        $this->assertStringContainsString('#tab-reset-content > .metabox-holder > .postbox', $partial);
        $this->assertStringContainsString('width: auto !important;', $partial);
        $this->assertStringContainsString('max-inline-size: calc(100vw - 24px) !important;', $partial);
        $this->assertStringContainsString('z-index: 90;', $partial);
        $this->assertStringContainsString(':where(.iframe-demo):not(:where(.wp-editor-wrap *))', $partial);

        // モバイルで既存の表、inline幅付き2カラム、直書きプレビューが横溢れしない契約を固定する。
        $this->assertStringContainsString('table-layout: fixed;', $partial);
        $this->assertStringContainsString('inline-size: 100% !important;', $partial);
        $this->assertStringContainsString(':where(.demo):not(:where(.wp-editor-wrap *))', $partial);
        $this->assertStringContainsString(
            '.toplevel_page_theme-settings .wrap.admin-settings :where(#tabs > .metabox-holder > .postbox, #tabs > .metabox-holder > .metabox-holder > .postbox)',
            $settingsCss
        );
        $this->assertStringContainsString(
            '.toplevel_page_theme-settings .wrap.admin-settings :where(#tab-reset-content > .postbox, #tab-reset-content > .metabox-holder > .postbox)',
            $settingsCss
        );
        $this->assertStringContainsString(
            '.toplevel_page_theme-settings .wrap.admin-settings :where(#tabs > .metabox-holder) :where(.demo):not(:where(.wp-editor-wrap *))',
            $settingsCss
        );
        $this->assertMatchesRegularExpression(
            '/@media screen and \(width <= 782px\).*?'
                . preg_quote(
                    '.toplevel_page_theme-settings .wrap.admin-settings :where(#tabs > .metabox-holder) :where(.form-table)',
                    '/'
                )
                . '\s*\{[^}]*table-layout: fixed;/s',
            $settingsCss
        );
    }

    /**
     * CSSまたはSCSSの直下ブロックを解析し、ページ外へ裸の規則が漏れていないことを確認する。
     */
    private function assertAllModernStyleBlocksAreScoped(string $source, string $scope, bool $isScss): void
    {
        $withoutBlockComments = preg_replace('!/\*.*?\*/!s', '', $source);
        $this->assertIsString($withoutBlockComments);
        $withoutComments = preg_replace('/^\s*\/\/.*$/m', '', $withoutBlockComments);
        $this->assertIsString($withoutComments);
        $blocks = $this->extractImmediateStyleBlocks($withoutComments);

        $this->assertNotEmpty($blocks);

        foreach ($blocks as $block) {
            if (
                str_starts_with($block['header'], '@media')
                || str_starts_with($block['header'], '@container')
            ) {
                $mediaBlocks = $this->extractImmediateStyleBlocks($block['body']);
                $this->assertNotEmpty($mediaBlocks);

                foreach ($mediaBlocks as $mediaBlock) {
                    $this->assertStyleBlockHeaderIsScoped($mediaBlock['header'], $scope, $isScss);
                }

                continue;
            }

            $this->assertStyleBlockHeaderIsScoped($block['header'], $scope, $isScss);
        }
    }

    /**
     * スタイルブロックの全セレクター枝が設定画面スコープ内にあることを検証する。
     */
    private function assertStyleBlockHeaderIsScoped(string $header, string $scope, bool $isScss): void
    {
        foreach ($this->splitTopLevelSelectors($header) as $selector) {
            if ($isScss) {
                $this->assertSame($scope, $selector);
                continue;
            }

            $this->assertTrue(
                $selector === $scope || str_starts_with($selector, $scope . ' '),
                'Cocoon設定画面の外へ漏れているセレクター: ' . $selector
            );
        }
    }

    /**
     * :where()などの括弧内カンマを保持したまま、最上位のセレクターだけを分割する。
     *
     * @return array<int, string>
     */
    private function splitTopLevelSelectors(string $header): array
    {
        $selectors = [];
        $start = 0;
        $depth = 0;
        $quote = null;
        $escaped = false;
        $length = strlen($header);

        // 引用符と括弧の深さを追跡し、最上位にあるカンマだけを区切りとして扱う。
        for ($index = 0; $index < $length; $index++) {
            $character = $header[$index];

            if ($escaped) {
                $escaped = false;
                continue;
            }

            if ($character === '\\') {
                $escaped = true;
                continue;
            }

            if ($quote !== null) {
                if ($character === $quote) {
                    $quote = null;
                }

                continue;
            }

            if ($character === '"' || $character === "'") {
                $quote = $character;
                continue;
            }

            if ($character === '(') {
                $depth++;
                continue;
            }

            if ($character === ')') {
                $depth = max(0, $depth - 1);
                continue;
            }

            if ($character !== ',' || $depth !== 0) {
                continue;
            }

            $selectors[] = trim(substr($header, $start, $index - $start));
            $start = $index + 1;
        }

        $selectors[] = trim(substr($header, $start));

        return array_values(array_filter(
            $selectors,
            static fn (string $selector): bool => $selector !== ''
        ));
    }

    /**
     * 指定IDを持つ開始タグを1つだけ取得する。
     */
    private function findTagById(string $source, string $tagName, string $id): string
    {
        $pattern = '/<' . preg_quote($tagName, '/') . '\b[^>]*\bid="'
            . preg_quote($id, '/') . '"[^>]*>/';

        $this->assertSame(1, preg_match($pattern, $source, $matches));

        return $matches[0];
    }

    /**
     * 開始タグが指定classトークンを持つことを検証する。
     */
    private function assertTagHasClass(string $tag, string $className): void
    {
        $this->assertSame(1, preg_match('/\bclass="([^"]*)"/', $tag, $matches));
        $classes = preg_split('/\s+/', trim($matches[1]), -1, PREG_SPLIT_NO_EMPTY);
        $this->assertIsArray($classes);
        $this->assertContains($className, $classes);
    }

    /**
     * 文字列から現在階層のスタイルブロック見出しと本文だけを取り出す。
     *
     * @return array<int, array{header: string, body: string}>
     */
    private function extractImmediateStyleBlocks(string $source): array
    {
        $blocks = [];
        $depth = 0;
        $headerStart = 0;
        $bodyStart = null;
        $currentHeader = '';
        $quote = null;
        $escaped = false;
        $length = strlen($source);

        // 引用符内の波括弧を無視し、現在階層の開始と終了だけを記録する。
        for ($index = 0; $index < $length; $index++) {
            $character = $source[$index];

            if ($escaped) {
                $escaped = false;
                continue;
            }

            if ($character === '\\') {
                $escaped = true;
                continue;
            }

            if ($quote !== null) {
                if ($character === $quote) {
                    $quote = null;
                }

                continue;
            }

            if ($character === '"' || $character === "'") {
                $quote = $character;
                continue;
            }

            if ($character === '{') {
                if ($depth === 0) {
                    $currentHeader = trim(substr($source, $headerStart, $index - $headerStart));
                    $bodyStart = $index + 1;
                }

                $depth++;
                continue;
            }

            if ($character !== '}') {
                continue;
            }

            $depth--;

            if ($depth === 0 && $bodyStart !== null) {
                $blocks[] = [
                    'header' => $currentHeader,
                    'body' => substr($source, $bodyStart, $index - $bodyStart),
                ];
                $headerStart = $index + 1;
                $bodyStart = null;
                $currentHeader = '';
            }
        }

        return $blocks;
    }

    /**
     * テーマルートからUTF-8テキストを読み込む。
     */
    private function readThemeFile(string $relativePath): string
    {
        $path = $this->themeRoot . '/' . $relativePath;
        $this->assertFileExists($path);
        $contents = file_get_contents($path);
        $this->assertIsString($contents);

        return $contents;
    }
}
