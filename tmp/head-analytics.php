<?php //解析が有効でもAMPページでは使用しない
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

//タグマネージャIDが設定されているときは計測しない
if (is_analytics() && !get_google_tag_manager_tracking_id() && !is_amp()): ?>
  <?php //Google Analytics(gtag.js)
  if ( $ga_tracking_id = get_google_analytics_tracking_id() )://トラッキングIDが設定されているとき ?>
    <?php if (is_google_analytics_script_gtag_js()): ?>

<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo $ga_tracking_id; ?>"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', '<?php echo $ga_tracking_id; ?>');
</script>
<!-- /Global site tag (gtag.js) - Google Analytics -->

    <?php else: ?>

<!-- Global site tag (ga-lite.min.js) - Google Analytics -->
<script>
(function(e,t,n,i,s,a,c){e[n]=e[n]||function(){(e[n].q=e[n].q||[]).push(arguments)}
;a=t.createElement(i);c=t.getElementsByTagName(i)[0];a.async=true;a.src=s
;c.parentNode.insertBefore(a,c)
})(window,document,"galite","script","https://cdn.jsdelivr.net/npm/ga-lite@2/dist/ga-lite.min.js");

galite('create', '<?php echo $ga_tracking_id; ?>', 'auto');
galite('send', 'pageview');
</script>
<!-- /Global site tag (ga-lite.min.js) - Google Analytics -->

    <?php endif; ?>
  <?php endif; ?>

<?php endif ?>
<?php //その他の解析コード
if (is_analytics() && !is_amp()): ?>
  <?php //その他<head></head>内用の解析コード
  if ($head_tags = get_other_analytics_head_tags()) {
    echo '<!-- Other Analytics -->'.PHP_EOL;
    echo $head_tags.PHP_EOL;
    echo '<!-- /Other Analytics -->'.PHP_EOL;
  }?>
<?php endif; ?>
