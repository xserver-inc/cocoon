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
      QTags.addButton('qt-badge','<?php _e( 'バッジ', THEME_NAME ); ?>','<span class="badge">','</span>');
      QTags.addButton('qt-keyboard-key','<?php _e( 'キーボード', THEME_NAME ); ?>','<span class="keyboard-key">','</span>');
      QTags.addButton('qt-information','<?php _e( '補足情報(i)', THEME_NAME ); ?>','<div class="information-box">','</div>');
      QTags.addButton('qt-question','<?php _e( '補足情報(?)', THEME_NAME ); ?>','<div class="question-box">','</div>');
      QTags.addButton('qt-alert','<?php _e( '補足情報(!)', THEME_NAME ); ?>','<div class="alert-box">','</div>');
      QTags.addButton('qt-sp-primary','<?php _e( 'primary', THEME_NAME ); ?>','<div class="primary-box">','</div>');
      QTags.addButton('qt-sp-success','<?php _e( 'success', THEME_NAME ); ?>','<div class="success-box">','</div>');
      QTags.addButton('qt-sp-info','info','<div class="info-box">','</div>');
      QTags.addButton('qt-sp-warning','<?php _e( 'warning', THEME_NAME ); ?>','<div class="warning-box">','</div>');
      QTags.addButton('qt-sp-danger','<?php _e( 'danger', THEME_NAME ); ?>','<div class="danger-box">','</div>');
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
  //_v($init_array);
  //追加するスタイルの配列を作成
  $style_formats = array(
    array(
      'title' => __( 'インライン', THEME_NAME ),
      'items' => array(
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
          'title' => __( '赤色マーカー', THEME_NAME ),
          'inline' => 'span',
          'classes' => 'marker-red'
        ),
        array(
          'title' => __( '青色マーカー', THEME_NAME ),
          'inline' => 'span',
          'classes' => 'marker-blue'
        ),
        array(
          'title' => __( '黄色アンダーラインマーカー', THEME_NAME ),
          'inline' => 'span',
          'classes' => 'marker-under'
        ),
        array(
          'title' => __( '赤色アンダーラインマーカー', THEME_NAME ),
          'inline' => 'span',
          'classes' => 'marker-under-red'
        ),
        array(
          'title' => __( '青色アンダーラインマーカー', THEME_NAME ),
          'inline' => 'span',
          'classes' => 'marker-under-blue'
        ),
        array(
          'title' => __( '打ち消し線', THEME_NAME ),
          'inline' => 'span',
          'classes' => 'strike'
        ),
        array(
          'title' => __( 'キーボードキー', THEME_NAME ),
          'inline' => 'span',
          'classes' => 'keyboard-key'
        ),
      ),
    ),
    array(
      'title' => __( 'ボックス（アイコン）', THEME_NAME ),
      'items' => array(
        array(
          'title' => __( '補足情報(i)', THEME_NAME ),
          'block' => 'div',
          'classes' => 'information-box'
        ),
        array(
          'title' => __( '補足情報(?)', THEME_NAME ),
          'block' => 'div',
          'classes' => 'question-box'
        ),
        array(
          'title' => __( '補足情報(!)', THEME_NAME ),
          'block' => 'div',
          'classes' => 'alert-box'
        ),
        array(
          'title' => __( '補足情報(メモ)', THEME_NAME ),
          'block' => 'div',
          'classes' => 'memo-box'
        ),
        array(
          'title' => __( '補足情報(コメント)', THEME_NAME ),
          'block' => 'div',
          'classes' => 'comment-box'
        ),
      ),
    ),
    array(
      'title' => __( 'ボックス（案内）', THEME_NAME ),
      'items' => array(
        array(
          'title' => __( 'プライマリー（濃い水色）', THEME_NAME ),
          'block' => 'div',
          'classes' => 'primary-box'
        ),
        array(
          'title' => __( 'サクセス（薄い緑）', THEME_NAME ),
          'block' => 'div',
          'classes' => 'success-box'
        ),
        array(
          'title' => __( 'インフォ（薄い青）', THEME_NAME ),
          'block' => 'div',
          'classes' => 'info-box'
        ),
        array(
          'title' => __( 'ワーニング（薄い黄色）', THEME_NAME ),
          'block' => 'div',
          'classes' => 'warning-box'
        ),
        array(
          'title' => __( 'デンジャー（薄い赤色）', THEME_NAME ),
          'block' => 'div',
          'classes' => 'danger-box',
        ),
      ),
    ),
    array(
      'title' => __( 'バッジ', THEME_NAME ),
      'items' => array(
        array(
          'title' => __( 'バッジ（オレンジ）', THEME_NAME ),
          'inline' => 'span',
          'classes' => 'badge'
        ),
        array(
          'title' => __( 'バッジ（レッド）', THEME_NAME ),
          'inline' => 'span',
          'classes' => 'badge badge-red'
        ),
        array(
          'title' => __( 'バッジ（ピンク）', THEME_NAME ),
          'inline' => 'span',
          'classes' => 'badge badge-pink'
        ),
        array(
          'title' => __( 'バッジ（パープル）', THEME_NAME ),
          'inline' => 'span',
          'classes' => 'badge badge-purple'
        ),
        array(
          'title' => __( 'バッジ（ブルー）', THEME_NAME ),
          'inline' => 'span',
          'classes' => 'badge badge-blue'
        ),
        array(
          'title' => __( 'バッジ（グリーン）', THEME_NAME ),
          'inline' => 'span',
          'classes' => 'badge badge-green'
        ),
        array(
          'title' => __( 'バッジ（イエロー）', THEME_NAME ),
          'inline' => 'span',
          'classes' => 'badge badge-yellow'
        ),
        array(
          'title' => __( 'バッジ（ブラウン）', THEME_NAME ),
          'inline' => 'span',
          'classes' => 'badge badge-brown'
        ),
        array(
          'title' => __( 'バッジ（グレー）', THEME_NAME ),
          'inline' => 'span',
          'classes' => 'badge badge-grey'
        ),
      ),
    ),
    array(
      'title' => __( 'ボタン（β機能）', THEME_NAME ),
      'items' => array(

        array(
          'title' => __( 'レッド（小）', THEME_NAME ),
          'inline' => 'a',
          'classes' => 'btn btn-red'
        ),
        array(
          'title' => __( 'レッド（中）', THEME_NAME ),
          'inline' => 'a',
          'classes' => 'btn btn-red btn-m'
        ),
        array(
          'title' => __( 'レッド（大）', THEME_NAME ),
          'inline' => 'a',
          'classes' => 'btn btn-red btn-l',
        ),

        array(
          'title' => __( 'ピンク（小）', THEME_NAME ),
          'inline' => 'a',
          'classes' => 'btn btn-pink'
        ),
        array(
          'title' => __( 'ピンク（中）', THEME_NAME ),
          'inline' => 'a',
          'classes' => 'btn btn-pink btn-m'
        ),
        array(
          'title' => __( 'ピンク（大）', THEME_NAME ),
          'inline' => 'a',
          'classes' => 'btn btn-pink btn-l'
        ),

        array(
          'title' => __( 'パープル（小）', THEME_NAME ),
          'inline' => 'a',
          'classes' => 'btn btn-purple'
        ),
        array(
          'title' => __( 'パープル（中）', THEME_NAME ),
          'inline' => 'a',
          'classes' => 'btn btn-purple btn-m'
        ),
        array(
          'title' => __( 'パープル（大）', THEME_NAME ),
          'inline' => 'a',
          'classes' => 'btn btn-purple btn-l'
        ),

        array(
          'title' => __( 'ディープパープル（小）', THEME_NAME ),
          'inline' => 'a',
          'classes' => 'btn btn-deep'
        ),
        array(
          'title' => __( 'ディープパープル（中）', THEME_NAME ),
          'inline' => 'a',
          'classes' => 'btn btn-deep btn-m'
        ),
        array(
          'title' => __( 'ディープパープル（大）', THEME_NAME ),
          'inline' => 'a',
          'classes' => 'btn btn-deep btn-l'
        ),

        array(
          'title' => __( 'インディゴ[紺色]（小）', THEME_NAME ),
          'inline' => 'a',
          'classes' => 'btn btn-indigo'
        ),
        array(
          'title' => __( 'インディゴ[紺色]（中）', THEME_NAME ),
          'inline' => 'a',
          'classes' => 'btn btn-indigo btn-m'
        ),
        array(
          'title' => __( 'インディゴ[紺色]（大）', THEME_NAME ),
          'inline' => 'a',
          'classes' => 'btn btn-indigo btn-l'
        ),

        array(
          'title' => __( 'ブルー（小）', THEME_NAME ),
          'inline' => 'a',
          'classes' => 'btn btn-blue'
        ),
        array(
          'title' => __( 'ブルー（中）', THEME_NAME ),
          'inline' => 'a',
          'classes' => 'btn btn-blue btn-m'
        ),
        array(
          'title' => __( 'ブルー（大）', THEME_NAME ),
          'inline' => 'a',
          'classes' => 'btn btn-blue btn-l'
        ),

        array(
          'title' => __( 'ライトブルー（小）', THEME_NAME ),
          'inline' => 'a',
          'classes' => 'btn btn-light-blue'
        ),
        array(
          'title' => __( 'ライトブルー（中）', THEME_NAME ),
          'inline' => 'a',
          'classes' => 'btn btn-light-blue btn-m'
        ),
        array(
          'title' => __( 'ライトブルー（大）', THEME_NAME ),
          'inline' => 'a',
          'classes' => 'btn btn-light-blue btn-l'
        ),

        array(
          'title' => __( 'シアン（小）', THEME_NAME ),
          'inline' => 'a',
          'classes' => 'btn btn-cyan'
        ),
        array(
          'title' => __( 'シアン（中）', THEME_NAME ),
          'inline' => 'a',
          'classes' => 'btn btn-cyan btn-m'
        ),
        array(
          'title' => __( 'シアン（大）', THEME_NAME ),
          'inline' => 'a',
          'classes' => 'btn btn-cyan btn-l'
        ),

        array(
          'title' => __( 'ティール[緑色がかった青]（小）', THEME_NAME ),
          'inline' => 'a',
          'classes' => 'btn btn-teal'
        ),
        array(
          'title' => __( 'ティール[緑色がかった青]（中）', THEME_NAME ),
          'inline' => 'a',
          'classes' => 'btn btn-teal btn-m'
        ),
        array(
          'title' => __( 'ティール[緑色がかった青]（大）', THEME_NAME ),
          'inline' => 'a',
          'classes' => 'btn btn-teal btn-l'
        ),

        array(
          'title' => __( 'グリーン（小）', THEME_NAME ),
          'inline' => 'a',
          'classes' => 'btn btn-green'
        ),
        array(
          'title' => __( 'グリーン（中）', THEME_NAME ),
          'inline' => 'a',
          'classes' => 'btn btn-green btn-m'
        ),
        array(
          'title' => __( 'グリーン（大）', THEME_NAME ),
          'inline' => 'a',
          'classes' => 'btn btn-green btn-l'
        ),

        array(
          'title' => __( 'ライトグリーン（小）', THEME_NAME ),
          'inline' => 'a',
          'classes' => 'btn btn-light-green'
        ),
        array(
          'title' => __( 'ライトグリーン（中）', THEME_NAME ),
          'inline' => 'a',
          'classes' => 'btn btn-light-green btn-m'
        ),
        array(
          'title' => __( 'ライトグリーン（大）', THEME_NAME ),
          'inline' => 'a',
          'classes' => 'btn btn-light-green btn-l'
        ),

        array(
          'title' => __( 'ライム（小）', THEME_NAME ),
          'inline' => 'a',
          'classes' => 'btn btn-lime'
        ),
        array(
          'title' => __( 'ライム（中）', THEME_NAME ),
          'inline' => 'a',
          'classes' => 'btn btn-lime btn-m'
        ),
        array(
          'title' => __( 'ライム（大）', THEME_NAME ),
          'inline' => 'a',
          'classes' => 'btn btn-lime btn-l'
        ),

        array(
          'title' => __( 'イエロー（小）', THEME_NAME ),
          'inline' => 'a',
          'classes' => 'btn btn-yellow'
        ),
        array(
          'title' => __( 'イエロー（中）', THEME_NAME ),
          'inline' => 'a',
          'classes' => 'btn btn-yellow btn-m'
        ),
        array(
          'title' => __( 'イエロー（大）', THEME_NAME ),
          'inline' => 'a',
          'classes' => 'btn btn-yellow btn-l'
        ),

        array(
          'title' => __( 'アンバー[琥珀色]（小）', THEME_NAME ),
          'inline' => 'a',
          'classes' => 'btn btn-amber'
        ),
        array(
          'title' => __( 'アンバー[琥珀色]（中）', THEME_NAME ),
          'inline' => 'a',
          'classes' => 'btn btn-amber btn-m'
        ),
        array(
          'title' => __( 'アンバー[琥珀色]（大）', THEME_NAME ),
          'inline' => 'a',
          'classes' => 'btn btn-amber btn-l'
        ),

        array(
          'title' => __( 'オレンジ（小）', THEME_NAME ),
          'inline' => 'a',
          'classes' => 'btn btn-orange'
        ),
        array(
          'title' => __( 'オレンジ（中）', THEME_NAME ),
          'inline' => 'a',
          'classes' => 'btn btn-orange btn-m'
        ),
        array(
          'title' => __( 'オレンジ（大）', THEME_NAME ),
          'inline' => 'a',
          'classes' => 'btn btn-orange btn-l'
        ),

        array(
          'title' => __( 'ディープオレンジ（小）', THEME_NAME ),
          'inline' => 'a',
          'classes' => 'btn btn-deep-orange'
        ),
        array(
          'title' => __( 'ディープオレンジ（中）', THEME_NAME ),
          'inline' => 'a',
          'classes' => 'btn btn-deep-orange btn-m'
        ),
        array(
          'title' => __( 'ディープオレンジ（大）', THEME_NAME ),
          'inline' => 'a',
          'classes' => 'btn btn-deep-orange btn-l'
        ),

        array(
          'title' => __( 'ブラウン（小）', THEME_NAME ),
          'inline' => 'a',
          'classes' => 'btn btn-brown'
        ),
        array(
          'title' => __( 'ブラウン（中）', THEME_NAME ),
          'inline' => 'a',
          'classes' => 'btn btn-brown btn-m'
        ),
        array(
          'title' => __( 'ブラウン（大）', THEME_NAME ),
          'inline' => 'a',
          'classes' => 'btn btn-brown btn-l'
        ),

        array(
          'title' => __( 'グレー（小）', THEME_NAME ),
          'inline' => 'a',
          'classes' => 'btn btn-grey'
        ),
        array(
          'title' => __( 'グレー（中）', THEME_NAME ),
          'inline' => 'a',
          'classes' => 'btn btn-grey btn-m'
        ),
        array(
          'title' => __( 'グレー（大）', THEME_NAME ),
          'inline' => 'a',
          'classes' => 'btn btn-grey btn-l'
        ),

      ),
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
add_filter('mce_buttons_2', 'add_ilc_mce_buttons_to_bar');
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

//TinyMCEに次ページボタン追加
add_filter( 'mce_buttons', 'my_add_next_page_button', 1, 2 );
if ( !function_exists( 'my_add_next_page_button' ) ):
function my_add_next_page_button( $buttons, $id ){
  //コンテントエディターのみに挿入
  if ( 'content' != $id )
      return $buttons;

  //moreタグボタンの後に挿入
  array_splice( $buttons, 13, 0, 'wp_page' );

  return $buttons;
}
endif;
