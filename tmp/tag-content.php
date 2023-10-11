<?php //タグ用の内容
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

//タグIDの取得
$tag_id = get_queried_object_id();
$eye_catch_url = get_the_tag_eye_catch_url($tag_id);
$content = get_the_tag_content($tag_id);
if ($eye_catch_url || $content): ?>
<article class="tag-content article">
  <header class="article-header tag-header">
    <?php //タイトル
    cocoon_template_part('tmp/list-title'); ?>
    <?php if ($eye_catch_url): ?>
      <div class="eye-catch-wrap">
        <figure class="eye-catch">
          <img src="<?php echo esc_url($eye_catch_url); ?>" class="eye-catch-image wp-tag-image" alt="<?php echo esc_attr(get_the_tag_title($tag_id)); ?>">
        </figure>
      </div>
      <?php do_action('tag_eye_catch_after'); ?>
    <?php endif ?>
    <?php //タグシェアボタン
    cocoon_template_part('tmp/tag-sns-share-top'); ?>

    <?php //PR表記（大）の出力
    if (is_large_pr_labels_visible()) {
      generate_large_pr_label_tag();
    } ?>

  </header>
  <?php if ($content): ?>
    <div class="tag-page-content entry-content">
      <?php echo $content; ?>
    </div>
  <?php endif ?>
</article>
<?php else: ?>
  <?php //タグタイトル
  cocoon_template_part('tmp/list-title');
  //タグシェアボタン
  cocoon_template_part('tmp/tag-sns-share-top'); ?>

  <?php //PR表記（大）の出力
  if (is_large_pr_labels_visible()) {
    generate_large_pr_label_tag();
  } ?>
<?php endif ?>
