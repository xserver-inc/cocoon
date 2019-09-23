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
  add_meta_box_custom_post_types( 'singular_sns_image_settings',__( 'SNS画像', THEME_NAME ), 'sns_image_custom_box_view', 'custum_post', 'side' );
}
endif;

///////////////////////////////////////
// SNS画像
///////////////////////////////////////
if ( !function_exists( 'sns_image_custom_box_view' ) ):
function sns_image_custom_box_view(){
  $sns_image_url = get_post_meta(get_the_ID(),'sns_image_url', true);

  generate_upload_image_tag('sns_image_url', $sns_image_url);
  generate_howto_tag(__( 'FacebookやTwitterなど、SNSでシェアする画像を設定します。未設定の場合はアイキャッチが利用されます。', THEME_NAME ), 'sns_image_url');

}
endif;

add_action('save_post', 'sns_image_custom_box_save_data');
if ( !function_exists( 'sns_image_custom_box_save_data' ) ):
function sns_image_custom_box_save_data(){
  $id = get_the_ID();
  //SNS画像
  if ( isset( $_POST['sns_image_url'] ) ){
    $sns_image_url = $_POST['sns_image_url'];
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





