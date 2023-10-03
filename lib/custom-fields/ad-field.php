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
  add_meta_box( 'singular_ad_settings',__( '広告', THEME_NAME ), 'view_ad_custom_box', 'post', 'side' );
  add_meta_box( 'singular_ad_settings',__( '広告', THEME_NAME ), 'view_ad_custom_box', 'page', 'side' );
  //カスタム投稿タイプに登録
  add_meta_box_custom_post_types( 'singular_ad_settings',__( '広告', THEME_NAME ), 'view_ad_custom_box', 'custum_post', 'side' );
}
endif;


///////////////////////////////////////
// AdSenseの設定
///////////////////////////////////////
if ( !function_exists( 'view_ad_custom_box' ) ):
function view_ad_custom_box(){
  //広告を除外する
  generate_checkbox_tag('the_page_ads_novisible' , is_the_page_ads_novisible(), __( '広告を除外する', THEME_NAME ));
  generate_howto_tag(__( 'ページ上の広告（AdSenseなど）表示を切り替えます。「広告」設定からカテゴリーごとの設定も行えます。', THEME_NAME ), 'the_page_ads_novisible');

  //PR表記を除外する
  generate_checkbox_tag('the_page_pr_labels_novisible' , is_the_page_pr_labels_novisible(), __( 'PR表記を除外する', THEME_NAME ));
  generate_howto_tag(__( 'Cocoon設定で「PR表記」を有効にしていたとしても、この除外オプションを有効にすれば非表示になります。', THEME_NAME ), 'the_page_pr_labels_novisible');


  // //PR表記タイプ
  // $the_pr_label_type = get_the_pr_label_type();
  // if (!$the_pr_label_type) {
  //   $the_pr_label_type = 'default';
  // }
  // generate_label_tag('the_pr_label_type', '<b>'.__('この記事のPR表記', THEME_NAME).'</b>' );
  // $options = array(
  //   'default' => __( 'デフォルト', THEME_NAME ),
  //   'visible' => __( 'この記事は常に表示する', THEME_NAME ),
  //   'invisible' => __( 'この記事は常に表示しない', THEME_NAME ),
  // );
  // generate_radiobox_tag('the_pr_label_type', $options, $the_pr_label_type, '');
  // generate_howto_tag(__( '「デフォルト」の場合は「Cocoon設定」の「PR表記」設定に準拠します。', THEME_NAME ).__( 'それ以外の「表示・非表示」設定は当設定が優先されます。', THEME_NAME ), 'the_pr_label_type');

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
  //Simplicity互換
  if (is_migrate_from_simplicity()) {
    add_post_meta($id, 'is_ads_removed_in_page', $the_page_ads_novisible, true);
    update_post_meta($id, 'is_ads_removed_in_page', $the_page_ads_novisible);
  }

  //PR表記の除外
  $the_page_pr_labels_novisible = !empty($_POST['the_page_pr_labels_novisible']) ? 1 : 0;
  $the_page_pr_labels_novisible_key = 'the_page_pr_labels_novisible';
  add_post_meta($id, $the_page_pr_labels_novisible_key, $the_page_pr_labels_novisible, true);
  update_post_meta($id, $the_page_pr_labels_novisible_key, $the_page_pr_labels_novisible);

  // //PR表記動作
  // if ( isset( $_POST['the_pr_label_type'] ) ){
  //   $the_pr_label_type = $_POST['the_pr_label_type'];
  //   $the_pr_label_type_key = 'the_pr_label_type';
  //   add_post_meta($id, $the_pr_label_type_key, $the_pr_label_type, true);
  //   update_post_meta($id, $the_pr_label_type_key, $the_pr_label_type);
  // }

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


//PR表記を除外しているか
if ( !function_exists( 'is_the_page_pr_labels_novisible' ) ):
function is_the_page_pr_labels_novisible(){
  $value = 0;
  if (is_singular() || is_admin()) {
    $value = get_post_meta(get_the_ID(), 'the_page_pr_labels_novisible', true);
  }
  return $value;
}
endif;

//PR表記を表示するか
if ( !function_exists( 'is_the_page_pr_labels_visible' ) ):
function is_the_page_pr_labels_visible(){
  return !is_the_page_pr_labels_novisible();
}
endif;

// //PR表記を表示するか
// if ( !function_exists( 'is_the_page_pr_labels_visible' ) ):
// function is_the_page_pr_labels_visible(){
//   return get_the_pr_label_type() === 'visible';
// }
// endif;


// //PR表記を表示しないか
// if ( !function_exists( 'is_the_page_pr_labels_invisible' ) ):
// function is_the_page_pr_labels_invisible(){
//   return get_the_pr_label_type() === 'invisible';
// }
// endif;

// //PR表記を表示するか
// if ( !function_exists( 'is_the_page_pr_label_type_default' ) ):
// function is_the_page_pr_label_type_default(){
//   return get_the_pr_label_type() === 'default';
// }
// endif;

// //PRラベルタイプ
// if ( !function_exists( 'get_the_pr_label_type' ) ):
// function get_the_pr_label_type(){
//   $type = trim(get_post_meta(get_the_ID(), 'the_pr_label_type', true));
//   //旧オプションの値を取得して有効の場合は無効にして上書き
//   $pr_labels_novisible = is_the_page_pr_labels_novisible();
//   if ($pr_labels_novisible) {
//     $type = 'invisible';
//     //無効にして上書き
//     $id = get_the_ID();
//     $the_page_pr_labels_novisible = 0;
//     $the_page_pr_labels_novisible_key = 'the_page_pr_labels_novisible';
//     add_post_meta($id, $the_page_pr_labels_novisible_key, $the_page_pr_labels_novisible, true);
//     update_post_meta($id, $the_page_pr_labels_novisible_key, $the_page_pr_labels_novisible);
//     //非表示で保存
//     $the_pr_label_type = $type;
//     $the_pr_label_type_key = 'the_pr_label_type';
//     add_post_meta($id, $the_pr_label_type_key, $the_pr_label_type, true);
//     update_post_meta($id, $the_pr_label_type_key, $the_pr_label_type);
//   }
//   return $type;
// }
// endif;