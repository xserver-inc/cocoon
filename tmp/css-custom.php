<?php //CSS設定用 ?>
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
.demo .cat-label{
  background-color: <?php echo get_site_key_color(); ?>;
}
#navi .navi-in a:hover{
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
.pagination-next-link {
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
.list.entry-card-border .entry-card-wrap,
.related-entries.related-entry-border .related-entry-card-wrap,
.pager-post-navi.post-navi-border a.a-wrap,
.article .toc,
.a-wrap .blogcard,
/*
input[type="text"], input[type="password"], input[type="search"], input[type="number"], textarea, select,
*/
.author-box,
.comment-reply-link,
.ranking-item{
  border-color: <?php echo colorcode_to_rgb_css_code(get_site_key_color(), 0.5); ?>
}
table tr:nth-of-type(2n+1),
.page-numbers.dots,
.a-wrap:hover,
.a-wrap:hover .card-meta,
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
.sidebar h3{
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
.sidebar h3{
  color: <?php echo get_site_key_text_color(); ?>;
}
<?php endif ?>
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
//カテゴリ色の振り分け
foreach ($cats as $cat) {
  $color = get_category_color($cat->cat_ID);
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
}
//CSSの生成
$css = '';
foreach ($colors as $color_code => $ids) {
  $selector = implode(', ', $ids);
  $css .= $selector.'{'.PHP_EOL.
    '  background-color: '.$color_code.';'.PHP_EOL.
    //'  border-color: '.$color_code.';'.PHP_EOL.
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
if (get_main_column_border_width()): ?>
.main{
  border-width: <?php echo get_main_column_border_width(); ?>px;
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
  padding: 20px <?php echo get_sidebar_padding(); ?>px;
}
<?php endif ?>
<?php //枠線の幅
if (get_sidebar_border_width()): ?>
.sidebar{
  border-width: <?php echo get_sidebar_border_width(); ?>px;
}
<?php endif ?>
<?php //枠線の色
if (get_sidebar_border_color()): ?>
.sidebar{
  border-color: <?php echo get_sidebar_border_color(); ?>;
}
<?php endif ?>
<?php //カラム間の幅
if (get_main_sidebar_margin()): ?>
.main{
  margin-right: <?php echo get_main_sidebar_margin(); ?>px;
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
//次のブレークポイント（1030px）より幅が狭い場合はブレークポイントの値にする
if ($responsive_width <= 1030) {
  $responsive_width = 1030;
} ?>
<?php if (!is_admin()): ?>
@media screen and (max-width: <?php echo $responsive_width; ?>px){
  <?php echo wp_filesystem_get_contents(get_template_directory().'/scss/breakpoints/_max-width-1240.scss'); ?>
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
  display: none;
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
@media screen and (max-width: 768px){
  .header div.header-in{
    min-height: <?php echo $mhah; ?>px;
  }
}
<?php endif ?>