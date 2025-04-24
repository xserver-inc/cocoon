<?php //よく利用するHTML（レイアウトなど）
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

if (is_admin_post_page()) {
  add_action('admin_init', 'add_html_tags_dropdown');
  add_action('admin_head', 'generate_html_tags_is');
}

if ( !function_exists( 'add_html_tags_dropdown' ) ):
function add_html_tags_dropdown(){
  //if( current_user_can('edit_posts') &&  current_user_can('edit_pages') )  {
    add_filter( 'mce_external_plugins',  'add_html_tags_to_mce_external_plugins' );
    add_filter( 'mce_buttons_3',  'register_html_tags' );
  //}
}
endif;

//ボタン用スクリプトの登録
if ( !function_exists( 'add_html_tags_to_mce_external_plugins' ) ):
function add_html_tags_to_mce_external_plugins( $plugin_array ){
  $path=get_template_directory_uri() . '/js/html-tags.js';
  $plugin_array['html_tags'] = $path;
  return $plugin_array;
}
endif;

//ドロップダウンをTinyMCEに登録
if ( !function_exists( 'register_html_tags' ) ):
function register_html_tags( $buttons ){
  array_push( $buttons, 'separator', 'html_tags' );
  return $buttons;
}
endif;

//値渡し用のJavaScriptを生成
if ( !function_exists( 'generate_html_tags_is' ) ):
function generate_html_tags_is($value){
  echo '<script type="text/javascript">
  var htmlTagsTitle = "'.__( 'タグ', THEME_NAME ).'";
  var htmlTagsEmptyText = "'.__( '内容を入力してください。', THEME_NAME ).'";
  var htmlTags = new Array();';
  $left_msg = __( '左側に入力する内容', THEME_NAME );
  $center_msg = __( '中央に入力する内容', THEME_NAME );
  $right_msg = __( '右側に入力する内容', THEME_NAME );
  $keyword_msg = __( 'キーワード', THEME_NAME );
   ?>;

  <?php //２カラムレイアウト
  $before = '<div class="column-wrap column-2"><div class="column-left"><p>';
  $after = '</p></div><div class="column-right"><p>'.$right_msg.'</p></div></div>';
   ?>
  htmlTags[0] = new Array();
  htmlTags[0].title  = '<?php echo __( '2カラム（1:1, ｜□｜□｜）', THEME_NAME ); ?>';
  htmlTags[0].tag = '<?php echo $before.$left_msg.$after; ?>';
  htmlTags[0].before = '<?php echo $before; ?>';
  htmlTags[0].after = '<?php echo $after; ?>';

  <?php //２カラムレイアウト（1:2）
  $before = '<div class="column-wrap column-2 column-2-3-1-2"><div class="column-left"><p>';
  $after = '</p></div><div class="column-right"><p>'.$right_msg.'</p></div></div>';
  ?>
  htmlTags[1] = new Array();
  htmlTags[1].title  = '<?php echo __( '2カラム（1:2, ｜□｜□□｜）', THEME_NAME ); ?>';
  htmlTags[1].tag = '<?php echo $before.$left_msg.$after; ?>';
  htmlTags[1].before = '<?php echo $before; ?>';
  htmlTags[1].after = '<?php echo $after; ?>';

  <?php //２カラムレイアウト（2:1）
  $before = '<div class="column-wrap column-2 column-2-3-2-1"><div class="column-left"><p>';
  $after = '</p></div><div class="column-right"><p>'.$right_msg.'</p></div></div>';
  ?>
  htmlTags[2] = new Array();
  htmlTags[2].title  = '<?php echo __( '2カラム（2:1, ｜□□｜□｜）', THEME_NAME ); ?>';
  htmlTags[2].tag = '<?php echo $before.$left_msg.$after; ?>';
  htmlTags[2].before = '<?php echo $before; ?>';
  htmlTags[2].after = '<?php echo $after; ?>';

  <?php //２カラムレイアウト（1:3）
  $before = '<div class="column-wrap column-2 column-2-4-1-3"><div class="column-left"><p>';
  $after = '</p></div><div class="column-right"><p>'.$right_msg.'</p></div></div>';
  ?>
  htmlTags[3] = new Array();
  htmlTags[3].title  = '<?php echo __( '2カラム（1:3, ｜□｜□□□｜）', THEME_NAME ); ?>';
  htmlTags[3].tag = '<?php echo $before.$left_msg.$after; ?>';
  htmlTags[3].before = '<?php echo $before; ?>';
  htmlTags[3].after = '<?php echo $after; ?>';

  <?php //２カラムレイアウト（3:1）
  $before = '<div class="column-wrap column-2 column-2-4-3-1"><div class="column-left"><p>';
  $after = '</p></div><div class="column-right"><p>'.$right_msg.'</p></div></div>';
  ?>
  htmlTags[4] = new Array();
  htmlTags[4].title  = '<?php echo __( '2カラム（3:1, ｜□□□｜□｜）', THEME_NAME ); ?>';
  htmlTags[4].tag = '<?php echo $before.$left_msg.$after; ?>';
  htmlTags[4].before = '<?php echo $before; ?>';
  htmlTags[4].after = '<?php echo $after; ?>';

  <?php //3カラムレイアウト
  $before = '<div class="column-wrap column-3"><div class="column-left"><p>';
  $after = '</p></div><div class="column-center"><p>'.$center_msg.'</p></div><div class="column-right"><p>'.$right_msg.'</p></div></div>';
   ?>
  htmlTags[5] = new Array();
  htmlTags[5].title  = '<?php echo __( '3カラム', THEME_NAME ); ?>';
  htmlTags[5].tag = '<?php echo $before.$left_msg.$after; ?>';
  htmlTags[5].before = '<?php echo $before; ?>';
  htmlTags[5].after = '<?php echo $after; ?>';

  <?php //検索
  $before = '<div class="search-form"><div class="sform">';
  $after = '</div><div class="sbtn">'.__( '検索', THEME_NAME ).'</div></div>';
   ?>
  htmlTags[6] = new Array();
  htmlTags[6].title  = '<?php echo __( '検索フォーム風', THEME_NAME ); ?>';
  htmlTags[6].tag = '<?php echo $before.$keyword_msg.$after; ?>';
  htmlTags[6].before = '<?php echo $before; ?>';
  htmlTags[6].after = '<?php echo $after; ?>';

  <?php //アコーディオンボックス
  $before = '<div class="toggle-wrap"><input id="toggle-checkbox-COCOON_DATE_ID" class="toggle-checkbox" type="checkbox"><label class="toggle-button" for="toggle-checkbox-COCOON_DATE_ID">'.__( 'アコーディオンボックス見出し', THEME_NAME ).'</label><span class="toggle-content">';
  $after = '</span></div>';
  ?>
  htmlTags[7] = new Array();
  htmlTags[7].title  = '<?php echo __( 'アコーディオンボックス', THEME_NAME ); ?>';
  htmlTags[7].tag = '<?php echo $before.__( 'アコーディオンボックス内容', THEME_NAME ).$after; ?>';
  htmlTags[7].before = '<?php echo $before; ?>';
  htmlTags[7].after = '<?php echo $after; ?>';

  <?php //ふりがな
  $before = '<ruby>';
  $after = '<rt>'.__( 'ふりがな', THEME_NAME ).'</rt></ruby>';
  ?>
  htmlTags[8] = new Array();
  htmlTags[8].title  = '<?php echo __( 'ふりがな（ルビ）', THEME_NAME ); ?>';
  htmlTags[8].tag = '<?php echo $before.__( '振り仮名', THEME_NAME ).$after; ?>';
  htmlTags[8].before = '<?php echo $before; ?>';
  htmlTags[8].after = '<?php echo $after; ?>';

  <?php

  echo '</script>';
}
endif;

