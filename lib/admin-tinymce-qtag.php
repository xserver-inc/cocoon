<?php //クイックタグ関係の関数
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

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
      QTags.addButton('qt-ruby','<?php _e( 'ふりがな', THEME_NAME ) ?>','<ruby>','<rt><?php _e( 'ふりがな', THEME_NAME ) ?></rt></ruby>');
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
  $font_sizes = array();
  for ($i=12; $i <= 22 ; $i++) {
    $font_sizes[] = array(
          'title' => $i.'px',
          'inline' => 'span',
          'classes' => 'fz-'.$i.'px'
        );
    $i++;
  }
  for ($i=24; $i <= 48 ; $i++) {
    $font_sizes[] = array(
          'title' => $i.'px',
          'inline' => 'span',
          'classes' => 'fz-'.$i.'px'
        );
    $i++;
    $i++;
    $i++;
  }
  //_v($font_sizes);
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
          'title' => __( '赤太字', THEME_NAME ),
          'inline' => 'span',
          'classes' => 'bold red'
        ),
        array(
          'title' => __( '赤アンダーライン', THEME_NAME ),
          'inline' => 'span',
          'classes' => 'red-under'
        ),
        array(
          'title' => __( '青', THEME_NAME ),
          'inline' => 'span',
          'classes' => 'blue'
        ),
        array(
          'title' => __( '青太字', THEME_NAME ),
          'inline' => 'span',
          'classes' => 'bold blue'
        ),
        array(
          'title' => __( '緑', THEME_NAME ),
          'inline' => 'span',
          'classes' => 'green'
        ),
        array(
          'title' => __( '緑太字', THEME_NAME ),
          'inline' => 'span',
          'classes' => 'bold green'
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
      'title' => __( 'マーカー', THEME_NAME ),
      'items' => array(
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
      ),
    ),
    array(
      'title' => __( 'フォントサイズ', THEME_NAME ),
      'items' => $font_sizes,
    ),
    array(
      'title' => __( 'ボックス（アイコン）', THEME_NAME ),
      'items' => array(
        array(
          'title' => __( '補足情報(i)', THEME_NAME ),
          'block' => 'div',
          'classes' => 'information-box common-icon-box'
        ),
        array(
          'title' => __( '補足情報(?)', THEME_NAME ),
          'block' => 'div',
          'classes' => 'question-box common-icon-box'
        ),
        array(
          'title' => __( '注意喚起(!)', THEME_NAME ),
          'block' => 'div',
          'classes' => 'alert-box common-icon-box'
        ),
        array(
          'title' => __( 'メモ', THEME_NAME ),
          'block' => 'div',
          'classes' => 'memo-box common-icon-box'
        ),
        array(
          'title' => __( 'コメント', THEME_NAME ),
          'block' => 'div',
          'classes' => 'comment-box common-icon-box'
        ),
        array(
          'title' => __( 'OK', THEME_NAME ),
          'block' => 'div',
          'classes' => 'ok-box common-icon-box'
        ),
        array(
          'title' => __( 'NG', THEME_NAME ),
          'block' => 'div',
          'classes' => 'ng-box common-icon-box'
        ),
        array(
          'title' => __( 'GOOD', THEME_NAME ),
          'block' => 'div',
          'classes' => 'good-box common-icon-box'
        ),
        array(
          'title' => __( 'BAD', THEME_NAME ),
          'block' => 'div',
          'classes' => 'bad-box common-icon-box'
        ),
        array(
          'title' => __( 'プロフィール', THEME_NAME ),
          'block' => 'div',
          'classes' => 'profile-box common-icon-box'
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
          'title' => __( 'セカンダリー（濃い灰色）', THEME_NAME ),
          'block' => 'div',
          'classes' => 'secondary-box'
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
        array(
          'title' => __( 'ライト（白色）', THEME_NAME ),
          'block' => 'div',
          'classes' => 'light-box',
        ),
        array(
          'title' => __( 'ダーク（暗い灰色）', THEME_NAME ),
          'block' => 'div',
          'classes' => 'dark-box',
        ),
      ),
    ),

    array(
      'title' => __( 'ボックス（白抜き）', THEME_NAME ),
      'items' => array(
        array(
          'title' => __( '灰色', THEME_NAME ),
          'block' => 'div',
          'classes' => 'blank-box'
        ),
        array(
          'title' => __( '黄色', THEME_NAME ),
          'block' => 'div',
          'classes' => 'blank-box bb-yellow'
        ),
        array(
          'title' => __( '赤色', THEME_NAME ),
          'block' => 'div',
          'classes' => 'blank-box bb-red'
        ),
        array(
          'title' => __( '青色', THEME_NAME ),
          'block' => 'div',
          'classes' => 'blank-box bb-blue'
        ),
        array(
          'title' => __( '緑色', THEME_NAME ),
          'block' => 'div',
          'classes' => 'blank-box bb-green'
        ),
      ),
    ),

    array(
      'title' => __( 'ボックス（タブ）', THEME_NAME ),
      'items' => array(
        array(
          'title' => __( 'チェック', THEME_NAME ),
          'items' => array(
            array(
              'title' => __( '灰色', THEME_NAME ),
              'block' => 'div',
              'classes' => 'blank-box bb-tab bb-check'
            ),
            array(
              'title' => __( '黄色', THEME_NAME ),
              'block' => 'div',
              'classes' => 'blank-box bb-tab bb-check bb-yellow'
            ),
            array(
              'title' => __( '赤色', THEME_NAME ),
              'block' => 'div',
              'classes' => 'blank-box bb-tab bb-check bb-red'
            ),
            array(
              'title' => __( '青色', THEME_NAME ),
              'block' => 'div',
              'classes' => 'blank-box bb-tab bb-check bb-blue'
            ),
            array(
              'title' => __( '緑色', THEME_NAME ),
              'block' => 'div',
              'classes' => 'blank-box bb-tab bb-check bb-green'
            ),
          ),
        ),
        array(
          'title' => __( 'コメント', THEME_NAME ),
          'items' => array(
            array(
              'title' => __( '灰色', THEME_NAME ),
              'block' => 'div',
              'classes' => 'blank-box bb-tab bb-comment'
            ),
            array(
              'title' => __( '黄色', THEME_NAME ),
              'block' => 'div',
              'classes' => 'blank-box bb-tab bb-comment bb-yellow'
            ),
            array(
              'title' => __( '赤色', THEME_NAME ),
              'block' => 'div',
              'classes' => 'blank-box bb-tab bb-comment bb-red'
            ),
            array(
              'title' => __( '青色', THEME_NAME ),
              'block' => 'div',
              'classes' => 'blank-box bb-tab bb-comment bb-blue'
            ),
            array(
              'title' => __( '緑色', THEME_NAME ),
              'block' => 'div',
              'classes' => 'blank-box bb-tab bb-comment bb-green'
            ),
          ),
        ),
        array(
          'title' => __( 'ポイント', THEME_NAME ),
          'items' => array(
            array(
              'title' => __( '灰色', THEME_NAME ),
              'block' => 'div',
              'classes' => 'blank-box bb-tab bb-point'
            ),
            array(
              'title' => __( '黄色', THEME_NAME ),
              'block' => 'div',
              'classes' => 'blank-box bb-tab bb-point bb-yellow'
            ),
            array(
              'title' => __( '赤色', THEME_NAME ),
              'block' => 'div',
              'classes' => 'blank-box bb-tab bb-point bb-red'
            ),
            array(
              'title' => __( '青色', THEME_NAME ),
              'block' => 'div',
              'classes' => 'blank-box bb-tab bb-point bb-blue'
            ),
            array(
              'title' => __( '緑色', THEME_NAME ),
              'block' => 'div',
              'classes' => 'blank-box bb-tab bb-point bb-green'
            ),
          ),
        ),
        array(
          'title' => __( 'ティップス', THEME_NAME ),
          'items' => array(
            array(
              'title' => __( '灰色', THEME_NAME ),
              'block' => 'div',
              'classes' => 'blank-box bb-tab bb-tips'
            ),
            array(
              'title' => __( '黄色', THEME_NAME ),
              'block' => 'div',
              'classes' => 'blank-box bb-tab bb-tips bb-yellow'
            ),
            array(
              'title' => __( '赤色', THEME_NAME ),
              'block' => 'div',
              'classes' => 'blank-box bb-tab bb-tips bb-red'
            ),
            array(
              'title' => __( '青色', THEME_NAME ),
              'block' => 'div',
              'classes' => 'blank-box bb-tab bb-tips bb-blue'
            ),
            array(
              'title' => __( '緑色', THEME_NAME ),
              'block' => 'div',
              'classes' => 'blank-box bb-tab bb-tips bb-green'
            ),
          ),
        ),
        array(
          'title' => __( 'ヒント', THEME_NAME ),
          'items' => array(
            array(
              'title' => __( '灰色', THEME_NAME ),
              'block' => 'div',
              'classes' => 'blank-box bb-tab bb-hint'
            ),
            array(
              'title' => __( '黄色', THEME_NAME ),
              'block' => 'div',
              'classes' => 'blank-box bb-tab bb-hint bb-yellow'
            ),
            array(
              'title' => __( '赤色', THEME_NAME ),
              'block' => 'div',
              'classes' => 'blank-box bb-tab bb-hint bb-red'
            ),
            array(
              'title' => __( '青色', THEME_NAME ),
              'block' => 'div',
              'classes' => 'blank-box bb-tab bb-hint bb-blue'
            ),
            array(
              'title' => __( '緑色', THEME_NAME ),
              'block' => 'div',
              'classes' => 'blank-box bb-tab bb-hint bb-green'
            ),
          ),
        ),
        array(
          'title' => __( 'ピックアップ', THEME_NAME ),
          'items' => array(
            array(
              'title' => __( '灰色', THEME_NAME ),
              'block' => 'div',
              'classes' => 'blank-box bb-tab bb-pickup'
            ),
            array(
              'title' => __( '黄色', THEME_NAME ),
              'block' => 'div',
              'classes' => 'blank-box bb-tab bb-pickup bb-yellow'
            ),
            array(
              'title' => __( '赤色', THEME_NAME ),
              'block' => 'div',
              'classes' => 'blank-box bb-tab bb-pickup bb-red'
            ),
            array(
              'title' => __( '青色', THEME_NAME ),
              'block' => 'div',
              'classes' => 'blank-box bb-tab bb-pickup bb-blue'
            ),
            array(
              'title' => __( '緑色', THEME_NAME ),
              'block' => 'div',
              'classes' => 'blank-box bb-tab bb-pickup bb-green'
            ),
          ),
        ),
        array(
          'title' => __( 'ブックマーク', THEME_NAME ),
          'items' => array(
            array(
              'title' => __( '灰色', THEME_NAME ),
              'block' => 'div',
              'classes' => 'blank-box bb-tab bb-bookmark'
            ),
            array(
              'title' => __( '黄色', THEME_NAME ),
              'block' => 'div',
              'classes' => 'blank-box bb-tab bb-bookmark bb-yellow'
            ),
            array(
              'title' => __( '赤色', THEME_NAME ),
              'block' => 'div',
              'classes' => 'blank-box bb-tab bb-bookmark bb-red'
            ),
            array(
              'title' => __( '青色', THEME_NAME ),
              'block' => 'div',
              'classes' => 'blank-box bb-tab bb-bookmark bb-blue'
            ),
            array(
              'title' => __( '緑色', THEME_NAME ),
              'block' => 'div',
              'classes' => 'blank-box bb-tab bb-bookmark bb-green'
            ),
          ),
        ),
        array(
          'title' => __( 'メモ', THEME_NAME ),
          'items' => array(
            array(
              'title' => __( '灰色', THEME_NAME ),
              'block' => 'div',
              'classes' => 'blank-box bb-tab bb-memo'
            ),
            array(
              'title' => __( '黄色', THEME_NAME ),
              'block' => 'div',
              'classes' => 'blank-box bb-tab bb-memo bb-yellow'
            ),
            array(
              'title' => __( '赤色', THEME_NAME ),
              'block' => 'div',
              'classes' => 'blank-box bb-tab bb-memo bb-red'
            ),
            array(
              'title' => __( '青色', THEME_NAME ),
              'block' => 'div',
              'classes' => 'blank-box bb-tab bb-memo bb-blue'
            ),
            array(
              'title' => __( '緑色', THEME_NAME ),
              'block' => 'div',
              'classes' => 'blank-box bb-tab bb-memo bb-green'
            ),
          ),
        ),
        array(
          'title' => __( 'ダウンロード', THEME_NAME ),
          'items' => array(
            array(
              'title' => __( '灰色', THEME_NAME ),
              'block' => 'div',
              'classes' => 'blank-box bb-tab bb-download'
            ),
            array(
              'title' => __( '黄色', THEME_NAME ),
              'block' => 'div',
              'classes' => 'blank-box bb-tab bb-download bb-yellow'
            ),
            array(
              'title' => __( '赤色', THEME_NAME ),
              'block' => 'div',
              'classes' => 'blank-box bb-tab bb-download bb-red'
            ),
            array(
              'title' => __( '青色', THEME_NAME ),
              'block' => 'div',
              'classes' => 'blank-box bb-tab bb-download bb-blue'
            ),
            array(
              'title' => __( '緑色', THEME_NAME ),
              'block' => 'div',
              'classes' => 'blank-box bb-tab bb-download bb-green'
            ),
          ),
        ),
        array(
          'title' => __( 'ブレイク', THEME_NAME ),
          'items' => array(
            array(
              'title' => __( '灰色', THEME_NAME ),
              'block' => 'div',
              'classes' => 'blank-box bb-tab bb-break'
            ),
            array(
              'title' => __( '黄色', THEME_NAME ),
              'block' => 'div',
              'classes' => 'blank-box bb-tab bb-break bb-yellow'
            ),
            array(
              'title' => __( '赤色', THEME_NAME ),
              'block' => 'div',
              'classes' => 'blank-box bb-tab bb-break bb-red'
            ),
            array(
              'title' => __( '青色', THEME_NAME ),
              'block' => 'div',
              'classes' => 'blank-box bb-tab bb-break bb-blue'
            ),
            array(
              'title' => __( '緑色', THEME_NAME ),
              'block' => 'div',
              'classes' => 'blank-box bb-tab bb-break bb-green'
            ),
          ),
        ),

        array(
          'title' => __( 'Amazon', THEME_NAME ),
          'items' => array(
            array(
              'title' => __( '灰色', THEME_NAME ),
              'block' => 'div',
              'classes' => 'blank-box bb-tab bb-amazon'
            ),
            array(
              'title' => __( '黄色', THEME_NAME ),
              'block' => 'div',
              'classes' => 'blank-box bb-tab bb-amazon bb-yellow'
            ),
            array(
              'title' => __( '赤色', THEME_NAME ),
              'block' => 'div',
              'classes' => 'blank-box bb-tab bb-amazon bb-red'
            ),
            array(
              'title' => __( '青色', THEME_NAME ),
              'block' => 'div',
              'classes' => 'blank-box bb-tab bb-amazon bb-blue'
            ),
            array(
              'title' => __( '緑色', THEME_NAME ),
              'block' => 'div',
              'classes' => 'blank-box bb-tab bb-amazon bb-green'
            ),
          ),
        ),

        array(
          'title' => __( 'OK', THEME_NAME ),
          'items' => array(
            array(
              'title' => __( '灰色', THEME_NAME ),
              'block' => 'div',
              'classes' => 'blank-box bb-tab bb-ok'
            ),
            array(
              'title' => __( '黄色', THEME_NAME ),
              'block' => 'div',
              'classes' => 'blank-box bb-tab bb-ok bb-yellow'
            ),
            array(
              'title' => __( '赤色', THEME_NAME ),
              'block' => 'div',
              'classes' => 'blank-box bb-tab bb-ok bb-red'
            ),
            array(
              'title' => __( '青色', THEME_NAME ),
              'block' => 'div',
              'classes' => 'blank-box bb-tab bb-ok bb-blue'
            ),
            array(
              'title' => __( '緑色', THEME_NAME ),
              'block' => 'div',
              'classes' => 'blank-box bb-tab bb-ok bb-green'
            ),
          ),
        ),

        array(
          'title' => __( 'NG', THEME_NAME ),
          'items' => array(
            array(
              'title' => __( '灰色', THEME_NAME ),
              'block' => 'div',
              'classes' => 'blank-box bb-tab bb-ng'
            ),
            array(
              'title' => __( '黄色', THEME_NAME ),
              'block' => 'div',
              'classes' => 'blank-box bb-tab bb-ng bb-yellow'
            ),
            array(
              'title' => __( '赤色', THEME_NAME ),
              'block' => 'div',
              'classes' => 'blank-box bb-tab bb-ng bb-red'
            ),
            array(
              'title' => __( '青色', THEME_NAME ),
              'block' => 'div',
              'classes' => 'blank-box bb-tab bb-ng bb-blue'
            ),
            array(
              'title' => __( '緑色', THEME_NAME ),
              'block' => 'div',
              'classes' => 'blank-box bb-tab bb-ng bb-green'
            ),
          ),
        ),

        array(
          'title' => __( 'GOOD', THEME_NAME ),
          'items' => array(
            array(
              'title' => __( '灰色', THEME_NAME ),
              'block' => 'div',
              'classes' => 'blank-box bb-tab bb-good'
            ),
            array(
              'title' => __( '黄色', THEME_NAME ),
              'block' => 'div',
              'classes' => 'blank-box bb-tab bb-good bb-yellow'
            ),
            array(
              'title' => __( '赤色', THEME_NAME ),
              'block' => 'div',
              'classes' => 'blank-box bb-tab bb-good bb-red'
            ),
            array(
              'title' => __( '青色', THEME_NAME ),
              'block' => 'div',
              'classes' => 'blank-box bb-tab bb-good bb-blue'
            ),
            array(
              'title' => __( '緑色', THEME_NAME ),
              'block' => 'div',
              'classes' => 'blank-box bb-tab bb-good bb-green'
            ),
          ),
        ),

        array(
          'title' => __( 'BAD', THEME_NAME ),
          'items' => array(
            array(
              'title' => __( '灰色', THEME_NAME ),
              'block' => 'div',
              'classes' => 'blank-box bb-tab bb-bad'
            ),
            array(
              'title' => __( '黄色', THEME_NAME ),
              'block' => 'div',
              'classes' => 'blank-box bb-tab bb-bad bb-yellow'
            ),
            array(
              'title' => __( '赤色', THEME_NAME ),
              'block' => 'div',
              'classes' => 'blank-box bb-tab bb-bad bb-red'
            ),
            array(
              'title' => __( '青色', THEME_NAME ),
              'block' => 'div',
              'classes' => 'blank-box bb-tab bb-bad bb-blue'
            ),
            array(
              'title' => __( '緑色', THEME_NAME ),
              'block' => 'div',
              'classes' => 'blank-box bb-tab bb-bad bb-green'
            ),
          ),
        ),

        array(
          'title' => __( 'プロフィール', THEME_NAME ),
          'items' => array(
            array(
              'title' => __( '灰色', THEME_NAME ),
              'block' => 'div',
              'classes' => 'blank-box bb-tab bb-profile'
            ),
            array(
              'title' => __( '黄色', THEME_NAME ),
              'block' => 'div',
              'classes' => 'blank-box bb-tab bb-profile bb-yellow'
            ),
            array(
              'title' => __( '赤色', THEME_NAME ),
              'block' => 'div',
              'classes' => 'blank-box bb-tab bb-profile bb-red'
            ),
            array(
              'title' => __( '青色', THEME_NAME ),
              'block' => 'div',
              'classes' => 'blank-box bb-tab bb-profile bb-blue'
            ),
            array(
              'title' => __( '緑色', THEME_NAME ),
              'block' => 'div',
              'classes' => 'blank-box bb-tab bb-profile bb-green'
            ),
          ),
        ),

      ),
    ),
    array(
      'title' => __( 'ボックス（付箋風）', THEME_NAME ),
      'items' => array(
        array(
          'title' => __( '灰色', THEME_NAME ),
          'block' => 'div',
          'classes' => 'blank-box sticky'
        ),
        array(
          'title' => __( '黄色', THEME_NAME ),
          'block' => 'div',
          'classes' => 'blank-box sticky st-yellow'
        ),
        array(
          'title' => __( '赤色', THEME_NAME ),
          'block' => 'div',
          'classes' => 'blank-box sticky st-red'
        ),
        array(
          'title' => __( '青色', THEME_NAME ),
          'block' => 'div',
          'classes' => 'blank-box sticky st-blue'
        ),
        array(
          'title' => __( '緑色', THEME_NAME ),
          'block' => 'div',
          'classes' => 'blank-box sticky st-green'
        ),
      ),
    ),
    array(
      'title' => __( 'バッジ', THEME_NAME ),
      'items' => array(
        array(
          'title' => __( 'オレンジ', THEME_NAME ),
          'inline' => 'span',
          'classes' => 'badge'
        ),
        array(
          'title' => __( 'レッド', THEME_NAME ),
          'inline' => 'span',
          'classes' => 'badge badge-red'
        ),
        array(
          'title' => __( 'ピンク', THEME_NAME ),
          'inline' => 'span',
          'classes' => 'badge badge-pink'
        ),
        array(
          'title' => __( 'パープル', THEME_NAME ),
          'inline' => 'span',
          'classes' => 'badge badge-purple'
        ),
        array(
          'title' => __( 'ブルー', THEME_NAME ),
          'inline' => 'span',
          'classes' => 'badge badge-blue'
        ),
        array(
          'title' => __( 'グリーン', THEME_NAME ),
          'inline' => 'span',
          'classes' => 'badge badge-green'
        ),
        array(
          'title' => __( 'イエロー', THEME_NAME ),
          'inline' => 'span',
          'classes' => 'badge badge-yellow'
        ),
        array(
          'title' => __( 'ブラウン', THEME_NAME ),
          'inline' => 'span',
          'classes' => 'badge badge-brown'
        ),
        array(
          'title' => __( 'グレー', THEME_NAME ),
          'inline' => 'span',
          'classes' => 'badge badge-grey'
        ),
      ),
    ),
    array(
      'title' => __( 'マイクロコピー', THEME_NAME ),
      'items' => array(
        array(
          'title' => __( 'テキスト（上）', THEME_NAME ),
          'items' => array(
            array(
              'title' => __( '左側', THEME_NAME ),
              'block' => 'div',
              'classes' => 'micro-top micro-left'
            ),
            array(
              'title' => __( '中央', THEME_NAME ),
              'block' => 'div',
              'classes' => 'micro-top micro-center'
            ),
            array(
              'title' => __( '右側', THEME_NAME ),
              'block' => 'div',
              'classes' => 'micro-top micro-right'
            ),
          ),
        ),
        array(
          'title' => __( 'テキスト（下）', THEME_NAME ),
          'items' => array(
            array(
              'title' => __( '左側', THEME_NAME ),
              'block' => 'div',
              'classes' => 'micro-bottom micro-left'
            ),
            array(
              'title' => __( '中央', THEME_NAME ),
              'block' => 'div',
              'classes' => 'micro-bottom micro-center'
            ),
            array(
              'title' => __( '右側', THEME_NAME ),
              'block' => 'div',
              'classes' => 'micro-bottom micro-right'
            ),
          ),
        ),
        array(
          'title' => __( '吹き出し（上）', THEME_NAME ),
          'items' => array(
            array(
              'title' => __( '左側', THEME_NAME ),
              'block' => 'div',
              'classes' => 'micro-top micro-balloon micro-balloon-left'
            ),
            array(
              'title' => __( '中央', THEME_NAME ),
              'block' => 'div',
              'classes' => 'micro-top micro-balloon micro-balloon-center'
            ),
            array(
              'title' => __( '右側', THEME_NAME ),
              'block' => 'div',
              'classes' => 'micro-top micro-balloon micro-balloon-right'
            ),
          ),
        ),
        array(
          'title' => __( '吹き出し（下）', THEME_NAME ),
          'items' => array(

            array(
              'title' => __( '左側', THEME_NAME ),
              'block' => 'div',
              'classes' => 'micro-bottom micro-balloon micro-balloon-left'
            ),
            array(
              'title' => __( '中央', THEME_NAME ),
              'block' => 'div',
              'classes' => 'micro-bottom micro-balloon micro-balloon-center'
            ),
            array(
              'title' => __( '右側', THEME_NAME ),
              'block' => 'div',
              'classes' => 'micro-bottom micro-balloon micro-balloon-right'
            ),
          ),
        ),
      ),
    ),
    array(
      'title' => __( 'ボタン', THEME_NAME ),
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

    array(
      'title' => __( '囲みボタン', THEME_NAME ),
      'items' => array(

        array(
          'title' => __( 'レッド（小）', THEME_NAME ),
          'inline' => 'span',
          'wrapper' => true,
          'classes' => 'btn-wrap btn-wrap-red'
        ),
        array(
          'title' => __( 'レッド（中）', THEME_NAME ),
          'inline' => 'span',
          'wrapper' => true,
          'classes' => 'btn-wrap btn-wrap-red btn-wrap-m'
        ),
        array(
          'title' => __( 'レッド（大）', THEME_NAME ),
          'block' => 'div',
          'classes' => 'btn-wrap btn-wrap-red btn-wrap-l',
        ),

        array(
          'title' => __( 'ピンク（小）', THEME_NAME ),
          'inline' => 'span',
          'wrapper' => true,
          'classes' => 'btn-wrap btn-wrap-pink'
        ),
        array(
          'title' => __( 'ピンク（中）', THEME_NAME ),
          'inline' => 'span',
          'wrapper' => true,
          'classes' => 'btn-wrap btn-wrap-pink btn-wrap-m'
        ),
        array(
          'title' => __( 'ピンク（大）', THEME_NAME ),
          'block' => 'div',
          'classes' => 'btn-wrap btn-wrap-pink btn-wrap-l'
        ),

        array(
          'title' => __( 'パープル（小）', THEME_NAME ),
          'inline' => 'span',
          'wrapper' => true,
          'classes' => 'btn-wrap btn-wrap-purple'
        ),
        array(
          'title' => __( 'パープル（中）', THEME_NAME ),
          'inline' => 'span',
          'wrapper' => true,
          'classes' => 'btn-wrap btn-wrap-purple btn-wrap-m'
        ),
        array(
          'title' => __( 'パープル（大）', THEME_NAME ),
          'block' => 'div',
          'classes' => 'btn-wrap btn-wrap-purple btn-wrap-l'
        ),

        array(
          'title' => __( 'ディープパープル（小）', THEME_NAME ),
          'inline' => 'span',
          'wrapper' => true,
          'classes' => 'btn-wrap btn-wrap-deep'
        ),
        array(
          'title' => __( 'ディープパープル（中）', THEME_NAME ),
          'inline' => 'span',
          'wrapper' => true,
          'classes' => 'btn-wrap btn-wrap-deep btn-wrap-m'
        ),
        array(
          'title' => __( 'ディープパープル（大）', THEME_NAME ),
          'block' => 'div',
          'classes' => 'btn-wrap btn-wrap-deep btn-wrap-l'
        ),

        array(
          'title' => __( 'インディゴ[紺色]（小）', THEME_NAME ),
          'inline' => 'span',
          'wrapper' => true,
          'classes' => 'btn-wrap btn-wrap-indigo'
        ),
        array(
          'title' => __( 'インディゴ[紺色]（中）', THEME_NAME ),
          'inline' => 'span',
          'wrapper' => true,
          'classes' => 'btn-wrap btn-wrap-indigo btn-wrap-m'
        ),
        array(
          'title' => __( 'インディゴ[紺色]（大）', THEME_NAME ),
          'block' => 'div',
          'classes' => 'btn-wrap btn-wrap-indigo btn-wrap-l'
        ),

        array(
          'title' => __( 'ブルー（小）', THEME_NAME ),
          'inline' => 'span',
          'wrapper' => true,
          'classes' => 'btn-wrap btn-wrap-blue'
        ),
        array(
          'title' => __( 'ブルー（中）', THEME_NAME ),
          'inline' => 'span',
          'wrapper' => true,
          'classes' => 'btn-wrap btn-wrap-blue btn-wrap-m'
        ),
        array(
          'title' => __( 'ブルー（大）', THEME_NAME ),
          'block' => 'div',
          'classes' => 'btn-wrap btn-wrap-blue btn-wrap-l'
        ),

        array(
          'title' => __( 'ライトブルー（小）', THEME_NAME ),
          'inline' => 'span',
          'wrapper' => true,
          'classes' => 'btn-wrap btn-wrap-light-blue'
        ),
        array(
          'title' => __( 'ライトブルー（中）', THEME_NAME ),
          'inline' => 'span',
          'wrapper' => true,
          'classes' => 'btn-wrap btn-wrap-light-blue btn-wrap-m'
        ),
        array(
          'title' => __( 'ライトブルー（大）', THEME_NAME ),
          'block' => 'div',
          'classes' => 'btn-wrap btn-wrap-light-blue btn-wrap-l'
        ),

        array(
          'title' => __( 'シアン（小）', THEME_NAME ),
          'inline' => 'span',
          'wrapper' => true,
          'classes' => 'btn-wrap btn-wrap-cyan'
        ),
        array(
          'title' => __( 'シアン（中）', THEME_NAME ),
          'inline' => 'span',
          'wrapper' => true,
          'classes' => 'btn-wrap btn-wrap-cyan btn-wrap-m'
        ),
        array(
          'title' => __( 'シアン（大）', THEME_NAME ),
          'block' => 'div',
          'classes' => 'btn-wrap btn-wrap-cyan btn-wrap-l'
        ),

        array(
          'title' => __( 'ティール[緑色がかった青]（小）', THEME_NAME ),
          'inline' => 'span',
          'wrapper' => true,
          'classes' => 'btn-wrap btn-wrap-teal'
        ),
        array(
          'title' => __( 'ティール[緑色がかった青]（中）', THEME_NAME ),
          'inline' => 'span',
          'wrapper' => true,
          'classes' => 'btn-wrap btn-wrap-teal btn-wrap-m'
        ),
        array(
          'title' => __( 'ティール[緑色がかった青]（大）', THEME_NAME ),
          'block' => 'div',
          'classes' => 'btn-wrap btn-wrap-teal btn-wrap-l'
        ),

        array(
          'title' => __( 'グリーン（小）', THEME_NAME ),
          'inline' => 'span',
          'wrapper' => true,
          'classes' => 'btn-wrap btn-wrap-green'
        ),
        array(
          'title' => __( 'グリーン（中）', THEME_NAME ),
          'inline' => 'span',
          'wrapper' => true,
          'classes' => 'btn-wrap btn-wrap-green btn-wrap-m'
        ),
        array(
          'title' => __( 'グリーン（大）', THEME_NAME ),
          'block' => 'div',
          'classes' => 'btn-wrap btn-wrap-green btn-wrap-l'
        ),

        array(
          'title' => __( 'ライトグリーン（小）', THEME_NAME ),
          'inline' => 'span',
          'wrapper' => true,
          'classes' => 'btn-wrap btn-wrap-light-green'
        ),
        array(
          'title' => __( 'ライトグリーン（中）', THEME_NAME ),
          'inline' => 'span',
          'wrapper' => true,
          'classes' => 'btn-wrap btn-wrap-light-green btn-wrap-m'
        ),
        array(
          'title' => __( 'ライトグリーン（大）', THEME_NAME ),
          'block' => 'div',
          'classes' => 'btn-wrap btn-wrap-light-green btn-wrap-l'
        ),

        array(
          'title' => __( 'ライム（小）', THEME_NAME ),
          'inline' => 'span',
          'wrapper' => true,
          'classes' => 'btn-wrap btn-wrap-lime'
        ),
        array(
          'title' => __( 'ライム（中）', THEME_NAME ),
          'inline' => 'span',
          'wrapper' => true,
          'classes' => 'btn-wrap btn-wrap-lime btn-wrap-m'
        ),
        array(
          'title' => __( 'ライム（大）', THEME_NAME ),
          'block' => 'div',
          'classes' => 'btn-wrap btn-wrap-lime btn-wrap-l'
        ),

        array(
          'title' => __( 'イエロー（小）', THEME_NAME ),
          'inline' => 'span',
          'wrapper' => true,
          'classes' => 'btn-wrap btn-wrap-yellow'
        ),
        array(
          'title' => __( 'イエロー（中）', THEME_NAME ),
          'inline' => 'span',
          'wrapper' => true,
          'classes' => 'btn-wrap btn-wrap-yellow btn-wrap-m'
        ),
        array(
          'title' => __( 'イエロー（大）', THEME_NAME ),
          'block' => 'div',
          'classes' => 'btn-wrap btn-wrap-yellow btn-wrap-l'
        ),

        array(
          'title' => __( 'アンバー[琥珀色]（小）', THEME_NAME ),
          'inline' => 'span',
          'wrapper' => true,
          'classes' => 'btn-wrap btn-wrap-amber'
        ),
        array(
          'title' => __( 'アンバー[琥珀色]（中）', THEME_NAME ),
          'inline' => 'span',
          'wrapper' => true,
          'classes' => 'btn-wrap btn-wrap-amber btn-wrap-m'
        ),
        array(
          'title' => __( 'アンバー[琥珀色]（大）', THEME_NAME ),
          'block' => 'div',
          'classes' => 'btn-wrap btn-wrap-amber btn-wrap-l'
        ),

        array(
          'title' => __( 'オレンジ（小）', THEME_NAME ),
          'inline' => 'span',
          'wrapper' => true,
          'classes' => 'btn-wrap btn-wrap-orange'
        ),
        array(
          'title' => __( 'オレンジ（中）', THEME_NAME ),
          'inline' => 'span',
          'wrapper' => true,
          'classes' => 'btn-wrap btn-wrap-orange btn-wrap-m'
        ),
        array(
          'title' => __( 'オレンジ（大）', THEME_NAME ),
          'block' => 'div',
          'classes' => 'btn-wrap btn-wrap-orange btn-wrap-l'
        ),

        array(
          'title' => __( 'ディープオレンジ（小）', THEME_NAME ),
          'inline' => 'span',
          'wrapper' => true,
          'classes' => 'btn-wrap btn-wrap-deep-orange'
        ),
        array(
          'title' => __( 'ディープオレンジ（中）', THEME_NAME ),
          'inline' => 'span',
          'wrapper' => true,
          'classes' => 'btn-wrap btn-wrap-deep-orange btn-wrap-m'
        ),
        array(
          'title' => __( 'ディープオレンジ（大）', THEME_NAME ),
          'block' => 'div',
          'classes' => 'btn-wrap btn-wrap-deep-orange btn-wrap-l'
        ),

        array(
          'title' => __( 'ブラウン（小）', THEME_NAME ),
          'inline' => 'span',
          'wrapper' => true,
          'classes' => 'btn-wrap btn-wrap-brown'
        ),
        array(
          'title' => __( 'ブラウン（中）', THEME_NAME ),
          'inline' => 'span',
          'wrapper' => true,
          'classes' => 'btn-wrap btn-wrap-brown btn-wrap-m'
        ),
        array(
          'title' => __( 'ブラウン（大）', THEME_NAME ),
          'block' => 'div',
          'classes' => 'btn-wrap btn-wrap-brown btn-wrap-l'
        ),

        array(
          'title' => __( 'グレー（小）', THEME_NAME ),
          'inline' => 'span',
          'wrapper' => true,
          'classes' => 'btn-wrap btn-wrap-grey'
        ),
        array(
          'title' => __( 'グレー（中）', THEME_NAME ),
          'inline' => 'span',
          'wrapper' => true,
          'classes' => 'btn-wrap btn-wrap-grey btn-wrap-m'
        ),
        array(
          'title' => __( 'グレー（大）', THEME_NAME ),
          'block' => 'div',
          'classes' => 'btn-wrap btn-wrap-grey btn-wrap-l'
        ),

      ),
    ),

    array(
      'title' => __( '囲みブログカードラベル', THEME_NAME ),
      'items' => array(

        array(
          'title' => __( '関連記事', THEME_NAME ),
          'block' => 'div',
          'wrapper' => true,
          'classes' => 'blogcard-type bct-related'
        ),
        array(
          'title' => __( '参考記事', THEME_NAME ),
          'block' => 'div',
          'wrapper' => true,
          'classes' => 'blogcard-type bct-reference'
        ),
        array(
          'title' => __( '参考リンク', THEME_NAME ),
          'block' => 'div',
          'wrapper' => true,
          'classes' => 'blogcard-type bct-reference-link'
        ),
        array(
          'title' => __( '人気記事', THEME_NAME ),
          'block' => 'div',
          'wrapper' => true,
          'classes' => 'blogcard-type bct-popular'
        ),
        array(
          'title' => __( 'あわせて読みたい', THEME_NAME ),
          'block' => 'div',
          'wrapper' => true,
          'classes' => 'blogcard-type bct-together'
        ),
        array(
          'title' => __( '詳細はこちら', THEME_NAME ),
          'block' => 'div',
          'wrapper' => true,
          'classes' => 'blogcard-type bct-detail'
        ),
        array(
          'title' => __( 'チェック', THEME_NAME ),
          'block' => 'div',
          'wrapper' => true,
          'classes' => 'blogcard-type bct-check'
        ),
        array(
          'title' => __( 'ピックアップ', THEME_NAME ),
          'block' => 'div',
          'wrapper' => true,
          'classes' => 'blogcard-type bct-pickup'
        ),
        array(
          'title' => __( '公式サイト', THEME_NAME ),
          'block' => 'div',
          'wrapper' => true,
          'classes' => 'blogcard-type bct-official'
        ),
        array(
          'title' => __( 'ダウンロード', THEME_NAME ),
          'block' => 'div',
          'wrapper' => true,
          'classes' => 'blogcard-type bct-dl'
        ),
      ),
    ),
  );

  //スタイルドロップダウンリスト変更用のフック
  $style_formats = apply_filters('tinymce_style_formats', $style_formats);
  //JSONに変換
  $init_array['style_formats'] = json_encode($style_formats);

  //ビジュアルエディターのフォントサイズ変更機能の文字サイズ指定
  $init_array['fontsize_formats'] = '10px 12px 14px 16px 18px 20px 24px 28px 32px 36px 42px 48px';

  //Tinymce配列用のフック
  $init_array = apply_filters('tinymce_init_array', $init_array);
  return $init_array;
}
endif;

//WordPressビジュアルエディターに文字サイズの変更機能を追加
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
