<a href="<?php the_permalink(); ?>" class="carousel-entry-card-wrap a-wrap cf" title="<?php the_title() ?>">
<article class="carousel-entry-card e-card cf">

  <figure class="carousel-entry-card-thumb card-thumb">
    <?php if ( has_post_thumbnail() ): // サムネイルを持っているとき ?>
    <?php echo get_the_post_thumbnail($post->ID, 'thumb320', array('class' => 'carousel-entry-card-thumb-image card-thumb-image', 'alt' => '') ); //サムネイルを呼び出す?>
    <?php else: // サムネイルを持っていないとき ?>
    <img src="<?php echo get_template_directory_uri(); ?>/images/no-image-320.png" alt="NO IMAGE" class="no-image carousel-entry-card-no-image" width="320" height="160" />
    <?php endif; ?>
    <?php the_nolink_category(); //カテゴリラベルの取得 ?>
  </figure><!-- /.carousel-entry-thumb -->

  <div class="carousel-entry-card-content card-content">
    <div class="carousel-entry-card-title card-title">
      <?php the_title(); //記事のタイトル?>
    </div>

  </div><!-- /.carousel-entry-card-content -->
</article><!-- /.carousel-entry-card -->
</a><!-- /.carousel-entry-card-wrap -->