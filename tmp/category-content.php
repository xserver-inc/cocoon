<?php //カテゴリー用の内容
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

//カテゴリIDの取得
$cat_id = get_query_var('cat');
$eye_catch_url = get_the_category_eye_catch_url($cat_id);
$content = get_the_category_content($cat_id);
if ($eye_catch_url || $content): ?>
<article class="category-content article">
  <header class="article-header category-header">
    <?php //カテゴリタイトル
    get_template_part('tmp/list-title'); ?>
    <?php if ($eye_catch_url): ?>
      <div class="eye-catch-wrap">
        <figure class="eye-catch">
          <img src="<?php echo esc_url($eye_catch_url); ?>" class="eye-catch-image wp-category-image" alt="<?php echo esc_attr(get_the_category_title($cat_id)); ?>">
          <?php //カテゴリラベル
          // echo get_original_image_tag($eye_catch_url, $width, $height, '"eye-catch-image wp-category-image', get_the_category_title($cat_id));
          if (apply_filters('is_eyecatch_category_label_visible', true)) {
            echo '<span class="cat-label cat-label-'.$cat_id.'">'.single_cat_title( '', false ).'</span>';
          } ?>
        </figure>
      </div>
      <?php do_action('category_eye_catch_after'); ?>
    <?php endif ?>
    <?php //カテゴリシェアボタン
    get_template_part('tmp/category-sns-share-top'); ?>
  </header>
  <?php if ($content): ?>
    <div class="category-page-content entry-content">
      <?php echo $content; ?>
    </div>
  <?php endif ?>
</article>
<?php else: ?>
  <?php //カテゴリタイトル
  get_template_part('tmp/list-title');
  //カテゴリシェアボタン
  get_template_part('tmp/category-sns-share-top'); ?>
<?php endif ?>
