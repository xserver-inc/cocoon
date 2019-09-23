<?php //広告カスタムフィールドを設置する
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
add_action('admin_menu', 'add_ad_custom_box');
if ( !function_exists( 'add_ad_custom_box' ) ):
function add_ad_custom_box(){
  //広告ボックス
  add_meta_box( 'singular_ad_settings',__( '広告設定', THEME_NAME ), 'view_ad_custom_box', 'post', 'side' );
  add_meta_box( 'singular_ad_settings',__( '広告設定', THEME_NAME ), 'view_ad_custom_box', 'page', 'side' );
  //カスタム投稿タイプに登録
  add_meta_box_custom_post_types( 'singular_ad_settings',__( '広告設定', THEME_NAME ), 'view_ad_custom_box', 'custum_post', 'side' );
}
endif;


///////////////////////////////////////
// AdSenseの設定
///////////////////////////////////////
if ( !function_exists( 'view_ad_custom_box' ) ):
function view_ad_custom_box(){
  //広告を表示する
  generate_checkbox_tag('the_page_ads_novisible' , is_the_page_ads_novisible(), __( '広告を除外する', THEME_NAME ));
  generate_howto_tag(__( 'ページ上の広告（AdSenseなど）表示を切り替えます。「広告」設定からカテゴリごとの設定も行えます。', THEME_NAME ), 'the_page_ads_novisible');
}
endif;


add_action('save_post', 'ad_custom_box_save_data');
if ( !function_exists( 'ad_custom_box_save_data' ) ):
function ad_custom_box_save_data(){
  $id = get_the_ID();

  //広告の除外
  $the_page_ads_novisible = !empty($_POST['the_page_ads_novisible']) ? 1 : 0;
  $the_page_ads_novisible_key = 'the_page_ads_novisible';
  add_post_meta($id, $the_page_ads_novisible_key, $the_page_ads_novisible, true);
  update_post_meta($id, $the_page_ads_novisible_key, $the_page_ads_novisible);

  if (is_migrate_from_simplicity()) {
    add_post_meta($id, 'is_ads_removed_in_page', $the_page_ads_novisible, true);
    update_post_meta($id, 'is_ads_removed_in_page', $the_page_ads_novisible);
  }

}
endif;


//広告を除外しているか
if ( !function_exists( 'is_the_page_ads_novisible' ) ):
function is_the_page_ads_novisible(){
  $value = 0;
  if (is_singular() || is_admin()) {
    $value = get_post_meta(get_the_ID(), 'the_page_ads_novisible', true);
    if (is_migrate_from_simplicity()){
      $simplicity_value = get_post_meta(get_the_ID(), 'is_ads_removed_in_page', true) ? 1 : 0;
      $value = $value ? $value : $simplicity_value;
    }
  }
  return $value;
}
endif;


//広告を表示するか
if ( !function_exists( 'is_the_page_ads_visible' ) ):
function is_the_page_ads_visible(){
  return !is_the_page_ads_novisible();
}
endif;
