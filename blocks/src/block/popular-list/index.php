<?php
/**
 * Server-side rendering of the `cocoon-blocks/popular-list` block.
 *
 * @package Cocoon
 * @author yhira
 * @link https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */

/**
 * Renders the `cocoon-blocks/popular-list` block on the server.
 *
 * @param  array  $attributes The block attributes.
 * @param  string $content    The block content.
 * @return string Returns the block content.
 */
function render_block_cocoon_block_popular_list($attributes, $content)
{
	$classes = $attributes['classNames'];
	$atts = [
		'days' => $attributes['showAllDays'] ? 'all' : $attributes['days'],
		'count' => $attributes['count'],
		'type' => $attributes['type'],
		'rank' => $attributes['rank'] ? 1 : 0,
		'pv' => $attributes['pv'] ? 1 : 0,
		'cats' => $attributes['showAllCats'] ? 'all' : $attributes['cats'],
		'children' => $attributes['children'] ? 1 : 0,
		'ex_posts' => $attributes['ex_posts'],
		'ex_cats' => $attributes['ex_cats'],
		'bold' => $attributes['bold'] ? 1 : 0,
		'arrow' => $attributes['arrow'] ? 1 : 0,
		'post_type' => $attributes['post_type'],
		'horizontal' => $attributes['horizontal'] ? 1 : 0,
	];

	$popular_list_content = popular_entries_shortcode($atts);
	ob_start();
	echo '<div class="' . $classes . '">';
	echo $popular_list_content;
	echo '</div>';
	$html = ob_get_clean();
	if (is_rest()) {
		$html = replace_a_tags_to_span_tags($html);
	}
	return $html;
}

/**
 * Registers the `cocoon-blocks/popular-list` block on server.
 */
if (function_exists('register_block_type')) {
	register_block_type(__DIR__, [
		'render_callback' => 'render_block_cocoon_block_popular_list',
	]);
}
