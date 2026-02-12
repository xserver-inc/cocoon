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
  //カスタム投稿タイプに登録
  add_meta_box_custom_post_types( 'singular_memo_settings',__( 'メモ', THEME_NAME ), 'view_memo_custom_box', 'custom_post', 'side' );
}
endif;


///////////////////////////////////////
// 表示
///////////////////////////////////////
if ( !function_exists( 'view_memo_custom_box' ) ):
function view_memo_custom_box(){
  // CSRF対策用のnonceフィールドを出力
  wp_nonce_field('cocoon_memo_custom_box', 'cocoon_memo_custom_box_nonce');
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
  // nonce検証（CSRF対策）
  if (!isset($_POST['cocoon_memo_custom_box_nonce']) || !wp_verify_nonce($_POST['cocoon_memo_custom_box_nonce'], 'cocoon_memo_custom_box')) return;
  // 自動保存時はスキップ
  if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
  // 投稿の編集権限をチェック
  if (!current_user_can('edit_post', $post_id)) return;
  if (isset($_POST['the_page_memo'])) {
    $the_page_memo = sanitize_textarea_field($_POST['the_page_memo']);
    update_post_meta( $post_id, 'the_page_memo', $the_page_memo );
  }
}
endif;


//メモ記入欄を取得
if ( !function_exists( 'get_the_page_memo' ) ):
function get_the_page_memo($post_id = null){
  if (!$post_id) {
    $post_id = get_the_ID();
  }
  $value = get_post_meta($post_id, 'the_page_memo', true);
  return $value;
}
endif;
