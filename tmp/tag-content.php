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
    get_template_part('tmp/list-title'); ?>
    <?php if ($eye_catch_url): ?>
      <div class="eye-catch-wrap">
        <figure class="eye-catch">
          <img src="<?php echo esc_url($eye_catch_url); ?>" class="eye-catch-image wp-tag-image" alt="<?php echo esc_attr(get_the_tag_title($tag_id)); ?>">
        </figure>
      </div>
      <?php do_action('tag_eye_catch_after'); ?>
    <?php endif ?>
    <?php //タグシェアボタン
    get_template_part('tmp/tag-sns-share-top'); ?>
  </header>
  <?php if ($content): ?>
    <div class="tag-page-content entry-content">
      <?php echo $content; ?>
    </div>
  <?php endif ?>
</article>
<?php else: ?>
  <?php //タグタイトル
  get_template_part('tmp/list-title');
  //タグシェアボタン
  get_template_part('tmp/tag-sns-share-top'); ?>
<?php endif ?>
