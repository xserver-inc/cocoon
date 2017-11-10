<?php //著者のプロフィールボックス
//_v($_WIDGET_NAME) ?>
<div class="author-box cf">
  <?php //ウィジェット名がある場合
  if ($_WIDGET_NAME): ?>
    <div class="author-widget-name">
      <?php echo $_WIDGET_NAME; ?>
    </div>
  <?php endif ?>
  <figure class="author-thumb">
    <?php echo get_avatar( get_the_author_id(), 120 ); ?>
  </figure>
  <div class="author-content">
    <div class="author-display-name">
      <?php the_author_posts_link(); ?>
    </div>
    <div class="author-description">
      <?php the_author_meta( 'description', get_the_author_id() ); ?>
    </div>
    <div class="author-follows">
      <?php get_template_part('tmp/sns-follow-buttons'); ?>
    </div>
  </div>
</div>