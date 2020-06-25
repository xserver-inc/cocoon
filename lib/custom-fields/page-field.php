<?php //ページカスタムフィールドを設置する
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
add_action('admin_menu', 'add_page_custom_box');
if ( !function_exists( 'add_page_custom_box' ) ):
function add_page_custom_box(){

  //ページ設定
  add_meta_box( 'singular_page_settings',__( 'ページ設定', THEME_NAME ), 'page_custom_box_view', 'post', 'side' );
  add_meta_box( 'singular_page_settings',__( 'ページ設定', THEME_NAME ), 'page_custom_box_view', 'page', 'side' );
  //カスタム投稿タイプに登録
  add_meta_box_custom_post_types( 'singular_page_settings',__( 'ページ設定', THEME_NAME ), 'page_custom_box_view', 'custum_post', 'side' );
}
endif;

///////////////////////////////////////
// ページ設定
///////////////////////////////////////
if ( !function_exists( 'page_custom_box_view' ) ):
function page_custom_box_view(){

  //メインカテゴリー
  if (is_admin_single()) {
    $options = array(
      '' => __( 'デフォルト', THEME_NAME ),
    );
    $cats = get_the_category();
    foreach($cats as $cat):
      $options[$cat->cat_ID] = $cat->name;
    endforeach ;
    generate_selectbox_tag('the_page_main_category', $options, get_the_page_main_category(), __( 'メインカテゴリー', THEME_NAME ));
    generate_howto_tag(__( 'このページで優先するカテゴリを選択します。', THEME_NAME ).__( '優先カテゴリーは、アイキャッチやパンくずリストに適用されます。', THEME_NAME ).__( 'カテゴリ選択直後はすぐにセレクトボックスに反映されません。', THEME_NAME ).__( '一度ページを更新してください。', THEME_NAME ), 'the_page_main_category');
  }


  //ページタイプ
  $options = array(
    'default' => __( 'デフォルト', THEME_NAME ),
    'column1_wide' => __( '1カラム（広い）', THEME_NAME ),
    'column1_narrow' => __( '1カラム（狭い）', THEME_NAME ),
    'content_only_wide' => __( '本文のみ（広い）', THEME_NAME ),
    'content_only_narrow' => __( '本文のみ（狭い）', THEME_NAME ),
  );
  generate_selectbox_tag('page_type', $options, get_singular_page_type(), __( 'ページタイプ', THEME_NAME ));
  generate_howto_tag(__( 'このページの表示状態を設定します。「本文のみ」表示はランディングページ（LP）などにどうぞ。', THEME_NAME ), 'page_type');

  //記事を読む時間
  generate_checkbox_tag('the_page_read_time_novisible' , is_the_page_read_time_novisible(), __( '読む時間を表示しない', THEME_NAME ));
  generate_howto_tag(__( 'このページに「記事を読む時間」を表示するかを切り替えます。', THEME_NAME ), 'the_page_read_time_novisible');

  //目次表示
  generate_checkbox_tag('the_page_toc_novisible' , is_the_page_toc_novisible(), __( '目次を表示しない', THEME_NAME ));
  generate_howto_tag(__( 'このページに目次を表示するかを切り替えます。', THEME_NAME ), 'the_page_toc_novisible');

}
endif;

add_action('save_post', 'page_custom_box_save_data');
if ( !function_exists( 'page_custom_box_save_data' ) ):
function page_custom_box_save_data(){
  $id = get_the_ID();

  //メインカテゴリー
  if ( is_admin_single() && isset( $_POST['the_page_main_category'] ) ){
    $the_page_main_category = $_POST['the_page_main_category'];
    $the_page_main_category_key = 'the_page_main_category';
    add_post_meta($id, $the_page_main_category_key, $the_page_main_category, true);
    update_post_meta($id, $the_page_main_category_key, $the_page_main_category);
  }

  //ページタイプ
  if ( isset( $_POST['page_type'] ) ){
    $page_type = $_POST['page_type'];
    $page_type_key = 'page_type';
    add_post_meta($id, $page_type_key, $page_type, true);
    update_post_meta($id, $page_type_key, $page_type);
  }

  //読む時間
  $the_page_read_time_novisible = !empty($_POST['the_page_read_time_novisible']) ? 1 : 0;
  $the_page_read_time_novisible_key = 'the_page_read_time_novisible';
  add_post_meta($id, $the_page_read_time_novisible_key, $the_page_read_time_novisible, true);
  update_post_meta($id, $the_page_read_time_novisible_key, $the_page_read_time_novisible);

  //目次表示
  $the_page_toc_novisible = !empty($_POST['the_page_toc_novisible']) ? 1 : 0;
  $the_page_toc_novisible_key = 'the_page_toc_novisible';
  add_post_meta($id, $the_page_toc_novisible_key, $the_page_toc_novisible, true);
  update_post_meta($id, $the_page_toc_novisible_key, $the_page_toc_novisible);

  // //目次表示
  // $the_page_toc_visible = !empty($_POST['the_page_toc_visible']) ? 1 : 0;
  // $the_page_toc_visible_key = 'the_page_toc_visible';
  // add_post_meta($id, $the_page_toc_visible_key, $the_page_toc_visible, true);
  // update_post_meta($id, $the_page_toc_visible_key, $the_page_toc_visible);
}
endif;

//メインカテゴリーの取得
if ( !function_exists( 'get_the_page_main_category' ) ):
function get_the_page_main_category($id = null){
  if (!$id) {
    $id = get_the_ID();
  }
  return get_post_meta($id, 'the_page_main_category', true);
}
endif;

//ページタイプの取得
if ( !function_exists( 'get_singular_page_type' ) ):
function get_singular_page_type(){
  return get_post_meta(get_the_ID(), 'page_type', true);
}
endif;

//ページタイプはデフォルトか
if ( !function_exists( 'is_singular_page_type_default' ) ):
function is_singular_page_type_default(){
  return get_singular_page_type() == 'default';
}
endif;

//ページタイプは狭い1カラムか
if ( !function_exists( 'is_singular_page_type_column1_narrow' ) ):
function is_singular_page_type_column1_narrow(){
  return get_singular_page_type() == 'column1_narrow';
}
endif;

//ページタイプは広い1カラムか
if ( !function_exists( 'is_singular_page_type_column1_wide' ) ):
function is_singular_page_type_column1_wide(){
  return get_singular_page_type() == 'column1_wide';
}
endif;

//ページタイプは狭い本文のみか
if ( !function_exists( 'is_singular_page_type_content_only_narrow' ) ):
function is_singular_page_type_content_only_narrow(){
  return get_singular_page_type() == 'content_only_narrow';
}
endif;

//ページタイプは広い本文のみか
if ( !function_exists( 'is_singular_page_type_content_only_wide' ) ):
function is_singular_page_type_content_only_wide(){
  return get_singular_page_type() == 'content_only_wide';
}
endif;

//ページタイプの表示幅は狭いか
if ( !function_exists( 'is_singular_page_type_narrow' ) ):
function is_singular_page_type_narrow(){
  return is_singular_page_type_column1_narrow() || is_singular_page_type_content_only_narrow();
}
endif;

//ページタイプの表示幅は広いか
if ( !function_exists( 'is_singular_page_type_wide' ) ):
function is_singular_page_type_wide(){
  return is_singular_page_type_column1_wide() || is_singular_page_type_content_only_wide();
}
endif;

//ページタイプは1カラムか
if ( !function_exists( 'is_singular_page_type_column1' ) ):
function is_singular_page_type_column1(){
  return is_singular_page_type_column1_narrow() || is_singular_page_type_column1_wide();
}
endif;

//ページタイプは本文のみか
if ( !function_exists( 'is_singular_page_type_content_only' ) ):
function is_singular_page_type_content_only(){
  return is_singular_page_type_content_only_narrow() || is_singular_page_type_content_only_wide();
}
endif;

//このページで読む時間を表示するか
if ( !function_exists( 'is_the_page_read_time_novisible' ) ):
function is_the_page_read_time_novisible(){
  return get_post_meta(get_the_ID(), 'the_page_read_time_novisible', true);
}
endif;

//このページで読む時間を表示するか
if ( !function_exists( 'is_the_page_read_time_visible' ) ):
function is_the_page_read_time_visible(){
  return !is_the_page_read_time_novisible();
}
endif;

//このページで目次を表示するか
if ( !function_exists( 'is_the_page_toc_novisible' ) ):
function is_the_page_toc_novisible(){
  return get_post_meta(get_the_ID(), 'the_page_toc_novisible', true);
}
endif;

//このページで目次を表示するか
if ( !function_exists( 'is_the_page_toc_visible' ) ):
function is_the_page_toc_visible(){
  return !is_the_page_toc_novisible();
}
endif;

// //このページで目次を表示するか
// if ( !function_exists( 'is_the_page_toc_visible' ) ):
// function is_the_page_toc_visible(){
//   $value = get_post_meta(get_the_ID(), 'the_page_toc_visible', true);
//   //初回利用時は1を返す
//   if (is_field_checkbox_value_default($value)) {
//     $value = 1;
//   }
//   return $value;
// }
// endif;


