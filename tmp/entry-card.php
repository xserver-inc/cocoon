<a href="<?php the_permalink(); ?>" class="entry-card-wrap a-wrap cf" title="<?php the_title() ?>">
  <article id="post-<?php the_ID(); ?>" class="post-<?php the_ID(); ?> entry-card cf">
    <figure class="entry-card-thumnail">
      <?php if ( has_post_thumbnail() ): // サムネイルを持っているとき?>
        <?php the_post_thumbnail('thumb320' , array('class' => 'entry-card-thumnail-img', 'alt' => '') ); ?>
      <?php else: // サムネイルを持っていないとき ?>
        <img src="<?php echo get_template_directory_uri(); ?>/images/no-image-320.png" alt="NO IMAGE" class="entry-card-thumnail-img no-image list-no-image" />
      <?php endif; ?>
      <?php the_nolink_category() ?>
    </figure><!-- /.entry-card-thumb -->

    <div class="entry-card-content">
      <h2 class="entry-card-title" itemprop="headline"><?php the_title() ?></h2>
      <div class="entry-card-snippet">
        <?php echo get_content_excerpt( get_the_content(''), 100 ); //カスタマイズで指定した文字の長さだけ本文抜粋?>
      </div>
      <div class="entry-card-meta">
        <div class="entry-card-day"><span class="published"><?php the_time('Y/m/d'); ?></span></div>
        <div class="entry-card-categorys"><?php the_nolink_categories() ?></div>
      </div>
    </div><!-- /.entry-card-content -->
  </article>
</a>