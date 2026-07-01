<?php
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

$prefix       = $args['prefix'];
$url          = $args['url'];
$title        = $args['title'];
$snippet      = $args['snippet'];
$date         = $args['date'];
$swiper_slide = $args['swiper_slide'];
$class_text   = $args['class_text'];
$target_attr  = $args['target_attr'];
$ribbon_tag   = $args['ribbon_tag'];
$div_class    = $args['div_class'];
$thumbnail    = $args['thumbnail'];
$post_id      = $args['post_id'];
$comment      = $args['comment'];
?>
<a href="<?php echo esc_url($url); ?>" class="<?php echo $prefix; ?>-entry-card-link widget-entry-card-link a-wrap<?php echo $class_text; ?><?php echo $swiper_slide; ?>" title="<?php echo esc_attr(escape_shortcodes($title)); ?>"<?php echo $target_attr; ?>>
  <div <?php echo $div_class; ?>>
    <?php echo $ribbon_tag; ?>
    <figure class="<?php echo $prefix; ?>-entry-card-thumb widget-entry-card-thumb card-thumb">
      <?php echo $thumbnail; ?>
    </figure><!-- /.entry-card-thumb -->

    <div class="<?php echo $prefix; ?>-entry-card-content widget-entry-card-content card-content">
      <div class="<?php echo $prefix; ?>-entry-card-title widget-entry-card-title card-title"><?php echo $title; ?></div>
      <?php if ($snippet): ?>
        <div class="<?php echo $prefix; ?>-entry-card-snippet widget-entry-card-snippet card-snippet"><?php echo $snippet; ?></div>
      <?php endif; ?>
      <?php if (!is_widget_navi_entry_card_prefix($prefix)): ?>
        <?php do_action( 'widget_entry_card_date_before', $prefix, $post_id); ?>
        <div class="<?php echo $prefix; ?>-entry-card-meta widget-entry-card-meta card-meta">
          <div class="<?php echo $prefix; ?>-entry-card-info widget-entry-card-info card-info">
            <?php generate_widget_entry_card_date($prefix, null, $date); ?>
            <?php if ($comment): ?>
              <span class="<?php echo $prefix; ?>-entry-card-comment widget-entry-card-comment card-comment post-comment-count"><span class="fa fa-comment-o comment-icon" aria-hidden="true"></span><?php echo get_comments_number(); ?></span>
            <?php endif; ?>
          </div><!-- /.entry-card-info -->
        </div><!-- /.entry-card-meta -->
      <?php endif; ?>
    </div><!-- /.entry-content -->
  </div><!-- /.entry-card -->
</a><!-- /.entry-card-link -->
