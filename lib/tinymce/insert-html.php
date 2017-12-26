<?php //

// ビジュアルエディタにHTMLを直挿入するためのボタンを追加
add_filter( 'mce_buttons', 'add_insert_html_button' );
if ( !function_exists( 'add_insert_html_button' ) ):
function add_insert_html_button( $buttons ) {
  $buttons[] = 'button_insert_html';
  return $buttons;
}
endif;

//ビジュアルエディターにHTML挿入ボタン動作を行うJavaScriptを追加する
add_filter( 'mce_external_plugins', 'add_insert_html_button_plugin' );
if ( !function_exists( 'add_insert_html_button_plugin' ) ):
function add_insert_html_button_plugin( $plugin_array ) {
  $plugin_array['custom_button_script'] =  get_template_directory_uri() . "/js/button-insert-html.js";
  return $plugin_array;
}
endif;

//ビジュアルエディターにHTML挿入ボタンで出てくるダイアログにラベルを設定する
add_action('admin_head', 'set_insert_html_dialog_label');
if ( !function_exists( 'set_insert_html_dialog_label' ) ):
function set_insert_html_dialog_label($value){
  echo '<script type="text/javascript">
  var insert_html_button_title = "'.__( 'HTML挿入', THEME_NAME ).'";
  var insert_html_dialog_label = "'.__( 'HTMLを挿入してください', THEME_NAME ).'";';
  echo '</script>';
}
endif;