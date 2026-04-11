<?php //ビジュアルエディターの吹き出し挿入ドロップダウン
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

//テーブル内にレコードが存在するとき
if (!is_speech_balloons_record_empty() && is_admin_post_page()) {
  add_action('admin_init', 'add_speech_balloons_dropdown');
  add_action('admin_head', 'generate_speech_balloons_js');
}


if ( !function_exists( 'add_speech_balloons_dropdown' ) ):
function add_speech_balloons_dropdown(){
  //if( current_user_can('edit_posts') &&  current_user_can('edit_pages') )  {
    add_filter( 'mce_external_plugins',  'add_speech_balloons_to_mce_external_plugins' );
    add_filter( 'mce_buttons_3',  'register_speech_balloons' );
  //}
}
endif;

//ボタン用スクリプトの登録
if ( !function_exists( 'add_speech_balloons_to_mce_external_plugins' ) ):
function add_speech_balloons_to_mce_external_plugins( $plugin_array ){
  $path=get_cocoon_template_directory_uri() . '/js/speech-balloons.js';
  $plugin_array['speech_balloons'] = $path;
  //_v($plugin_array);
  return $plugin_array;
}
endif;

//吹き出しドロップダウンをTinyMCEに登録
if ( !function_exists( 'register_speech_balloons' ) ):
function register_speech_balloons( $buttons ){
  array_push( $buttons, 'separator', 'speech_balloons' );
  return $buttons;
}
endif;

//吹き出しの値渡し用のJavaScriptを生成
if ( !function_exists( 'generate_speech_balloons_js' ) ):
function generate_speech_balloons_js($value){
  $records = get_speech_balloons(null, 'title');

  // esc_js()はHTMLの<>を&lt;&gt;に変換してしまい、吹き出しHTMLがテキストとして
  // 出力される不具合を引き起こすため、wp_json_encodeでJSONエンコードして安全に渡す
  $json_flags = JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT;

  echo '<script type="text/javascript">';
  echo 'var speechBalloonsTitle = ' . wp_json_encode(__( '吹き出し', THEME_NAME ), $json_flags) . ';';
  echo 'var speechBalloonsEmptyText = ' . wp_json_encode(__( '内容を入力してください。', THEME_NAME ), $json_flags) . ';';
  echo 'var speechBalloons = new Array();';

  $count = 0;

  foreach($records as $record){
    //非表示の場合は跳ばす
    if (!$record->visible) {
      continue;
    }
    ob_start();
    generate_speech_balloon_tag($record, 'VOICE');
    $speech_balloon_tag = ob_get_clean();
    $speech_balloon_tag_split = explode('VOICE', $speech_balloon_tag);
    //JavaScriptで改行エラーになるため取り除く
    $sb_tag_before = minify_html($speech_balloon_tag_split[0]);
    $sb_tag_after  = minify_html($speech_balloon_tag_split[1]);
    ?>

    var count = <?php echo $count; ?>;
    speechBalloons[count] = new Array();
    speechBalloons[count].title  = <?php echo wp_json_encode($record->title, $json_flags); ?>;
    speechBalloons[count].id     = <?php echo wp_json_encode($record->id, $json_flags); ?>;
    speechBalloons[count].before = <?php echo wp_json_encode($sb_tag_before, $json_flags); ?>;
    speechBalloons[count].after  = <?php echo wp_json_encode($sb_tag_after, $json_flags); ?>;

    <?php
    $count++;
  }
  echo '</script>';
}
endif;

