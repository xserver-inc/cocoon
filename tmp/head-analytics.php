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

  <?php //GA4測定ID
  if ( $ga4_tracking_id = get_ga4_tracking_id() ) : ?>

    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo $ga4_tracking_id; ?>"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());

      gtag('config', '<?php echo $ga4_tracking_id; ?>');
    </script>

  <?php endif; ?>

<?php endif ?>

<?php //その他の解析コード
if (is_analytics() && !is_amp()): ?>

  <?php //Clarityコードの表示
  if ( get_clarity_project_id() ): ?>
    <!-- Clarity -->
    <script type="text/javascript">
      (function(c,l,a,r,i,t,y){
        c[a]=c[a]||function(){(c[a].q=c[a].q||[]).push(arguments)};
        t=l.createElement(r);t.async=1;t.src="https://www.clarity.ms/tag/"+i;
        y=l.getElementsByTagName(r)[0];y.parentNode.insertBefore(t,y);
      })(window, document, "clarity", "script", "<?php echo get_clarity_project_id() ?>");
    </script>
    <!-- /Clarity -->
  <?php endif;//Clarity終了 ?>

  <?php //Google Tag Manager
  if ($tracking_id = get_google_tag_manager_tracking_id()): ?>
    <!-- Google Tag Manager -->
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
    j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
    'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','<?php echo $tracking_id; ?>');</script>
    <!-- End Google Tag Manager -->
  <?php endif //Google Tag Manager終了 ?>


  <?php //その他<head></head>内用の解析コード
  if ($head_tags = get_other_analytics_head_tags()) {
    echo '<!-- Other Analytics -->'.PHP_EOL;
    echo $head_tags.PHP_EOL;
    echo '<!-- /Other Analytics -->'.PHP_EOL;
  }?>
<?php endif; ?>
