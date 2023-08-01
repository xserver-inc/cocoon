<?php
/**
 * Server-side rendering of the `cocoon-blocks/info-list` block.
 *
 * @package Cocoon
 * @author yhira
 * @link https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */

/**
 * Renders the `cocoon-blocks/info-list` block on the server.
 *
 * @param  array  $attributes The block attributes.
 * @param  string $content    The block content.
 * @return string Returns the block content.
 */
function render_block_cocoon_block_info_list($attributes, $content)
{
	$classes = $attributes['classNames'];
	$atts = [
		'count' => $attributes['count'],
		'cats' => $attributes['showAllCats'] ? 'all' : $attributes['cats'],
		'caption' => $attributes['caption'],
		'frame' => $attributes['showFrame'] ? 1 : 0,
		'divider' => $attributes['showDivider'] ? 1 : 0,
		'modified' => $attributes['modified'] ? 1 : 0,
	];

	ob_start();
	echo '<div class="' . $classes . '">';
	generate_info_list_tag($atts);
	echo '</div>';
	$html = ob_get_clean();
	if (is_rest()) {
		$html = replace_a_tags_to_span_tags($html);
	}
	return $html;
}

/**
 * Registers the `cocoon-blocks/info-list` block on server.
 */
if (function_exists('register_block_type')) {
	register_block_type(__DIR__, [
		'render_callback' => 'render_block_cocoon_block_info_list',
	]);
}
