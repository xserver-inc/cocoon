/**
 * @package Cocoon Blocks
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */

/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { InnerBlocks, useBlockProps } from '@wordpress/block-editor';
import { Fragment } from '@wordpress/element';

export default function edit() {
	return (
		<Fragment>
			<div { ...useBlockProps() }>
				<InnerBlocks />
			</div>
		</Fragment>
	);
}
