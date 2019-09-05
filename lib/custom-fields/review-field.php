<?php //レビューカスタムフィールドを設置する
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
add_action('admin_menu', 'add_review_custom_box');
if ( !function_exists( 'add_review_custom_box' ) ):
function add_review_custom_box(){

  //レビュー
  add_meta_box( 'singular_review_settings',__( 'レビュー', THEME_NAME ), 'review_custom_box_view', 'post', 'side' );
  add_meta_box( 'singular_review_settings',__( 'レビュー', THEME_NAME ), 'review_custom_box_view', 'page', 'side' );
  //カスタム投稿タイプに登録
  add_meta_box_custom_post_types( 'singular_review_settings',__( 'レビュー', THEME_NAME ), 'review_custom_box_view', 'custum_post', 'side' );
}
endif;

///////////////////////////////////////
// レビュー
///////////////////////////////////////
if ( !function_exists( 'review_custom_box_view' ) ):
function review_custom_box_view(){


  //AMPの除外
  $the_review_enable = is_the_review_enable();
  generate_checkbox_tag('the_review_enable' , $the_review_enable, __( '評価を表示する', THEME_NAME ));
  generate_howro_tag(__( 'レビュー構造化データを出力するか。', THEME_NAME ).get_help_page_tag('https://wp-cocoon.com/review-structured-data/'));

  //対象
  $the_review_name = get_the_review_name();
  generate_label_tag('the_review_name', __('レビュー対象', THEME_NAME) );
  generate_textbox_tag('the_review_name', $the_review_name, '');
  generate_howro_tag(__( 'レビュー対象名を入力。※必須', THEME_NAME ));

  //レート
  $the_review_rate = get_the_review_rate();
  if (!$the_review_rate) {
    $the_review_rate = 5;
  }
  generate_label_tag('the_review_rate', __('レビュー評価', THEME_NAME) );
  generate_range_tag('the_review_rate',$the_review_rate, 0, 5, 0.5);
  generate_howro_tag(__( '0から5の範囲で評価を入力。', THEME_NAME ));

}
endif;

add_action('save_post', 'review_custom_box_save_data');
if ( !function_exists( 'review_custom_box_save_data' ) ):
function review_custom_box_save_data(){
  $id = get_the_ID();
  //有効/無効
  $the_review_enable = !empty($_POST['the_review_enable']) ? 1 : 0;
  $the_review_enable_key = 'the_review_enable';
  add_post_meta($id, $the_review_enable_key, $the_review_enable, true);
  update_post_meta($id, $the_review_enable_key, $the_review_enable);

  //名前
  if ( isset( $_POST['the_review_name'] ) ){
    $the_review_name = $_POST['the_review_name'];
    $the_review_name_key = 'the_review_name';
    add_post_meta($id, $the_review_name_key, $the_review_name, true);
    update_post_meta($id, $the_review_name_key, $the_review_name);
  }

  //レート
  if ( isset( $_POST['the_review_rate'] ) ){
    $the_review_rate = $_POST['the_review_rate'];
    $the_review_rate_key = 'the_review_rate';
    add_post_meta($id, $the_review_rate_key, $the_review_rate, true);
    update_post_meta($id, $the_review_rate_key, $the_review_rate);
  }
}
endif;

//名前
if ( !function_exists( 'get_the_review_name' ) ):
function get_the_review_name(){
  return trim(get_post_meta(get_the_ID(), 'the_review_name', true));
}
endif;

//レート
if ( !function_exists( 'get_the_review_rate' ) ):
function get_the_review_rate(){
  return get_post_meta(get_the_ID(), 'the_review_rate', true);
}
endif;

//レビューが有効か
if ( !function_exists( 'is_the_review_enable' ) ):
function is_the_review_enable(){
  return get_post_meta(get_the_ID(), 'the_review_enable', true);
}
endif;

//ページのレビューが有効か
if ( !function_exists( 'is_the_page_review_enable' ) ):
function is_the_page_review_enable(){
  return is_the_review_enable() && get_the_review_name();
}
endif;





