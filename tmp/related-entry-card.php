<a href="<?php the_permalink(); ?>" class="related-entry-card-wrap a-wrap cf" title="<?php the_title() ?>">
<article class="related-entry-card cf">

  <figure class="related-entry-card-thumb">
    <?php if ( has_post_thumbnail() ): // サムネイルを持っているとき ?>
    <?php echo get_the_post_thumbnail($post->ID, array(160, 90), array('class' => 'related-entry-thumb-image', 'alt' => '') ); //サムネイルを呼び出す?>
    <?php else: // サムネイルを持っていないとき ?>
    <img src="<?php echo get_template_directory_uri(); ?>/images/no-image-320.png" alt="NO IMAGE" class="no-image related-entry-card-no-image" width="160" height="90" />
    <?php endif; ?>
    <?php the_nolink_category(); //カテゴリラベルの取得 ?>
  </figure><!-- /.related-entry-thumb -->

  <div class="related-entry-card-content">
    <h3 class="related-entry-card-title">
      <?php the_title(); //記事のタイトル?>
    </h3>
    <div class="related-entry-card-snippet">
   <?php echo get_content_excerpt( $post->post_content, 100 ); //カスタマイズで指定した文字の長さだけ本文抜粋?></div>
  </div><!-- /.related-entry-card-content -->

</article><!-- /.related-entry-card -->
</a><!-- /.related-entry-card-wrap -->