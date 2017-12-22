<?php //ページカスタムフィールドを設置する

///////////////////////////////////////
// カスタムボックスの追加
///////////////////////////////////////
add_action('admin_menu', 'add_page_custom_box');
if ( !function_exists( 'add_page_custom_box' ) ):
function add_page_custom_box(){

  //ページ設定
  add_meta_box( 'singular_page_settings',__( 'ページ設定', THEME_NAME ), 'page_custom_box_view', 'post', 'side' );
  add_meta_box( 'singular_page_settings',__( 'ページ設定', THEME_NAME ), 'page_custom_box_view', 'page', 'side' );
  add_meta_box( 'singular_page_settings',__( 'ページ設定', THEME_NAME ), 'page_custom_box_view', 'topic', 'side' );
}
endif;

///////////////////////////////////////
// ページ設定
///////////////////////////////////////
if ( !function_exists( 'page_custom_box_view' ) ):
function page_custom_box_view(){
  $page_type = get_post_meta(get_the_ID(),'page_type', true);

  //ページタイプ
  echo '<label>'.__( 'ページタイプ', THEME_NAME ).'</label><br>';
  echo '<select name="page_type">';
  //デフォルト
  echo '<option value="default"';
  if( $page_type == 'default' ){echo ' selected';}
  echo '>'.__( 'デフォルト', THEME_NAME ).'</option>';
  // //1カラム（狭い）
  // echo '<option value="column1_narrow"';
  // if( $page_type == 'column1_narrow' ){echo ' selected';}
  // echo '>'.__( '1カラム（狭い）', THEME_NAME ).'</option>';
  //1カラム（広い）
  echo '<option value="column1_wide"';
  if( $page_type == 'column1_wide' ){echo ' selected';}
  echo '>'.__( '1カラム', THEME_NAME ).'</option>';
  // //本文のみ（狭い）
  // echo '<option value="content_only_narrow"';
  // if( $page_type == 'content_only_narrow' ){echo ' selected';}
  // echo '>'.__( '本文のみ（狭い）', THEME_NAME ).'</option>';
  //本文のみ（広い）
  echo '<option value="content_only_wide"';
  if( $page_type == 'content_only_wide' ){echo ' selected';}
  echo '>'.__( '本文のみ', THEME_NAME ).'</option>';
  echo '</select>';
  echo '<p class="howto">'.__( 'このページの表示状態を設定します。「本文のみ」表示はランディングページ（LP）などにどうぞ。', THEME_NAME ).'</p>';

}
endif;

add_action('save_post', 'page_custom_box_save_data');
if ( !function_exists( 'page_custom_box_save_data' ) ):
function page_custom_box_save_data(){
  $id = get_the_ID();
  //ページタイプ
  if ( isset( $_POST['page_type'] ) ){
    $page_type = $_POST['page_type'];
    $page_type_key = 'page_type';
    add_post_meta($id, $page_type_key, $page_type, true);
    update_post_meta($id, $page_type_key, $page_type);
  }
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




