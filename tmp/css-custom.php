<?php //CSS設定用
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit; ?>

<?php //サイトキー色
if ($site_key_color = get_site_key_color()): ?>
#header-container,
#header-container .navi,
#navi .navi-in > .menu-header .sub-menu,
.article h2,
.sidebar h2,
.sidebar h3,
.cat-link,
.cat-label,
.appeal-content .appeal-button,
.demo .cat-label,
.blogcard-type .blogcard-label,
#footer{
  background-color: <?php echo $site_key_color; ?>;
}
#navi .navi-in a:hover,
#footer a:not(.sns-button):hover{
  background-color: rgba(255, 255, 255, 0.2);
}
.article h3,
.article h4,
.article h5,
.article h6,
.cat-link,
.tag-link{
  border-color: <?php echo $site_key_color; ?>;
}
blockquote::before, blockquote::after,
.pager-post-navi a.a-wrap::before {
  color: <?php echo colorcode_to_rgb_css_code($site_key_color, 0.5); ?>;
}
blockquote,
.key-btn {
  background-color: <?php echo colorcode_to_rgb_css_code($site_key_color, 0.05); ?>;
  border-color: <?php echo colorcode_to_rgb_css_code($site_key_color, 0.5); ?>;
}
pre,
.pager-links span,
.scrollable-table table th,
table th,
.pagination .current {
  background-color: <?php echo colorcode_to_rgb_css_code($site_key_color, 0.1); ?>;
  border-color: <?php echo colorcode_to_rgb_css_code($site_key_color, 0.5); ?>;
}
table:not(.has-border-color) th,
table:not(.has-border-color) td,
table:not(.has-border-color) thead,
table:not(.has-border-color) tfoot,
.page-numbers,
.page-numbers.dots,
.tagcloud a,
.list.ecb-entry-border .entry-card-wrap,
.related-entries.recb-entry-border .related-entry-card-wrap,
.carousel .a-wrap,
.pager-post-navi.post-navi-border a.a-wrap,
.article .toc,
.a-wrap .blogcard,
.author-box,
.comment-reply-link,
.ranking-item{
  border-color: <?php echo colorcode_to_rgb_css_code($site_key_color, 0.5); ?>
}

table tr:nth-of-type(2n+1),
.page-numbers.dots,
.a-wrap:hover,
.pagination a:hover,
.pagination-next-link:hover,
.widget_recent_entries ul li a:hover,
.widget_categories ul li a:hover,
.widget_archive ul li a:hover,
.widget_pages ul li a:hover,
.widget_meta ul li a:hover,
.widget_rss ul li a:hover,
.widget_nav_menu ul li a:hover,
.pager-links a:hover span,
/*.cat-link:hover,*/
.tag-link:hover,
.tagcloud a:hover{
  background-color: <?php echo colorcode_to_rgb_css_code($site_key_color, 0.05); ?>;
}

.header,
.header .site-name-text,
#navi .navi-in a,
#navi .navi-in a:hover,
.article h2,
.sidebar h2,
.sidebar h3,
#footer,
#footer a:not(.sns-button){
  color: #fff;
}

<?php endif ?>
<?php //サイトキー文字色
if ($site_key_text_color = get_site_key_text_color()): ?>
.header,
.header .site-name-text,
#navi .navi-in a,
#navi .navi-in a:hover,
.appeal-content .appeal-button,
.article h2,
.sidebar h2,
.sidebar h3,
.cat-link,
.cat-label,
.blogcard-type .blogcard::before,
#footer,
#footer a:not(.sns-button){
  color: <?php echo $site_key_text_color; ?>;
}
<?php endif ?>
<?php //サイト文字色
if($site_text_color = get_site_text_color()): ?>
body.public-page{
  --cocoon-text-color: <?php echo $site_text_color; ?>;
  color: var(--cocoon-text-color);
}
<?php endif; ?>
<?php //サイト背景色
if (get_site_background_color()): ?>
body.public-page{
  background-color: <?php echo get_site_background_color(); ?>;
}
<?php endif ?>
<?php //サイトリンク色
if (get_site_link_color()): ?>
a{
  color: <?php echo get_site_link_color(); ?>;
}
<?php endif ?>
<?php //サイト背景画像
if (get_site_background_image_url()): ?>
body.public-page{
  background-image: url(<?php echo get_site_background_image_url(); ?>);
}
<?php endif ?>
<?php //ヘッダーの高さ
if (is_header_layout_type_center_logo() && get_header_area_height()): ?>
.header .header-in{
  min-height: <?php echo get_header_area_height(); ?>px;
}
<?php endif ?>
<?php //ヘッダー背景画像
if (get_header_background_image_url()): ?>
.header{
  background-image: url(<?php echo get_header_background_image_url(); ?>);
}
<?php endif ?>
<?php //ヘッダー全体背景色
if ($header_container_background_color = get_header_container_background_color()): ?>
#header-container,
#header-container .navi,
#navi .navi-in > .menu-header .sub-menu{
  background-color: <?php echo $header_container_background_color; ?>;
}

.header,
.header .site-name-text,
#navi .navi-in a,
#navi .navi-in a:hover{
  color: #fff;
}

<?php endif ?>
<?php //ヘッダー全体文字色
if ($header_container_text_color = get_header_container_text_color()): ?>
.header,
.header .site-name-text,
#navi .navi-in a,
#navi .navi-in a:hover{
  color: <?php echo $header_container_text_color; ?>;
}
<?php endif ?>
<?php //ヘッダー背景色
if ($header_background_color = get_header_background_color()): ?>
.header{
  background-color: <?php echo $header_background_color; ?>;
}

.header,
.header .site-name-text{
  color: #fff;
}

<?php endif ?>
<?php //ヘッダー文字色
if ($header_text_color = get_header_text_color()): ?>
.header,
.header .site-name-text{
  color: <?php echo $header_text_color; ?>;
}
<?php endif ?>
<?php //グローバルナビ背景色
if ($global_navi_background_color = get_global_navi_background_color()): ?>
#header-container .navi,
#navi .navi-in > .menu-header .sub-menu{
  background-color: <?php echo $global_navi_background_color; ?>;
}

#navi .navi-in a,
#navi .navi-in a:hover{
  color: #fff;
}

<?php endif ?>
<?php //グローバルナビ文字色
if ($global_navi_text_color = get_global_navi_text_color()): ?>
#navi .navi-in a,
#navi .navi-in a:hover{
  color: <?php echo $global_navi_text_color; ?>;
}
<?php endif ?>
<?php //グローバルナビホバー背景色
if (get_header_container_background_color() || get_header_background_color() || get_global_navi_background_color()): ?>
#navi .navi-in a:hover{
  background-color: rgba(255, 255, 255, 0.2);
}
<?php endif ?>
<?php //グローバルナビメニュー幅
if (get_global_navi_menu_width()): ?>
#navi .navi-in > ul > li{
  width: <?php echo get_global_navi_menu_width(); ?>px;
}
<?php endif ?>
<?php //グローバルナビサブメニュー幅
if (get_global_navi_sub_menu_width()): ?>
#navi .navi-in > ul .sub-menu{
  width: <?php echo get_global_navi_sub_menu_width(); ?>px;
}
#navi .navi-in > ul .sub-menu ul{
  left: <?php echo get_global_navi_sub_menu_width(); ?>px;
}
<?php endif ?>
<?php //トップへ戻るボタンは背景色
if (get_go_to_top_background_color()): ?>
.go-to-top .go-to-top-button{
  background-color: <?php echo get_go_to_top_background_color(); ?>;
}
<?php endif ?>
<?php //トップへ戻るボタン文字色
if (get_go_to_top_text_color()): ?>
.go-to-top .go-to-top-button{
  color: <?php echo get_go_to_top_text_color(); ?>;
}
<?php endif ?>
<?php //アピールエリア画像
if (get_appeal_area_image_url()): ?>
.appeal{
  background-image: url(<?php echo get_appeal_area_image_url(); ?>);
}
<?php endif ?>
<?php //アピールエリアの高さ
if (get_appeal_area_height()): ?>
.appeal-in{
  min-height: <?php echo get_appeal_area_height(); ?>px;
}
<?php endif ?>
<?php //アピールボタンの背景色
if (get_appeal_area_button_background_color()): ?>
.appeal-content .appeal-button{
  background-color: <?php echo get_appeal_area_button_background_color(); ?>;
}
<?php endif ?>
<?php
///////////////////////////////////////
// カテゴリー色の設定
///////////////////////////////////////
// すべての投稿タイプの階層型タクソノミーを取得
$taxonomies = get_taxonomies(array('hierarchical' => true), 'objects');

$colors = array();
$text_colors = array();

foreach ($taxonomies as $taxonomy) {
  // タクソノミーのタームを取得
  $terms = get_terms(array(
    'taxonomy'   => $taxonomy->name,
    'hide_empty' => false, // 投稿がなくても取得する
  ));

  foreach ($terms as $term) {
    $color = get_the_category_color($term->term_id); // カラー取得関数（カスタム関数）
    $text_color = get_the_category_text_color($term->term_id); // 文字色取得関数（カスタム関数）

    $cat_label_pre = '.cat-label.cat-label-';
    $cat_link_pre = '.cat-link.cat-link-';

    if ($color) {
      $selectors = $cat_label_pre . $term->term_id . ', ' . $cat_link_pre . $term->term_id;
      if (isset($colors[$color])) {
        array_push($colors[$color], $selectors);
      } else {
        $colors[$color] = array($selectors);
      }
    }

    if ($text_color) {
      $selectors = $cat_label_pre . $term->term_id . ', ' . $cat_link_pre . $term->term_id;
      if (isset($text_colors[$text_color])) {
        array_push($text_colors[$text_color], $selectors);
      } else {
        $text_colors[$text_color] = array($selectors);
      }
    }
  }
}

// CSS の生成
$css = '';

// 背景色の設定
foreach ($colors as $color_code => $ids) {
  $selector = implode(', ', $ids);
  $css .= $selector . '{' . PHP_EOL .
    '  background-color: ' . $color_code . ';' . PHP_EOL .
    '  color: #fff;' . PHP_EOL .
  '}' . PHP_EOL . PHP_EOL;
}

// 文字色の設定
foreach ($text_colors as $color_code => $ids) {
  $selector = implode(', ', $ids);
  $css .= $selector . '{' . PHP_EOL .
    '  color: ' . $color_code . ';' . PHP_EOL .
  '}' . PHP_EOL . PHP_EOL;
}

echo $css;
 ?>
<?php
///////////////////////////////////////
// メインカラムの設定
///////////////////////////////////////?>
<?php //カラム幅
if (get_main_column_width()): ?>
.main{
  width: <?php echo get_main_column_width(); ?>px;
}
<?php endif ?>
<?php //パディング
if (get_main_column_padding()): ?>
.main{
  padding-left: <?php echo get_main_column_padding(); ?>px;
  padding-right: <?php echo get_main_column_padding(); ?>px;
}
<?php endif ?>
<?php //枠線の幅
$main_column_border_width = get_main_column_border_width();
if (is_numeric($main_column_border_width)): ?>
.main{
  border-width: <?php echo $main_column_border_width; ?>px;
}
<?php endif ?>
<?php //枠線の色
if (get_main_column_border_color()): ?>
.main{
  border-color: <?php echo get_main_column_border_color(); ?>;
}
<?php endif ?>
<?php
///////////////////////////////////////
// サイドバーの設定
///////////////////////////////////////?>
<?php //カラム幅
if (get_sidebar_width()): ?>
.sidebar{
  width: <?php echo get_sidebar_width(); ?>px;
}
<?php endif ?>
<?php //パディング
if (get_sidebar_padding()): ?>
.sidebar{
  padding-left: <?php echo get_sidebar_padding(); ?>px;
  padding-right: <?php echo get_sidebar_padding(); ?>px;
}
<?php endif ?>
<?php //枠線の幅
$sidebar_border_width = get_sidebar_border_width();
if (is_numeric($sidebar_border_width)): ?>
.sidebar{
  border-width: <?php echo $sidebar_border_width; ?>px;
}
<?php endif ?>
<?php //枠線の色
if (get_sidebar_border_color()): ?>
.sidebar{
  border-color: <?php echo get_sidebar_border_color(); ?>;
}
<?php endif ?>
<?php //カラム間の幅
$main_sidebar_margin = get_main_sidebar_margin();
if (false && is_numeric($main_sidebar_margin)): ?>
.main{
  <?php if (is_sidebar_position_right()): ?>
  margin-right: <?php echo $main_sidebar_margin; ?>px;
  margin-left: 0;
  <?php else: ?>
  margin-left: <?php echo $main_sidebar_margin; ?>px;
  margin-right: 0;
  <?php endif; ?>
}
<?php endif ?>
<?php //サイト幅
if (is_clumns_changed() && !is_admin()): ?>
.wrap{
  width: <?php echo get_site_wrap_width(); ?>px;
}
<?php endif ?>
<?php //レスポンシブ
$responsive_width = intval(get_site_wrap_width()) - 1;
//次のブレークポイント（1023px）より幅が狭い場合はブレークポイントの値にする
if ($responsive_width <= 1023) {
  $responsive_width = 1023;
} ?>
<?php if (!is_admin()): ?>
@media screen and (max-width: <?php echo $responsive_width; ?>px){
  <?php //echo wp_filesystem_get_contents(get_template_directory().'/scss/breakpoints/_max-width-1240.scss'); ?>
  <?php require_once(get_template_directory().'/scss/breakpoints/_max-width-1240.scss'); ?>
}
<?php endif ?>
<?php //通知エリア背景色
if ($color = get_notice_area_background_color()): ?>
#notice-area{
  background-color: <?php echo $color; ?>;
}
<?php endif ?>
<?php //通知エリア文字色
if ($color = get_notice_area_text_color()): ?>
#notice-area{
  color: <?php echo $color; ?>;
}
<?php endif ?>
<?php //アクセスカウント取得用スタイル
if (!is_admin() && is_singular() && is_access_count_enable()): ?>
body::after{
  content: url("<?php echo get_template_directory_uri(); ?>/lib/analytics/access.php?post_id=<?php echo get_the_ID(); ?>&post_type=<?php echo get_accesses_post_type(); ?>") !important;
  visibility: hidden;
  position: absolute;
  bottom: 0;
  right: 0;
  width: 1px;
  height: 1px;
  overflow: hidden;
  display: inline !important;
}
<?php endif ?>
<?php //アピールエリア背景色
if ($color = get_appeal_area_background_color()): ?>
.appeal{
  background-color: <?php echo $color; ?>;
}
<?php endif ?>
<?php //フッターメニュー幅
if ($width = get_footer_navi_menu_width()): ?>
.navi-footer-in > .menu-footer li{
  width: <?php echo $width; ?>px;
}
<?php endif ?>
<?php //ページ設定で1カラムかつ狭い場合
if (is_singular_page_type_narrow()):
$main_column_width = get_main_column_width();
 ?>
.column-narrow .content-in{
  width: <?php echo $main_column_width; ?>px;
}
@media screen and (max-width: <?php echo $main_column_width; ?>px){
  .column-narrow .content-in{
    width: auto;
  }
}
<?php endif ?>
<?php //ページ設定でフルワイドの場合
if (is_singular_page_type_full_wide()):
 ?>
.column-full-wide .content-in{
  width: 100%;
}
<?php endif ?>
<?php //選択文字色
if ($selection_color = get_site_selection_color()): ?>
*::selection {
  color: <?php echo $selection_color; ?>;
}
*::-moz-selection {
  color: <?php echo $selection_color; ?>;
}
<?php endif ?>
<?php //選択背景色
if ($selection_background_color = get_site_selection_background_color()): ?>
*::selection {
  background: <?php echo $selection_background_color; ?>;
}
*::-moz-selection {
  background: <?php echo $selection_background_color; ?>;
}
<?php endif ?>
<?php //ヘッダーロゴ高さ
if ($site_logo_height = get_the_site_logo_height()): ?>
.logo-header img{
  height: <?php echo $site_logo_height; ?>px;
  width: auto;
}
<?php endif ?>
<?php //ヘッダーロゴ幅
if ($site_logo_width = get_the_site_logo_width()): ?>
.logo-header img{
  width: <?php echo $site_logo_width; ?>px;
  height: auto;
}
<?php endif ?>
<?php //モバイルのヘッダーの高さ
if ($mhah = get_mobile_header_area_height()): ?>
@media screen and (max-width: 834px){
  .header div.header-in{
    min-height: <?php echo $mhah; ?>px;
  }
}
<?php endif ?>
<?php //目次切り換えが有効な時
if (is_toc_toggle_switch_enable()): ?>
/* .toc-content, */
.toc-checkbox {
  display: none;
}
.toc-content{
  visibility: hidden;
  /*display: block; */
  /* margin-top: -24px; */
  height: 0;
  opacity: 0.2;
  transition: all 0.5s ease-out;
}
.toc-checkbox:checked ~ .toc-content {
  /* display: block; */
  visibility: visible;
  padding-top: 0.6em;
  height: 100%;
  opacity: 1;
}
<?php
$open_caption = get_toc_open_caption();
if ($open_caption === '') {
  $open_caption = __('開く', THEME_NAME);
}
; ?>
.toc-title::after{
  content: '[<?php echo $open_caption; ?>]';
  margin-left: 0.5em;
  cursor: pointer;
  font-size: 0.8em;
}
.toc-title:hover::after{
  text-decoration: underline;
}

<?php
$close_caption = get_toc_close_caption();
if ($close_caption === '') {
  $close_caption = __('閉じる', THEME_NAME);
}
; ?>
.toc-checkbox:checked + .toc-title::after{
  content: '[<?php echo $close_caption; ?>]';
}<?php endif ?>
<?php //アイキャッチを中央表示
if (is_eyecatch_center_enable()): ?>
.eye-catch-wrap{
  justify-content: center;
}
<?php endif ?>
<?php //アイキャッチをカラム幅にする
if (is_eyecatch_width_100_percent_enable()): ?>
.eye-catch {
  width: 100%;
}
.eye-catch img,
.eye-catch amp-img{
  width: 100%;
  min-width: 100%;
  display: block;
}
<?php endif ?>
<?php //AMPページ用のアイキャッチスタイル
if(false && is_amp()):
$main_column_contents_width = get_main_column_contents_width();
if (!$main_column_contents_width) {
  $main_column_contents_width = 800;
} ?>
.eye-catch amp-img{
  display: block;
  max-width: <?php echo $main_column_contents_width; ?>px;
}
@media screen and (max-width: <?php echo $responsive_width; ?>px){
  .eye-catch {
    display: block;
  }
  .eye-catch amp-img{
    min-width: 100%;
    max-width: 100%;
  }
}
<?php endif; ?>
<?php //コメント入力欄を表示ボタンで切り替えるとき
if (is_comment_form_display_type_toggle_button()): ?>
#respond {
  inset: 0;
  position: absolute;
  visibility: hidden;
}
<?php endif ?>
<?php //グローバルメニュー幅をテキストの幅にする
if (is_global_navi_menu_text_width_enable()): ?>
#navi .navi-in > ul > li{
  width: auto;
}
#navi .navi-in > ul > li > a{
  padding: 0 1.4em;
}
<?php endif ?>
<?php //フッター背景色
if ($footer_background_color = get_footer_background_color()): ?>
#footer {
  background: <?php echo $footer_background_color; ?>;
}
<?php endif ?>
<?php //フッター文字色
if ($footer_text_color = get_footer_text_color()): ?>
#footer,
#footer a:not(.sns-button),
.footer-title {
  color: <?php echo $footer_text_color; ?>;
}
<?php endif ?>
<?php //行の高さ
if ($entry_content_line_hight = get_entry_content_line_hight()): ?>
.entry-content > *,
.demo .entry-content p {
  line-height: <?php echo $entry_content_line_hight; ?>;
}
  <?php //管理画面用
  if(is_admin() && is_gutenberg_editor_enable()): ?>
  .main,
  .main p,
  .main p.wp-block-paragraph {
    line-height: <?php echo $entry_content_line_hight; ?>;
  }
  <?php endif; ?>
<?php endif ?>
<?php //行の余白
// $entry_content_line_hight = get_entry_content_line_hight()
// if (!$entry_content_line_hight) {
//   $entry_content_line_hight = OP_ENTRY_CONTENT_LINE_HIGHT_DEFAULT;
// }
$entry_content_margin_hight = get_entry_content_margin_hight();
if (!$entry_content_margin_hight) {
  $entry_content_margin_hight = OP_ENTRY_CONTENT_MARGIN_HIGHT_DEFAULT;
} ?>
.entry-content > *,
.mce-content-body > *,
.article p,
.demo .entry-content p,
.article dl,
.article ul,
.article ol,
.article blockquote,
.article pre,
.article table,
.article .toc,
.body .article,
.body .column-wrap,
.body .new-entry-cards,
.body .popular-entry-cards,
.body .navi-entry-cards,
.body .box-menus,
.body .ranking-item,
.body .rss-entry-cards,
.body .widget,
.body .author-box,
.body .blogcard-wrap,
.body .login-user-only,
.body .information-box,
.body .question-box,
.body .alert-box,
.body .information,
.body .question,
.body .alert,
.body .memo-box,
.body .comment-box,
.body .common-icon-box,
.body .blank-box,
.body .button-block,
.body .micro-bottom,
.body .caption-box,
.body .tab-caption-box,
.body .label-box,
.body .toggle-wrap,
.body .wp-block-image,
.body .booklink-box,
.body .kaerebalink-box,
.body .tomarebalink-box,
.body .product-item-box,
.body .speech-wrap,
.body .wp-block-categories,
.body .wp-block-archives,
.body .wp-block-archives-dropdown,
.body .wp-block-calendar,
.body .ad-area,
.body .wp-block-gallery,
.body .wp-block-audio,
.body .wp-block-cover,
.body .wp-block-file,
.body .wp-block-media-text,
.body .wp-block-video,
.body .wp-block-buttons,
.body .wp-block-columns,
.body .wp-block-separator,
.body .components-placeholder,
.body .wp-block-search,
.body .wp-block-social-links,
.body .timeline-box,
.body .blogcard-type,
.body .btn-wrap,
.body .btn-wrap a,
.body .block-box,
.body .wp-block-embed,
.body .wp-block-group,
.body .wp-block-table,
.body .scrollable-table,
.body .wp-block-separator,
.body .wp-block,
.body .video-container,
.comment-area,
.related-entries,
.pager-post-navi,
.comment-respond {
  margin-bottom: <?php echo $entry_content_margin_hight; ?>em;
}

.is-root-container > * {
  margin-bottom: <?php echo $entry_content_margin_hight; ?>em !important;
}
.article h2,
.article h3,
.article h4,
.article h5,
.article h6{
  margin-bottom: <?php echo round($entry_content_margin_hight * 0.9, 2); ?>em;
}
<?php //モバイルサイトフォント
$mobile_site_font_size = get_mobile_site_font_size();
if ($mobile_site_font_size): ?>
@media screen and (max-width: 480px){
  .body,
  .menu-content{
    font-size: <?php echo $mobile_site_font_size; ?>;
  }
}
<?php endif ?>
@media screen and (max-width:781px) {
  .wp-block-column{
    margin-bottom: <?php echo $entry_content_margin_hight; ?>em;
  }
}
@media screen and (max-width:834px) {
  .container .column-wrap{
    gap: <?php echo $entry_content_margin_hight; ?>em;
  }
}
.article .micro-top{
  margin-bottom: <?php echo $entry_content_margin_hight * 0.2; ?>em;
}
.article .micro-bottom{
  margin-top: -<?php echo $entry_content_margin_hight * 0.9; ?>em;
}

.article .micro-balloon{
  margin-bottom: <?php echo $entry_content_margin_hight * 0.5; ?>em;
}
.article .micro-bottom.micro-balloon{
  margin-top: -<?php echo $entry_content_margin_hight * 0.7; ?>em;
}

.blank-box.bb-key-color{
  border-color: <?php echo get_editor_key_color(); ?>;
}
.iic-key-color li::before{
  color: <?php echo get_editor_key_color(); ?>;
}
.blank-box.bb-tab.bb-key-color::before {
  background-color: <?php echo get_editor_key_color(); ?>;
}
.tb-key-color .toggle-button {
  border: 1px solid <?php echo get_editor_key_color(); ?>;
  background: <?php echo get_editor_key_color(); ?>;
  color: #fff;
}
.tb-key-color .toggle-button::before {
  color: #ccc;
}
.tb-key-color .toggle-checkbox:checked ~ .toggle-content {
  border-color: <?php echo get_editor_key_color(); ?>;
}
.cb-key-color.caption-box {
  border-color: <?php echo get_editor_key_color(); ?>;
}
.cb-key-color .caption-box-label {
  background-color: <?php echo get_editor_key_color(); ?>;
  color: #fff;
}
.tcb-key-color .tab-caption-box-label {
  background-color: <?php echo get_editor_key_color(); ?>;
  color: #fff;
}
.tcb-key-color .tab-caption-box-content {
  border-color: <?php echo get_editor_key_color(); ?>;
}
.lb-key-color .label-box-content {
  border-color: <?php echo get_editor_key_color(); ?>;
}
.mc-key-color {
  background-color: <?php echo get_editor_key_color(); ?>;
  color: #fff;
  border: none;
}
.mc-key-color.micro-bottom::after {
  border-bottom-color: <?php echo get_editor_key_color(); ?>;
  border-top-color: transparent;
}
.mc-key-color::before {
  border-top-color: transparent;
  border-bottom-color: transparent;
}
.mc-key-color::after {
  border-top-color: <?php echo get_editor_key_color(); ?>;
}
.btn-key-color, .btn-wrap.btn-wrap-key-color > a {
  background-color: <?php echo get_editor_key_color(); ?>;
}
.has-text-color.has-key-color-color {
  color: <?php echo get_editor_key_color(); ?>;
}
.has-background.has-key-color-background-color {
  background-color: <?php echo get_editor_key_color(); ?>;
}
<?php //エディター背景色
$editor_background_color = get_editor_background_color();
if (!$editor_background_color) {
  $editor_background_color = '#fff';
} ?>
.body.article,
body#tinymce.wp-editor{
  background-color: <?php echo $editor_background_color; ?>
}
<?php //エディター文字色
$editor_text_color = get_editor_text_color();
if (!$editor_text_color) {
  $editor_text_color = '#333';
} ?>
.body.article,
.editor-post-title__block .editor-post-title__input,
body#tinymce.wp-editor{
  color: <?php echo $editor_text_color; ?>
}
<?php //Internet Explorer（IE）用
global $is_IE;
if ($is_IE): ?>
.wp-block-image figure.aligncenter {
  display: block;
  text-align: center;
}
.wp-block-image figure.aligncenter figcaption{
  display: block;
}
  <?php if(is_singular() && is_eyecatch_visible() && has_post_thumbnail()): ?>
  .eye-catch-wrap {
    text-align: center;
  }
  .eye-catch{
    display: inline-block;
  }
  <?php endif; ?>
<?php endif; ?>
<?php //デフォルトブロックエディター色の色指定
if (!is_admin()) {
  echo get_block_editor_color_palette_css();
}
?>
<?php if (is_code_row_number_enable()):
$max_code_row_count = 99;
$max_code_row_count = apply_filters('max_code_row_count', $max_code_row_count);
$rows = array();
for ($i=1; $i <= $max_code_row_count; $i++) {
  $rows[] = $i.'\A';
}
?>
.is-code-row-number-enable pre.hljs::before {
  content: "<?php echo implode(' ', $rows); ?>";
}
<?php endif; ?>
<?php //ボックスメニュースタイル
$color = get_site_key_color() ? get_site_key_color() : '#f6a068';
 ?>
.box-menu:hover{
  box-shadow: inset 2px 2px 0 0 <?php echo $color; ?>, 2px 2px 0 0 <?php echo $color; ?>, 2px 0 0 0 <?php echo $color; ?>, 0 2px 0 0
<?php echo $color; ?>;
}
.box-menu-icon{
  color: <?php echo $color; ?>;
}
@font-face {
  font-family: 'icomoon';
  src: url(<?php echo FONT_ICOMOON_WOFF_URL; ?>) format('woff');
  font-weight: normal;
  font-style: normal;
  font-display: swap;
}
<?php //囲みブログカードスタイル（エディター画面）
global $locale;
//日本語でない時
if (is_admin() && isset($locale) && !preg_match('/^ja/', $locale)): ?>
.blogcard-type.bct-none::before {
  content: "  <?php _e('ラベルなし', THEME_NAME); ?>";
}

.blogcard-type.bct-related::before {
  content: "  <?php _e('関連記事', THEME_NAME); ?>";
}

.blogcard-type.bct-reference::before {
  content: "  <?php _e('参考記事', THEME_NAME); ?>";
}

.blogcard-type.bct-reference-link::before {
  content: "  <?php _e('参考リンク', THEME_NAME); ?>";
}

.blogcard-type.bct-popular::before {
  content: "  <?php _e('人気記事', THEME_NAME); ?>";
}

.blogcard-type.bct-pickup::before {
  content: "  <?php _e('ピックアップ', THEME_NAME); ?>";
}

.blogcard-type.bct-check::before {
  content: "  <?php _e('チェック', THEME_NAME); ?>";
}

.blogcard-type.bct-together::before {
  content: "  <?php _e('あわせて読みたい', THEME_NAME); ?>";
}

.blogcard-type.bct-detail::before {
  content: "  <?php _e('詳細はこちら', THEME_NAME); ?>";
}

.blogcard-type.bct-official::before {
  content: "  <?php _e('公式サイト', THEME_NAME); ?>";
}

.blogcard-type.bct-dl::before {
  content: "  <?php _e('ダウンロード', THEME_NAME); ?>";
}

.blogcard-type.bct-prev::before {
  content: "  <?php _e('前回の記事', THEME_NAME); ?>";
}

.blogcard-type.bct-next::before {
  content: "  <?php _e('続きの記事', THEME_NAME); ?>";
}
<?php endif ?>
<?php //囲みブログカードスタイル（公開画面）
//日本語でない時
if (!is_admin() && isset($locale) && !preg_match('/^ja/', $locale)): ?>

.bct-related .blogcard-label::after {
  content: "<?php _e('関連記事', THEME_NAME); ?>";
}
.bct-reference .blogcard-label::after {
  content: "<?php _e('参考記事', THEME_NAME); ?>";
}
.bct-reference-link .blogcard-label::after {
  content: "<?php _e('参考リンク', THEME_NAME); ?>";
}
.bct-popular .blogcard-label::after {
  content: "<?php _e('人気記事', THEME_NAME); ?>";
}
.bct-pickup .blogcard-label::after {
  content: "<?php _e('ピックアップ', THEME_NAME); ?>";
}
.bct-check .blogcard-label::after {
  content: "<?php _e('チェック', THEME_NAME); ?>";
}
.bct-together .blogcard-label::after {
  content: "<?php _e('あわせて読みたい', THEME_NAME); ?>";
}
.bct-detail .blogcard-label::after {
  content: "<?php _e('詳細はこちら', THEME_NAME); ?>";
}
.bct-official .blogcard-label::after {
  content: "<?php _e('公式サイト', THEME_NAME); ?>";
}
.bct-dl .blogcard-label::after {
  content: "<?php _e('ダウンロード', THEME_NAME); ?>";
}
.bct-prev .blogcard-label::after {
  content: "<?php _e('前回の記事', THEME_NAME); ?>";
}
.bct-next .blogcard-label::after {
  content: "<?php _e('続きの記事', THEME_NAME); ?>";
}
<?php endif ?>
<?php //ヘッダーのサイズを背景画像のアスペクト比率にするか
$image_url = get_header_background_image_url();
if ($image_url && is_header_size_background_image_aspect_ratio() && is_header_layout_type_center_logo()):
  $size = get_image_width_and_height($image_url);
  $width = isset($size['width']) ? $size['width'] : 0;
  $height = isset($size['height']) ? $size['height'] : 0; ?>
  <?php //サイズを取得できた時
  if (isset($width) && isset($height)): ?>
    .header {
      aspect-ratio: <?php echo $width; ?> / <?php echo $height; ?>;
      background-position: top center;
      display: flex;
      align-items: center;
    }
    <?php //高さ設定の無効化 ?>
    .header .header-in {
      min-height: 0 !important;
    }
    <?php //ヘッダー背景画像が固定されていてサイズをアスペクト比にする場合はアドミンバーの高さを考慮 ?>
    .ba-fixed{
      background-position: 0 var(--wp-admin--admin-bar--height);
    }
  <?php endif; ?>
<?php endif; ?>