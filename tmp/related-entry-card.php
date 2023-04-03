<?php
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit; ?>
 <a href="<?php echo esc_url(get_the_permalink()); ?>" class="related-entry-card-wrap a-wrap border-element cf" title="<?php echo esc_attr(get_the_title()); ?>">
<article <?php post_class( array('post-'.get_the_ID(), 'related-entry-card', 'e-card', 'cf') ); ?>>

  <figure class="related-entry-card-thumb card-thumb e-card-thumb">
    <?php if ( has_post_thumbnail() ): // サムネイルを持っているとき ?>
    <?php
    echo get_the_post_thumbnail($post->ID, get_related_entry_card_thumbnail_size(), array('class' => 'related-entry-card-thumb-image card-thumb-image', 'alt' => '') ); //サムネイルを呼び出す?>
    <?php else: // サムネイルを持っていないとき ?>
      <?php //NO IMAGEサムネイルの出力
      echo get_original_image_tag(get_no_image_160x90_url(), THUMB160WIDTH, THUMB160HEIGHT, 'no-image related-entry-card-no-image', ''); ?>

    <?php endif; ?>
    <?php the_nolink_category(null, apply_filters('is_related_entry_card_category_label_visible', true)); //カテゴリーラベルの取得 ?>
  </figure><!-- /.related-entry-thumb -->

  <div class="related-entry-card-content card-content e-card-content">
    <h3 class="related-entry-card-title card-title e-card-title">
      <?php the_title(); //記事のタイトル?>
    </h3>
    <?php //スニペットの表示
    if (is_related_entry_card_snippet_visible()): ?>
    <div class="related-entry-card-snippet card-snippet e-card-snippet">
      <?php echo get_the_snippet( get_the_content(''), get_related_excerpt_max_length() ); //カスタマイズで指定した文字の長さだけ本文抜粋?>
    </div>
    <?php endif ?>
    <?php do_action( 'related_entry_card_snippet_after', get_the_ID() ); ?>
    <?php //表示するものがあるか判定
    if (is_related_entry_card_post_date_visible() || is_related_entry_card_post_update_visible() || is_related_entry_card_post_author_visible()): ?>
    <div class="related-entry-card-meta card-meta e-card-meta">
      <div class="related-entry-card-info e-card-info">
        <?php
        //更新日の取得
        $update_time = get_update_time(get_site_date_format());
        //echo $update_time;
        //投稿日の表示
        if (is_related_entry_card_post_date_visible() || (is_related_entry_card_post_date_or_update_visible() && !$update_time && is_related_entry_card_post_update_visible())): ?>
          <span class="post-date"><span class="fa fa-clock-o" aria-hidden="true"></span> <?php the_time(get_site_date_format()); ?></span>
        <?php endif ?>
        <?php //更新時の表示
        //_v(is_related_entry_card_post_update_visible());
        if (is_related_entry_card_post_update_visible() && $update_time && (get_the_time('U') < get_update_time('U'))): ?>
          <span class="post-update"><span class="fa fa-history" aria-hidden="true"></span> <?php echo $update_time; ?></span>
        <?php endif ?>
        <?php //投稿者の表示
        if (is_related_entry_card_post_author_visible()): ?>
          <span class="post-author">
            <span class="post-author-image"><?php echo get_avatar( get_the_author_meta( 'ID' ), '16', null ); ?></span>
            <span class="post-author-name"><?php echo esc_html(get_the_author()); ?></span>
          </span>
        <?php endif ?>
      </div>
      <div class="related-entry-card-categorys e-card-categorys"><?php the_nolink_categories() ?></div>
    </div>
    <?php endif ?>
  </div><!-- /.related-entry-card-content -->



</article><!-- /.related-entry-card -->
</a><!-- /.related-entry-card-wrap -->
