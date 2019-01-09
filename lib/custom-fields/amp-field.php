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
add_action('admin_menu', 'add_amp_custom_box');
if ( !function_exists( 'add_amp_custom_box' ) ):
function add_amp_custom_box(){
  //AMPボックス
  add_meta_box( 'singular_amp_settings',__( 'AMP設定', THEME_NAME ), 'view_amp_custom_box', 'post', 'side' );
  add_meta_box( 'singular_amp_settings',__( 'AMP設定', THEME_NAME ), 'view_amp_custom_box', 'page', 'side' );
  //カスタム投稿タイプに登録
  add_meta_box_custom_post_types( 'singular_amp_settings',__( 'AMP設定', THEME_NAME ), 'view_amp_custom_box', 'page', 'side' );
}
endif;


///////////////////////////////////////
// AMPの設定
///////////////////////////////////////
if ( !function_exists( 'view_amp_custom_box' ) ):
function view_amp_custom_box(){
  // $the_page_no_amp = is_the_page_no_amp();

  // echo '<label><input type="checkbox" name="the_page_no_amp"';
  // if( $the_page_no_amp ){echo " checked";}
  // echo '>'.__( 'AMP表示しない', THEME_NAME ).'</label>';
  // echo '<p class="howto">'.__( 'チェックを付けたページのAMPを無効にします。。', THEME_NAME ).'</p>';

  //AMPページを生成する
  generate_checkbox_tag('the_page_no_amp' , is_the_page_no_amp(), __( 'AMPページを生成しない', THEME_NAME ));
  generate_howro_tag(__( 'AMP(Accelerated Mobile Pages)ページを生成して、モバイル端末に最適化するかを切り替えます。', THEME_NAME ));
}
endif;


add_action('save_post', 'amp_custom_box_save_data');
if ( !function_exists( 'amp_custom_box_save_data' ) ):
function amp_custom_box_save_data(){
  $id = get_the_ID();

  //AMPの除外
  $the_page_no_amp = !empty($_POST['the_page_no_amp']) ? 1 : 0;
  $the_page_no_amp_key = 'the_page_no_amp';
  add_post_meta($id, $the_page_no_amp_key, $the_page_no_amp, true);
  update_post_meta($id, $the_page_no_amp_key, $the_page_no_amp);

  if (is_migrate_from_simplicity()) {
    add_post_meta($id, 'is_noamp', $the_page_no_amp, true);
    update_post_meta($id, 'is_noamp', $the_page_no_amp);
  }

  // //AMPの除外
  // $the_page_amp_enable = !empty($_POST['the_page_amp_enable']) ? 1 : 0;
  // $the_page_amp_enable_key = 'the_page_amp_enable';
  // add_post_meta($id, $the_page_amp_enable_key, $the_page_amp_enable, true);
  // update_post_meta($id, $the_page_amp_enable_key, $the_page_amp_enable);
  // if (is_migrate_from_simplicity()) {
  //   add_post_meta($id, 'is_noamp', !$the_page_amp_enable, true);
  //   update_post_meta($id, 'is_noamp', !$the_page_amp_enable);
  // }
}
endif;


//AMPを除外しているか
if ( !function_exists( 'is_the_page_no_amp' ) ):
function is_the_page_no_amp(){
  $value = get_post_meta(get_the_ID(), 'the_page_no_amp', true);

  if (is_migrate_from_simplicity())
    $value = $value ? $value : get_post_meta(get_the_ID(), 'is_noamp', true);

  return $value;
}
endif;


//AMPを表示するか
if ( !function_exists( 'is_the_page_amp_enable' ) ):
function is_the_page_amp_enable(){
  return !is_the_page_no_amp();
}
endif;

// //広告を表示するか
// if ( !function_exists( 'is_the_page_amp_enable' ) ):
// function is_the_page_amp_enable(){
//   $value = get_post_meta(get_the_ID(), 'the_page_amp_enable', true);
//   //初回利用時は1を返す
//   if (is_field_checkbox_value_default($value)) {
//     $old_value = is_the_page_no_amp();
//     if (is_field_checkbox_value_default($old_value)) {
//       $value = 1;
//     } else {
//       $value = !$old_value;
//     }
//   }
//   return $value;
// }
// endif;
