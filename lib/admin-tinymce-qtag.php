<?php //クイックタグ関係の関数

//テキストがエディタにクイックタグボタン追加
//http://webtukuru.com/web/wordpress-quicktag/
//https://wpdocs.osdn.jp/%E3%82%AF%E3%82%A4%E3%83%83%E3%82%AF%E3%82%BF%E3%82%B0API
//管理画面のウィジェットページでは表示しない
if ( is_admin() && (($pagenow != 'widgets.php') && ($pagenow != 'customize.php')) ) {
  add_action( 'admin_print_footer_scripts', 'add_quicktags_to_text_editor' );
}
if ( !function_exists( 'add_quicktags_to_text_editor' ) ):
function add_quicktags_to_text_editor() {
  //スクリプトキューにquicktagsが保存されているかチェック
  if (wp_script_is('quicktags')){?>
    <script>
      QTags.addButton('qt-pre','pre','<pre>','</pre>');
      QTags.addButton('qt-bold','<?php _e( '太字', THEME_NAME ) ?>','<span class="bold">','</span>');
      QTags.addButton('qt-red','<?php _e( '赤字', THEME_NAME ); ?>','<span class="red">','</span>');
      QTags.addButton('qt-bold-red','<?php _e( '太い赤字', THEME_NAME ); ?>','<span class="bold-red">','</span>');
      QTags.addButton('qt-red-under','<?php _e( '赤アンダー', THEME_NAME ); ?>','<span class="red-under">','</span>');
      QTags.addButton('qt-marker','<?php _e( '黄色マーカー', THEME_NAME ); ?>','<span class="marker">','</span>');
      QTags.addButton('qt-marker-under','<?php _e( '黄色アンダーマーカー', THEME_NAME ); ?>','<span class="marker-under">','</span>');
      QTags.addButton('qt-strike','<?php _e( '打ち消し線', THEME_NAME ); ?>','<span class="strike">','</span>');
      QTags.addButton('qt-ref','<?php _e( 'バッジ', THEME_NAME ); ?>','<span class="ref">','</span>');
      QTags.addButton('qt-keyboard-key','<?php _e( 'キーボード', THEME_NAME ); ?>','<span class="keyboard-key">','</span>');
      QTags.addButton('qt-information','<?php _e( '補足情報(i)', THEME_NAME ); ?>','<div class="information">','</div>');
      QTags.addButton('qt-question','<?php _e( '補足情報(?)', THEME_NAME ); ?>','<div class="question">','</div>');
      QTags.addButton('qt-alert','<?php _e( '補足情報(!)', THEME_NAME ); ?>','<div class="alert">','</div>');
      QTags.addButton('qt-sp-primary','<?php _e( 'primary', THEME_NAME ); ?>','<div class="sp-primary">','</div>');
      QTags.addButton('qt-sp-success','<?php _e( 'success', THEME_NAME ); ?>','<div class="sp-success">','</div>');
      QTags.addButton('qt-sp-info','info','<div class="sp-info">','</div>');
      QTags.addButton('qt-sp-warning','<?php _e( 'warning', THEME_NAME ); ?>','<div class="sp-warning">','</div>');
      QTags.addButton('qt-sp-danger','<?php _e( 'danger', THEME_NAME ); ?>','<div class="sp-danger">','</div>');
    </script>
  <?php
  }
}
endif;

//TinyMCE追加用のスタイルを初期化
//http://com4tis.net/tinymce-advanced-post-custom/
add_filter('tiny_mce_before_init', 'initialize_tinymce_styles');
if ( !function_exists( 'initialize_tinymce_styles' ) ):
function initialize_tinymce_styles($init_array) {
  //追加するスタイルの配列を作成
  $style_formats = array(
    array(
      'title' => __( '太字', THEME_NAME ),
      'inline' => 'span',
      'classes' => 'bold'
    ),
    array(
      'title' => __( '赤字', THEME_NAME ),
      'inline' => 'span',
      'classes' => 'red'
    ),
    array(
      'title' => __( '太い赤字', THEME_NAME ),
      'inline' => 'span',
      'classes' => 'bold-red'
    ),
    array(
      'title' => __( '赤アンダーライン', THEME_NAME ),
      'inline' => 'span',
      'classes' => 'red-under'
    ),
    array(
      'title' => __( '黄色マーカー', THEME_NAME ),
      'inline' => 'span',
      'classes' => 'marker'
    ),
    array(
      'title' => __( '黄色アンダーラインマーカー', THEME_NAME ),
      'inline' => 'span',
      'classes' => 'marker-under'
    ),
    array(
      'title' => __( '打ち消し線', THEME_NAME ),
      'inline' => 'span',
      'classes' => 'strike'
    ),
    array(
      'title' => __( 'バッジ', THEME_NAME ),
      'inline' => 'span',
      'classes' => 'ref'
    ),
    array(
      'title' => __( 'キーボードキー', THEME_NAME ),
      'inline' => 'span',
      'classes' => 'keyboard-key'
    ),
    array(
      'title' => __( '補足情報(i)', THEME_NAME ),
      'block' => 'div',
      'classes' => 'information'
    ),
    array(
      'title' => __( '補足情報(?)', THEME_NAME ),
      'block' => 'div',
      'classes' => 'question'
    ),
    array(
      'title' => __( '補足情報(!)', THEME_NAME ),
      'block' => 'div',
      'classes' => 'alert'
    ),
    array(
      'title' => __( 'primaryボックス', THEME_NAME ),
      'block' => 'div',
      'classes' => 'sp-primary'
    ),
    array(
      'title' => __( 'successボックス', THEME_NAME ),
      'block' => 'div',
      'classes' => 'sp-success'
    ),
    array(
      'title' => __( 'infoボックス', THEME_NAME ),
      'block' => 'div',
      'classes' => 'sp-info'
    ),
    array(
      'title' => __( 'warningボックス', THEME_NAME ),
      'block' => 'div',
      'classes' => 'sp-warning'
    ),
    array(
      'title' => __( 'dangerボックス', THEME_NAME ),
      'block' => 'div',
      'classes' => 'sp-danger'
    ),
  );
  //JSONに変換
  $init_array['style_formats'] = json_encode($style_formats);

  //ビジュアルエディターのフォントサイズ変更機能の文字サイズ指定
  $init_array['fontsize_formats'] = '10px 12px 14px 16px 18px 20px 24px 28px 32px 36px 42px 48px';

  return $init_array;
}
endif;

//Wordpressビジュアルエディターに文字サイズの変更機能を追加
add_filter('mce_buttons', 'add_ilc_mce_buttons_to_bar');
if ( !function_exists( 'add_ilc_mce_buttons_to_bar' ) ):
function add_ilc_mce_buttons_to_bar($buttons){
  array_push($buttons, 'backcolor', 'fontsizeselect', 'cleanup');
  return $buttons;
}
endif;

//TinyMCEにスタイルセレクトボックスを追加
add_filter('mce_buttons','add_styles_to_tinymce_buttons');
//https://codex.wordpress.org/Plugin_API/Filter_Reference/mce_buttons,_mce_buttons_2,_mce_buttons_3,_mce_buttons_4
if ( !function_exists( 'add_styles_to_tinymce_buttons' ) ):
function add_styles_to_tinymce_buttons($buttons) {
  //見出しなどが入っているセレクトボックスを取り出す
  $temp = array_shift($buttons);
  //先頭にスタイルセレクトボックスを追加
  array_unshift($buttons, 'styleselect');
  //先頭に見出しのセレクトボックスを追加
  array_unshift($buttons, $temp);

  return $buttons;
}
endif;
