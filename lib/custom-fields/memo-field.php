<?php //メモカスタムフィールドを設置する
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
add_action('admin_menu', 'add_memo_custom_box');
if ( !function_exists( 'add_memo_custom_box' ) ):
function add_memo_custom_box(){
  //広告ボックス
  add_meta_box( 'singular_memo_settings',__( 'メモ', THEME_NAME ), 'view_memo_custom_box', 'post', 'side' );
  add_meta_box( 'singular_memo_settings',__( 'メモ', THEME_NAME ), 'view_memo_custom_box', 'page', 'side' );
  //add_meta_box( 'singular_memo_settings',__( 'メモ', THEME_NAME ), 'view_memo_custom_box', 'topic', 'side' );
  //カスタム投稿タイプに登録
  add_meta_box_custom_post_types( 'singular_memo_settings',__( 'メモ', THEME_NAME ), 'view_memo_custom_box', 'custum_post', 'side' );
}
endif;


///////////////////////////////////////
// 表示
///////////////////////////////////////
if ( !function_exists( 'view_memo_custom_box' ) ):
function view_memo_custom_box(){
  //メモ記入欄
  generate_textarea_tag('the_page_memo', get_the_page_memo(), '') ;
  generate_howto_tag(__( 'この投稿に記録しておきたいメモがある場合は記入してください。', THEME_NAME ).get_help_page_tag('https://wp-cocoon.com/memo/'), 'the_page_memo');
}
endif;

///////////////////////////////////////
// 保存
///////////////////////////////////////
add_action('save_post', 'memo_custom_box_save_data');
if ( !function_exists( 'memo_custom_box_save_data' ) ):
function memo_custom_box_save_data($post_id){
  if (isset($_POST['the_page_memo'])) {
    $the_page_memo = $_POST['the_page_memo'];
    update_post_meta( $post_id, 'the_page_memo', $the_page_memo );
  }
}
endif;


//広告を除外しているか
if ( !function_exists( 'get_the_page_memo' ) ):
function get_the_page_memo($post_id = null){
  if (!$post_id) {
    $post_id = get_the_ID();
  }
  $value = get_post_meta($post_id, 'the_page_memo', true);
  return $value;
}
endif;
