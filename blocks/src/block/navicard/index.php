<?php
/**
 * Server-side rendering of the `cocoon-blocks/navicard` block.
 *
 * @package Cocoon
 * @author yhira
 * @link https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */

/**
 * Renders the `cocoon-blocks/navicard` block on the server.
 *
 * @param  array  $attributes The block attributes.
 * @param  string $content    The block content.
 * @return string Returns the block content.
 */
function render_block_cocoon_navi_card($attributes, $content) {
  $name = $attributes['menuName'];

  if ($name != '') {
    $classes = $attributes['classNames'];
    $atts = [
      'name' => $name,
      'type' => $attributes['menuType'],
      'bold' => $attributes['bold'] ? 1 : 0,
      'arrow' => $attributes['arrow'] ? 1 : 0,
      'horizontal' => $attributes['horizontal'] ? 1 : 0,
    ];

    $navi_card_content = get_navi_card_list_tag($atts);
    ob_start();
    echo '<div class="'.$classes.'">';
    echo $navi_card_content;
    echo '</div>';
    $html = ob_get_clean();
    if (is_rest()) {
      $html = replace_a_tags_to_span_tags($html);
    }
    return $html;
  }
}

/**
 * Registers the `cocoon-blocks/navicard` block on server.
 */
if( function_exists('register_block_type')) {
  register_block_type(
    __DIR__,
     array(
      'render_callback' => 'render_block_cocoon_navi_card',
    )
  );
}
