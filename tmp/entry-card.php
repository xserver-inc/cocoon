<?php //エントリーカード
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit; ?>

<a href="<?php echo esc_url(get_the_permalink()); ?>" class="entry-card-wrap a-wrap border-element cf" title="<?php echo esc_attr(get_the_title()); ?>">
  <article id="post-<?php the_ID(); ?>" <?php post_class( array('post-'.get_the_ID(), 'entry-card','e-card', 'cf') ); ?>>
    <figure class="entry-card-thumb card-thumb e-card-thumb">
      <?php
      //サムネイルタグを取得
      $thumbnail_tag =
        get_the_post_thumbnail(
          get_the_ID(),
          get_entry_card_thumbnail_size($count),
          array(
            'class' => 'entry-card-thumb-image card-thumb-image',
            'alt' => ''
          )
        );
      // サムネイルを持っているとき
      if ( has_post_thumbnail() && $thumbnail_tag ): ?>
        <?php echo $thumbnail_tag;
        //the_post_thumbnail(get_entry_card_thumbnail_size() , array('class' => 'entry-card-thumb-image card-thumb-image', 'alt' => '') ); ?>
      <?php else: // サムネイルを持っていないとき ?>
        <?php echo get_entry_card_no_image_tag($count); ?>
      <?php endif; ?>
      <?php the_nolink_category(null, apply_filters('is_entry_card_category_label_visible', true)); //カテゴリラベルの取得 ?>
    </figure><!-- /.entry-card-thumb -->

    <div class="entry-card-content card-content e-card-content">
      <h2 class="entry-card-title card-title e-card-title" itemprop="headline"><?php the_title() ?></h2>
      <?php //スニペットの表示
      if (is_entry_card_snippet_visible()): ?>
      <div class="entry-card-snippet card-snippet e-card-snippet">
        <?php echo get_the_snippet( get_the_content(''), get_entry_card_excerpt_max_length() ); //カスタマイズで指定した文字の長さだけ本文抜粋?>
      </div>
      <?php endif ?>
      <?php //PVエリアの表示
      if (is_admin_index_pv_visible() && is_user_administrator()) {
        get_template_part('tmp/admin-pv');
      } ?>
      <div class="entry-card-meta card-meta e-card-meta">
        <div class="entry-card-info e-card-info">
          <?php
          //更新日の取得
          $update_time = get_update_time(get_site_date_format());
          //投稿日の表示
          if (is_entry_card_post_date_visible() || (is_entry_card_post_date_or_update_visible() && !$update_time && is_entry_card_post_update_visible())): ?>
            <span class="post-date"><span class="fa fa-clock-o" aria-hidden="true"></span> <?php the_time(get_site_date_format()); ?></span>
          <?php endif ?>
          <?php //更新時の表示
          if (is_entry_card_post_update_visible() && $update_time && (get_the_time('U') < get_update_time('U'))): ?>
            <span class="post-update"><span class="fa fa-history" aria-hidden="true"></span> <?php echo $update_time; ?></span>
          <?php endif ?>
          <?php //投稿者の表示
          if (is_entry_card_post_author_visible()): ?>
            <span class="post-author">
              <span class="post-author-image"><?php echo get_avatar( get_the_author_meta( 'ID' ), '16', null ); ?></span>
              <span class="post-author-name"><?php echo get_the_author(); ?></span>
            </span>
          <?php endif ?>
          <?php //コメント数の表示
          $count = get_comments_number();
          if(is_entry_card_post_comment_count_visible() && is_single_comment_visible() && apply_filters('entry_card_post_comment_count_visible', true, $count)): ?>
            <span class="post-comment-count"><span class="fa fa-comment-o" aria-hidden="true"></span> <?php echo $count; ?></span>
          <?php endif; ?>
        </div>
        <div class="entry-card-categorys"><?php the_nolink_categories() ?></div>
      </div>
    </div><!-- /.entry-card-content -->
  </article>
</a>
