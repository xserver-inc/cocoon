<?php //グローバルナビ
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */ ?>
<?php
$name = get_recommended_cards_menu_name();
$wrap = null;
if (is_recommended_cards_area_both_sides_margin_enable()) {
  $wrap = ' wrap';
}
if (is_recommended_cards_visible() && $name): ?>
<!-- Recommended -->
<div id="recommended" class="recommended cf<?php echo get_additional_recommend_cards_classes(); ?>">
  <div id="recommended-in" class="recommended-in<?php echo $wrap; ?> cf">
    <?php
    $atts = array(
      'name' => $name,
      'type' => ET_LARGE_THUMB_ON,
    );
    echo get_navi_card_list_tag($atts);
    ?>
  </div><!-- /#recommended-in -->
</div><!-- /.recommended -->
<?php endif; ?>


