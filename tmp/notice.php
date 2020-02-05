<?php //通知テンプレート
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

$msg = get_notice_area_message();
if (is_notice_area_visible() && $msg && !is_amp() && apply_filters('notice_area_visible', true)):
  $url = get_notice_area_url();
 ?>

<?php //リンクの開始タグ
if ($url):
  $target = is_notice_link_target_blank() ? ' target="_blank" rel="noopener"' : '';
?>
<div id="notice-area-wrap" class="notice-area-wrap">
  <a href="<?php echo $url; ?>" id="notice-area-link" class="notice-area-link"<?php echo $target; ?>>
  <?php endif ?>

    <div id="notice-area" class="notice-area nt-<?php echo get_notice_type(); ?>">
      <?php echo $msg; ?>
    </div>

  <?php //aリンクの閉じタグ
  if ($url): ?>
  </a>
</div>
<?php endif ?>

<?php endif ?>
