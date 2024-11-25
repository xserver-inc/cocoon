<?php //ウィジェットエリア用の関数
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

add_action('after_setup_theme', function(){
  /////////////////////////////////////
  // ウィジェットエリアの指定
  /////////////////////////////////////
  if ( !function_exists( 'register_sidebar_widget_area' ) ):
  function register_sidebar_widget_area(){
    register_sidebars(1,
      array(
      'name' => __( 'サイドバー', THEME_NAME ),
      'id' => 'sidebar',
      'description' => __( 'サイドバーのウィジェットエリアです。ウィジェットを入れていない場合は1カラム表示になります。', THEME_NAME ),
      'before_widget' => '<aside id="%1$s" class="widget widget-sidebar widget-sidebar-standard %2$s">',
      'after_widget' => '</aside>',
      'before_title'  => '<h3 class="widget-sidebar-title widget-title">',
      'after_title'   => '</h3>',
    ));
  }
  endif;
  register_sidebar_widget_area();

  if ( !function_exists( 'register_sidebar_scroll_widget_area' ) ):
  function register_sidebar_scroll_widget_area(){
    register_sidebars(1,
      array(
      'name' => __( 'サイドバースクロール追従', THEME_NAME ),
      'id' => 'sidebar-scroll',
      'description' => __( 'サイドバーで下にスクロールすると追いかけてくるエリアです。', THEME_NAME ),
      'before_widget' => '<aside id="%1$s" class="widget widget-sidebar widget-sidebar-scroll %2$s">',
      'after_widget' => '</aside>',
      'before_title' => '<h3 class="widget-sidebar-scroll-title widget-title">',
      'after_title' => '</h3>',
    ));
  }
  endif;
  register_sidebar_scroll_widget_area();

  if ( !function_exists( 'register_main_scroll_widget_area' ) ):
  function register_main_scroll_widget_area(){
    register_sidebars(1,
      array(
      'name' => __( 'メインカラムスクロール追従', THEME_NAME ),
      'id' => 'main-scroll',
      'description' => __( 'メインカラムで下にスクロールすると追いかけてくるエリアです。サイドバーの方が長い場合に追従してきます。', THEME_NAME ),
      'before_widget' => '<aside id="%1$s" class="widget widget-main-scroll %2$s">',
      'after_widget' => '</aside>',
      'before_title' => '<h2 class="widget-main-scroll-title main-widget-label widget-title">',
      'after_title' => '</h2>',
    ));
  }
  endif;
  register_main_scroll_widget_area();

  if ( !function_exists( 'register_above_single_content_title_widget_area' ) ):
  function register_above_single_content_title_widget_area(){
    register_sidebars(1,
      array(
      'name' => __( '投稿タイトル上', THEME_NAME ),
      'id' => 'above-single-content-title',
      'description' => __( '投稿タイトル上に表示されるウィジェット。', THEME_NAME ),
      'before_widget' => '<div id="%1$s" class="widget widget-above-single-content-title %2$s">',
      'after_widget' => '</div>',
      'before_title' => '<div class="widget-above-single-content-title-title main-widget-label widget-title">',
      'after_title' => '</div>',
    ));
  }
  endif;
  register_above_single_content_title_widget_area();

  if ( !function_exists( 'register_below_single_content_title_widget_area' ) ):
  function register_below_single_content_title_widget_area(){
    register_sidebars(1,
      array(
      'name' => __( '投稿タイトル下', THEME_NAME ),
      'id' => 'below-single-content-title',
      'description' => __( '投稿タイトル下に表示されるウィジェット。', THEME_NAME ),
      'before_widget' => '<div id="%1$s" class="widget widget-below-single-content-title %2$s">',
      'after_widget' => '</div>',
      'before_title' => '<div class="widget-below-single-content-title-title main-widget-label widget-title">',
      'after_title' => '</div>',
    ));
  }
  endif;
  register_below_single_content_title_widget_area();

  if ( !function_exists( 'register_single_content_top_widget_area' ) ):
  function register_single_content_top_widget_area(){
    register_sidebars(1,
      array(
      'name' => __( '投稿本文上', THEME_NAME ),
      'id' => 'single-content-top',
      'description' => __( '投稿本文上に表示されるウィジェット。', THEME_NAME ),
      'before_widget' => '<div id="%1$s" class="widget widget-single-content-top %2$s">',
      'after_widget' => '</div>',
      'before_title' => '<div class="widget-single-content-top-title main-widget-label widget-title">',
      'after_title' => '</div>',
    ));
  }
  endif;
  register_single_content_top_widget_area();

  if ( !function_exists( 'register_single_content_middle_widget_area' ) ):
  function register_single_content_middle_widget_area(){
    register_sidebars(1,
      array(
      'name' => __( '投稿本文中', THEME_NAME ),
      'id' => 'single-content-middle',
      'description' => __( '投稿本文中に表示されるウィジェット。文中最初のH2タグの手前に表示されます。広告が表示されている場合は、広告の下に表示されます。', THEME_NAME ),
      'before_widget' => '<div id="%1$s" class="widget widget-single-content-middle %2$s">',
      'after_widget' => '</div>',
      'before_title' => '<div class="widget-single-content-middle-title main-widget-label widget-title">',
      'after_title' => '</div>',
    ));
  }
  endif;
  register_single_content_middle_widget_area();

  if ( !function_exists( 'register_single_content_bottom_widget_area' ) ):
  function register_single_content_bottom_widget_area(){
    register_sidebars(1,
      array(
      'name' => __( '投稿本文下', THEME_NAME ),
      'id' => 'single-content-bottom',
      'description' => __( '投稿本文下に表示されるウィジェット。', THEME_NAME ),
      'before_widget' => '<div id="%1$s" class="widget widget-single-content-bottom %2$s">',
      'after_widget' => '</div>',
      'before_title' => '<div class="widget-single-content-bottom-title main-widget-label widget-title">',
      'after_title' => '</div>',
    ));
  }
  endif;
  register_single_content_bottom_widget_area();

  if ( !function_exists( 'register_abobe_single_sns_buttons_widget_area' ) ):
  function register_abobe_single_sns_buttons_widget_area(){
    register_sidebars(1,
      array(
      'name' => __( '投稿SNSボタン上', THEME_NAME ),
      'id' => 'above-single-sns-buttons',
      'description' => __( '投稿のメインカラムの一番下となるSNSボタンの上に表示されるウィジェット。広告を表示している場合は、広告の下になります。', THEME_NAME ),
      'before_widget' => '<div id="%1$s" class="widget widget-above-single-sns-buttons %2$s">',
      'after_widget' => '</div>',
      'before_title' => '<div class="widget-above-single-sns-buttons-title main-widget-label widget-title">',
      'after_title' => '</div>',
    ));
  }
  endif;
  register_abobe_single_sns_buttons_widget_area();

  if ( !function_exists( 'register_below_single_sns_buttons_widget_area' ) ):
  function register_below_single_sns_buttons_widget_area(){
    register_sidebars(1,
      array(
      'name' => __( '投稿SNSボタン下', THEME_NAME ),
      'id' => 'below-single-sns-buttons',
      'description' => __( '投稿のメインカラムの一番下となるSNSボタンの下に表示されるウィジェット。', THEME_NAME ),
      'before_widget' => '<div id="%1$s" class="widget widget-below-sns-buttons %2$s">',
      'after_widget' => '</div>',
      'before_title' => '<div class="widget-below-sns-buttons-title main-widget-label widget-title">',
      'after_title' => '</div>',
    ));
  }
  endif;
  register_below_single_sns_buttons_widget_area();

  if ( !function_exists( 'register_above_single_related_entries_widget_area' ) ):
  function register_above_single_related_entries_widget_area(){
  register_sidebars(1,
    array(
    'name' => __( '投稿関連記事上', THEME_NAME ),
    'id' => 'above-single-related-entries',
    'description' => __( '関連記事の上に表示されるウィジェット。', THEME_NAME ),
    'before_widget' => '<div id="%1$s" class="widget widget-above-related-entries %2$s">',
    'after_widget' => '</div>',
    'before_title' => '<h2 class="widget-above-related-entries-title main-widget-label widget-title">',
    'after_title' => '</h2>',
  ));
  }
  endif;
  register_above_single_related_entries_widget_area();

  if ( !function_exists( 'register_below_single_related_entries_widget_area' ) ):
  function register_below_single_related_entries_widget_area(){
  register_sidebars(1,
    array(
    'name' => __( '投稿関連記事下', THEME_NAME ),
    'id' => 'below-single-related-entries',
    'description' => __( '関連記事の下（広告を表示している場合はその下）に表示されるウィジェット。', THEME_NAME ),
    'before_widget' => '<div id="%1$s" class="widget widget-below-related-entries %2$s">',
    'after_widget' => '</div>',
    'before_title' => '<h2 class="widget-below-related-entries-title main-widget-label widget-title">',
    'after_title' => '</h2>',
  ));
  }
  endif;
  register_below_single_related_entries_widget_area();

  if ( !function_exists( 'register_above_single_comment_aria_widget_area' ) ):
  function register_above_single_comment_aria_widget_area(){
  register_sidebars(1,
    array(
    'name' => __( '投稿コメント上', THEME_NAME ),
    'id' => 'above-single-comment-aria',
    'description' => __( 'コメントエリアの上に表示されるウィジェット。', THEME_NAME ),
    'before_widget' => '<div id="%1$s" class="widget above-single-comment-aria %2$s">',
    'after_widget' => '</div>',
    'before_title' => '<h2 class="above-single-comment-aria-title main-widget-label widget-title">',
    'after_title' => '</h2>',
  ));
  }
  endif;
  register_above_single_comment_aria_widget_area();

  if ( !function_exists( 'register_below_single_comment_form_widget_area' ) ):
  function register_below_single_comment_form_widget_area(){
  register_sidebars(1,
    array(
    'name' => __( '投稿コメント下', THEME_NAME ),
    'id' => 'below-single-comment-form',
    'description' => __( 'コメントフォームの下に表示されるウィジェット。', THEME_NAME ),
    'before_widget' => '<div id="%1$s" class="widget widget-below-comment-form %2$s">',
    'after_widget' => '</div>',
    'before_title' => '<h2 class="widget-below-comment-form-title main-widget-label widget-title">',
    'after_title' => '</h2>',
  ));
  }
  endif;
  register_below_single_comment_form_widget_area();

  if ( !function_exists( 'register_above_page_content_title_widget_area' ) ):
  function register_above_page_content_title_widget_area(){
    register_sidebars(1,
      array(
      'name' => __( '固定ページタイトル上', THEME_NAME ),
      'id' => 'above-page-content-title',
      'description' => __( '固定ページタイトル上に表示されるウィジェット。', THEME_NAME ),
      'before_widget' => '<div id="%1$s" class="widget widget-above-page-content-title %2$s">',
      'after_widget' => '</div>',
      'before_title' => '<div class="widget-above-page-content-title-title main-widget-label widget-title">',
      'after_title' => '</div>',
    ));
  }
  endif;
  register_above_page_content_title_widget_area();

  if ( !function_exists( 'register_below_page_content_title_widget_area' ) ):
  function register_below_page_content_title_widget_area(){
    register_sidebars(1,
      array(
      'name' => __( '固定ページタイトル下', THEME_NAME ),
      'id' => 'below-page-content-title',
      'description' => __( '固定ページタイトル下に表示されるウィジェット。', THEME_NAME ),
      'before_widget' => '<div id="%1$s" class="widget widget-below-page-content-title %2$s">',
      'after_widget' => '</div>',
      'before_title' => '<div class="widget-below-page-content-title-title main-widget-label widget-title">',
      'after_title' => '</div>',
    ));
  }
  endif;
  register_below_page_content_title_widget_area();

  if ( !function_exists( 'register_page_content_top_widget_area' ) ):
  function register_page_content_top_widget_area(){
    register_sidebars(1,
      array(
      'name' => __( '固定ページ本文上', THEME_NAME ),
      'id' => 'page-content-top',
      'description' => __( '固定ページ本文上に表示されるウィジェット。', THEME_NAME ),
      'before_widget' => '<div id="%1$s" class="widget widget-page-content-top %2$s">',
      'after_widget' => '</div>',
      'before_title' => '<div class="widget-page-content-top-title main-widget-label widget-title">',
      'after_title' => '</div>',
    ));
  }
  endif;
  register_page_content_top_widget_area();

  if ( !function_exists( 'register_page_content_middle_widget_area' ) ):
  function register_page_content_middle_widget_area(){
    register_sidebars(1,
      array(
      'name' => __( '固定ページ本文中', THEME_NAME ),
      'id' => 'page-content-middle',
      'description' => __( '固定ページ本文中に表示されるウィジェット。文中最初のH2タグの手前に表示されます。広告が表示されている場合は、広告の下に表示されます。', THEME_NAME ),
      'before_widget' => '<div id="%1$s" class="widget widget-page-content-middle %2$s">',
      'after_widget' => '</div>',
      'before_title' => '<div class="widget-page-content-middle-title main-widget-label widget-title">',
      'after_title' => '</div>',
    ));
  }
  endif;
  register_page_content_middle_widget_area();

  if ( !function_exists( 'register_page_content_bottom_widget_area' ) ):
  function register_page_content_bottom_widget_area(){
  register_sidebars(1,
    array(
    'name' => __( '固定ページ本文下', THEME_NAME ),
    'id' => 'page-content-bottom',
    'description' => __( '固定ページ本文下に表示されるウィジェット。', THEME_NAME ),
    'before_widget' => '<div id="%1$s" class="widget widget-page-content-bottom %2$s">',
    'after_widget' => '</div>',
    'before_title' => '<div class="widget-page-content-bottom-title main-widget-label widget-title">',
    'after_title' => '</div>',
  ));
  }
  endif;
  register_page_content_bottom_widget_area();


  if ( !function_exists( 'register_above_page_sns_buttonwidget_area' ) ):
  function register_above_page_sns_buttonwidget_area(){
    register_sidebars(1,
      array(
      'name' => __( '固定ページSNSボタン上', THEME_NAME ),
      'id' => 'above-page-sns-buttons',
      'description' => __( '固定ページのメインカラムの一番下となるSNSボタンの上に表示されるウィジェット。広告を表示している場合は、広告の下になります。', THEME_NAME ),
      'before_widget' => '<div id="%1$s" class="widget widget-above-page-sns-buttons %2$s">',
      'after_widget' => '</div>',
      'before_title' => '<div class="widget-above-page-sns-buttons-title main-widget-label widget-title">',
      'after_title' => '</div>',
    ));
  }
  endif;
  register_above_page_sns_buttonwidget_area();

  if ( !function_exists( 'register_below_page_sns_buttons_widget_area' ) ):
  function register_below_page_sns_buttons_widget_area(){
    register_sidebars(1,
      array(
      'name' => __( '固定ページSNSボタン下', THEME_NAME ),
      'id' => 'below-page-sns-buttons',
      'description' => __( '固定ページのメインカラムの一番下となるSNSボタンの下に表示されるウィジェット。', THEME_NAME ),
      'before_widget' => '<div id="%1$s" class="widget widget-below-page-sns-buttons %2$s">',
      'after_widget' => '</div>',
      'before_title' => '<div class="widget-below-page-sns-buttons-title main-widget-label widget-title">',
      'after_title' => '</div>',
    ));
  }
  endif;
  register_below_page_sns_buttons_widget_area();

  if ( !function_exists( 'register_index_top_widget_area' ) ):
  function register_index_top_widget_area(){
    register_sidebars(1,
      array(
      'name' => __( 'インデックスリストトップ', THEME_NAME ),
      'id' => 'index-top',
      'description' => __( 'インデックスリストのトップに表示されるウィジェット。広告が表示されているときは広告の下に表示されます。', THEME_NAME ),
      'before_widget' => '<aside id="%1$s" class="widget widget-index-top %2$s">',
      'after_widget' => '</aside>',
      'before_title' => '<div class="widget-index-top-title main-widget-label widget-title">',
      'after_title' => '</div>',
    ));
  }
  endif;
  register_index_top_widget_area();

  if ( !function_exists( 'register_index_middle_widget_area' ) ):
  function register_index_middle_widget_area(){
    register_sidebars(1,
      array(
      'name' => __( 'インデックスリストミドル', THEME_NAME ),
      'id' => 'index-middle',
      'description' => __( 'インデックスリストの3つ目下に表示されるウィジェット。「一覧リストのスタイル」が「サムネイルカード」の時のみの機能です。広告が表示されているときは広告の下に表示されます。', THEME_NAME ),
      'before_widget' => '<aside id="%1$s" class="widget widget-index-middle %2$s">',
      'after_widget' => '</aside>',
      'before_title' => '<div class="widget-index-middle-title main-widget-label widget-title">',
      'after_title' => '</div>',
    ));
  }
  endif;
  register_index_middle_widget_area();

  if ( !function_exists( 'register_index_bottom_widget_area' ) ):
  function register_index_bottom_widget_area(){
    register_sidebars(1,
      array(
      'name' => __( 'インデックスリストボトム', THEME_NAME ),
      'id' => 'index-bottom',
      'description' => __( 'インデックスリストのボトムに表示されるウィジェット。広告が表示されているときは広告の下に表示されます。', THEME_NAME ),
      'before_widget' => '<aside id="%1$s" class="widget widget-index-bottom %2$s">',
      'after_widget' => '</aside>',
      'before_title' => '<div class="widget-index-bottom-title main-widget-label widget-title">',
      'after_title' => '</div>',
    ));
  }
  endif;
  register_index_bottom_widget_area();

  if ( !function_exists( 'register_content_top_widget_area' ) ):
  function register_content_top_widget_area(){
    register_sidebars(1,
      array(
      'name' => __( 'コンテンツ上部', THEME_NAME ),
      'id' => 'content-top',
      'description' => __( 'メインカラムとサイドバーの上部エリア。グローバルメニューの下にもなります。', THEME_NAME ),
      'before_widget' => '<aside id="%1$s" class="widget widget-content-top %2$s">',
      'after_widget' => '</aside>',
      'before_title' => '<h2 class="widget-content-top-title main-widget-label widget-title">',
      'after_title' => '</h2>',
    ));
  }
  endif;
  register_content_top_widget_area();

  if ( !function_exists( 'register_content_bottom_widget_area' ) ):
  function register_content_bottom_widget_area(){
    register_sidebars(1,
      array(
      'name' => __( 'コンテンツ下部', THEME_NAME ),
      'id' => 'content-bottom',
      'description' => __( 'メインカラムとサイドバーの下部エリア。フッターの上になります。', THEME_NAME ),
      'before_widget' => '<aside id="%1$s" class="widget widget-content-bottom %2$s">',
      'after_widget' => '</aside>',
      'before_title' => '<h2 class="widget-content-bottom-title main-widget-label widget-title">',
      'after_title' => '</h2>',
    ));
  }
  endif;
  register_content_bottom_widget_area();

  if ( !function_exists( 'register_footer_left_widget_area' ) ):
  function register_footer_left_widget_area(){
    register_sidebars(1,
      array(
      'name' => __( 'フッター左', THEME_NAME ),
      'id' => 'footer-left',
      'description' => __( 'フッター左側のウィジェットエリアです。', THEME_NAME ),
      'before_widget' => '<aside id="%1$s" class="widget widget-footer-left %2$s">',
      'after_widget' => '</aside>',
      'before_title' => '<h3 class="widget-footer-left-title footer-title widget-title">',
      'after_title' => '</h3>',
    ));
  }
  endif;
  register_footer_left_widget_area();

  if ( !function_exists( 'register_footer_center_widget_area' ) ):
  function register_footer_center_widget_area(){
  register_sidebars(1,
    array(
    'id' => 'footer-center',
    'name' => __( 'フッター中', THEME_NAME ),
    'description' => __( 'フッター中間のウィジェットエリアです。', THEME_NAME ),
    'before_widget' => '<aside id="%1$s" class="widget widget-footer-center %2$s">',
    'after_widget' => '</aside>',
    'before_title' => '<h3 class="widget-footer-center-title footer-title widget-title">',
    'after_title' => '</h3>',
  ));
  }
  endif;
  register_footer_center_widget_area();

  if ( !function_exists( 'register_footer_right_widget_area' ) ):
  function register_footer_right_widget_area(){
    register_sidebars(1,
      array(
      'name' => __( 'フッター右', THEME_NAME ),
      'id' => 'footer-right',
      'description' => __( 'フッター右側フッター中のウィジェットエリアです。', THEME_NAME ),
      'before_widget' => '<aside id="%1$s" class="widget widget-footer-right %2$s">',
      'after_widget' => '</aside>',
      'before_title' => '<h3 class="widget-footer-right-title footer-title widget-title">',
      'after_title' => '</h3>',
    ));

  }
  endif;
  register_footer_right_widget_area();

  if ( !function_exists( 'register_footer_mobile_widget_area' ) ):
  function register_footer_mobile_widget_area(){
    register_sidebars(1,
      array(
      'name' => __( 'フッター（モバイル用）', THEME_NAME ),
      'id' => 'footer-mobile',
      'description' => __( 'モバイルで表示するウィジェットエリアです。834px以下で表示されます。', THEME_NAME ),
      'before_widget' => '<aside id="%1$s" class="widget widget-footer-mobile %2$s">',
      'after_widget' => '</aside>',
      'before_title' => '<h3 class="widget-footer-mobile-title footer-title widget-title">',
      'after_title' => '</h3>',
    ));

  }
  endif;
  register_footer_mobile_widget_area();

  if ( !function_exists( 'register_404_page_widget_area' ) ):
  function register_404_page_widget_area(){
    register_sidebars(1,
      array(
      'name' => __( '404ページ', THEME_NAME ),
      'id' => '404-page',
      'description' => __( '404ページをカスタマイズするためのウィジェットエリアです。', THEME_NAME ),
      'before_widget' => '<div id="%1$s" class="widget widget-404-page %2$s">',
      'after_widget' => '</div>',
      'before_title' => '<div class="widget-404-page-title widget-title">',
      'after_title' => '</div>',
    ));
  }
  endif;
  register_404_page_widget_area();
});




/////////////////////////////////////
// 投稿本文中にウィジェットを表示する
/////////////////////////////////////
add_filter('the_content','add_widget_area_before_1st_h2_in_single', BEFORE_1ST_H2_WIDGET_PRIORITY_STANDARD);
if ( !function_exists( 'add_widget_area_before_1st_h2_in_single' ) ):
function add_widget_area_before_1st_h2_in_single($the_content) {
  // if ( is_amp() ) {
  //   return $the_content;
  // }
  if ( is_single() && //投稿ページのとき
       is_active_sidebar( 'single-content-middle' ) && //ウィジェットが設定されているとき
       !is_multi_paged() //マルチページの2ページ目以降でない場合
  ) {
    //広告（AdSense）タグを記入
    ob_start();//バッファリング
    //echo '<div id="single-content-middle" class="widgets">';
    dynamic_sidebar( 'single-content-middle' );;//本文中ウィジェットの表示
    //echo '</div>';
    $ad_template = ob_get_clean();
    $h2result = get_h2_included_in_body( $the_content );//本文にH2タグが含まれていれば取得
    if ( $h2result ) {//H2見出しが本文中にある場合のみ
      //最初のH2の手前に広告を挿入（最初のH2を置換）
      $count = 1;
      $the_content = preg_replace(H2_REG, $ad_template.PHP_EOL.PHP_EOL.$h2result, $the_content, 1);
    }
  }
  return $the_content;
}
endif;

/////////////////////////////////////
// 固定ページ本文中にウィジェットを表示する
/////////////////////////////////////
add_filter('the_content','add_widget_area_before_1st_h2_in_page', BEFORE_1ST_H2_AD_PRIORITY_STANDARD);
if ( !function_exists( 'add_widget_area_before_1st_h2_in_page' ) ):
function add_widget_area_before_1st_h2_in_page($the_content) {
  // if ( is_amp() ) {
  //   return $the_content;
  // }

  if ( is_page() && //固定ページのとき
       is_active_sidebar( 'page-content-middle' ) && //ウィジェットが設定されているとき
       !is_multi_paged() //マルチページの2ページ目以降でない場合
  ) {
    //広告（AdSense）タグを記入
    ob_start();//バッファリング
    //echo '<div id="page-content-middle" class="widgets">';
    dynamic_sidebar( 'page-content-middle' );;//本文中ウィジェットの表示
    //echo '</div>';
    $ad_template = ob_get_clean();
    $h2result = get_h2_included_in_body( $the_content );//本文にH2タグが含まれていれば取得
    if ( $h2result ) {//H2見出しが本文中にある場合のみ
      //最初のH2の手前に広告を挿入（最初のH2を置換）
      $count = 1;
      $the_content = preg_replace(H2_REG, $ad_template.PHP_EOL.PHP_EOL.$h2result, $the_content, 1);
    }
  }
  return $the_content;
}
endif;

