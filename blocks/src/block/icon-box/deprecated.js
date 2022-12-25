/**
 * @package Cocoon Blocks
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */

/**
 * External dependencies
 */
import classnames from 'classnames';

/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
const { createBlock } = wp.blocks;
const { InnerBlocks } = wp.editor;
import { Fragment } from '@wordpress/element';

/**
 * Internal dependencies
 */
import { CLICK_POINT_MSG} from '../../helpers';

//classの取得
function getClasses() {
  const classes = classnames(
    {
      'common-icon-box': true,
      [ 'block-box' ]: true,
    }
  );
  return classes;
}

export default [{
  attributes: {
    content: {
      type: 'string',
      default: CLICK_POINT_MSG,
    },
  },
  transforms: {
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
  },

  edit( { attributes, setAttributes, className } ) {
    const { content } = attributes;

    return (
      <Fragment>
        <div className={ classnames(getClasses(), className) }>
          <InnerBlocks />
        </div>
      </Fragment>
    );
  },

  save( { attributes } ) {
    const { content } = attributes;
    return (
      <div className={ getClasses() }>
        <InnerBlocks.Content />
      </div>
    );
  },
}];
