<?php //AMPページでは呼び出さない（通常ページのみで呼び出す）
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;
?>
<?php //performance.mark とか performance.measure とかを利用するためのPolyfill
//user-timing.js（https://gist.github.com/pmeenan/5902672#file-user-timing-js）
//https://developers.google.com/web/tools/lighthouse/audits/user-timing?hl=ja
//https://www.html5rocks.com/en/tutorials/webperformance/usertiming/
if (false): ?>
<script>!function(){var e="undefined"!=typeof window?window:exports,n=[];if(e.performance||(e.performance={}),e.performance.now||(e.performance.now=performance.now||performance.webkitNow||performance.msNow||performance.mozNow),!e.performance.now){var r=Date.now?Date.now():+new Date;performance.timing&&performance.timing&&(r=performance.timing.navigationStart),e.performance.now=function(){var e=Date.now?Date.now():+new Date;return e-r}}e.performance.mark||(e.performance.mark=e.performance.webkitMark?e.performance.webkitMark:function(r){n.push({name:r,entryType:"mark",startTime:e.performance.now(),duration:0})}),e.performance.getEntriesByType||(e.performance.getEntriesByType=e.performance.webkitGetEntriesByType?e.performance.webkitGetEntriesByType:function(e){return"mark"==e?n:void 0})}(),window.markUserTime=function(e){var n=window.requestAnimationFrame||function(e){setTimeout(e,0)};n(function(){window.performance.mark(e),window.console&&console.timeStamp&&console.timeStamp(e)})};</script>
<?php endif; ?>
<?php //LinkSwitchスクリプト
if (is_all_linkswitch_enable()): ?>
<script type="text/javascript" language="javascript">
    var vc_pid = "<?php echo esc_attr(get_ad_linkswitch_id()); ?>";
</script><script type="text/javascript" src="//aml.valuecommerce.com/vcdal.js" async></script>
<?php endif; ?>
