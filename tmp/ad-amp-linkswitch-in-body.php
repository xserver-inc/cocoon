<?php //AdSense AMP自動広告の<body>直後コード
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

if (is_all_linkswitch_enable()): ?>
<!-- LinkSwitch body script -->
<amp-link-rewriter layout="nodisplay">
  <script type="application/json">
    {
      "output": "https://lsr.valuecommerce.com/ard?p=${vc_pid}&u=${href}&vcptn=${vc_ptn}&s=SOURCE_URL&r=DOCUMENT_REFERRER",
      "vars": { "vc_pid": "<?php echo esc_attr(get_ad_linkswitch_id()); ?>", "vc_ptn": "ampls" }
    }
  </script>
</amp-link-rewriter>
<!-- End LinkSwitch body script -->
<?php endif ?>
