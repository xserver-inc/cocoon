<?php //よく利用するHTML（レイアウトなど）

add_action('admin_init', 'add_html_tags_dropdown');
add_action('admin_head', 'generate_html_tags_is');

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
  htmlTags[0].title  = '<?php echo __( '2カラム', THEME_NAME ); ?>';
  htmlTags[0].tag = '<?php echo $before.$left_msg.$after; ?>';
  htmlTags[0].before = '<?php echo $before; ?>';
  htmlTags[0].after = '<?php echo $after; ?>';

  <?php //3カラムレイアウト
  $before = '<div class="column-wrap column-3"><div class="column-left"><p>';
  $after = '</p></div><div class="column-center"><p>'.$center_msg.'</p></div><div class="column-right"><p>'.$right_msg.'</p></div></div>';
   ?>
  htmlTags[1] = new Array();
  htmlTags[1].title  = '<?php echo __( '3カラム', THEME_NAME ); ?>';
  htmlTags[1].tag = '<?php echo $before.$left_msg.$after; ?>';
  htmlTags[1].before = '<?php echo $before; ?>';
  htmlTags[1].after = '<?php echo $after; ?>';

  <?php //検索
  $before = '<div class="search-form"><div class="sform">';
  $after = '</div><div class="sbtn">'.__( '検索', THEME_NAME ).'</div></div>';
   ?>
  htmlTags[2] = new Array();
  htmlTags[2].title  = '<?php echo __( '検索フォーム風', THEME_NAME ); ?>';
  htmlTags[2].tag = '<?php echo $before.$keyword_msg.$after; ?>';
  htmlTags[2].before = '<?php echo $before; ?>';
  htmlTags[2].after = '<?php echo $after; ?>';

  <?php

  echo '</script>';
}
endif;

