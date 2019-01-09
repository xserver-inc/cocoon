<?php //Google Tag Managerコード
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

if ( is_analytics() ) {
  //AMP用Google Tag Managerトラッキングコードを設定している場合
  $tracking_id = get_google_tag_manager_amp_tracking_id();

  if ( $tracking_id ) { ?>
  <!-- AMP Google Tag Manager -->
  <amp-analytics config="https://www.googletagmanager.com/amp.json?id=<?php echo $tracking_id; ?>&gtm.url=SOURCE_URL" data-credentials="include"></amp-analytics>
  <!-- /AMP Google Tag Manager -->
  <?php
  } //$tracking_id
}//AMP Google Tag Mana終了 ?>
