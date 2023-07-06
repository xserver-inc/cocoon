<?php //ヘッダーのアクセス解析
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

//Google Tag Manager (noscript)
if ( is_analytics() && $gtm_tracking_id = get_google_tag_manager_tracking_id() )://トラッキングIDが設定されているとき ?>
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=<?php echo $gtm_tracking_id; ?>" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
<?php endif; ?>
<?php
//以下その他の解析コードなど
if (is_analytics() && $header_tags = get_other_analytics_header_tags()) {
  echo '<!-- Other Analytics -->'.PHP_EOL;
  echo $header_tags.PHP_EOL;
  echo '<!-- /Other Analytics -->'.PHP_EOL;
}?>
