<?php
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

// RSSカード1件分の引数を展開（子テーマは cocoon_part_args__tmp/rss-card フックで上書き可能）
$url          = $args['url'] ?? '';
$title        = $args['title'] ?? '';
$target       = $args['target'] ?? '_blank';
$img          = $args['img'] ?? '';
$site         = $args['site'] ?? 0;
$site_title   = $args['site_title'] ?? '';
$desc         = $args['desc'] ?? '1';
$date         = $args['date'] ?? '1';
$text         = $args['text'] ?? '';
$date_str     = $args['date_str'] ?? '';
$cache_minute = $args['cache_minute'] ?? '60';
?>
<a href="<?php echo esc_url($url); ?>" title="<?php echo esc_attr(wp_strip_all_tags($title)); //HTMLタグを除去してツールチップにコードが表示されないようにする ?>" class="rss-entry-card-link widget-entry-card-link a-wrap" target="<?php echo esc_attr($target); ?>"<?php echo get_rel_by_target($target); ?>>
  <div class="rss-entry-card widget-entry-card e-card cf">
    <figure class="rss-entry-card-thumb widget-entry-card-thumb card-thumb">
      <img src="<?php echo esc_url($img); ?>" class="rss-entry-card-thumb-image widget-entry-card-thumb-image card-thumb-image" alt="">
    </figure>
    <div class="rss-entry-card-content widget-entry-card-content card-content">
      <div class="rss-entry-card-title widget-entry-card-title card-title"><?php echo esc_html($title); ?><?php if ($site == 1 && !empty($site_title)) : ?> <span class="rss-entry-card-site"><?php echo get_title_separator_caption(); ?> <?php echo esc_html($site_title); ?></span><?php endif; ?></div>
      <?php if ($desc) : ?>
      <div class="rss-entry-card-snippet widget-entry-card-snippet card-snippet"><?php echo esc_html($text); ?></div>
      <?php endif; ?>
      <?php if ($date) : ?>
      <div class="rss-entry-card-date widget-entry-card-date">
        <span class="rss-entry-card-post-date widget-entry-card-post-date post-date"><?php echo esc_html($date_str); ?></span>
      </div>
      <?php endif; ?>
    </div>
  </div>
</a>
