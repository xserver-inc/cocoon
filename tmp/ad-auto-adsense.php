<?php //オートアドセンス用のコード
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

if (is_ads_visible() && is_auto_adsense_enable()):
  global $_IS_ADSENSE_EXIST;
  $_IS_ADSENSE_EXIST = true;
?>
<!-- Google Auto AdSense -->
<?php //echo $adsense_script; ?>
<script>
  (adsbygoogle = window.adsbygoogle || []).push({
    google_ad_client: "<?php echo get_adsense_data_ad_client(); ?>",
    enable_page_level_ads: true
  });
</script>
<!-- End Google Auto AdSense -->
<?php endif ?>
