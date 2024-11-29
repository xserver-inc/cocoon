<?php //その他カスタムフィールドを設置する
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

///////////////////////////////////////
// カスタムボックスの追加
///////////////////////////////////////
add_action('admin_menu', 'add_other_custom_box');
if ( !function_exists( 'add_other_custom_box' ) ):
function add_other_custom_box(){
  //その他ボックス
  add_meta_box( 'singular_other_settings',__( 'その他', THEME_NAME ), 'view_other_custom_box', 'post', 'side' );
  // add_meta_box( 'singular_other_settings',__( 'その他', THEME_NAME ), 'view_other_custom_box', 'page', 'side' );
  //カスタム投稿タイプに登録
  add_meta_box_custom_post_types( 'singular_other_settings',__( 'その他', THEME_NAME ), 'view_other_custom_box', 'page', 'side' );
}
endif;


///////////////////////////////////////
// その他の設定
///////////////////////////////////////
if ( !function_exists( 'view_other_custom_box' ) ):
function view_other_custom_box(){
  //アーカイブ除外
  generate_checkbox_tag('the_page_no_archive' , is_the_page_no_archive(), __( 'アーカイブに出力しない', THEME_NAME ));
  generate_howto_tag(__( 'チェックを入れると、この記事はインデックスページ等のアーカイブに表示されなくなります。', THEME_NAME ).get_help_page_tag('https://wp-cocoon.com/do-not-output-to-archives/'), 'the_page_no_archive');

  //RSS除外
  generate_checkbox_tag('the_page_no_rss' , is_the_page_no_rss(), __( 'フィードに出力しない', THEME_NAME ));
  generate_howto_tag(__( 'チェックを入れると、この記事はRSS・Atomのフィードに出力されなくなります。', THEME_NAME ).get_help_page_tag('https://wp-cocoon.com/do-not-output-post-to-feed/'), 'the_page_no_rss');
}
endif;


add_action('save_post', 'other_custom_box_save_data');
if ( !function_exists( 'other_custom_box_save_data' ) ):
function other_custom_box_save_data(){
  $id = get_the_ID();

  //アーカイブページから除外
  $the_page_no_archive = !empty($_POST['the_page_no_archive']) ? 1 : 0;
  $the_page_no_archive_key = 'the_page_no_archive';
  add_post_meta($id, $the_page_no_archive_key, $the_page_no_archive, true);
  update_post_meta($id, $the_page_no_archive_key, $the_page_no_archive);

  //RSSから除外
  $the_page_no_rss = !empty($_POST['the_page_no_rss']) ? 1 : 0;
  $the_page_no_rss_key = 'the_page_no_rss';
  add_post_meta($id, $the_page_no_rss_key, $the_page_no_rss, true);
  update_post_meta($id, $the_page_no_rss_key, $the_page_no_rss);
}
endif;


//アーカイブを除外しているか
if ( !function_exists( 'is_the_page_no_archive' ) ):
function is_the_page_no_archive(){
  $value = get_post_meta(get_the_ID(), 'the_page_no_archive', true);

  return $value;
}
endif;

//このページをアーカイブに表示するか
if ( !function_exists( 'is_the_page_archive_visible' ) ):
function is_the_page_archive_visible(){
  return !is_the_page_no_archive();
}
endif;

//アーカイブ除外IDの取得
if ( !function_exists( 'get_archive_exclude_post_ids' ) ):
function get_archive_exclude_post_ids(){
  global $wpdb;
  $res = $wpdb->get_results("SELECT DISTINCT GROUP_CONCAT(post_id) AS ids FROM {$wpdb->prefix}postmeta WHERE (meta_key = 'the_page_no_archive') AND (meta_value = 1)");
  $result = (isset($res[0]) && $res[0]->ids) ? explode(',', $res[0]->ids) : array();
  return $result;
}
endif;
// var_dump(get_archive_exclude_post_ids());


//RSSを除外しているか
if ( !function_exists( 'is_the_page_no_rss' ) ):
function is_the_page_no_rss(){
  $value = get_post_meta(get_the_ID(), 'the_page_no_rss', true);

  return $value;
}
endif;

//このページをRSSに表示するか
if ( !function_exists( 'is_the_page_rss_visible' ) ):
function is_the_page_rss_visible(){
  return !is_the_page_no_rss();
}
endif;

//RSS除外IDの取得
if ( !function_exists( 'get_rss_exclude_post_ids' ) ):
function get_rss_exclude_post_ids(){
  return get_postmeta_value_enable_post_ids('the_page_no_rss');
}
endif;
