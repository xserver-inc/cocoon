<?php //CSS設定用 ?>
<?php //サイトキー色
if (get_site_key_color()): ?>
.header-container,
.demo .header-container,
.header-container .navi,
.navi .navi-in > .menu-header .sub-menu,
.article h2,
.sidebar h3,
.catlink,
.category-label,
.demo .category-label{
  background-color: <?php echo get_site_key_color(); ?>;
}
.navi .navi-in a:hover{
  background-color: rgba(255, 255, 255, 0.2);
}
.article h3,
.article h4,
.article h5,
.article h6,
.catlink,
.taglink{
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
.entry-content .toc,
.a-wrap .blogcard,
input[type="text"], input[type="password"], input[type="search"], input[type="number"], textarea, select{
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
.catlink:hover,
.taglink:hover,
.tagcloud a:hover{
  background-color: <?php echo colorcode_to_rgb_css_code(get_site_key_color(), 0.05); ?>;
}
.header,
.header .site-name-text-link,
.navi .navi-in a,
.navi .navi-in a:hover,
.article h2,
.sidebar h3{
  color: #fff;
}
<?php endif ?>
<?php //サイトキー文字色
if (get_site_key_text_color()): ?>
.header,
.header .site-name-text-link,
.navi .navi-in a,
.navi .navi-in a:hover,
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
<?php //ヘッダー背景画像
if (get_header_background_image_url()): ?>
.header{
  background-image: url(<?php echo get_header_background_image_url(); ?>);
}
<?php endif ?>
<?php //ヘッダー全体背景色
if (get_header_container_background_color()): ?>
.header-container,
.demo .header-container,
.header-container .navi,
.navi .navi-in > .menu-header .sub-menu{
  background-color: <?php echo get_header_container_background_color(); ?>;
}
.header,
.header .site-name-text-link,
.navi .navi-in a,
.navi .navi-in a:hover{
  color: #fff;
}
<?php endif ?>
<?php //ヘッダー全体文字色
if (get_header_container_text_color()): ?>
.header,
.header .site-name-text-link,
.navi .navi-in a,
.navi .navi-in a:hover{
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
.header .site-name-text-link{
  color: <?php echo get_header_text_color(); ?>;
}
<?php endif ?>
<?php //グローバルナビ背景色
if (get_global_navi_background_color()): ?>
.navi,
.demo .navi,
.navi .navi-in > .menu-header .sub-menu{
  background-color: <?php echo get_global_navi_background_color(); ?>;
}
.navi .navi-in a,
.navi .navi-in a:hover{
  color: #fff;
}
<?php endif ?>
<?php //グローバルナビ文字色
if (get_global_navi_text_color()): ?>
.navi .navi-in a,
.navi .navi-in a:hover{
  color: <?php echo get_global_navi_text_color(); ?>;
}
<?php endif ?>
<?php //グローバルナビホバー背景色
if (get_header_container_background_color() || get_header_background_color() || get_global_navi_background_color()): ?>
.navi .navi-in a:hover{
  background-color: rgba(255, 255, 255, 0.2);
}
<?php endif ?>
<?php //グローバルナビメニュー幅
if (get_global_navi_menu_width()): ?>
.navi .navi-in > ul > li{
  width: <?php echo get_global_navi_menu_width(); ?>px;
}
<?php endif ?>
<?php //グローバルナビサブメニュー幅
if (get_global_navi_sub_menu_width()): ?>
.navi .navi-in > ul .sub-menu{
  min-width: <?php echo get_global_navi_sub_menu_width(); ?>px;
}
.navi .navi-in > ul .sub-menu ul{
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