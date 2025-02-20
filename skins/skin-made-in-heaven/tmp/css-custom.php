<?php
if (!defined('ABSPATH')) exit;

global $_HVN_EYECATCH;


//******************************************************************************
//  多言語
//******************************************************************************
?>
:root{
  --hvn-notice: '<?php echo __('詳細はこちら', THEME_NAME); ?>';
  --hvn-prev: '<?php echo __('過去の投稿', THEME_NAME); ?>';
  --hvn-next: '<?php echo __('新しい投稿', THEME_NAME); ?>';
  --hvn-table: '<?php echo __('スクロールできます→', THEME_NAME); ?>';
  --hvn-ribbon1: '<?php echo __('お勧め', THEME_NAME); ?>';
  --hvn-ribbon2: '<?php echo __('新着', THEME_NAME); ?>';
  --hvn-ribbon3: '<?php echo __('注目', THEME_NAME); ?>';
  --hvn-ribbon4: '<?php echo __('必見', THEME_NAME); ?>';
  --hvn-ribbon5: '<?php echo __('お得', THEME_NAME); ?>';
}
<?php
//******************************************************************************
//  ダークモード
//******************************************************************************
if (is_front_top_page() && (get_theme_mod('hvn_front_loading_setting', 'none') != 'none')) {
  echo <<< EOF
.body {
  visibility: hidden;
}
EOF;

}

echo <<< EOF
.body .is-auto-horizontal {
  --swiper-pagination-bullet-inactive-color: var(--title-color);
}

.body.hvn-card-border .is-auto-horizontal {
  --swiper-pagination-bullet-inactive-color: var(--dark-text-color, #333);
}

.body.hvn-dark {
  --body-rgb-color: 51 51 51;
  --hover-color: #777;
  --dark-body-color: #333;
  --dark-content-bgcolor: #444;
  --dark-footer-color: #666;
  --dark-category-color: 85 85 85;
  --dark-text-color: #fff;

  --cocoon-text-color: var(--dark-text-color);
  --content-bgcolor: var(--dark-content-bgcolor);
  --body-color:var(--dark-body-color);
  --title-color: var(--dark-text-color);
}

.hvn-dark .author-thumb img {
  background-color: var(--main-color);
}

.hvn-dark #footer {
  background-color: var(--dark-footer-color);
}

.hvn-dark .footer-bottom,
.hvn-dark .footer-bottom a,
.hvn-dark .footer-bottom a:hover {
  border-color: var(--dark-text-color);
  color: var(--dark-text-color);
}

.hvn-dark .navi-footer-in > .menu-footer li,
.hvn-dark .navi-footer-in > .menu-footer li:last-child,
.hvn-dark .footer .footer-in .footer-bottom-content {
  border-color: var(--dark-text-color);
}

.hvn-dark-switch {
  display: inline-block;
  margin-left: 5px;
  width: fit-content;
}

#hvn-dark {
  display: none;
}

.hvn-dark-switch label:before {
  content: '\\f186';
  font-family: 'Font Awesome 5 Free';
  font-weight: 400;
}

#hvn-dark:checked + label:before {
  color: #fff176;
  font-weight: 900;
}

EOF;


//******************************************************************************
//  モバイルSNSシェアボタン
//******************************************************************************
$css = [];
$count = 0;
$css_arr = [
  ['is_bottom_twitter_share_button_visible'   ,'.twitter-button'],
  ['is_bottom_mastodon_share_button_visible'  ,'.mastodon-button'],
  ['is_bottom_bluesky_share_button_visible'   ,'.bluesky-button'],
  ['is_bottom_misskey_share_button_visible'   ,'.misskey-button'],
  ['is_bottom_facebook_share_button_visible'  ,'.facebook-button'],
  ['is_bottom_hatebu_share_button_visible'    ,'.hatebu-button'],
  ['is_bottom_pocket_share_button_visible'    ,'.pocket-button'],
  ['is_bottom_line_at_share_button_visible'   ,'.line-button'],
  ['is_bottom_pinterest_share_button_visible' ,'.pinterest-button'],
  ['is_bottom_linkedin_share_button_visible'  ,'.linkedin-button'],
  ['is_bottom_copy_share_button_visible'      ,'.copy-button']
];

for ($i=0; $i<count($css_arr); $i++){
  $func = $css_arr[$i][0];
  if (!$func()) {
    $count ++;
    $css[] = $css_arr[$i][1];
  }
}

if ($count == count($css_arr)) {
  echo ".body .share-menu-button { display: none; }\n";
} else if ($css) {
  $css = implode(',', $css);
  echo ".share-menu-content :is({$css}){ display: none; }\n";
}


//******************************************************************************
//  タブ一覧
//******************************************************************************
$id_array = [];
$tab_cnt = apply_filters('cocoon_index_max_category_tab_count', 3);

for ($i=1; $i<=$tab_cnt + 1; $i++) {
  $id_array [$i - 1] = "#index-tab-{$i}:checked ~ .index-tab-buttons .index-tab-button[for='index-tab-{$i}']";
}
$id = implode(',', $id_array ) ;

echo <<< EOF
{$id} {
  background-color: var(--main-color);
  border: 1px solid var(--main-color);
  color: var(--text-color);
  font-weight: unset;
}

EOF;

for ($i=1; $i<=$tab_cnt + 1; $i++) {
  $id_array [$i - 1] = "#index-tab-{$i}:checked ~ .index-tab-buttons .index-tab-button[for='index-tab-{$i}']:before";
}
$id = implode(',', $id_array ) ;

echo <<< EOF
{$id} {
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

EOF;

for ($i=1; $i<=$tab_cnt + 1; $i++) {
  $id_array [$i - 1] = "#index-tab-{$i}:checked ~ .tab-cont.tb{$i}";
}
$id = implode(',', $id_array ) ;

echo <<< EOF
{$id} {
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
  echo <<< EOF
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
//
//  基本カラー
//
//******************************************************************************

//******************************************************************************
//  ヘッダー背景カラー
//******************************************************************************

// モバイルフッタボタン背景カラー
$text = 'var(--main-color)';
$color = get_header_background_color();
if ($color) {
  $text = '#ffffff';
  echo <<< EOF
.mobile-menu-buttons {
  background-color: {$color};
}

EOF;
}

// モバイルフッタボタンテキスト
$color = get_header_text_color();
if ($color) {
  $text = $color;
}

echo <<< EOF
.mobile-menu-buttons .menu-button > a,
.mobile-menu-buttons .menu-caption,
.mobile-menu-buttons .menu-icon{
  color: {$text};
}

EOF;


//******************************************************************************
//  フッター背景カラー
//******************************************************************************
$text = '#333333';
$footer_color = get_footer_background_color();
if ($footer_color == null || $footer_color == "#ffffff") {
  $footer_color = '#ffffff';
}

if (is_dark_hexcolor($footer_color)) {
  $text = '#ffffff';
}


echo <<< EOF
.footer-bottom,
.footer-bottom a,
.footer-bottom a:hover {
  border-color: {$text};
  color: {$text};
}

.navi-footer-in > .menu-footer li,
.navi-footer-in > .menu-footer li:last-child,
.footer .footer-in .footer-bottom-content {
  border-color: {$text};
}

.hvn-dark-switch label:before {
  color: {$text};
}

EOF;


//******************************************************************************
//  グローバルナビメニューデザイン
//******************************************************************************
$no = get_theme_mod('hvn_navi_setting');
if ($no) {
  $o_array = ['', 'center', 'left'];
  echo <<< EOF
.navi-in a:hover {
  background-color: unset;
}

.navi-in a:after {
  background: var(--text-color);
  bottom: 0;
  content: '';
  height: 2px;
  left: 0;
  transition: all .3s;
  transform: scale(0, 1);
  transform-origin: {$o_array[$no]} top;
  position: absolute;
  width: 100%;
}

.navi-in a:hover:after {
  transform: scale(1, 1);
}

EOF;
}


//******************************************************************************
//
//  拡張
//
//******************************************************************************

//******************************************************************************
//  カード枠
//******************************************************************************
echo <<< EOF
.body :is(.list, .is-auto-horizontal) .e-card {
  color: var(--title-color);
}

.body.hvn-card-border :is(.list, .is-auto-horizontal) .e-card {
  background-color: var(--dark-content-bgcolor, var(--hvn-white-color));
  border-radius: var(--border-radius10);
  color: var(--dark-text-color, var(--cocoon-text-color));
  padding: var(--padding15);
}

.body:not(.hvn-card-border) :is(.list, .is-auto-horizontal) figure {
  border-radius: var(--border-radius10);
}

EOF;


//******************************************************************************
//  コンテンツ枠
//******************************************************************************
echo <<< EOF
.body.hvn-content-border {
  --content-bgcolor: var(--dark-content-bgcolor, var(--hvn-white-color));
}

.body:not(.hvn-content-border):is(.error404, .page, .single) .main,
.body:not(.hvn-content-border).archive .entry-content,
.body:not(.hvn-content-border) .container aside.widget {
  --main-padding: 5px;
  --content-bgcolor: var(--dark-body-color, var(--body-color));
  --cocoon-text-color: var(--title-color);
  color: var(--cocoon-text-color);
}

.body:not(.hvn-content-border) .footer aside.widget {
  --content-bgcolor: var(--dark-footer-color, {$footer_color});
  --cocoon-text-color: var(--dark-text-color, {$text});
  color: var(--cocoon-text-color);
}

.body:not(.hvn-content-border) .nwa .widget_author_box .author-box {
  --main-padding: var(--padding15);
  border: 1px solid var(--border-color);
  border-radius: 0;
}

EOF;


//******************************************************************************
//  縦型カード3列
//******************************************************************************
echo <<< EOF
@media (width > 834px) {
  [class*=front-page-type-category] .ect-3-columns .a-wrap:nth-of-type(4) {
    display: none;
  }
}

EOF;


//******************************************************************************
//  ローディング画面
//******************************************************************************
if (is_front_top_page() && (get_theme_mod('hvn_front_loading_setting', 'none') != 'none')) {
  if (!is_admin()) {
    echo <<< EOF
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
}


//******************************************************************************
//  拡張タイプ
//******************************************************************************
if (get_theme_mod('hvn_card_expansion_setting')) {

  // カード2列
  $css1 =<<< EOF
.list {
  --column: 2;
}

@media (width <=834px)  {
  .list {
    --column: 1;
  }
}

EOF;

  // 大きなカード（先頭のみ）
  $css2 =<<< EOF
.front-top-page .list .a-wrap:first-child {
  grid-column: 1 / 3;
  grid-row: 1 / 3;
}

@media (width <=834px)  {
  .front-top-page .list .a-wrap:first-child {
    grid-column: unset;
    grid-row: unset;
  }
}

EOF;

  // 縦型カード2、3列+カテゴリーごと（2、3カラム）
  $css3 =<<< EOF
.body .list-new-entries .card-content,
.body .list-popular .card-content {
  padding: 0 0 20px;
}

.body .list-new-entries .like,
.body .list-popular .like {
  display: flex;
}

.list-new-entries .widget-entry-card-date,
.list-popular .widget-entry-card-date {
  bottom: 0;
  display: flex;
  justify-content: flex-end;
  position: absolute;
  width: 100%;
}

.list-new-entries .widget-entry-card-date,
.list-popular .widget-entry-card-date {
  bottom: 0;
  display: flex;
  gap: 5px;
  justify-content: flex-end;
  position: absolute;
  width: 100%;
}

.front-top-page .post-date:before {
  content: '\\f017';
  font-family: 'Font Awesome 5 Free';
  margin-right: 3px;
}

.front-top-page .post-update:before {
  content: '\\f2f1';
  font-family: 'Font Awesome 5 Free';
  font-weight: 900;
  margin-right: 3px;
}


/* 人気記事なし */

.list.widget-entry-cards p {
  color: var(--title-color);
  width:100%;
}

EOF;

  // 大きなカード
  $css4 =<<< EOF
.body.hvn-card-border .list.ect-big-card {
  background-color: var(--dark-content-bgcolor, var(--hvn-white-color));
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
  margin-top: 5px;
}

EOF;

  // カードタイプ
  $card = [
    'entry_card'      => 0,
    'big_card_first'  => 1,
    'big_card'        => 2,
    'vertical_card_2' => 3,
    'vertical_card_3' => 4
  ];

  // フロントページタイプ
  $type = [
    'index'               => 0,
    'tab_index'           => 1,
    'category'            => 2,
    'category_2_columns'  => 3,
    'category_3_columns'  => 4
  ];

  // CSS
  $css_array = [
    [1, 1, 1, 1, 1],
    [0, 0, 2, 2, 2],
    [5, 5, 5, 5, 5],
    [0, 0, 3, 4, 4],
    [0, 0, 0, 4, 4]
  ];

  $css =[ 0, $css1, $css1 . $css2, $css2, $css3, $css4];
  $no = $css_array[$card[get_entry_card_type()]][$type[get_front_page_type()]];

  if ($no) {
    echo $css[$no];
  }
}


//******************************************************************************
//  カテゴリーごと背景色
//******************************************************************************
if (get_theme_mod('hvn_category_color_setting')) {
  if (is_entry_card_type_vertical_card_3()
   || is_front_page_type_category_3_columns()) {
    $color = get_theme_mod('hvn_main_color_setting', HVN_MAIN_COLOR);
    $rgb = hvn_color_mix_rgb($color, 0.25);

    echo <<< EOF
:root {
  --category-color: {$rgb['red']} {$rgb['green']} {$rgb['blue']};
}

.front-top-page.no-sidebar #list-columns {
  --title-color: var(--dark-text-color, #333);
  background-color: rgb(var(--dark-category-color, var(--category-color)) / 100%);
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
      echo <<< EOF
.hvn-wave-category {
  --body-rgb-color: var(--dark-category-color, var(--category-color));
  display: block;
  height: 50px;
  margin: 0 calc(50% - 50vw) calc(var(--gap30) * -1);
  padding: 0 calc(50vw - 50%);
  position: relative;
}

EOF;
    }
  }
}


//******************************************************************************
//  「新着記事」タブ表示
//******************************************************************************
if (!get_theme_mod('hvn_front_none_setting', true)) {
  echo <<< EOF
.body .index-tab-button[for="index-tab-1"],
.body .tab-cont.tb1 {
  display: none;
}

EOF;
}


//******************************************************************************
//  目次スタイル
//******************************************************************************
switch(get_theme_mod('hvn_toc_style_setting')) {
  case 1:
    echo <<< EOF
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
  padding: var(--gap30) var(--main-padding);
}

EOF;

    break;

  case 2:
    echo <<< EOF
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
  echo <<< EOF
.hvn-scroll-toc .sidebar-scroll .toc-content li.current:before {
  color: var(--main-color);
}

.hvn-scroll-toc .sidebar-scroll .toc-content li:before {
  color: var(--hover-color);
}

EOF;
}


//******************************************************************************
//  目次ボタン
//******************************************************************************
if (get_theme_mod('hvn_toc_fix_setting')) {
  echo <<< EOF
.hvn-modal {
  display: none;
  height: 100%;
  left: 0;
  position: fixed;
  top: 0;
  width: 100%;
  z-index: 9999;
}

#hvn-open:checked + .hvn-modal {
  animation:hvn-animation .3s;
  display:block;
}

.hvn-content-wrap {
  background-color: var(--dark-content-bgcolor, var(--hvn-white-color));
  border: 0;
  left: 50%;
  max-height: calc(100vh - 120px);
  overflow-y: auto;
  padding: var(--gap30);
  position: absolute;
  top: 50%;
  transform: translate(-50%, -50%);
  width: 1170px;
  z-index: 2;
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

.hvn-open-btn {
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
  visibility: hidden;
  width: 50px;
  z-index: 999;
}

.hvn-open-btn.active{
  visibility: visible;
}

@keyframes hvn-animation {
  0% {
    opacity: 0;
  }
  100% {
    opacity: 1;
  }
}

@media (width < 1190px) {
  .hvn-content-wrap {
    width: calc(100% - 20px);
  }
}

@media (width <=1023px) {
  .hvn-open-btn {
    bottom: 60px;
    right: 10px;
  }
}

EOF;
}


//******************************************************************************
//  通知エリア固定
//******************************************************************************
if (get_theme_mod('hvn_notice_setting')) {
  echo <<< EOF
.notice-area {
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
  echo <<< EOF
.notice-area-message .swiper .swiper-wrapper {
  transition-timing-function: linear;
}

EOF;
}


//******************************************************************************
//  いいねボタン
//******************************************************************************
if (get_theme_mod('hvn_like_setting')) {
  echo <<< EOF
.list .like {
  bottom: 0;
  display: flex;
  position: absolute;
  z-index: 1;
}


.like {
  font-size: var(--cocoon-text-size-s);
  line-height: 1;
}

.date-tags .like {
  display: flex;
}

.like .button:before {
  color: var(--s-text-color);
  content: '\\f004';
  font-family: 'Font Awesome 5 Free';
  font-weight: 900;
}

.like .button.active:before {
  color: #e589a2;
}

.like .count {
  color: var(--s-text-color);
  font-size: var(--cocoon-text-size-s);
  margin-left: 3px;
}

.single .date-tags {
  justify-content: space-between;
}

.like .button {
  cursor: pointer;
}

EOF;
}


//******************************************************************************
//  評価スター・ランキングハート
//******************************************************************************
if (get_theme_mod('hvn_star_setting')) {
  echo <<< EOF
.rating-star {
  gap: 2px;
}

.rating-star .fa-star:before,
.rating-star .fa-star-half-alt:before,
.rating-star .fa-star-half-alt:after {
  content: "\\f004";
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
  echo <<< EOF
.body {
  --aspect-ratio: var(--card-ratio);
}

.a-wrap figure {
  aspect-ratio: var(--aspect-ratio);
}

EOF;
}


//******************************************************************************
//  横型カードオートプレイ
//******************************************************************************
if (get_theme_mod('hvn_swiper_auto_setting')) {
  echo <<< EOF
.body.hvn .swiper-pagination {
  bottom: 0;
}

.body .swiper-pagination-bullet-active {
  background-color: var(--main-color);
}

.body .content-top aside.widget_navi_entries:has(.swiper) {
  background-color: transparent;
  border-radius: 0;
  padding: 0;
}

.body .content-top aside.widget_navi_entries:has(.swiper) .widget-title {
  display: none;
}

.body .content-top .navi-entry-cards.swiper {
  margin: 0;
}

EOF;
}


//******************************************************************************
//  縦アイキャッチ背景ぼかし
//******************************************************************************
if ($_HVN_EYECATCH) {
  echo <<< EOF
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
  echo <<< EOF
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
  padding-left: calc(1.5em + 5px);
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

.sidebar .widget_tag_cloud .tagcloud a {
  width: 100%;
}

EOF;
}

echo <<< EOF
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
if (wp_get_attachment_url(get_theme_mod('hvn_prof_setting'))) {
  echo <<< EOF
.body .nwa .author-thumb {
  background: var(--prof-image) no-repeat center;
  background-size: cover;
  height: 200px;
  margin: 0 0 50px;
  width: 100%;
}

.body .nwa .author-thumb img {
  margin-top: 150px;
}

EOF;
}


//******************************************************************************
//
//  オプション
//
//******************************************************************************

if (defined('HVN_OPTION') && HVN_OPTION) {

//******************************************************************************
//  並び替え選択
//******************************************************************************
  if (get_theme_mod('hvn_orderby_option_setting')) {
    echo <<< EOF
.orderby {
  align-items: center;
  display: flex;
  gap: 10px;
  justify-content: flex-end;
}

.hvn-dark .orderby .sort-select {
  background-color: transparent;
}

.sort-title {
  color: var(--title-color);
}

.sort-title i {
  margin-right: 3px;
}

EOF;
  }


//******************************************************************************
//  タイトル・説明文表示
//******************************************************************************
  if (get_theme_mod('hvn_tcheck_option_setting')) {
    $n_title= get_theme_mod('hvn_title_new_option_setting' ,'NewPost');
    $n_sub  = get_theme_mod('hvn_title_new_sub_option_setting' ,'新着・更新された記事です');

    $p_title= get_theme_mod('hvn_title_popular_option_setting' ,'Popular');
    $p_sub  = get_theme_mod('hvn_title_popular_sub_option_setting' ,'本日読まれている記事です');

    $c_title= get_theme_mod('hvn_title_category_option_setting','Category');
    $c_sub  = get_theme_mod('hvn_title_category_sub_option_setting' ,'カテゴリーから記事を探す');

    echo <<< EOF
:root {
  --main-font-size: 40px;
  --sub-font-size: 14px;
}

.hvn .list-new-entries-title,
.hvn .list-popular-title {
  display: none;
}

.hvn .list-new-entries:before,
.hvn .list-columns:before {
  color: var(--title-color);
  font-size: var(--main-font-size);
  font-weight: bold;
  position: absolute;
  text-align: center;
  top: 0;
  width: 100%;
  z-index: 1;
}

.hvn .list-new-entries:before {
  content: "{$n_title}";
}

.hvn .list-columns.list-popular:before {
  content: "{$p_title}";
}

.hvn .list-columns:before {
  content: "{$c_title}";
}

.hvn .list-new-entries:after,
.hvn .list-columns:after {
  color: var(--title-color);
  display: block;
  font-size: var(--sub-font-size);
  position: absolute;
  text-align: center;
  width: 100%;
  top: calc(var(--main-font-size) * 1.8);
}

.hvn .list-new-entries:after {
  content: "{$n_sub}";
}

.hvn .list-columns.list-popular:after {
  content: "{$p_sub}";
}

.hvn .list-columns:after {
  content: "{$c_sub}";
}

.hvn .list-new-entries,
.hvn .list-columns {
  padding-top: calc((var(--main-font-size) + var(--sub-font-size)) * 1.8 + var(--gap30));
}

EOF;
  }


//******************************************************************************
//  コメント
//******************************************************************************
  if (get_theme_mod('hvn_comment_setting')) {
    echo <<< EOF
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
}


//******************************************************************************
//
//  メインビジュアル
//
//******************************************************************************

//******************************************************************************
//  波線
//******************************************************************************
if (get_theme_mod('hvn_header_wave_setting')) {
  echo <<< EOF
.waves {
  bottom: -1px;
  height: 50px;
  left: 0;
  position: absolute;
  width: 100%;
  z-index: 1;
}

.parallax > use {
  animation: move-forever 25s cubic-bezier(.55, .5, .45, .5) infinite;
}

.parallax > use:nth-child(1) {
  animation-delay: -2s;
  animation-duration: 7s;
}

.parallax > use:nth-child(2) {
  animation-delay: -3s;
  animation-duration: 10s;
}

.parallax > use:nth-child(3) {
  animation-delay: -4s;
  animation-duration: 13s;
}

.parallax > use:nth-child(4) {
  animation-delay: -5s;
  animation-duration: 20s;
}

@keyframes move-forever {
  0% {
    transform: translate3d(-90px, 0, 0);
  }
  100% {
    transform: translate3d(85px, 0, 0);
  }
}

EOF;
}


//******************************************************************************
//  ヘッダーロゴ非表示
//******************************************************************************
if (!get_theme_mod('hvn_header_option_setting', true)) {
  echo <<< EOF
.front-top-page .header {
  display: none;
}

EOF;
}


//******************************************************************************
//  動画・スライドヘッダー
//******************************************************************************
if (get_theme_mod('hvn_header_setting', 'none') == 'none') return;

echo <<< EOF
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

.scrolldown {
  color: #fff;
  height: fit-content;
  inset: 0;
  letter-spacing: 0.05em;
  margin: auto 0 40px;
  text-align: center;
  position: absolute;
  z-index: 1;
}

.scrolldown span{
  cursor: pointer;
}

EOF;


//******************************************************************************
//  Scrollボタン
//******************************************************************************
switch(get_theme_mod('hvn_header_scroll_setting')) {
  case '1': 
    echo <<< EOF
.scrolldown:after {
  animation: pathmove 1.4s ease-in-out infinite;
  background-color: #fff;
  content: '';
  display: block;
  height: 50px;
  left: 50%;
  opacity: 0;
  position: absolute;
  top: 0;
  width: 1px;
}

@keyframes pathmove {
  0% {
    height: 0;
    opacity: 0;
    top: 25px;
  }
  30% {
    height: 30px;
    opacity: 1;
  }
  100% {
    height: 0;
    opacity: 0;
    top: 65px;
  }
}

EOF;
    break;

  case '2':
    echo <<< EOF
.scrolldown:after {
  animation: pathmove 1.4s ease-in-out infinite alternate;
  content: '\\f078';
  font-family: 'Font Awesome 5 Free';
  font-weight: 900;
  left: 50%;
  position: absolute;
  transform: translateX(-50%);
}

@keyframes pathmove {
  from {
    bottom: -20px;
  }
  to {
    bottom: -50px;
  }
}

EOF;
}


//******************************************************************************
//  フォントサイズ
//******************************************************************************
$font_size = get_theme_mod('hvn_appea_font_size_setting', 40);

echo <<< EOF
.hvn-header .message {
  font-size: {$font_size}px;
}

EOF;


//******************************************************************************
//  テキスト縦書き
//******************************************************************************
if (get_theme_mod('hvn_header_vertival_setting')) {
  echo <<< EOF
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
//  ヘッダーロゴ表示
//******************************************************************************
if (get_theme_mod('hvn_header_logo_setting')) {
  echo <<< EOF
.front-top-page .header {
  display: none;
}

EOF;
}


//******************************************************************************
//  フィルター処理
//******************************************************************************
$url  = 'none';
$no = get_theme_mod('hvn_header_filter_setting');
switch($no) {
  case 1:
  case 2:
  case 3:
    $url = HVN_SKIN_URL . 'assets/img/' . $no . '.gif';
    $url = "url({$url})";
    break;

  case 4:
    echo <<< EOF
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

echo <<< EOF
.hvn-mask {
  background-color: {$color};
  background-image: {$url};
}

EOF;

if (get_theme_mod('hvn_header_setting') != 'image' && hvn_image_count() < 2) return;


//******************************************************************************
//  スライド中のズーム
//******************************************************************************
$zoom = get_theme_mod('hvn_header_animation_setting');
if ($zoom != '0') {
  if ($zoom == '1') {
    $s_zoom = 1;
    $e_zoom = 1.1;
  } else {
    $s_zoom = 1.1;
    $e_zoom = 1;
  }

  echo <<< EOF
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
echo <<< EOF
:root {
  --ani: 2s cubic-bezier(.4, 0, .2, 1) 0s forwards;
}

.hvn-swiper {
  margin: 0;
  padding: 0;
  position: relative;
}

.hvn-swiper img {
  height: var(--height);
  object-fit: cover;
  object-position: top center;
  vertical-align: top;
  width: 100%;
}

.swiper-wrapper .img2 {
  left: 0;
  position: absolute;
  top: 0;
  width: 100%;
}

.hvn-swiper .swiper-slide-active .img1 {
  animation: slide1 var(--ani);
}

.hvn-swiper .swiper-slide-active .img2 {
  animation: slide2 var(--ani);
}

EOF;

switch(get_theme_mod('hvn_header_fade_setting')) {
  // 横方向
  case 'horizontal':
    echo <<< EOF
@keyframes slide1 {
  0% {
    clip-path: polygon(0 100%, 0 100%, 0 0, 0 0);
  }
  100% {
    clip-path: polygon(100% 100%, 0 100%, 0 0, 100% 0);
  }
}

EOF;
    break;

  // 縦方向
  case 'vertical':
    echo <<< EOF
@keyframes slide1 {
  0% {
    clip-path: polygon(0 100%, 100% 100%, 100% 100%, 0 100%);
  }
  100% {
    clip-path: polygon(0 0, 100% 0, 100% 100%, 0 100%);
  }
}

EOF;
    break;

  // 横分割
  case 'h-split':
    echo <<< EOF
@keyframes slide1 {
  0% {
    clip-path: polygon(0 0, 0 0%, 0 50%, 0 50%);
  }
  100% {
    clip-path: polygon(0 0, 100% 0, 100% 50%, 0 50%);
  }
}

@keyframes slide2 {
  0% {
    clip-path: polygon(100% 49%, 100% 49%, 100% 100%, 100% 100%);
  }
  100% {
    clip-path: polygon(100% 49%, 0 49%, 0 100%, 100% 100%);
  }
}

EOF;
    break;

  // 縦分割
  case 'v-split':
    echo <<< EOF
@keyframes slide1 {
  0% {
    clip-path: polygon(0 100%, 50% 100%, 50% 100%, 0 100%);
  }
  100% {
    clip-path: polygon(0 100%, 50% 100%, 50% 0, 0 0);
  }
}

@keyframes slide2 {
  0% {
    clip-path: polygon(49% 0, 100% 0, 100% 0, 49% 0);
  }
  100% {
    clip-path: polygon(49% 100%, 100% 100%, 100% 0, 49% 0);
  }
}

EOF;
    break;
}
