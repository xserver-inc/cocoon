<?php
//one WordPress Theme Cocoon skin is derived from SILK WordPress Theme Cocoon skin, Copyright 2020 roko(https://dateqa.com/cocoon/)
//SILK WordPress Theme Cocoon sukin is distributed under the terms of the GNU GPL
//& one WordPress Theme Cocoon skin is derived from Simple-Darkmode WordPress Theme Cocoon skin, Copyright 2021 hiro(https://hirosite.com/)
//Simple-Dark-mode WordPress Theme Cocoon sukin is distributed under the terms of the GNU GPL
/* ブロックエディターに適用するCSSを登録 */
add_theme_support( 'editor-styles' );

function org_theme_add_editor_styles() {
    $editor_style_url = get_theme_file_uri( '/skins/one/editor-style.css' );
    wp_enqueue_style( 'block-editor-style', $editor_style_url );
}
add_action( 'enqueue_block_editor_assets', 'org_theme_add_editor_styles' );


add_action('get_template_part_tmp/css-custom', function() {
  $content_color = get_site_background_color() ?: '#fff';
  $color = get_site_key_color() ?: 'rgb(91,227,255,1)';
  $text_color = get_site_key_text_color() ?: '#ffffff';
  $content_text_color = get_site_text_color() ?: 'rgb(61,61,61,1)';



  echo '
body.body,  #main.main, #sidebar.sidebar, .blogcard-label, .blogcard, .index-tab-buttons .index-tab-button, .sidebar h2, .sidebar h3, hr.wp-block-separator::after, #slide-in-sidebar, .menu-content,
  .carousel-in, .pager-post-navi, .page-numbers.dots{
    background-color: '.$content_color.';
  }
  .page-numbers,
  .tagcloud a,.tag-link,
  .pagination-next-link,
  .comment-reply-link{
    background-color: '.$color.';
    border-color:'.$color.';
    color:white;
  }
  .pager-links a span, .comment-btn,#index-tab-1:checked ~ .index-tab-buttons .index-tab-button[for="index-tab-1"],
   #index-tab-2:checked ~ .index-tab-buttons .index-tab-button[for="index-tab-2"],
   #index-tab-3:checked ~ .index-tab-buttons .index-tab-button[for="index-tab-3"],
    #index-tab-4:checked ~ .index-tab-buttons .index-tab-button[for="index-tab-4"], .index-tab-buttons .index-tab-button:hover{
      background-color: '.$color.';
      border-color:  '.$color.';
      color:  white;
    }
.index-tab-buttons .index-tab-button, .author-box, hr.wp-block-separator,
.widget_recent_entries ul li a, .widget_categories ul li a, .widget_archive ul li a, .widget_pages ul li a, .widget_meta ul li a, .widget_rss ul li a, .widget_nav_menu ul li a,
.ranking-item, .timeline-box, .booklink-box, .kaerebalink-box, .tomarebalink-box, .product-item-box,
.blogcard,
.search-edit, input[type="text"], input[type="password"], input[type="date"], input[type="datetime"], input[type="email"], input[type="number"],
input[type="search"], input[type="tel"], input[type="time"], input[type="url"], textarea, select, .list-more-button, .ecb-entry-border .entry-card-wrap,
.recb-entry-border .related-entry-card-wrap, .border-square .a-wrap, .border-partition .a-wrap, .border-partition a:first-of-type, .post-navi-default.post-navi-border a,
.pager-post-navi.post-navi-square.post-navi-border a, .ccb-carousel-border .a-wrap{
  border-color: '.colorcode_to_rgb_css_code(get_site_text_color(),0.3).';
}
.article h3{
  border-color: '.  $content_text_color.';
}
.widget_recent_entries ul li a:hover, .widget_categories ul li a:hover, .widget_archive ul li a:hover, .widget_pages ul li a:hover,
.widget_meta ul li a:hover, .widget_rss ul li a:hover, .widget_nav_menu ul li a:hover, .menu-drawer a:hover{
  color: inherit;
  background: '.$color.';
  opacity: 0.3;
}
#header-container #navi .navi-in a::before{
   background: '.$color.';
 }
  .entry-card-thumb::before, .widget-entry-card-thumb::before, figure.prev-post-thumb.card-thumb::before,
  figure.next-post-thumb.card-thumb::before, #content-in .article h2 ::before,
  .related-entry-card-thumb::before, .carousel-entry-card-thumb::before,
  .article h3::before, .article h5::before, .blogcard-thumbnail::before, .sidebar h2::before, .sidebar h3::before, hr.wp-block-separator::before{
    background-color: '.$color.';
  }
  .pager-post-navi a.prev-next-home:hover, .box-menus .box-menu-icon, .search-submit, .page-numbers.dots{
    color:'.$color.';
  }
.box-menus  .box-menu:hover {
      box-shadow: inset 2px 2px 0 0 '.$color.', 2px 2px 0 0 '.$color.', 2px 0 0 0 '.$color.', 0 2px 0 0 '.$color.';
  }

.pagination a:hover, .pagination-next-link:hover, .comment-btn:hover,
.pagination .current, span.page-numbers.current, .pager-links a span:hover,
.tag-link:hover, .comment-reply-link:hover, .tagcloud a:hover, .tag-link:hover{
  color:'.$color.';
  background-color: white;
}
.content, .blogcard-label, .entry-card, .related-entry-card, .blogcard, .widget-entry-cards .a-wrap,
.post-navi-square.post-navi-border a, .index-tab-buttons .index-tab-button, .pager-post-navi a, .sidebar h2, .sidebar h3,
.widget_recent_entries ul li a, .widget_categories ul li a, .widget_archive ul li a, .widget_pages ul li a, .widget_meta ul li a, .widget_rss ul li a, .widget_nav_menu ul li a,
.recent-comment, .list-more-button, .menu-drawer a, .slick-initialized .slick-slide, .pager-post-navi a div, .menu-content, .is-dark-on .search-form div.sbtn::after{
  color:'.$content_text_color.';
}

.article h4::before, .article h6::before{
  background: '.$content_text_color.';
}
  ';

  global $_THEME_OPTIONS;
  $_THEME_OPTIONS['site_bakground_color'] = $_THEME_OPTIONS['site_key_color'] = $_THEME_OPTIONS['site_text_color'] = $_THEME_OPTIONS['site_key_text_color'] = '';
});

add_action('cocoon_settings_after_save', function() {
global $_THEME_OPTIONS;
unset($_THEME_OPTIONS['site_background_color'] , $_THEME_OPTIONS['site_key_color'], $_THEME_OPTIONS['site_text_color'], $_THEME_OPTIONS['site_key_text_color']);
});
add_filter('get_editor_key_color', function($color) {
  return get_theme_mod('site_key_color') ?: '#5be3ff';
});
//カスタマイザーに追加
function one_customize_register( $wp_customize ) {

    $wp_customize->add_section( 'theme_setting', array(
        'title'    => __( 'スキンoneの設定', 'theme_slug' ),
        'description' => '各項目を変更して、【公開】ボタンを押すことでデザインが反映されます。',
        'priority' => 1,
    ));
    $wp_customize->add_setting( 'checkbox_one', array(
        'default'  => true,
        'sanitize_callback' => 'one_sanitize_checkbox',
    ));
    $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'checkbox_one', array(
        'label'       => __( '本文見出し(H2)にカウントを付ける', 'theme' ),
        'description' => __( '本文の見出しにカウントをつけたくない場合はチェックを外してください', 'theme' ),
        'section'     => 'theme_setting',
        'priority'    => 1,
        'type'        => 'checkbox',
    )));

    $wp_customize->add_setting( 'checkbox_one2', array(
        'default'  => true,
        'sanitize_callback' => 'one_sanitize_checkbox',
    ));
    $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'checkbox_one2', array(
        'label'       => __( 'PCでもスマートフォン用の固定フッターメニューを表示する', 'theme' ),
        'description' => __( '従来どおりPCでスマートフォン用の固定フッターメニューを表示させたくない場合はチェックを外してください', 'theme' ),
        'section'     => 'theme_setting',
        'priority'    => 4,
        'type'        => 'checkbox',
    )));
    $wp_customize->add_setting( 'checkbox_one3', array(
        'default'  => true,
        'sanitize_callback' => 'one_sanitize_checkbox',
    ));
    $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'checkbox_one3', array(
        'label'       => __( '全体的に影を付ける', 'theme' ),
        'section'     => 'theme_setting',
        'priority'    => 5,
        'type'        => 'checkbox',
    )));
    $wp_customize->add_setting( 'checkbox_one4', array(
        'default'  => false,
        'sanitize_callback' => 'one_sanitize_checkbox',
    ));
    $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'checkbox_one4', array(
        'label'       => __( 'ダークスキン対応にする', 'theme' ),
        'description' => __( '背景をダークにしたときはチェックを入れてください', 'theme' ),
        'section'     => 'theme_setting',
        'priority'    => 6,
        'type'        => 'checkbox',
    )));
    $wp_customize->add_setting( 'checkbox_one5', array(
        'default'  => true,
        'sanitize_callback' => 'one_sanitize_checkbox',
    ));
    $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'checkbox_one5', array(
        'label'       => __( '丸みをなくす', 'theme' ),
        'description' => __( 'ボタンなどの角の丸みをなくしたいときはチェックを入れてください', 'theme' ),
        'section'     => 'theme_setting',
        'priority'    => 7,
        'type'        => 'checkbox',
    )));
    $wp_customize->add_setting( 'checkbox_one6', array(
        'default'  => true,
        'sanitize_callback' => 'one_sanitize_checkbox',
    ));
    $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'checkbox_one6', array(
        'label'       => __( '本文の見出しを明朝体にする', 'theme' ),
        'section'     => 'theme_setting',
        'priority'    => 2,
        'type'        => 'checkbox',
    )));
    $wp_customize->add_setting( 'checkbox_one7', array(
        'default'  => true,
        'sanitize_callback' => 'one_sanitize_checkbox',
    ));
    $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'checkbox_one7', array(
        'label'       => __( 'サイドバーの見出しを明朝体にする', 'theme' ),
        'section'     => 'theme_setting',
        'priority'    => 3,
        'type'        => 'checkbox',
    )));


}
function one_sanitize_checkbox( $checked ) {
  // Boolean check.
  return ( ( isset( $checked ) && true == $checked ) ? true : false );
}

add_action( 'customize_register', 'one_customize_register' );

if (get_theme_mod('checkbox_one',true)) {
  add_filter( 'body_class', 'my_class_names' );
function my_class_names( $classes ) {
 $classes[] = 'is-count-on';
 return $classes;
} }
 if (get_theme_mod('checkbox_one2',true)) {
add_filter( 'body_class', 'my_class_names2' );
function my_class_names2( $classes ) {
  $classes[] = 'is-pcmenu-on';
  return $classes;
 } }
  if (get_theme_mod('checkbox_one3',true)) {
  add_filter( 'body_class', 'my_class_names3' );
  function my_class_names3( $classes ) {
   $classes[] = 'is-shadow-on';
   return $classes;
  } }
  if (get_theme_mod('checkbox_one4',false)) {
   add_filter( 'body_class', 'my_class_names4' );
   function my_class_names4( $classes ) {
    $classes[] = 'is-dark-on';
    return $classes;
   } }
    if (get_theme_mod('checkbox_one5',true)) {
     add_filter( 'body_class', 'my_class_names5' );
     function my_class_names5( $classes ) {
      $classes[] = 'is-border-0';
      return $classes;
     } }
      if (get_theme_mod('checkbox_one6',true)) {
       add_filter( 'body_class', 'my_class_names6' );
       function my_class_names6( $classes ) {
        $classes[] = 'is-main-serif';
        return $classes;
       } }
      if (get_theme_mod('checkbox_one7',true)) {
       add_filter( 'body_class', 'my_class_names7' );
       function my_class_names7( $classes ) {
        $classes[] = 'is-sidebar-serif';
        return $classes;
       } }


//ウィジェットエリア追加
  function one_widget_area() {
  // ウィジェットを登録
  register_sidebar( array(
    'id' => 'sidebar-one',
    'name' => '左固定サイドバー',
    'description' => 'PCでの閲覧時左端に固定で表示されるサイドバーです。',
    'before_widget' => '<div class="one-fixed-sidebar">',
    'after_widget' => '</div>',
    'before_title' => '<p class="one-fixed-title">',
    'after_title' => '</p>',
  ) );
  }
  add_action( 'widgets_init', 'one_widget_area' );

  function one_widget_area_add() {

  		if ( is_active_sidebar( 'sidebar-one' ) ) {
  			dynamic_sidebar( 'sidebar-one' );
  		}
  	}

  add_action( 'wp_footer', 'one_widget_area_add' );

?>
