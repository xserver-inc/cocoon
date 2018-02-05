<?php //AMPカスタムフィールドを設置する

///////////////////////////////////////
// カスタムボックスの追加
///////////////////////////////////////
add_action('admin_menu', 'add_amp_custom_box');
if ( !function_exists( 'add_amp_custom_box' ) ):
function add_amp_custom_box(){
  //AMPボックス
  add_meta_box( 'singular_amp_settings',__( 'AMPの設定', THEME_NAME ), 'view_amp_custom_box', 'post', 'side' );
  add_meta_box( 'singular_amp_settings',__( 'AMPの設定', THEME_NAME ), 'view_amp_custom_box', 'page', 'side' );
  add_meta_box( 'singular_amp_settings',__( 'AMPの設定', THEME_NAME ), 'view_amp_custom_box', 'topic', 'side' );
}
endif;


///////////////////////////////////////
// AMPの設定
///////////////////////////////////////
if ( !function_exists( 'view_amp_custom_box' ) ):
function view_amp_custom_box(){
  $the_page_no_amp = get_post_meta(get_the_ID(), 'the_page_no_amp', true);

  echo '<label><input type="checkbox" name="the_page_no_amp"';
  if( $the_page_no_amp ){echo " checked";}
  echo '>'.__( 'AMP表示しない', THEME_NAME ).'</label>';
  echo '<p class="howto">'.__( 'チェックを付けたページのAMPを無効にします。。', THEME_NAME ).'</p>';
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
}
endif;


//AMPを除外しているか
if ( !function_exists( 'is_the_page_no_amp' ) ):
function is_the_page_no_amp(){
  return get_post_meta(get_the_ID(), 'the_page_no_amp', true);
}
endif;


//AMPを表示するか
if ( !function_exists( 'is_the_page_amp_enable' ) ):
function is_the_page_amp_enable(){
  return !is_the_page_no_amp();
}
endif;
