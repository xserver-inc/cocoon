<?php //SNS画像カスタムフィールドを設置する
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
add_action('admin_menu', 'add_sns_image_custom_box');
if ( !function_exists( 'add_sns_image_custom_box' ) ):
function add_sns_image_custom_box(){

  //SNS画像
  add_meta_box( 'singular_sns_image_settings',__( 'SNS画像', THEME_NAME ), 'sns_image_custom_box_view', 'post', 'side' );
  add_meta_box( 'singular_sns_image_settings',__( 'SNS画像', THEME_NAME ), 'sns_image_custom_box_view', 'page', 'side' );
  //カスタム投稿タイプに登録
  add_meta_box_custom_post_types( 'singular_sns_image_settings',__( 'SNS画像', THEME_NAME ), 'sns_image_custom_box_view', 'custom_post', 'side' );
}
endif;

///////////////////////////////////////
// SNS画像
///////////////////////////////////////
if ( !function_exists( 'sns_image_custom_box_view' ) ):
function sns_image_custom_box_view(){
  // CSRF対策用のnonceフィールドを出力
  wp_nonce_field('cocoon_sns_image_custom_box', 'cocoon_sns_image_custom_box_nonce');
  $sns_image_url = get_post_meta(get_the_ID(),'sns_image_url', true);

  generate_upload_image_tag('sns_image_url', $sns_image_url);
  generate_howto_tag(__( 'FacebookやXなど、SNSでシェアする画像を設定します。未設定の場合はアイキャッチが利用されます。', THEME_NAME ), 'sns_image_url');

}
endif;

add_action('save_post', 'sns_image_custom_box_save_data');
if ( !function_exists( 'sns_image_custom_box_save_data' ) ):
function sns_image_custom_box_save_data($post_id){
  // nonce検証（CSRF対策）
  if (!isset($_POST['cocoon_sns_image_custom_box_nonce']) || !wp_verify_nonce($_POST['cocoon_sns_image_custom_box_nonce'], 'cocoon_sns_image_custom_box')) return;
  // 自動保存時はスキップ
  if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
  // 投稿の編集権限をチェック
  if (!current_user_can('edit_post', $post_id)) return;
  $id = $post_id;
  //SNS画像
  if ( isset( $_POST['sns_image_url'] ) ){
    $sns_image_url = esc_url_raw($_POST['sns_image_url']);
    $sns_image_url_key = 'sns_image_url';
    add_post_meta($id, $sns_image_url_key, $sns_image_url, true);
    update_post_meta($id, $sns_image_url_key, $sns_image_url);
  }
}
endif;

//SNS画像の取得
if ( !function_exists( 'get_singular_sns_image_url' ) ):
function get_singular_sns_image_url(){
  return trim(get_post_meta(get_the_ID(), 'sns_image_url', true));
}
endif;





