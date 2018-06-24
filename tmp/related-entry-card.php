<?php
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */ ?>
 <a href="<?php the_permalink(); ?>" class="related-entry-card-wrap a-wrap cf" title="<?php echo esc_attr(get_the_title()); ?>">
<article class="related-entry-card e-card cf">

  <figure class="related-entry-card-thumb card-thumb e-card-thumb">
    <?php if ( has_post_thumbnail() ): // サムネイルを持っているとき ?>
    <?php
    //適切なサムネイルサイズの選択
    switch (get_related_entry_type()) {
      case 'vartical_card_3':
        $thumb_size = 'thumb320';
        break;
      case 'mini_card':
        $thumb_size = 'thumb120';
        break;
      default:
        $thumb_size = 'thumb160';
        break;
    }
    echo get_the_post_thumbnail($post->ID, $thumb_size, array('class' => 'related-entry-card-thumb-image card-thumb-image', 'alt' => '') ); //サムネイルを呼び出す?>
    <?php else: // サムネイルを持っていないとき ?>
    <img src="<?php echo get_template_directory_uri(); ?>/images/no-image-320.png" alt="NO IMAGE" class="no-image related-entry-card-no-image" width="160" height="90" />
    <?php endif; ?>
    <?php the_nolink_category(); //カテゴリラベルの取得 ?>
  </figure><!-- /.related-entry-thumb -->

  <div class="related-entry-card-content card-content e-card-content">
    <h3 class="related-entry-card-title card-title e-card-title">
      <?php the_title(); //記事のタイトル?>
    </h3>
    <div class="related-entry-card-snippet card-snippet e-card-snippet">
      <?php echo get_the_snipet( get_the_content(''), get_related_excerpt_max_length() ); //カスタマイズで指定した文字の長さだけ本文抜粋?>
    </div>

    <div class="related-entry-card-meta card-meta e-card-meta">
      <div class="related-entry-card-day"><span class="related-entry-post-date post-date"><?php the_time(get_site_date_format()); ?></span></div>
    </div>

  </div><!-- /.related-entry-card-content -->



</article><!-- /.related-entry-card -->
</a><!-- /.related-entry-card-wrap -->