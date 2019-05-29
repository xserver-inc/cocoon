<?php //トップへ戻るボタン
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

if ( is_go_to_top_button_visible() ): //トップへ戻るボタンを表示するか
  $on = null;
  //AMP用のイベントを設定
  if (is_amp()) {
    $on = AMP_GO_TO_TOP_ON_CODE;
  }
?>
<div id="go-to-top" class="go-to-top">
  <?php if ( get_go_to_top_button_image_url() ): //カスタマイザーでトップへ戻る画像が指定されている時 ?>
    <a class="go-to-top-button go-to-top-common go-to-top-hide go-to-top-button-image"<?php echo $on; ?>><img src="<?php echo get_go_to_top_button_image_url(); ?>" alt="<?php _e( 'トップへ戻る', THEME_NAME ) ?>"></a>
  <?php else: ?>
    <a class="go-to-top-button go-to-top-common go-to-top-hide go-to-top-button-icon-font"<?php echo $on; ?>><span class="fa <?php echo get_go_to_top_button_icon_font(); //Font Awesomeアイコンフォントの取得 ?>"></span></a>
  <?php endif ?>
</div>
<?php endif; ?>
<?php //AMPトップへ戻るボタン用のトリガー
if (is_amp() && (is_go_to_top_button_visible())): ?>
  <div class="go-to-top-trigger">
    <amp-position-observer on="enter:hide-page-top.start; exit:show-page-top.start" layout="nodisplay"></amp-position-observer>
  </div>
<?php endif ?>
