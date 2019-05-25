<?php //タグ用の内容
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

//タグIDの取得
$tag_id = get_query_var('tag_id');
if ($tag_id && get_tag_meta($tag_id)): ?>
<article class="tag-content article">
  <?php //タイトル
  get_template_part('tmp/list-title'); ?>
  <?php if ($eye_catch = get_tag_eye_catch($tag_id)): ?>
    <header class="article-header tag-header">
      <figure class="eye-catch">
        <img src="<?php echo esc_url($eye_catch); ?>" alt="<?php echo esc_attr(get_tag_title($tag_id)); ?>">
      </figure>
    </header>
  <?php endif ?>
  <?php if ($content = get_tag_content($tag_id)): ?>
    <div class="tag-page-content entry-content">
      <?php echo $content; ?>
    </div>
  <?php endif ?>
</article>
<?php else: ?>
  <?php //タグタイトル
  get_template_part('tmp/list-title'); ?>
<?php endif ?>
