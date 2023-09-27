<?php //HTML挿入ボタン
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

// ビジュアルエディタにHTMLを直挿入するためのボタンを追加
add_filter( 'mce_buttons_2', 'add_insert_html_button' );
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
if (is_admin())
  add_action('admin_head', 'generate_insert_html_label_js');
if ( !function_exists( 'generate_insert_html_label_js' ) ):
function generate_insert_html_label_js($value){
  echo '<script type="text/javascript">
  var insert_html_button_title = "'.__( 'HTML挿入', THEME_NAME ).'";
  var insert_html_dialog_label = "'.__( 'HTMLを挿入してください', THEME_NAME ).'";';
  echo '</script>';
}
endif;
