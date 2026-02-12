<?php //リダイレクトカスタムフィールドを設置する
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
add_action('admin_menu', 'add_redirect_custom_box');
if ( !function_exists( 'add_redirect_custom_box' ) ):
function add_redirect_custom_box(){

  //リダイレクト
  add_meta_box( 'singular_redirect_settings',__( 'リダイレクト', THEME_NAME ), 'redirect_custom_box_view', 'post', 'side' );
  add_meta_box( 'singular_redirect_settings',__( 'リダイレクト', THEME_NAME ), 'redirect_custom_box_view', 'page', 'side' );
  //カスタム投稿タイプに登録
  add_meta_box_custom_post_types( 'singular_redirect_settings',__( 'リダイレクト', THEME_NAME ), 'redirect_custom_box_view', 'custom_post', 'side' );
}
endif;

///////////////////////////////////////
// リダイレクト
///////////////////////////////////////
if ( !function_exists( 'redirect_custom_box_view' ) ):
function redirect_custom_box_view(){
  // CSRF対策用のnonceフィールドを出力
  wp_nonce_field('cocoon_redirect_custom_box', 'cocoon_redirect_custom_box_nonce');
  $redirect_url = get_post_meta(get_the_ID(),'redirect_url', true);

  generate_label_tag('redirect_url', __('リダイレクトURL', THEME_NAME) );
  generate_textbox_tag('redirect_url', $redirect_url, __( 'https://', THEME_NAME ));
  generate_howto_tag(__( 'このページに訪れるユーザーを設定したURLに301リダイレクトします。', THEME_NAME ), 'redirect_url');

}
endif;

add_action('save_post', 'redirect_custom_box_save_data');
if ( !function_exists( 'redirect_custom_box_save_data' ) ):
function redirect_custom_box_save_data($post_id){
  // nonce検証（CSRF対策）
  if (!isset($_POST['cocoon_redirect_custom_box_nonce']) || !wp_verify_nonce($_POST['cocoon_redirect_custom_box_nonce'], 'cocoon_redirect_custom_box')) return;
  // 自動保存時はスキップ
  if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
  // 投稿の編集権限をチェック
  if (!current_user_can('edit_post', $post_id)) return;
  $id = $post_id;
  //リダイレクトURL
  if ( isset( $_POST['redirect_url'] ) ){
    $redirect_url = esc_url_raw($_POST['redirect_url']);
    $redirect_url_key = 'redirect_url';
    add_post_meta($id, $redirect_url_key, $redirect_url, true);
    update_post_meta($id, $redirect_url_key, $redirect_url);
  }
}
endif;

//リダイレクトURLの取得
if ( !function_exists( 'get_singular_redirect_url' ) ):
function get_singular_redirect_url(){
  return trim(get_post_meta(get_the_ID(), 'redirect_url', true));
}
endif;





