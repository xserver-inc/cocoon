<?php //カテゴリー用の内容
//カテゴリIDの取得
$cat_id = get_query_var('cat');
if ($cat_id && get_category_meta($cat_id)): ?>
<div class="category-content">
  <?php if ($eye_catch = get_category_eye_catch($cat_id)): ?>
    <header class="article-header category-header">
      <figure class="eye-catch">
        <img src="<?php echo $eye_catch; ?>" alt="<?php echo get_category_title($cat_id); ?>">
      </figure>
    </header>
  <?php endif ?>
  <?php if ($content = get_category_content($cat_id)): ?>
    <div class="category-page-content entry-content">
      <?php echo $content; ?>
    </div>
  <?php endif ?>
</div>
<?php endif ?>