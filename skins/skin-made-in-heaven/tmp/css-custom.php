<?php
if (!defined('ABSPATH')) exit;

global $_HVN_EYECATCH;
global $_HVN_OPTION;


//******************************************************************************
//  ブログカードスニペット非表示
//******************************************************************************
if (!is_entry_card_snippet_visible()) {
  echo <<< EOF
.blogcard-title {
  --title-line: 4;
}

.body .blogcard-snippet {
  display: none;
}

EOF;
}


//******************************************************************************
//  モバイルSNSシェアボタン
//******************************************************************************
$css_arr = [];

// Twitter
if (!is_bottom_twitter_share_button_visible()) {
  $css_arr[] = '.share-menu-content .twitter-button';
}

// Mastodon
if (!is_bottom_mastodon_share_button_visible()) {
  $css_arr[] = '.share-menu-content .mastodon-button';
}

// Bluesky
if (!is_bottom_bluesky_share_button_visible()) {
  $css_arr[] = '.share-menu-content .bluesky-button';
}

// Misskey
if (!is_bottom_misskey_share_button_visible()) {
  $css_arr[] = '.share-menu-content .misskey-button';
}

// Facebook
if (!is_bottom_facebook_share_button_visible()) {
  $css_arr[] = '.share-menu-content .facebook-button';
}

// はてなブックマーク
if (!is_bottom_hatebu_share_button_visible()) {
  $css_arr[] ='.share-menu-content .hatebu-button';
}

// Pocket
if (!is_bottom_pocket_share_button_visible()) {
  $css_arr[] = '.share-menu-content .pocket-button';
}

// LINE@
if (!is_bottom_line_at_share_button_visible()) {
  $css_arr[] ='.share-menu-content .line-button';
}

// Pinterest
if (!is_bottom_pinterest_share_button_visible()) {
  $css_arr[] = '.share-menu-content .pinterest-button';
}

// LinkedIn
if (!is_bottom_linkedin_share_button_visible()) {
  $css_arr[] = '.share-menu-content .linkedin-button';
}

// タイトルとURLをコピー
if (!is_bottom_copy_share_button_visible()) {
  $css_arr[] = '.share-menu-content .copy-button';
}

if (!empty($css_arr)) {
  $css = implode(',', $css_arr) ;
  echo $css . "{display: none;}\n";
  if (count($css_arr) == 8) {
    echo <<< EOF
.share-menu-button {
  display: none;
}

EOF;
  }
}


//******************************************************************************
//  タブ一覧
//******************************************************************************
function hvn_delimiter($i, $max) {
  if ($i == ($max + 1)) {
    echo "{\n";
  } else {
    echo ",\n";
  }
}

$tab_cnt = apply_filters('cocoon_index_max_category_tab_count', 3);

for ($i=1; $i<=$tab_cnt + 1; $i++) {
  echo "#index-tab-{$i}:checked ~ .index-tab-buttons .index-tab-button[for='index-tab-{$i}']";
  hvn_delimiter($i ,$tab_cnt);
}
echo <<< EOF
  background-color: var(--main-color);
  border: 1px solid var(--main-color);
  color: var(--text-color);
}

EOF;

for ($i=1; $i<=$tab_cnt + 1; $i++) {
  echo "#index-tab-{$i}:checked ~ .index-tab-buttons .index-tab-button[for='index-tab-{$i}']:before";
  hvn_delimiter($i, $tab_cnt);
}
echo <<< EOF
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
  echo "#index-tab-{$i}:checked ~ .tab-cont.tb{$i}";
  hvn_delimiter($i, $tab_cnt);
}
echo <<< EOF
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
  background-color: var(--white-bgcolor);
  border: 1px solid var(--main-color);
  border-radius: 0;
  box-shadow: var(--shadow-color);
  display: block;
  color: var(--cocoon-text-color);
  font-size: var(--cocoon-text-size-s);
  font-weight: unset!important;
  height: 40px;
  line-height: 40px;
  margin: 0;
  padding: 0;
  position: relative;
  text-align: center;
  width: 100%;
}

.body .index-tab-button:hover {
  background-color: var(--hover-color);
}

@media (width <=834px) {
  .body .index-tab-button {
    max-width: unset;
    width: calc((100% - var(--padding15)) / 2)!important;
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
//  bodyカラー変更
//******************************************************************************

if (!get_theme_mod('hvn_border_setting', true)) {
  echo <<< EOF
.body .list .e-card {
  background-color: transparent;
  border-radius: 0;
  padding: 0!important;
  overflow: unset;
}

EOF;
  if (!get_theme_mod('hvn_border_radius_setting')) {
    echo <<< EOF
.body .list .e-card .card-thumb {
  border-radius: 10px;
  overflow: hidden;
}

EOF;
  }
}


//******************************************************************************
//  footerカラー変更
//******************************************************************************
$color = get_footer_background_color();
if ($color == null || $color == "#ffffff") {
  $color = 'ffffff';
}

if (is_dark_hexcolor($color)) {
  $color = '#fff';
} else {
  $color = '#333';
}

echo <<< EOF
.footer-bottom,
.footer-bottom a,
.footer-bottom a:hover {
  border-color: {$color};
  color: {$color};
}

.navi-footer-in > .menu-footer li,
.navi-footer-in > .menu-footer li:last-child,
.footer .footer-in .footer-bottom-content {
  border-color: {$color};
}

EOF;


//******************************************************************************
//  背景カラー(モバイルフッターボタン)変更
//******************************************************************************
// 背景カラー
$color = get_header_background_color();
if ($color) {
  echo <<< EOF
.mobile-menu-buttons {
  background-color: {$color};
}

EOF;
}


// テキストカラー
$color = get_header_text_color();
if ($color) {
  echo <<< EOF
.mobile-menu-buttons .menu-button > a,
.mobile-menu-buttons .menu-caption,
.mobile-menu-buttons .menu-icon{
  color: {$color};
}

EOF;
}


//******************************************************************************
//
//  拡張
//
//******************************************************************************

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
body {
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
}


//******************************************************************************
//  オリジナルレイアウト
//******************************************************************************
if ((is_front_page_type_category_2_columns() || is_front_page_type_category_3_columns())
 && (is_entry_card_type_vertical_card_2() || is_entry_card_type_vertical_card_3())) {
 echo <<< EOF
.body .list-new-entries .card-content,
.body .list-popular .card-content {
  padding: 0 0 var(--gap30)!important;
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
  content: '\\f1da';
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
}


//******************************************************************************
//  カテゴリーごと背景色
//******************************************************************************
if ((get_theme_mod('entry_card_type') == 'vertical_card_3'
 || get_theme_mod('front_page_type') == 'category_3_columns')
  && get_theme_mod('hvn_category_color_setting')) {
  $color = get_theme_mod('hvn_main_color_setting', HVN_MAIN_COLOR);
  $rgb = hvn_color_mix_rgb($color, 0.25);

  echo <<< EOF
:root {
  --category-color: {$rgb['red']}, {$rgb['green']}, {$rgb['blue']};
}

.front-top-page.no-sidebar #list-columns {
  background-color: rgb(var(--category-color), 1);
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
//  フロントページから新着記事を除外
//******************************************************************************
if (get_theme_mod('hvn_front_none_setting')) {
  echo <<< EOF
.body .index-tab-button[for="index-tab-1"],
.body .tab-cont.tb1,
.body .list-new-entries {
  display: none;
}

EOF;
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
//  横型カードオートプレイ
//******************************************************************************
if (get_theme_mod('hvn_swiper_auto_setting')) {
  echo <<< EOF
.body .swiper-pagination {
  bottom: 0!important;
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

.body .content-top .navi-entry-cards.swiper.border-square .e-card,
.body .content-top .navi-entry-cards.swiper.card-large-image .e-card {
  border: 0;
  border-radius: var(--border-radius10);
}

EOF;

  if (!get_theme_mod('hvn_border_setting', true)) {
    echo <<< EOF
.body .content-top .navi-entry-cards.swiper .e-card {
  background-color: transparent;
  border-radius: 0;
  padding: 0;
}

EOF;

    if (!get_theme_mod('hvn_border_radius_setting')) {
      echo <<< EOF
.body .content-top .navi-entry-cards.swiper .e-card .card-thumb {
  border-radius: 10px;
}

EOF;
    }
  }
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
  text-align: center;
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
  display:block;
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

@media (width <=1023px) {
  .front-top-page {
    margin-top:0;
  }

  .front-top-page .mobile-header-menu-buttons {
    display:none;
  }
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


// 文字サイズ
$font_size = get_theme_mod('hvn_appea_font_size_setting', 40);

echo <<< EOF
.hvn-mask {
  background-color: {$color};
  background-image: {$url};
}

.hvn-header .message {
  font-size: {$font_size}px;
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
    transform: scale(var(--s-zoom));
  }
  100% {
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
