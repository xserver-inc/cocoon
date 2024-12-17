<?php //ウィジェットの表示制御
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

global $_ALL_USER_COUNT;
$_ALL_USER_COUNT = intval($wpdb->get_var("SELECT COUNT(ID) FROM $wpdb->users"));

//ウィジェットはD&Dされたものか
if ( !function_exists( 'is_widget_dropped' ) ):
function is_widget_dropped($widget){
  return $widget->number == '__i__';
}
endif;

if ( !function_exists( 'get_dropped_widget_id' ) ):
function get_dropped_widget_id($widget){
  $widget_id = $widget->id;
  $widget_number = 0;
  //ウィジェットをD&Dでエリアにドロップ時スクデットナンバーを取得できないときに無理やり取得する
  if (is_widget_dropped($widget)) {
    foreach( $widget->get_settings() as $index => $settings ) {
      $widget_number = $index + 1;
    }
    $widget_id = str_replace('__i__', $widget_number, $widget_id);
    $widget_id = intval($widget_id);
  }
  return $widget_id;
}
endif;

if ( !function_exists( 'get_dropped_widget' ) ):
function get_dropped_widget($widget){
  //ウィジェットをD&Dでエリアにドロップ時スクデットナンバーを取得できないときに無理やり取得する
  if (is_widget_dropped($widget)) {
    $widget_number = 0;
    foreach( $widget->get_settings() as $index => $settings ) {
      $widget_number = intval($index) + 1;
    }
    $widget->number = intval($widget_number);
    $widget->id = str_replace('__i__', $widget_number, $widget->id);
  }
  return $widget;
}
endif;

///////////////////////////////////////
// ウィジェットフォーム
///////////////////////////////////////
add_filter( 'in_widget_form', 'display_widgets_in_widget_form', 10, 3 );
if ( !function_exists( 'display_widgets_in_widget_form' ) ):
function display_widgets_in_widget_form( $widget, $return, $instance ){

  $info = display_widgets_info_by_id( $widget->id );

  //値の初期化
  $widget_action_def = 'hide';
  $widget_categories_def = array();
  $widget_pages_def = array();
  $widget_authors_def = array();
  $widget_posts_def = '';
  $widget_fixed_pages_def = '';
  $widget_tags_def = '';
  $widget_custom_post_types_def = array();
  if ($info) {
    if (isset($info['widget_action'])) {
      $widget_action_def = $info['widget_action'];
    }
    if (isset($info['widget_categories'])) {
      $widget_categories_def = $info['widget_categories'];
    }
    if (isset($info['widget_pages'])) {
      $widget_pages_def = $info['widget_pages'];
    }
    if (isset($info['widget_authors'])) {
      $widget_authors_def = $info['widget_authors'];
    }
    if (!empty($info['widget_posts'])) {
      $widget_posts_def = $info['widget_posts'];
    }
    if (!empty($info['widget_fixed_pages'])) {
      $widget_fixed_pages_def = $info['widget_fixed_pages'];
    }
    if (!empty($info['widget_tags'])) {
      $widget_tags_def = $info['widget_tags'];
    }
    if (!empty($info['widget_custom_post_types'])) {
      $widget_custom_post_types_def = $info['widget_custom_post_types'];
    }
  }

  $widget_action = isset( $instance['widget_action'] ) ? $instance['widget_action'] : $widget_action_def;
  $widget_categories = isset( $instance['widget_categories'] ) ? $instance['widget_categories'] : $widget_categories_def;
  $widget_pages = isset( $instance['widget_pages'] ) ? $instance['widget_pages'] : $widget_pages_def;
  $widget_authors = isset( $instance['widget_authors'] ) ? $instance['widget_authors'] : $widget_authors_def;
  $widget_posts = !empty( $instance['widget_posts'] ) ? $instance['widget_posts'] : $widget_posts_def;
  $widget_fixed_pages = !empty( $instance['widget_fixed_pages'] ) ? $instance['widget_fixed_pages'] : $widget_fixed_pages_def;
  $widget_tags = !empty( $instance['widget_tags'] ) ? $instance['widget_tags'] : $widget_tags_def;
  $widget_custom_post_types = !empty( $instance['widget_custom_post_types'] ) ? $instance['widget_custom_post_types'] : $widget_custom_post_types_def;

  ?>
  <?php
  //ウィジェットIDを取得
  $widget_id = $widget->id;
  //フィールドID
  $field_id = $widget->get_field_id('toggle-link');
  //ウィジェットナンバーを取得
  $widget_number = $widget->number;

  $toggle_name = 'tlink-'.$widget_id;
  $checkbox_id = 'toggle-checkbox-'.$widget_id;

   ?>
  <div class="toggle-wrap">
    <label class="toggle-button display-widgets-toggle" id="<?php echo $toggle_name; ?>" for="<?php echo $checkbox_id; ?>"><?php _e( '表示設定', THEME_NAME ); ?></label>
    <input type="checkbox" id="<?php echo $checkbox_id; ?>">
    <div class="display-widgets-area toggle-content">
      <label for="<?php echo $widget->get_field_id('widget_action'); ?>">
        <?php _e('ウィジェットの表示', THEME_NAME) ?>
      </label>
      <?php
        $options = array(
          'hide' => __( 'チェック・入力したページで非表示にする', THEME_NAME ),
          'show' => __( 'チェック・入力したページで表示する', THEME_NAME ),
        );
        generate_selectbox_tag($widget->get_field_name('widget_action'), $options, $widget_action);
        $dw =get_dropped_widget($widget);
        //var_dump($dw);
        //$widget_style_id = get_dropped_widget_id($widget);
        $cat_tab_id = 'cat-tab-'.$widget_id;
        $page_tab_id = 'page-tab-'.$widget_id;
        $author_tab_id = 'author-tab-'.$widget_id;
        $post_tab_id = 'post-tab-'.$widget_id;
        $fixed_page_tab_id = 'fixed-page-tab-'.$widget_id;
        $tag_tab_id = 'tag-tab-'.$widget_id;
        $custom_post_type_tab_id = 'custom-post-type-tab-'.$widget_id;
      ?>
      <style type="text/css">
        /*選択されているタブのコンテンツのみを表示*/
        #cat-tab-<?php echo $dw->id; ?>:checked ~ .category-check-list,
        #page-tab-<?php echo $dw->id; ?>:checked ~ .page-display-check-list,
        #author-tab-<?php echo $dw->id; ?>:checked ~ .author-check-list,
        #post-tab-<?php echo $dw->id; ?>:checked ~ .post-check-list,
        #fixed-page-tab-<?php echo $dw->id; ?>:checked ~ .fixed-page-check-list,
        #tag-tab-<?php echo $dw->id; ?>:checked ~ .tag-check-list,
        #custom-post-type-tab-<?php echo $dw->id; ?>:checked ~ .custom-post-type-check-list {
          display: block;
        }
      </style>
      <div id="tabs-<?php echo $widget_id; ?>" class="tabs">
        <input id="<?php echo $cat_tab_id; ?>" type="radio" name="tab_item" checked>
        <label id="cat-<?php echo $widget_id; ?>" class="cat-tab tab-item" for="<?php echo $cat_tab_id; ?>"><?php _e( 'カテゴリー', THEME_NAME ) ?></label>

        <input id="<?php echo $page_tab_id; ?>" type="radio" name="tab_item">
        <label id="page-<?php echo $widget_id; ?>" class="page-tab tab-item" for="<?php echo $page_tab_id; ?>"><?php _e( 'ページ', THEME_NAME ) ?></label>

        <?php if (is_widget_authors_tab_visible()): ?>
        <input id="<?php echo $author_tab_id; ?>" type="radio" name="tab_item">
        <label id="author-<?php echo $widget_id; ?>" class="author-tab tab-item" for="<?php echo $author_tab_id; ?>"><?php _e( '投稿者', THEME_NAME ) ?></label>
        <?php endif ?>

        <input id="<?php echo $post_tab_id; ?>" type="radio" name="tab_item">
        <label id="post-<?php echo $widget_id; ?>" class="post-tab tab-item" for="<?php echo $post_tab_id; ?>"><?php _e( '投稿', THEME_NAME ) ?></label>

        <input id="<?php echo $fixed_page_tab_id; ?>" type="radio" name="tab_item">
        <label id="fixed-page-<?php echo $widget_id; ?>" class="fixed-page-tab tab-item" for="<?php echo $fixed_page_tab_id; ?>"><?php _e( '固定ページ', THEME_NAME ) ?></label>

        <input id="<?php echo $tag_tab_id; ?>" type="radio" name="tab_item">
        <label id="tag-<?php echo $widget_id; ?>" class="tag-tab tab-item" for="<?php echo $tag_tab_id; ?>"><?php _e( 'タグ', THEME_NAME ) ?></label>

        <input id="<?php echo $custom_post_type_tab_id; ?>" type="radio" name="tab_item">
        <label id="custom-post-type-<?php echo $widget_id; ?>" class="custom-post-type-tab tab-item" for="<?php echo $custom_post_type_tab_id; ?>"><?php _e( 'カスタム投稿タイプ', THEME_NAME ) ?></label>

        <?php
        generate_hierarchical_category_check_list(0, $widget->get_field_name('widget_categories'), $widget_categories);
        generate_page_display_check_list($widget->get_field_name('widget_pages'), $widget_pages);

        //著者タブ
        if (is_widget_authors_tab_visible()) {
          generate_author_check_list($widget->get_field_name('widget_authors'), $widget_authors);
        }

        generate_post_check_list($widget->get_field_name('widget_posts'), $widget_posts);

        generate_fixed_page_check_list($widget->get_field_name('widget_fixed_pages'), $widget_fixed_pages);

        generate_tag_check_list($widget->get_field_name('widget_tags'), $widget_tags);

        generate_custom_post_type_check_list($widget->get_field_name('widget_custom_post_types'), $widget_custom_post_types);

       ?>
      </div>
    </div>
  </div><!-- .toggle-wrap -->
  <?php
  return;
}
endif;

///////////////////////////////////////
// ウィジェット保存時のコールバック
///////////////////////////////////////
add_filter( 'widget_update_callback', 'display_widgets_update_callback', 10, 4);
if ( !function_exists( 'display_widgets_update_callback' ) ):
function display_widgets_update_callback( $instance, $new_instance, $old_instance, $this_widget ){
  //実行条件
  if ( isset( $new_instance['widget_action'] ) )
    $instance['widget_action'] = $new_instance['widget_action'];
  else
    $instance['widget_action'] = 'hide';

  //カテゴリ条件
  if ( isset( $new_instance['widget_categories'] ) )
    $instance['widget_categories'] = $new_instance['widget_categories'];
  else
    $instance['widget_categories'] = array();

  //ページ条件
  if ( isset( $new_instance['widget_pages'] ) )
    $instance['widget_pages'] = $new_instance['widget_pages'];
  else
    $instance['widget_pages'] = array();

  //投稿者条件
  if ( isset( $new_instance['widget_authors'] ) )
    $instance['widget_authors'] = $new_instance['widget_authors'];
  else
    $instance['widget_authors'] = array();

  //投稿ページ条件
  if ( isset( $new_instance['widget_posts'] ) )
    $instance['widget_posts'] = $new_instance['widget_posts'];
  else
    $instance['widget_posts'] = array();

  //固定ページ条件
  if ( isset( $new_instance['widget_fixed_pages'] ) )
    $instance['widget_fixed_pages'] = $new_instance['widget_fixed_pages'];
  else
    $instance['widget_fixed_pages'] = array();

  //タグ条件
  if ( isset( $new_instance['widget_tags'] ) )
    $instance['widget_tags'] = $new_instance['widget_tags'];
  else
    $instance['widget_tags'] = array();

  //カスタム投稿タイプ条件
  if ( isset( $new_instance['widget_custom_post_types'] ) )
    $instance['widget_custom_post_types'] = $new_instance['widget_custom_post_types'];
  else
    $instance['widget_custom_post_types'] = array();

  return $instance;
}
endif;

///////////////////////////////////////
// ウィジェットの表示制御
///////////////////////////////////////
if ( !function_exists( 'is_display_widgets_widget_visible' ) ):
function is_display_widgets_widget_visible( $info ){
  $widget_action = isset($info['widget_action']) ? $info['widget_action'] : 'hide';
  $widget_categories = isset($info['widget_categories']) ? $info['widget_categories'] : array();
  $widget_pages = isset($info['widget_pages']) ? $info['widget_pages'] : array();
  $widget_authors = isset($info['widget_authors']) ? $info['widget_authors'] : array();
  $widget_posts = isset($info['widget_posts']) ? $info['widget_posts'] : '';
  $widget_fixed_pages = isset($info['widget_fixed_pages']) ? $info['widget_fixed_pages'] : '';
  $widget_tags = isset($info['widget_tags']) ? $info['widget_tags'] : '';
  $widget_custom_post_types = isset($info['widget_custom_post_types']) ? $info['widget_custom_post_types'] : array();

  $display = false;

  // //チェックリストすべてが空かどうか
  $is_all_empty = empty($widget_categories) && empty($widget_pages) && empty($widget_authors) && empty($widget_posts) && empty($widget_fixed_pages) && empty($widget_tags) && empty($widget_custom_post_types);
  //カテゴリーリストに何かチェックがついている場合
  if (!empty($widget_categories)) {
    $display = $display || (in_category($widget_categories) && is_singular()) || is_category($widget_categories);
  }

  //ページリストに何かチェックがついている場合
  if (!empty($widget_pages)) {
    foreach ($widget_pages as $value) {
      switch ($value) {
        case 'is_front_page':
          $display = $display || (is_front_top_page());
          break;//break;は必要
        case 'is_single':
          $display = $display || is_single();
          break;
        case 'is_page':
          $display = $display || is_page();
          break;
        case 'is_category':
          $display = $display || is_category();
          break;
        case 'is_tag':
          $display = $display || is_tag();
          break;
        case 'is_author':
          $display = $display || is_author();
          break;
        case 'is_archive':
          $display = $display || is_archive();
          break;
        case 'is_search':
          $display = $display || is_search();
          break;
        case 'is_404':
          $display = $display || is_404();
          break;
        case 'is_amp':
          $display = $display || is_amp();
          break;
        case 'is_wpforo_plugin_page':
          $display = $display || is_wpforo_plugin_page();
          break;
        case 'is_bbpress_page':
          $display = $display || is_bbpress_page();
          break;
        case 'is_buddypress_page':
          $display = $display || is_buddypress_page();
          break;
        case 'is_plugin_fourm_page':
          $display = $display || is_plugin_fourm_page();
          break;
      }
    }
  }

  //投稿者リストに何かチェックがついている場合
  if (!empty($widget_authors)) {
    $display = $display || in_authors($widget_authors) || is_authors($widget_authors);
  }

  //投稿IDが設定されているとき
  if (!empty($widget_posts)) {
    $widget_posts = sanitize_comma_text($widget_posts);
    $widget_posts = explode(',', $widget_posts);
    $display = $display || is_single($widget_posts);
  }

  //固定ページIDが設定されているとき
  if (!empty($widget_fixed_pages)) {
    $widget_fixed_pages = sanitize_comma_text($widget_fixed_pages);
    $widget_fixed_pages = explode(',', $widget_fixed_pages);
    $display = $display || is_page($widget_fixed_pages);
  }

  //タグIDが設定されているとき
  if (!empty($widget_tags)) {
    $widget_tags = sanitize_comma_text($widget_tags);
    $widget_tags = explode(',', $widget_tags);
    $display = $display || (has_tag($widget_tags) && is_singular()) || is_tag($widget_tags);
  }

  //カスタム投稿タイプに何かチェックがついている場合
  if (!empty($widget_custom_post_types)) {
    $display = $display || is_singular($widget_custom_post_types);
  }

  //AMPページではドロップダウンウィジェットは表示しない
  if (is_amp()) {
    if (isset($info['dropdown']) && $info['dropdown']) {
      return false;
    }
  }

  //ウィジェットを表示する条件
  if ($widget_action == 'show') {
    if ($is_all_empty) {
      $display = false;
    } else {
      $display = $display;
    }
  } elseif ($widget_action == 'hide') {
    if ($is_all_empty) {
      $display = true;
    } else {
      $display = !$display;
    }
  }

  return $display;
}
endif;

///////////////////////////////////////
// ウィジェット表示フィルター（表示しない場合は取り除く）
///////////////////////////////////////
if (!is_admin()) {
  add_filter( 'sidebars_widgets', 'display_widgets_filter_sidebars_widgets', 10);
}
if ( !function_exists( 'display_widgets_filter_sidebars_widgets' ) ):
function display_widgets_filter_sidebars_widgets( $sidebars_widgets ){
  global $wp_reset_query_is_done, $wl_options, $wl_in_customizer;

  if ( $wl_in_customizer )
    return $sidebars_widgets;

  // reset any database queries done now that we're about to make decisions based on the context given in the WP query for the page
  if ( !empty( $wl_options['widget_logic-options-wp_reset_query'] ) && empty( $wp_reset_query_is_done ) )
  {
    wp_reset_query();
    $wp_reset_query_is_done = true;
  }

  // loop through every widget in every sidebar (barring 'wp_inactive_widgets') checking WL for each one
  foreach($sidebars_widgets as $widget_area => $widget_list)  {
    if ($widget_area=='wp_inactive_widgets' || empty($widget_list))
      continue;

    foreach($widget_list as $pos => $widget_id)    {
      //$logic = 'a';
      $info = display_widgets_info_by_id( $widget_id );
      //_v($info);

      if ( !is_display_widgets_widget_visible( $info ) )
        unset($sidebars_widgets[$widget_area][$pos]);
    }
  }
  return $sidebars_widgets;
}
endif;

///////////////////////////////////////
// ウィジェットの表示情報の取得
///////////////////////////////////////
if ( !function_exists( 'display_widgets_info_by_id' ) ):
function display_widgets_info_by_id( $widget_id ){
  global $wl_options;

  if ( preg_match( '/^(.+)-(\d+)$/', $widget_id, $m ) )  {
    $widget_class = $m[1];
    $widget_i = $m[2];

    $info = get_option( 'widget_'.$widget_class );
    if ( empty( $info[ $widget_i ] ) )
      return '';

    $info = $info[ $widget_i ];
  }
  else {
    $info = (array)get_option( 'widget_'.$widget_id, array() );
  }

  return $info;
}
endif;

//ウィジェットの著者タブ表示制御関数
if ( !function_exists( 'is_widget_authors_tab_visible' ) ):
function is_widget_authors_tab_visible(){
  global $_ALL_USER_COUNT;
  return $_ALL_USER_COUNT < 100;
}
endif;
