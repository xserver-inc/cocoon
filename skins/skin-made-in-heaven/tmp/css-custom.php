<?php
if (!defined('ABSPATH')) exit;


//******************************************************************************
//  多言語
//******************************************************************************
?>
:root{
  --hvn-notice: '<?php echo __('詳細はこちら', THEME_NAME); ?>';
  --hvn-prev: '<?php echo __('古い投稿', THEME_NAME); ?>';
  --hvn-next: '<?php echo __('新しい投稿', THEME_NAME); ?>';
  --hvn-table: '<?php echo __('スクロールできます→', THEME_NAME); ?>';
  --hvn-ribbon1: '<?php echo __('お勧め', THEME_NAME); ?>';
  --hvn-ribbon2: '<?php echo __('新着', THEME_NAME); ?>';
  --hvn-ribbon3: '<?php echo __('注目', THEME_NAME); ?>';
  --hvn-ribbon4: '<?php echo __('必見', THEME_NAME); ?>';
  --hvn-ribbon5: '<?php echo __('お得', THEME_NAME); ?>';
  --hvn-profile: '<?php echo  __('プロフィール', THEME_NAME); ?>';
  --hvn-new: 'NEW';
  --hvn-up: 'UP';
  --hvn-toc-height: 20lh;
}

<?php
//******************************************************************************
//  アピールエリア画像
//******************************************************************************
if (!get_appeal_area_image_url()) {
  echo <<<EOF
.appeal-in {
  padding-bottom: 0;
}

.appeal-content {
  background-color: #fff;
}

EOF;
}


//******************************************************************************
//  ローディング画面
//******************************************************************************
if (is_front_top_page() && (get_theme_mod('hvn_front_loading_setting', 'none') != 'none')) {
  echo <<<EOF
.body {
  visibility: hidden;
}

.loader-bg {
  background-color: var(--body-color);
  display: grid;
  height: 100svh;
  margin: auto;
  place-content: center;
  position: fixed;
  top: 0;
  width: 100vw;
  z-index: 9999;
}

EOF;
}


//******************************************************************************
//  モバイルSNSシェアボタン
//******************************************************************************
$css = [];
$sns_map = [
  ['is_bottom_twitter_share_button_visible'   , '.twitter-button'],
  ['is_bottom_mastodon_share_button_visible'  , '.mastodon-button'],
  ['is_bottom_bluesky_share_button_visible'   , '.bluesky-button'],
  ['is_bottom_misskey_share_button_visible'   , '.misskey-button'],
  ['is_bottom_facebook_share_button_visible'  , '.facebook-button'],
  ['is_bottom_threads_share_button_visible'   , '.threads-button'],
  ['is_bottom_reddit_share_button_visible'    , '.reddit-button'],
  ['is_bottom_hatebu_share_button_visible'    , '.hatebu-button'],
  ['is_bottom_line_at_share_button_visible'   , '.line-button'],
  ['is_bottom_pinterest_share_button_visible' , '.pinterest-button'],
  ['is_bottom_linkedin_share_button_visible'  , '.linkedin-button'],
  ['is_bottom_copy_share_button_visible'      , '.copy-button']
];

foreach ($sns_map as [$check_func, $selector]) {
  // SNSシェアボタン表示チェック
  if (!$check_func()) {
    $css[] = $selector;
  }
}

if (count($css) === count($sns_map)) {
  echo ".body .share-menu-button { display: none; }\n";
} else if (!empty($css)) {
  $selectors = implode(', ', $css);
  echo ".share-menu-content :is({$selectors}) { display: none; }\n";
}


//******************************************************************************
//  タブ一覧
//******************************************************************************
$tab_cnt = apply_filters('cocoon_index_max_category_tab_count', 3);
$total_tabs = $tab_cnt + 1;

$btn_selectors     = [];
$arrow_selectors   = [];
$content_selectors = [];

for ($i=1; $i<=$total_tabs; $i++) {
  $base = "#index-tab-{$i}:checked ~ .index-tab-buttons .index-tab-button[for='index-tab-{$i}']";

  $btn_selectors[]     = $base;
  $arrow_selectors[]   = $base . ":before";
  $content_selectors[] = "#index-tab-{$i}:checked ~ .tab-cont.tb{$i}";
}

$btn_id     = implode(",\n", $btn_selectors);
$arrow_id   = implode(",\n", $arrow_selectors);
$content_id = implode(",\n", $content_selectors);

echo <<<EOF
{$btn_id} {
  background-color: var(--main-color);
  border: 1px solid var(--main-color);
  color: var(--text-color);
  font-weight: unset;
}

{$arrow_id} {
  background-color: var(--main-color);
  bottom: -11px;
  clip-path: polygon(0 0,100% 0, 50% 100%);
  content: '';
  height: 11px;
  left: 50%;
  position: absolute;
  transform: translateX(-50%);
  width: 22px;
}

{$content_id} {
  animation: none;
  display: flex;
  flex-direction: column;
  gap: var(--gap30);
}

.index-tab-wrap {
  display: flex;
  flex-direction: column;
  row-gap: var(--gap30);
}

.body .index-tab-buttons {
  column-gap: var(--padding15);
  margin: 0;
  row-gap: var(--gap30);
}

.body .index-tab-button {
  align-content: center;
  background-color: #fff;
  border: 1px solid var(--main-color);
  border-radius: 0;
  box-shadow: var(--shadow-color);
  color: #333;
  font-size: var(--cocoon-text-size-s);
  height: 40px;
  margin: 0;
  padding: 0 var(--padding15);
  position: relative;
  width: 100%;
}

.body .index-tab-button:hover {
  background-color: var(--hover-color);
}

@media (width <=834px) {
  .body .index-tab-button {
    width: calc((100% - var(--padding15)) / 2);
  }
}

EOF;


//******************************************************************************
//  テーブルの1列目を固定対策
//******************************************************************************
if (is_responsive_table_first_column_sticky_enable()) {
  echo <<<EOF
.scrollable-table.stfc-sticky table:not(.wp-calendar-table) tr > *:first-child {
  background-color: unset;
  color: unset;
  left: unset;
  position: unset;
  z-index: unset;
}

.scrollable-table.stfc-sticky table:not(.wp-calendar-table) th:nth-child(1),
.scrollable-table.stfc-sticky table:not(.wp-calendar-table) tr td[rowspan] {
  background-color: #eee;
  left: 0;
  position: sticky;
  z-index: 1;
}

.scrollable-table.stfc-sticky table:not(.wp-calendar-table) th:nth-child(1):before,
.scrollable-table.stfc-sticky table:not(.wp-calendar-table) tr td[rowspan]:before {
  border: 1px solid var(--border-color);
  content: '';
  height: 100%;
  left: -1px;
  position: absolute;
  top: -1px;
  width: 100%;
}

EOF;
}


//******************************************************************************
//  ダークモード
//******************************************************************************
if (get_theme_mod('hvn_darkmode_setting')) {
  $body_color = '#333333';
  $body_rgb = colorcode_to_rgb($body_color);

  echo <<<EOF
.hvn-dark {
  --body-color: {$body_color};
  --body-rgb-color: {$body_rgb['red']} {$body_rgb['green']} {$body_rgb['blue']};
  --dark-content-color: #444;
  --dark-footer-color: #666;
  --hover-color: #777;
  --dark-text-color: #fff;
  --cocoon-text-color: var(--dark-text-color);
  --title-color: var(--dark-text-color);
}

.hvn-dark-switch {
  cursor: pointer;
  display: inline-block;
  margin-left: 5px;
}

.hvn-dark .hvn-dark-switch .fa-moon:before {
  color: #fff176;
  font-weight: 900;
}

.hvn-dark .body:not(.hvn-content-border) {
  --content-bgcolor: var(--body-color);
}

.hvn-dark .body:not(.hvn-content-border) .footer {
  --content-bgcolor: var(--dark-footer-color);
}

.hvn-dark #footer {
  background-color: var(--dark-footer-color);
}

.hvn-dark .body .appeal {
  background-color: var(--body-color);
}

EOF;
  if (get_theme_mod('hvn_category_color_setting')) {
    echo <<<EOF
.hvn-dark .front-top-page.no-sidebar #list-columns{
  --title-color: #333;
}

EOF;
  }
}


//******************************************************************************
//  フッターモバイルボタン
//******************************************************************************
$footer_text_color = 'var(--main-color)';
$header_bg = get_header_background_color();

if ($header_bg) {
  $footer_text_color = is_dark_hexcolor($header_bg) ? '#ffffff' : '#333333';

  // 背景カラー
  echo <<<EOF
.mobile-menu-buttons {
  background-color: {$header_bg};
}

EOF;
}

$header_text = get_header_text_color();
if ($header_text) {
  $footer_text_color = $header_text;
}

// テキストカラー
echo <<<EOF
.mobile-menu-buttons .menu-button > a,
.mobile-menu-buttons .menu-caption,
.mobile-menu-buttons .menu-icon {
  color: {$footer_text_color};
}

EOF;


//******************************************************************************
//  フッター背景カラー
//******************************************************************************
$footer_bgcolor = get_footer_background_color() ?: '#fff';
if (get_theme_mod('hvn_content_setting', true)) {
  $content_color = '#fff';
  $footer_color  = '#fff';
} else {
  $content_color = 'var(--body-color)';
  $footer_color  = "{$footer_bgcolor}";
}

$footer_text_color = is_dark_hexcolor($footer_bgcolor) ? '#ffffff' : '#333333';

echo <<<EOF
:root {
  --content-bgcolor: var(--dark-content-color, {$content_color});
}

.footer {
  --content-bgcolor: var(--dark-content-color, {$footer_color});
}

.footer-bottom,
.footer-bottom a,
.footer-bottom a:hover {
  color: var(--dark-text-color, {$footer_text_color});
}

.navi-footer-in > .menu-footer li,
.navi-footer-in > .menu-footer li:last-child {
  border-color: var(--dark-text-color, {$footer_text_color});
}

EOF;


//******************************************************************************
//  コンテンツ枠
//******************************************************************************
echo <<<EOF
.body:not(.hvn-content-border):is(.error404, .page, .single) .main,
.body:not(.hvn-content-border).archive .entry-content,
.body:not(.hvn-content-border) :is(.container, .navi-menu-content, .sidebar-menu-content) .widget {
  --cocoon-text-color: var(--title-color);
  --main-padding: 5px;
  background-color: transparent;
  color: var(--cocoon-text-color);
}

.body:not(.hvn-content-border) .footer aside.widget {
  --cocoon-text-color: var(--dark-text-color, {$footer_text_color});
}

@container (width <= 460px) {
  .body:not(.hvn-content-border) .widget_author_box .author-box {
    --main-padding: var(--padding15);
    border: 1px solid var(--border-color);
    border-radius: 0;
  }
}

EOF;

//******************************************************************************
//  カード枠
//******************************************************************************
echo <<<EOF
.body :is(.list, .recommended) .e-card {
  color: var(--title-color);
}

.body.hvn-card-border :is(.list, .recommended) .e-card {
  background-color: var(--dark-content-color, #fff);
  border-radius: var(--border-radius10);
  color: var(--dark-text-color, var(--cocoon-text-color));
  padding: var(--padding15);
}

.body:not(.hvn-card-border) :is(.list, .recommended) figure {
  border-radius: var(--border-radius10);
}

EOF;


//******************************************************************************
//  グローバルナビメニューデザイン
//******************************************************************************
$navi_setting = get_theme_mod('hvn_navi_setting', '0');
$origin_map = [
  '1' => 'center',
  '2' => 'left'
];

if ($navi_setting) {
  $origin = $origin_map[$navi_setting];

  echo <<<EOF
#navi .navi-in a:hover {
  background-color: unset;
}

.navi-in a:after {
  background: var(--text-color);
  bottom: 0;
  content: '';
  height: 2px;
  left: 0;
  position: absolute;
  transition: all .3s;
  transform: scale(0, 1);
  transform-origin: {$origin} top;
  width: 100%;
}

.navi-in a:hover:after {
  transform: scale(1, 1);
}

EOF;
}


//******************************************************************************
//  縦型カード3列
//******************************************************************************
echo <<<EOF
@media (width > 834px) {
  [class*=front-page-type-category] .ect-3-columns .a-wrap:nth-of-type(4) {
    display: none;
  }
}

EOF;


//******************************************************************************
//  拡張タイプ
//******************************************************************************
if (get_theme_mod('hvn_card_expansion_setting')) {

  $css_parts = [
    // カード2列
    'column_2' => <<<EOF
.list {
  --column: 2;
}

@media (width <= 834px) {
  .list {
     --column: 1;
  }
}

EOF,

    // 大きなカード（先頭のみ）
    'first_big' => <<<EOF
.front-top-page .list .a-wrap:first-child {
  grid-column: 1 / 3;
}

@media (width <=834px)  {
  .front-top-page .list .a-wrap:first-child {
    grid-column: unset;
  }
}

EOF,

    // 縦型カード2、3列+カテゴリーごと（2、3カラム）
    'vertical_common' => <<<EOF
.body .list-new-entries .card-content,
.body .list-popular .card-content {
  padding: 0 0 var(--gap30);
}

.list.widget-entry-cards p {
  color: var(--title-color);
  width: 100%;
}

EOF,

    // 大きなカード
    'big_card_full' => <<<EOF
.body.hvn-card-border .list.ect-big-card {
  background-color: var(--content-bgcolor);
  border-radius: var(--border-radius10);
  padding: var(--gap30) var(--main-padding);
}

.list.ect-big-card .a-wrap .entry-card {
  border-bottom: 1px dotted var(--border-color);
  border-radius: 0;
  padding: 0 0 var(--gap30) 0;
}

.list.ect-big-card .a-wrap:last-child .entry-card {
  border: 0;
  padding-bottom: 0;
}

.list.ect-big-card .a-wrap:hover img {
  transform: unset;
}

.entry-card-snippet {
  color: var(--s-text-color);
  line-height: 1.4;
  margin: 5px 0;
}

EOF
  ];

  // カードタイプ
  $presets = [
    1 => $css_parts['column_2'],
    2 => $css_parts['column_2'] . $css_parts['first_big'],
    3 => $css_parts['first_big'],
    4 => $css_parts['vertical_common'],
    5 => $css_parts['big_card_full'],
  ];

  // CSSマッピング（フロントページタイプとカードタイプ）
  $matrix = [
    'entry_card'      => ['index' => 1, 'tab_index' => 1, 'category' => 1, 'category_2_columns' => 1, 'category_3_columns' => 1],
    'big_card_first'  => ['index' => 0, 'tab_index' => 0, 'category' => 2, 'category_2_columns' => 2, 'category_3_columns' => 2],
    'big_card'        => ['index' => 5, 'tab_index' => 5, 'category' => 5, 'category_2_columns' => 5, 'category_3_columns' => 5],
    'vertical_card_2' => ['index' => 0, 'tab_index' => 0, 'category' => 3, 'category_2_columns' => 4, 'category_3_columns' => 4],
    'vertical_card_3' => ['index' => 0, 'tab_index' => 0, 'category' => 0, 'category_2_columns' => 4, 'category_3_columns' => 4],
  ];

  $card_type  = get_entry_card_type();
  $page_type  = get_front_page_type();

  $preset_no = $matrix[$card_type][$page_type] ?? 0;

  if ($preset_no && isset($presets[$preset_no])) {
    echo $presets[$preset_no];
  }
}


//******************************************************************************
//  カテゴリーごと背景色
//******************************************************************************
if (
  get_theme_mod('hvn_front_none_setting', true) &&
  get_theme_mod('hvn_category_color_setting') &&
  (is_entry_card_type_vertical_card_3() || is_front_page_type_category_3_columns())
) {
  $color = get_theme_mod('hvn_main_color_setting', HVN_MAIN_COLOR);
  $rgb = hvn_color_mix_rgb($color, 0.25);

  echo <<<EOF
:root {
  --category-color: {$rgb['red']} {$rgb['green']} {$rgb['blue']};
}

.front-top-page.no-sidebar #list-columns {
  --title-color: var(--dark-text-color, #333);
  background-color: rgb(var(--category-color) / 100%);
  margin: 0 calc(50% - 50vw);
  padding: var(--gap30) calc(50vw - 50%);
}

.front-top-page.no-sidebar:not(:has(.ad-index-bottom, .widget-index-bottom, .widget-content-bottom)) #footer {
  margin-top: 0;
}

.front-top-page.no-sidebar:not(:has(.list-column)) #footer {
  margin-top: var(--gap30);
}

EOF;

  if (get_theme_mod('hvn_header_wave_setting')) {
    echo <<<EOF
.hvn-wave-category {
  --body-rgb-color: var(--category-color);
  display: block;
  height: 50px;
  margin: 0 calc(50% - 50vw) calc(var(--gap30) * -1);
  padding: 0 calc(50vw - 50%);
  position: relative;
}

EOF;
  }
}


//******************************************************************************
//  「新着記事」表示
//******************************************************************************
if (!get_theme_mod('hvn_front_none_setting', true)) {
  echo <<<EOF
.body .index-tab-button[for="index-tab-1"],
.body .tab-cont.tb1,
.body .list-new-entries {
  display: none;
}

EOF;
}


//******************************************************************************
//  目次スタイル
//******************************************************************************
switch(get_theme_mod('hvn_toc_style_setting', '0')) {
  case '1':
    echo <<<EOF
.main .toc {
  border: 1px solid var(--main-color);
  padding: 0;
}

.main .toc-title {
  background-color: var(--main-color);
  border: 0;
  color: var(--text-color);
  margin: 0;
  padding: var(--padding15);
}

.main .toc-content {
  padding: var(--gap30);
}

EOF;

    break;

  case '2':
    echo <<<EOF
.main .toc {
  background-clip: padding-box;
  background-color: rgb(204 204 204 / 15%);
  border: 0;
  border-bottom: 4px double var(--border-color);
  border-top: 4px double var(--border-color);
}

.main .toc-title {
  border:0;
  margin-bottom:0;
}

EOF;
    break;
}


//******************************************************************************
//  目次ハイライト
//******************************************************************************
if (get_theme_mod('hvn_toc_setting')) {
  echo <<<EOF
.hvn-scroll-toc .sidebar-scroll .toc-content li.current:before {
  color: var(--main-color);
}

.hvn-scroll-toc .sidebar-scroll .toc-content li:before {
  color: var(--hover-color);
}

.hvn-scroll-toc .sidebar-scroll .toc-content {
  max-height: var(--hvn-toc-height);
  overflow-y: auto;
}

EOF;
}


//******************************************************************************
//  目次ボタン
//******************************************************************************
if (get_theme_mod('hvn_toc_fix_setting')) {
  echo <<<EOF
.hvn-modal {
  height: 100%;
  inset: 0;
  opacity: 0;
  pointer-events: none;
  position: fixed;
  transition: opacity .3s;
  visibility: hidden;
  width: 100%;
  z-index: 9999;
}

.hvn-modal:target {
  opacity: 1;
  pointer-events: auto;
  visibility: visible;
}

.hvn-content-wrap {
  background-color: var(--content-bgcolor);
  border: 0;
  display: flex;
  flex-direction: column;
  left: 50%;
  max-height: calc(100% - 100px);
  opacity: 0;
  overflow: hidden;
  padding: var(--gap30);
  position: absolute;
  top: 50%;
  transform: translate(-50%, -50%);
  width: 1170px;
  z-index: 2;
}

.hvn-modal:target .hvn-content-wrap {
  opacity: 1;
}

.hvn-content-wrap .toc {
  display: flex;
  flex-direction: column;
  flex-grow: 1;
  min-height: 0;
  overflow: hidden;
}

.hvn-content-wrap .toc-content {
  flex-grow: 1;
  min-height: 0;
  overflow-y: auto;
}

#hvn-toc .hvn-title {
  border-bottom: 1px dotted var(--border-color);
  margin-bottom: var(--gap30);
  padding: 0 0 var(--padding15);
  text-align: center;
}

.hvn-background {
  background-color: rgb(0 0 0 / 50%);
  height: 100%;
  left: 0;
  position: absolute;
  top: 0;
  width: 100%;
  z-index: 1;
}

.hvn .hvn-open-btn {
  align-items: center;
  background-color: #fff;
  border: 1px solid var(--main-color);
  border-radius: var(--border-radius100);
  bottom: 110px;
  box-shadow: var(--shadow-color);
  color: var(--main-color);
  display: grid;
  height: 50px;
  place-content: center;
  position: fixed;
  right: var(--gap30);
  text-decoration: none;
  width: 50px;
  z-index: 999;
}

@media (width < 1190px) {
  .hvn-content-wrap {
    width: calc(100% - 20px);
  }
}

@media (width <=1023px) {
  .hvn .hvn-open-btn {
    bottom: 60px;
    right: 10px;
  }
}

html:has(#hvn-toc:target) {
  overflow: hidden;
}

EOF;
}


//******************************************************************************
//  通知エリア固定
//******************************************************************************
if (get_theme_mod('hvn_notice_setting')) {
  echo <<<EOF
.header-container {
  position: relative;
  z-index: 3;
}

.notice-area-wrap {
  position: sticky;
  top: 0;
  z-index: 3;
}

EOF;
}


//******************************************************************************
//  通知メッセージ横スクロール
//******************************************************************************
if (get_theme_mod('hvn_notice_scroll_setting')) {
  echo <<<EOF
.notice-area-message .swiper .swiper-wrapper {
  transition-timing-function: linear;
}

EOF;
}


//******************************************************************************
//  評価スター・ランキングハート
//******************************************************************************
if (get_theme_mod('hvn_star_setting')) {
  echo <<<EOF
.rating-star {
  gap: 2px;
}

.rating-star .fa-star:before,
.rating-star .fa-star-half-alt:before,
.rating-star .fa-star-half-alt:after {
  content: '\\f004';
  color: #e589a2;
}

.rating-star .fa-star-half-alt:after {
  font-weight: 400;
}

.rating-star .fa-star-half-alt:before {
  display: block;
  overflow: hidden;
  position: absolute;
  width: 0.5em;
}

EOF;
}


//******************************************************************************
//  サムネイル画像の比率変更
//******************************************************************************
if (get_theme_mod('hvn_thumb_option_setting')) {
  echo <<<EOF
.body {
  --aspect-ratio: var(--card-ratio);
}

.a-wrap figure {
  aspect-ratio: var(--aspect-ratio);
}

EOF;
}


//******************************************************************************
//  オートプレイ
//******************************************************************************
if (get_theme_mod('hvn_swiper_auto_setting')) {
  echo <<<EOF
.body .is-auto-horizontal {
  --swiper-pagination-bullet-inactive-color: var(--dark-text-color, #333);
}

.body.hvn .swiper-pagination {
  bottom: 0;
}

.body .swiper-pagination-bullet-active {
  background-color: var(--main-color);
}

EOF;
}


//******************************************************************************
//  縦アイキャッチ背景ぼかし
//******************************************************************************
if ($GLOBALS['hvn_eyecatch']) {
  echo <<<EOF
.eye-catch img {
  margin: 0 auto;
  min-width: unset;
  position: relative;
  width: unset;
}

.eye-catch:before {
  background: var(--eyecatch) no-repeat center;
  background-size: cover;
  bottom: -5px;
  content: '';
  filter: blur(5px);
  left: -5px;
  position: absolute;
  right: -5px;
  top: -5px;
  z-index: 0;
}

EOF;
}


//******************************************************************************
//  アコーディオン化
//******************************************************************************
if (get_theme_mod('hvn_accordion_setting')) {
  echo <<<EOF
.body :is(.widget_pages, .body .widget_archive, .widget_categories) ul {
  display: block;
  margin: 0;
  padding: 0;
}

.body :is(.widget_pages, .widget_archive, .widget_categories)  a {
  background-color: unset;
  border-radius: 0;
  border-top: 1px dotted #ccc;
  color: var(--cocoon-text-color);
  justify-content: unset;
  padding: 3px 2em 3px 5px;
}

.body :is(.widget_pages, .widget_archive, .widget_categories) > ul > li:first-of-type > a {
  border: 0;
}

:is(.widget_pages, .widget_archive, .widget_categories) .children a {
  text-indent: 1.5em;
}

:is(.widget_pages, .widget_archive, .widget_categories) ul:not(.children) > li > a:before {
  content: '\\f07b';
  font-family: 'Font Awesome 5 Free';
  font-weight: bold;
  margin-right: 0.5em;
}

.widget_categories .post-count,
.widget_archive .post-count {
  margin-left:auto;
}

.widget_tag_cloud .tagcloud a {
  width: 100%;
}

EOF;
}

echo <<<EOF
button.sub-item {
  background-color: transparent;
  border: 1px solid #ccc;
  cursor: pointer;
  display: grid;
  height: 20px;
  place-content: center;
  position: absolute;
  right: 5px;
  top: 8px;
  width: 20px;
}

button.sub-item:before {
  color: #ccc;
  content: '\\f078';
  display: block;
  font-family: 'Font Awesome 5 Free';
  font-size: 5px;
  font-weight: bold;
}

button.sub-item.active:before {
  content: '\\f077';
}

.toc button.sub-item,
.widget_tag_cloud button.sub-item {
  margin-top: 5px;
  position: unset;
  width: 100%;
}

EOF;


//******************************************************************************
//  プロフィール背景画像
//******************************************************************************
$img_url   = wp_get_attachment_url(get_theme_mod('hvn_prof_setting'));
$video_url = wp_get_attachment_url(get_theme_mod('hvn_prof_video_setting'));

$bg_image = (!$video_url && $img_url) ? "url('$img_url')" : "none";

if ($img_url || $video_url) {
  echo <<<EOF
@container (width <=460px) {
  .body.hvn .author-thumb {
    background: {$bg_image} no-repeat center;
    background-size: cover;
    height: 200px;
    margin: 0 0 50px;
    position: relative;
    width: 100%;
  }

  .body.hvn .author-thumb img {
    margin-top: 150px;
  }

  .body.hvn .hvn-author-video {
    display: block;
    height: 200px;
    left: 0;
    object-fit: cover;
    position: absolute;
    top: 0;
    width: 100%;
  }
}

.hvn-author-video {
  display: none;
}

EOF;
}


/*******************************************************************************
**  プロフィールボタン表示
*******************************************************************************/
if (get_theme_mod('hvn_profile_btn_setting')) {
  echo <<<EOF
.hvn-profile-btn a {
  border-radius: 0;
  display: grid;
  margin: var(--padding15) 0 0 0;
  text-decoration: none;
}

.hvn-profile-btn a:before {
  content: var(--hvn-profile);
}

EOF;
}


/*******************************************************************************
**  SNSフォローカラー表示
*******************************************************************************/
if (get_theme_mod('hvn_profile_follows_color_setting')) {
  echo <<<EOF
.body .author-box .sns-follow-buttons a.follow-button {
  --cocoon-text-color: var(--cocoon-sns-color);
}

EOF;
}


//******************************************************************************
//  タイトル・説明文表示
//******************************************************************************
if (get_theme_mod('hvn_tcheck_option_setting')) {
  $settings = hvn_get_title_settings();
  $selectors = [
    'new'      => '.list-new-entries',
    'popular'  => '.list-popular',
    'category' => '.list-columns',
  ];

  $dynamic_css = '';
  $all_selectors = implode(',', $selectors);

  foreach ($selectors as $key => $sel) {
    if (!isset($settings[$key])) continue;

    $group = $settings[$key];
    $t = get_theme_mod($group['title']['key'], $group['title']['default']);
    $s = get_theme_mod($group['sub']['key'], $group['sub']['default']);

    echo <<<EOF
{$sel}:before { content: "{$t}"; }
{$sel}:after  { content: "{$s}"; }

EOF;
  }

  echo <<<EOF
:root {
  --main-font-size: 40px;
  --sub-font-size: 14px;
}

.hvn .list-new-entries-title,
.hvn .list-popular-title {
  display: none;
}

.hvn :is({$all_selectors}) {
  position: relative;
  padding-top: calc((var(--main-font-size) + var(--sub-font-size)) * 1.8 + var(--gap30))!important;
}

.hvn :is({$all_selectors}):before {
  color: var(--title-color);
  font-size: var(--main-font-size);
  font-weight: bold;
  position: absolute;
  text-align: center;
  top: 0;
  width: 100%;
  z-index: 1;
}

.hvn :is({$all_selectors}):after {
  color: var(--title-color);
  display: block;
  font-size: var(--sub-font-size);
  left: 0;
  position: absolute;
  text-align: center;
  width: 100%;
  top: calc(var(--main-font-size) * 1.8);
}

EOF;
}

//******************************************************************************
//  コメント
//******************************************************************************
if (get_theme_mod('hvn_comment_setting')) {
  echo <<<EOF
.hvn-comment {
  display: flex;
  gap: 5px;
}

.hvn-comment-icon {
  display: flex;
  flex-direction: column;
  gap: 5px;

}

.hvn-comment figure {
  aspect-ratio: 1 / 1;
  width: 50px;
}

.hvn-comment img {
  object-fit: cover;
  height: 100%;
}

EOF;
}


//******************************************************************************
//  「画像」ブロックに拡大効果
//******************************************************************************
if (get_theme_mod('image_zoom_effect', 'none') !== 'none') {
  echo <<<EOF
:is(.attachment, .wp-block-image) :is(
  a[href$=".jpg"  i],
  a[href$=".jpeg" i],
  a[href$=".png"  i],
  a[href$=".gif"  i],
  a[href$=".webp" i]
) {
  cursor: zoom-in;
}

EOF;
}


//******************************************************************************
//  フロントページヘッダー表示
//******************************************************************************
if (!get_theme_mod('hvn_header_option_setting', true)) {
  echo <<<EOF
.front-top-page .header {
  display: none;
}

EOF;
}


//******************************************************************************
//  波線
//******************************************************************************
if (get_theme_mod('hvn_header_wave_setting')) {
  echo <<<EOF
.waves {
  bottom: -1px;
  height: 50px;
  left: 0;
  position: absolute;
  width: 100%;
  z-index: 1;
}

.parallax > use {
  animation: move-forever var(--duration, 25s) cubic-bezier(.55, .5, .45, .5) infinite;
  animation-delay: var(--delay, 0s);
}

/* 属性による個別設定の管理 */
.parallax > use[data-wave-index="1"] { --duration: 7s;  --delay: -2s; }
.parallax > use[data-wave-index="2"] { --duration: 10s; --delay: -3s; }
.parallax > use[data-wave-index="3"] { --duration: 13s; --delay: -4s; }
.parallax > use[data-wave-index="4"] { --duration: 20s; --delay: -5s; }

@keyframes move-forever {
  0%   { transform: translate3d(-90px, 0, 0); }
  100% { transform: translate3d(85px, 0, 0); }
}

EOF;
}


//******************************************************************************
//  フォント
//******************************************************************************
$selectors = [];
$font_family = get_theme_mod('hvn_font_setting', '');
if (!get_theme_mod('hv_header_option_setting')) {
  $selectors[] = '.hvn-header .message';
}

$selectors[] = '.logo-text';

if (!empty($font_family)) {
  $selector_string = implode(",\n", $selectors);

  echo <<<EOF
{$selector_string} {
  font-family: "{$font_family}", sans-serif;
}

EOF;
}


//******************************************************************************
//  動画・スライドヘッダー
//******************************************************************************
if (get_theme_mod('hvn_header_setting', 'none') == 'none') return;

echo <<<EOF
:root {
  --height: calc(100svh - var(--ah));
}

@media (width <=1023px) {
  :root {
    --height: calc(100svh - var(--ah) - 50px);
  }
}

.hvn-header {
  height: var(--height);
  overflow: hidden;
  position: relative;
}

.hvn-mask {
  background-size: 2px 2px;
  content: '';
  display: block;
  height: 100%;
  position: absolute;
  top: 0;
  width: 100%;
  z-index: 1;
}

.hvn-header video {
  height: var(--height);
  object-fit: cover;
  width: 100%;
}

.hvn-header .message {
  color: #fff;
  font-weight: bold;
  display: grid;
  inset: 0;
  padding: 0 10px;
  place-content: center;
  position: absolute;
  z-index: 1;
}


EOF;


//******************************************************************************
//  フォントサイズ
//******************************************************************************
$font_size = get_theme_mod('hvn_appea_font_size_setting', 40);

echo <<<EOF
.hvn-header .message {
  font-size: {$font_size}px;
}

EOF;


//******************************************************************************
//  テキスト縦書き
//******************************************************************************
if (get_theme_mod('hvn_header_vertival_setting')) {
  echo <<<EOF
.message div {
  border: 1px solid #fff;
  padding: 1em 0;
  text-align: center;
  text-orientation: upright;
  writing-mode: vertical-rl;
}

EOF;
}


//******************************************************************************
//  フィルター処理
//******************************************************************************
$url  = 'none';
$no = get_theme_mod('hvn_header_filter_setting', '0');
switch($no) {
  case '1':
  case '2':
  case '3':
    $url = HVN_SKIN_URL . 'assets/img/' . $no . '.gif';
    $url = "url({$url})";
    break;

  case '4':
    echo <<<EOF
.hvn-header video,
.hvn-swiper {
  filter: grayscale(1);
}

EOF;
    break;
}


//******************************************************************************
//  オーバーレイカラー
//******************************************************************************
$color = get_theme_mod('hvn_header_color_setting');
$opacity = get_theme_mod('hvn_header_opacity_setting', 50);

if ($color && $opacity) {
  $rgb = colorcode_to_rgb($color);
  $color = "rgb({$rgb['red']} {$rgb['green']} {$rgb['blue']} / {$opacity}%)";
} else {
  $color = 'unset';
}

echo <<<EOF
.hvn-mask {
  background-color: {$color};
  background-image: {$url};
}

EOF;


//******************************************************************************
//  スライド中のズーム
//******************************************************************************
if (get_theme_mod('hvn_header_setting', 'none') != 'image' && hvn_image_count() < 2) return;

$zoom_setting = get_theme_mod('hvn_header_animation_setting', '0');
$zoom_map = [
  '1' => ['s' => 1,   'e' => 1.1],
  '2' => ['s' => 1.1, 'e' => 1],
];
if (isset($zoom_map[$zoom_setting])) {
  $s_zoom = $zoom_map[$zoom_setting]['s'];
  $e_zoom = $zoom_map[$zoom_setting]['e'];

  echo <<<EOF
:root {
  --s-zoom: {$s_zoom};
  --e-zoom: {$e_zoom};
}

.hvn-swiper .swiper-slide-active img,
.hvn-swiper .swiper-slide-duplicate-active img,
.hvn-swiper .swiper-slide-prev img {
  animation: zoom 8s linear 0s normal both;
}

@keyframes zoom {
  0% {
    filter: blur(3px);
    transform: scale(var(--s-zoom));
  }
  100% {
    filter: blur(0);
    transform: scale(var(--e-zoom));
  }
}

EOF;
}


//******************************************************************************
//  スライド切り替え
//******************************************************************************
echo <<<EOF
:root {
  --ani: 2s cubic-bezier(.4, 0, .2, 1) 0s forwards;
}

.hvn-swiper {
  margin: 0;
  padding: 0;
  position: relative;
  z-index: 0;
}

.hvn-swiper img {
  height: var(--height);
  object-fit: cover;
  object-position: center;
  vertical-align: top;
  width: 100%;
}

.swiper-wrapper .img1,
.swiper-wrapper .img2 {
  left: 0;
  position: absolute;
  top: 0;
  width: 100%;
}

.hvn-swiper.is-changed .swiper-slide-active .img1 {
  animation: slide1 var(--ani);
}

.hvn-swiper.is-changed .swiper-slide-active .img2 {
  animation: slide2 var(--ani);
}

EOF;

$fade_setting = get_theme_mod('hvn_header_fade_setting', 'fade');
$fade_map = [
  'horizontal' => [
    'slide1' => ['0%' => '100% 100%, 100% 100%, 100% 0, 100% 0', '100%' => '0 100%, 100% 100%, 100% 0, 0 0']
  ],
  'vertical' => [
    'slide1' => ['0%' => '0 100%, 100% 100%, 100% 100%, 0 100%', '100%' => '0 0, 100% 0, 100% 100%, 0 100%']
  ],
  'h-split' => [
    'slide1' => ['0%' => '100% 0, 100% 0, 100% 50%, 100% 50%', '100%' => '100% 0, 0 0, 0 50%, 100% 50%'],
    'slide2' => ['0%' => '0 49%, 0 49%, 0 100%, 0 100%', '100%' => '0 49%, 100% 49%, 100% 100%, 0 100%']
  ],
  'v-split' => [
    'slide1' => ['0%' => '0 100%, 50% 100%, 50% 100%, 0 100%', '100%' => '0 100%, 50% 100%, 50% 0, 0 0'],
    'slide2' => ['0%' => '49% 0, 100% 0, 100% 0, 49% 0', '100%' => '49% 100%, 100% 100%, 100% 0, 49% 0']
  ],
];

if (isset($fade_map[$fade_setting])) {
  foreach ($fade_map[$fade_setting] as $name => $steps) {
    echo <<<EOF
@keyframes {$name} {
  0% {
    clip-path: polygon({$steps['0%']});
  }
  100% {
    clip-path: polygon({$steps['100%']});
  }
}

EOF;
  }
}
