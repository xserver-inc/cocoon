<?php //投稿日と更新日 ?>
<div class="date-tags">
  <?php //投稿日と更新日タグの取得
  echo get_the_date_tags(); ?>

  <?php if (is_amp() && is_user_administrator()): ?>
    <span class="amp-back">
      <a href="<?php the_permalink() ?>"><?php _e( '通常ページへ', THEME_NAME ) ?></a>
    </span>

  <?php endif ?>
</div>