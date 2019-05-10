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
  <!-- Global site tag (gtag.js) - Google Analytics -->
  <script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo $ga_tracking_id; ?>"></script>
  <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', '<?php echo $ga_tracking_id; ?>');
  </script>
  <!-- /Global site tag (gtag.js) - Google Analytics -->
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
