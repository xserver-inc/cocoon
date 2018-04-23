<a href="<?php the_permalink(); ?>" class="entry-card-wrap a-wrap cf" title="<?php the_title() ?>">
  <article id="post-<?php the_ID(); ?>" class="post-<?php the_ID(); ?> entry-card e-card cf">
    <figure class="entry-card-thumb card-thumb">
      <?php if ( has_post_thumbnail() ): // サムネイルを持っているとき?>
        <?php the_post_thumbnail(get_entry_card_thumbnail_size() , array('class' => 'entry-card-thumb-image card-thumb-image', 'alt' => '') ); ?>
      <?php else: // サムネイルを持っていないとき ?>
        <img src="<?php echo get_template_directory_uri(); ?>/images/no-image-320.png" alt="NO IMAGE" class="entry-card-thumb-image no-image list-no-image" />
      <?php endif; ?>
      <?php the_nolink_category(); //カテゴリラベルの取得 ?>
    </figure><!-- /.entry-card-thumb -->

    <div class="entry-card-content card-content">
      <h2 class="entry-card-title card-title" itemprop="headline"><?php the_title() ?></h2>
      <?php //スニペットの表示
      if (is_entry_card_snippet_visible()): ?>
      <div class="entry-card-snippet card-snippet">
        <?php echo get_the_snipet( get_the_content(''), get_entry_card_excerpt_max_length() ); //カスタマイズで指定した文字の長さだけ本文抜粋?>
      </div>
      <?php endif ?>
      <div class="entry-card-meta card-meta">
        <div class="entry-card-info">
          <?php
          //更新日の取得
          $update_time = get_update_time('Y.m.d');
          //投稿日の表示
          if (is_entry_card_post_date_visible() || (is_entry_card_post_date_or_update_visible() && !$update_time && is_entry_card_post_update_visible())): ?>
            <span class="post-date"><?php the_time('Y.m.d'); ?></span>
          <?php endif ?>
          <?php //更新時の表示

          if (is_entry_card_post_update_visible() && $update_time): ?>
            <span class="post-update"><?php echo $update_time; ?></span>
          <?php endif ?>
          <?php //投稿者の表示
          if (is_entry_card_post_author_visible()): ?>
            <span class="post-author">
              <span class="post-author-image"><?php echo get_avatar( get_the_author_meta( 'ID' ), '12', null ); ?></span>
              <span class="post-author-name"><?php echo get_the_author(); ?></span>
            </span>
          <?php endif ?>
        </div>
        <div class="entry-card-categorys"><?php the_nolink_categories() ?></div>
      </div>
    </div><!-- /.entry-card-content -->
  </article>
</a>