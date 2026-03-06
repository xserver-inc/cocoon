<?php //AMPカスタムフィールドを設置する
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
if (is_amp_enable()) {
  add_action('admin_menu', 'add_amp_custom_box');
}
if ( !function_exists( 'add_amp_custom_box' ) ):
function add_amp_custom_box(){
  //AMPボックス
  add_meta_box( 'singular_amp_settings',__( 'AMP', THEME_NAME ), 'view_amp_custom_box', 'post', 'side' );
  add_meta_box( 'singular_amp_settings',__( 'AMP', THEME_NAME ), 'view_amp_custom_box', 'page', 'side' );
  //カスタム投稿タイプに登録
  add_meta_box_custom_post_types( 'singular_amp_settings',__( 'AMP', THEME_NAME ), 'view_amp_custom_box', 'page', 'side' );
}
endif;


///////////////////////////////////////
// AMPの設定
///////////////////////////////////////
if ( !function_exists( 'view_amp_custom_box' ) ):
function view_amp_custom_box(){
  // CSRF対策用のnonceフィールドを出力
  wp_nonce_field('cocoon_amp_custom_box', 'cocoon_amp_custom_box_nonce');
  //AMPページを生成する
  generate_checkbox_tag('the_page_no_amp' , is_the_page_no_amp(), __( 'AMPページを生成しない', THEME_NAME ));
  generate_howto_tag(__( 'AMP(Accelerated Mobile Pages)ページを生成して、モバイル端末に最適化するかを切り替えます。', THEME_NAME ), 'the_page_no_amp');
}
endif;


add_action('save_post', 'amp_custom_box_save_data');
if ( !function_exists( 'amp_custom_box_save_data' ) ):
function amp_custom_box_save_data($post_id){
  // nonce検証（CSRF対策）
  if (!isset($_POST['cocoon_amp_custom_box_nonce']) || !wp_verify_nonce($_POST['cocoon_amp_custom_box_nonce'], 'cocoon_amp_custom_box')) return;
  // 自動保存時はスキップ
  if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
  // 投稿の編集権限をチェック
  if (!current_user_can('edit_post', $post_id)) return;
  $id = $post_id;

  //AMPの除外
  $the_page_no_amp = !empty($_POST['the_page_no_amp']) ? 1 : 0;
  $the_page_no_amp_key = 'the_page_no_amp';
  add_post_meta($id, $the_page_no_amp_key, $the_page_no_amp, true);
  update_post_meta($id, $the_page_no_amp_key, $the_page_no_amp);

  if (is_migrate_from_simplicity()) {
    add_post_meta($id, 'is_noamp', $the_page_no_amp, true);
    update_post_meta($id, 'is_noamp', $the_page_no_amp);
  }
}
endif;


//AMPを除外しているか
if ( !function_exists( 'is_the_page_no_amp' ) ):
function is_the_page_no_amp(){
  $value = get_post_meta(get_the_ID(), 'the_page_no_amp', true);

  if (is_migrate_from_simplicity()){
    $simplicity_value = get_post_meta(get_the_ID(), 'is_noamp', true) ? 1 : 0;
    $value = $value ? $value : $simplicity_value;
  }


  return $value;
}
endif;


//AMPを表示するか
if ( !function_exists( 'is_the_page_amp_enable' ) ):
function is_the_page_amp_enable(){
  return !is_the_page_no_amp();
}
endif;
