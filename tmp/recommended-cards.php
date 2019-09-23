<?php //グローバルナビ
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */ ?>
<?php
if (is_recommended_cards_visible()){
  $atts = array(
    'name' => get_recommended_cards_menu_name(), // メニュー名
    'style' => get_recommended_cards_style(),
    'margin' => is_recommended_cards_margin_enable(),
    'wrap' => is_recommended_cards_area_both_sides_margin_enable(),
  );
  echo get_recommend_cards_tag($atts);
}; ?>


