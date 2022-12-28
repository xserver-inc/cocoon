/**
 * @package Cocoon Blocks
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */

/**
 * WordPress dependencies
 */
import { createBlock } from '@wordpress/blocks';

export default {
  to: [
    {
      type: 'block',
      blocks: [ 'cocoon-blocks/sticky-box' ],
      transform: ( attributes, innerBlocks ) => {
        return createBlock( 'cocoon-blocks/sticky-box', {}, innerBlocks );
      },
    },
    {
      type: 'block',
      blocks: [ 'cocoon-blocks/blank-box-1' ],
      transform: ( attributes, innerBlocks ) => {
        return createBlock( 'cocoon-blocks/blank-box-1', {}, innerBlocks );
      },
    },
    {
      type: 'block',
      blocks: [ 'cocoon-blocks/tab-box-1' ],
      transform: ( attributes, innerBlocks ) => {
        return createBlock( 'cocoon-blocks/tab-box-1', {}, innerBlocks );
      },
    },
    {
      type: 'block',
      blocks: [ 'cocoon-blocks/info-box' ],
      transform: ( attributes, innerBlocks ) => {
        return createBlock( 'cocoon-blocks/info-box', {}, innerBlocks );
      },
    },
  ],
};
