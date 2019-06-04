<?php //CSS設定用
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit; ?>

<?php //モバイルサイトフォント
if (get_mobile_site_font_size()): ?>
@media screen and (max-width: 480px){
  .page-body{
    font-size: <?php echo get_mobile_site_font_size(); ?>;
  }
}
<?php endif ?>
<?php //サイトキー色
if (get_site_key_color()): ?>
#header-container,
#header-container .navi,
#navi .navi-in > .menu-header .sub-menu,
.article h2,
.sidebar h3,
.cat-link,
.cat-label,
.appeal-content .appeal-button,
.demo .cat-label,
.blogcard-type .blogcard::before,
#footer{
  background-color: <?php echo get_site_key_color(); ?>;
}
#navi .navi-in a:hover,
#footer a:hover{
  background-color: rgba(255, 255, 255, 0.2);
}
.article h3,
.article h4,
.article h5,
.article h6,
.cat-link,
.tag-link{
  border-color: <?php echo get_site_key_color(); ?>;
}
blockquote::before, blockquote::after,
.pager-post-navi a.a-wrap::before {
  color: <?php echo colorcode_to_rgb_css_code(get_site_key_color(), 0.5); ?>;
}
blockquote,
.key-btn {
  background-color: <?php echo colorcode_to_rgb_css_code(get_site_key_color(), 0.05); ?>;
  border-color: <?php echo colorcode_to_rgb_css_code(get_site_key_color(), 0.5); ?>;
}
pre,
.pager-links span,
table th,
.pagination .current {
  background-color: <?php echo colorcode_to_rgb_css_code(get_site_key_color(), 0.1); ?>;
  border-color: <?php echo colorcode_to_rgb_css_code(get_site_key_color(), 0.5); ?>;
}
table th,
table td,
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
  border-color: <?php echo colorcode_to_rgb_css_code(get_site_key_color(), 0.5); ?>
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
  background-color: <?php echo colorcode_to_rgb_css_code(get_site_key_color(), 0.05); ?>;
}
.header,
.header .site-name-text,
#navi .navi-in a,
#navi .navi-in a:hover,
.article h2,
.sidebar h3,
#footer,
#footer a{
  color: #fff;
}
<?php endif ?>
<?php //サイトキー文字色
if (get_site_key_text_color()): ?>
.header,
.header .site-name-text,
#navi .navi-in a,
#navi .navi-in a:hover,
.appeal-content .appeal-button,
.article h2,
.sidebar h3,
.cat-link,
.cat-label,
.blogcard-type .blogcard::before,
#footer,
#footer a{
  color: <?php echo get_site_key_text_color(); ?>;
}
<?php endif ?>
<?php //サイト文字色
if($site_text_color = get_site_text_color()): ?>
body{
  color: <?php echo $site_text_color; ?>;
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
if (get_header_area_height()): ?>
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
if (get_header_container_background_color()): ?>
#header-container,
#header-container .navi,
#navi .navi-in > .menu-header .sub-menu{
  background-color: <?php echo get_header_container_background_color(); ?>;
}
.header,
.header .site-name-text,
#navi .navi-in a,
#navi .navi-in a:hover{
  color: #fff;
}
<?php endif ?>
<?php //ヘッダー全体文字色
if (get_header_container_text_color()): ?>
.header,
.header .site-name-text,
#navi .navi-in a,
#navi .navi-in a:hover{
  color: <?php echo get_header_container_text_color(); ?>;
}
<?php endif ?>
<?php //ヘッダー背景色
if (get_header_background_color()): ?>
.header{
  background-color: <?php echo get_header_background_color(); ?>;
}
<?php endif ?>
<?php //ヘッダー文字色
if (get_header_text_color()): ?>
.header,
.header .site-name-text{
  color: <?php echo get_header_text_color(); ?>;
}
<?php endif ?>
<?php //グローバルナビ背景色
if (get_global_navi_background_color()): ?>
#header-container .navi,
#navi .navi-in > .menu-header .sub-menu{
  background-color: <?php echo get_global_navi_background_color(); ?>;
}
#navi .navi-in a,
#navi .navi-in a:hover{
  color: #fff;
}
<?php endif ?>
<?php //グローバルナビ文字色
if (get_global_navi_text_color()): ?>
#navi .navi-in a,
#navi .navi-in a:hover{
  color: <?php echo get_global_navi_text_color(); ?>;
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
  min-width: <?php echo get_global_navi_sub_menu_width(); ?>px;
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
.appeal .appeal-in{
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
$cats = get_categories();
$colors = array();
$text_colors = array();
//カテゴリ色の振り分け
foreach ($cats as $cat) {
  $color = get_category_color($cat->cat_ID);
  $text_color = get_category_text_color($cat->cat_ID);
  $cat_label_pre = '.cat-label.cat-label-';
  $cat_link_pre = '.cat-link.cat-link-';
  if ($color) {
    $selectors = $cat_label_pre.$cat->cat_ID.', '.$cat_link_pre.$cat->cat_ID;
    if (isset($colors[$color])) {
      array_push($colors[$color], $selectors);
    } else {
      $colors[$color] = array($selectors);
    }
  }
  if ($text_color) {
    $selectors = $cat_label_pre.$cat->cat_ID.', '.$cat_link_pre.$cat->cat_ID;
    if (isset($text_colors[$text_color])) {
      array_push($text_colors[$text_color], $selectors);
    } else {
      $text_colors[$text_color] = array($selectors);
    }
  }
}
//CSSの生成
$css = '';
//カテゴリー背景色
foreach ($colors as $color_code => $ids) {
  $selector = implode(', ', $ids);
  $css .= $selector.'{'.PHP_EOL.
    '  background-color: '.$color_code.';'.PHP_EOL.
    '  color: #fff;'.PHP_EOL.
  '}'.PHP_EOL.PHP_EOL;
}
//カテゴリー文字色
foreach ($text_colors as $color_code => $ids) {
  $selector = implode(', ', $ids);
  $css .= $selector.'{'.PHP_EOL.
    '  color: '.$color_code.';'.PHP_EOL.
  '}'.PHP_EOL.PHP_EOL;
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
  padding: 20px <?php echo get_main_column_padding(); ?>px;
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
  padding: 19px <?php echo get_sidebar_padding(); ?>px;
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
if (is_numeric($main_sidebar_margin)): ?>
.main{
  <?php if(is_sidebar_position_right()): ?>
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
$responsive_width = get_site_wrap_width() + 4;
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
  content: url("<?php echo get_template_directory_uri(); ?>/lib/analytics/access.php?post_id=<?php echo get_the_ID(); ?>&post_type=<?php echo get_accesses_post_type(); ?>");
  visibility: hidden;
  position: absolute;
  bottom: 0;
  right: 0;
  width: 1px;
  height: 1px;
  overflow: hidden;
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
.toc-title::after{
  content: '[<?php echo get_toc_open_caption(); ?>]';
  margin-left: 0.5em;
  cursor: pointer;
  font-size: 0.8em;
}
.toc-title:hover::after{
  text-decoration: underline;
}
.toc-checkbox:checked + .toc-title::after{
  content: '[<?php echo get_toc_close_caption(); ?>]';
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
  display: none;
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
#footer a,
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
  .main p,
  .main p.wp-block-paragraph {
    line-height: <?php echo $entry_content_line_hight; ?>;
  }
  <?php endif; ?>
<?php endif ?>
<?php //行の余白
if ($entry_content_margin_hight = get_entry_content_margin_hight()): ?>
.entry-content > *,
.demo .entry-content p {
  margin-top: <?php echo $entry_content_margin_hight; ?>em;
  margin-bottom: <?php echo $entry_content_margin_hight; ?>em;
}
.entry-content > .micro-top{
  margin-bottom: -<?php echo $entry_content_margin_hight * 1.1; ?>em;
}
.entry-content > .micro-balloon{
  margin-bottom: -<?php echo $entry_content_margin_hight * 0.8; ?>em;
}
.entry-content > .micro-bottom{
  margin-top: -<?php echo $entry_content_margin_hight * 1.1; ?>em;
}
.entry-content > .micro-bottom.micro-balloon{
  margin-top: -<?php echo $entry_content_margin_hight * 0.8; ?>em;
  margin-bottom: <?php echo $entry_content_margin_hight; ?>em;
}
  <?php ////管理画面用
  if(is_admin() && is_gutenberg_editor_enable()): ?>
  .main p,
  .main p.wp-block-paragraph {
    margin-top: <?php echo $entry_content_margin_hight; ?>em;
    margin-bottom: <?php echo $entry_content_margin_hight; ?>em;
  }
  <?php endif; ?>
<?php endif ?>
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
.article.page-body,
body#tinymce.wp-editor{
  background-color: <?php echo $editor_background_color; ?>
}
<?php //エディター文字色
$editor_text_color = get_editor_text_color();
if (!$editor_text_color) {
  $editor_text_color = '#333';
} ?>
.article.page-body,
.editor-post-title__block .editor-post-title__input,
body#tinymce.wp-editor{
  color: <?php echo $editor_text_color; ?>
}
