<?php
/**
 * Server-side rendering of the `cocoon-blocks/new-list` block.
 *
 * @package Cocoon
 * @author yhira
 * @link https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */

/**
 * Renders the `cocoon-blocks/new-list` block on the server.
 *
 * @param  array  $attributes The block attributes.
 * @param  string $content    The block content.
 * @return string Returns the block content.
 */
function render_block_cocoon_block_new_list($attributes, $content)
{
	$classes = $attributes['classNames'];
	$atts = [
		'count' => $attributes['count'],
		'cats' => $attributes['showAllCats'] ? 'all' : $attributes['cats'],
		'tags' => $attributes['showAllTags'] ? 'all' : $attributes['tags'],
		'type' => $attributes['type'],
		'children' => $attributes['children'] ? 1 : 0,
		'post_type' => $attributes['post_type'],
		'taxonomy' => $attributes['taxonomy'],
		'sticky' => $attributes['sticky'] ? 1 : 0,
		'modified' => $attributes['modified'] ? 1 : 0,
		'order' => $attributes['order'],
		'bold' => $attributes['bold'] ? 1 : 0,
		'arrow' => $attributes['arrow'] ? 1 : 0,
		'snippet' => $attributes['snippet'] ? 1 : 0,
		'author' => $attributes['author'],
		'offset' => $attributes['offset'],
		'horizontal' => $attributes['horizontal'] ? 1 : 0,
	];

	$new_list_content = new_entries_shortcode($atts);
	ob_start();
	echo '<div class="' . $classes . '">';
	echo $new_list_content;
	echo '</div>';
	$html = ob_get_clean();
	if (is_rest()) {
		$html = replace_a_tags_to_span_tags($html);
	}
	return $html;
}

/**
 * Registers the `cocoon-blocks/new-list` block on server.
 */
if (function_exists('register_block_type')) {
	register_block_type(__DIR__, [
		'render_callback' => 'render_block_cocoon_block_new_list',
	]);
}
