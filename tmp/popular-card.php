<?php
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

$popular_post    = $args['popular_post'];
$i               = $args['index'];
$entry_type      = $args['entry_type'];
$ranking_visible = $args['ranking_visible'];
$pv_visible      = $args['pv_visible'];
$date            = $args['date'];
$snippet         = $args['snippet'];
$comment         = $args['comment'];
$swiper_slide    = $args['swiper_slide'];

$permalink  = get_permalink($popular_post->ID);
$title      = $popular_post->post_title;
$thumb_size = get_popular_entries_thumbnail_size($entry_type);

// サムネイル設定
$no_thumbnail_url = ($entry_type == ET_DEFAULT) ? get_no_image_120x68_url($popular_post->ID) : get_no_image_320x180_url($popular_post->ID);
$w = ($entry_type == ET_DEFAULT) ? THUMB120WIDTH  : THUMB320WIDTH;
$h = ($entry_type == ET_DEFAULT) ? THUMB120HEIGHT : THUMB320HEIGHT;

$post_thumbnail     = get_the_post_thumbnail($popular_post->ID, $thumb_size, array('alt' => ''));
$post_thumbnail_img = $post_thumbnail ? $post_thumbnail : get_original_image_tag($no_thumbnail_url, $w, $h, 'no-image popular-entry-card-thumb-no-image widget-entry-card-thumb-no-image', '');

// スニペット表示（「タイトルを重ねた大きなサムネイル」の時は非表示）
$snippet_tag = '';
$post_obj = get_post($popular_post->ID);
if ($snippet && isset($popular_post->ID) && isset($post_obj->post_content) && $entry_type !== ET_LARGE_THUMB_ON) {
  $snippet_tag = '<div class="popular-entry-card-snippet widget-entry-card-snippet card-snippet">'.get_the_snippet($post_obj->post_content, get_entry_card_excerpt_max_length(), $popular_post->ID).'</div>';
}

// PV表示
$pv_tag = null;
if ($pv_visible) {
  $pv      = $popular_post->sum_count;
  $pv_unit = ($pv == '1') ? 'view' : 'views';
  $pv_text = apply_filters('popular_entry_card_pv_text', $pv . ' ' . $pv_unit, $pv, $pv_unit);
  $pv_tag  = '<span class="popular-entry-card-pv widget-entry-card-pv">'.$pv_text.'</span>';
}
?>
<a href="<?php echo $permalink; ?>" class="popular-entry-card-link widget-entry-card-link a-wrap no-<?php echo $i; ?><?php echo $swiper_slide; ?>" title="<?php echo esc_attr(escape_shortcodes($title)); ?>">
  <div <?php post_class( array('post-'.$popular_post->ID, 'popular-entry-card', 'widget-entry-card', 'e-card', 'cf'), $popular_post->ID ); ?>>
    <figure class="popular-entry-card-thumb widget-entry-card-thumb card-thumb">
      <?php echo $post_thumbnail_img; ?>
      <?php
      $is_visible = apply_filters('is_popular_entry_card_category_label_visible', false);
      $is_visible = apply_filters('is_widget_entry_card_category_label_visible', $is_visible);
      the_nolink_category($popular_post->ID, $is_visible); //カテゴリーラベルの取得 ?>
    </figure><!-- /.popular-entry-card-thumb -->

    <div class="popular-entry-card-content widget-entry-card-content card-content">
      <div class="popular-entry-card-title widget-entry-card-title card-title"><?php echo escape_shortcodes($title); ?></div>
      <?php echo $snippet_tag; ?>
      <?php if ($entry_type != ET_LARGE_THUMB_ON): ?>
        <?php echo $pv_tag; ?>
      <?php endif ?>
      <?php do_action( 'widget_entry_card_date_before', 'popular', $popular_post->ID); ?>
      <div class="popular-entry-card-meta widget-entry-card-meta card-meta">
        <div class="popular-entry-card-info widget-entry-card-info card-info">
          <?php generate_widget_entry_card_date('popular', $popular_post->ID, $date); ?>
          <?php if ($comment): ?>
            <span class="popular-entry-card-comment widget-entry-card-comment card-comment post-comment-count"><span class="fa fa-comment-o comment-icon" aria-hidden="true"></span><?php echo get_comments_number( $popular_post->ID ); ?></span>
          <?php endif; ?>
        </div>
      </div>
    </div><!-- /.popular-entry-content -->
    <?php if ($entry_type == ET_LARGE_THUMB_ON): ?>
      <?php echo $pv_tag; ?>
    <?php endif ?>
  </div><!-- /.popular-entry-card -->
</a><!-- /.popular-entry-card-link -->
