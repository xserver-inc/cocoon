<?php //AMPページでは呼び出さない（通常ページのみで呼び出す）
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */

if (!is_amp()): ?>
  <?php //Pinterestシェア用のスクリプト
  if (is_singular() && is_pinterest_share_pin_visible()): ?>
  <script async defer data-pin-height="28" data-pin-hover="true" src="//assets.pinterest.com/js/pinit.js"></script>
  <?php endif ?>
  <?php //Pinterestシェアボタン用のスクリプト
  if (is_singular() && (is_top_twitter_share_button_visible() || is_bottom_pinterest_share_button_visible())): ?>
  <script>!function(d,i){if(!d.getElementById(i)){var j=d.createElement("script");j.id=i;j.src="//assets.pinterest.com/js/pinit_main.js";var w=d.getElementById(i);d.body.appendChild(j);}}(document,"pinterest-btn-js");</script>
  <?php endif ?>
  <?php //本文中のJavaScriptをまとめて出力
  global $_THE_CONTENT_SCRIPTS;
  if ($_THE_CONTENT_SCRIPTS): ?>
  <script><?php echo $_THE_CONTENT_SCRIPTS; ?></script>
  <?php endif ?>
<?php endif ?>

