<?php //ビジュアルエディターのテンプレート挿入ドロップダウン
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

//テーブル内にレコードが存在するとき
if (!is_function_texts_record_empty() && is_admin_post_page()) {
  add_action('admin_init', 'add_function_texts_dropdown');
  add_action('admin_head', 'generate_function_texts_is');
}

if ( !function_exists( 'add_function_texts_dropdown' ) ):
function add_function_texts_dropdown(){
  //if( current_user_can('edit_posts') &&  current_user_can('edit_pages') )  {
    add_filter( 'mce_external_plugins',  'add_function_texts_to_mce_external_plugins' );
    add_filter( 'mce_buttons_3',  'register_function_texts' );
  //}
}
endif;

//ボタン用スクリプトの登録
if ( !function_exists( 'add_function_texts_to_mce_external_plugins' ) ):
function add_function_texts_to_mce_external_plugins( $plugin_array ){
  $path=get_cocoon_template_directory_uri() . '/js/function-texts.js';
  $plugin_array['function_texts'] = $path;
  //_v($plugin_array);
  return $plugin_array;
}
endif;

//吹き出しドロップダウンをTinyMCEに登録
if ( !function_exists( 'register_function_texts' ) ):
function register_function_texts( $buttons ){
  array_push( $buttons, 'separator', 'function_texts' );
  return $buttons;
}
endif;

//吹き出しの値渡し用のJavaScriptを生成
if ( !function_exists( 'generate_function_texts_is' ) ):
function generate_function_texts_is($value){
  $records = get_function_texts(null, 'title');

  // esc_js()はHTMLの<>を&lt;&gt;に変換してしまうため、wp_json_encodeで安全にJS変数へ渡す
  $json_flags = JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT;

  echo '<script type="text/javascript">';
  echo 'var functionTextsTitle = ' . wp_json_encode(__( 'テンプレート', THEME_NAME ), $json_flags) . ';';
  echo 'var functionTexts = new Array();';

  $count = 0;

  foreach($records as $record){
    //非表示の場合は跳ばす
    if (!$record->visible) {
      continue;
    }
    ?>

    var count = <?php echo $count; ?>;
    functionTexts[count] = new Array();
    functionTexts[count].title  = <?php echo wp_json_encode($record->title, $json_flags); ?>;
    functionTexts[count].id     = <?php echo wp_json_encode($record->id, $json_flags); ?>;
    functionTexts[count].shrotecode = <?php echo wp_json_encode(get_function_text_shortcode($record->id), $json_flags); ?>;

    <?php
    $count++;
  }
  echo '</script>';
}
endif;

