<?php //カルーセルのエントリーカード
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit; ?>

<a href="<?php the_permalink(); ?>" class="carousel-entry-card-wrap a-wrap border-element cf" title="<?php echo esc_attr(get_the_title()); ?>">
<article class="carousel-entry-card e-card cf">

  <figure class="carousel-entry-card-thumb card-thumb">
    <?php if ( has_post_thumbnail() ): // サムネイルを持っているとき ?>
      <?php echo get_the_post_thumbnail($post->ID, apply_filters('get_carousel_entry_card_thumbnail_size', THUMB320), array('class' => 'carousel-entry-card-thumb-image card-thumb-image', 'alt' => '') ); //サムネイルを呼び出す?>
    <?php else: // サムネイルを持っていないとき ?>
      <?php //NO IMAGEサムネイルの出力
      echo get_original_image_tag(get_no_image_320x180_url(), THUMB320WIDTH, THUMB320HEIGHT, 'no-image carousel-entry-card-no-image', ''); ?>

    <?php endif; ?>
    <?php the_nolink_category(null, apply_filters('is_carousel_entry_card_category_label_visible', true)); //カテゴリラベルの取得 ?>
  </figure><!-- /.carousel-entry-thumb -->

  <div class="carousel-entry-card-content card-content">
    <div class="carousel-entry-card-title card-title">
      <?php the_title(); //記事のタイトル?>
    </div>

  </div><!-- /.carousel-entry-card-content -->
</article><!-- /.carousel-entry-card -->
</a><!-- /.carousel-entry-card-wrap -->
