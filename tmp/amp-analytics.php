<?php
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link:  https://wp-cocoon.com/
 * @license:  http://www.gnu.org/licenses/gpl-2.0.html  GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

$tracking_id = get_ga4_tracking_id();
if ( is_analytics() && $tracking_id ) {
  ?>
  <amp-analytics type="gtag" data-credentials="include">
    <script type="application/json">
      {
        "vars" : {
          "gtag_id": "<?php echo $tracking_id ?>",
          "config" : {
            "<?php echo $tracking_id ?>": { "groups": "default" }
          }
        }
      }
    </script>
  </amp-analytics>
  <?php
} ?>