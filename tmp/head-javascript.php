<?php //AMPページでは呼び出さない（通常ページのみで呼び出す）
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;
?>
<?php //LinkSwitchスクリプト
if (is_all_linkswitch_enable()): ?>
<script type="text/javascript" language="javascript">
    var vc_pid = "<?php echo esc_attr(get_ad_linkswitch_id()); ?>";
</script><script type="text/javascript" src="//aml.valuecommerce.com/vcdal.js" async></script>
<?php endif; ?>
