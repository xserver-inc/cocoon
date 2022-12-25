<?php
/**
 * Server-side rendering of the `cocoon-blocks/icon-box` block.
 *
 * @package Cocoon
 * @author yhira
 * @link https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */

/**
 * Renders the `cocoon-blocks/icon-box` block on the server.
 *
 * @param  array  $attributes The block attributes.
 * @param  string $content    The block content.
 * @return string Returns the block content.
 */
// function render_block_cocoon_block_icon_box($attributes, $content)
// {
// 	return;
// }

/**
 * Registers the `cocoon-blocks/icon-box` block on server.
 */
function register_block_cocoon_block_icon_box()
{
	register_block_type(__DIR__, [
		//'render_callback' => 'render_block_cocoon_block_icon_box',
	]);
}
add_action('init', 'register_block_cocoon_block_icon_box', 99);
