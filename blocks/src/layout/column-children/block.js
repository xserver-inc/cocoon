/**
 * Cocoon Blocks
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */

import {THEME_NAME, LAYOUT_BLOCK_CLASS} from '../../helpers';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';


import { __ } from '@wordpress/i18n';
import { registerBlockType } from '@wordpress/blocks';
import { InnerBlocks } from '@wordpress/block-editor';
import { Fragment } from '@wordpress/element';

//左カラム
registerBlockType( 'cocoon-blocks/column-left', {

  title: __( '左カラム', THEME_NAME ),
  parent: [
    'cocoon-blocks/column-2',
    'cocoon-blocks/column-3',
  ],
  icon: <FontAwesomeIcon icon={['far', 'square']} />,
  category: THEME_NAME + '-layout',
  description: __( 'カラム左側に表示される内容内容を入力。', THEME_NAME ),
  supports: {
    inserter: false,
  },

  edit( { attributes, setAttributes } ) {
    return (
      <Fragment>
        <div className="column-left">
          <InnerBlocks templateLock={ false } />
        </div>
      </Fragment>
    );
  },

  save( { attributes } ) {
    return (
      <div className="column-left">
        <InnerBlocks.Content />
      </div>
    );
  }
} );

//中央カラム
registerBlockType( 'cocoon-blocks/column-center', {

  title: __( '中央カラム', THEME_NAME ),
  parent: [
    'cocoon-blocks/column-2',
    'cocoon-blocks/column-3',
  ],
  icon: <FontAwesomeIcon icon={['far', 'square']} />,
  category: THEME_NAME + '-layout',
  description: __( 'カラム中央に表示される内容内容を入力。', THEME_NAME ),
  supports: {
    inserter: false,
  },

  edit( { attributes, setAttributes } ) {
    return (
      <Fragment>
        <div className="column-center">
          <InnerBlocks templateLock={ false } />
        </div>
      </Fragment>
    );
  },

  save( { attributes } ) {
    return (
      <div className="column-center">
        <InnerBlocks.Content />
      </div>
    );
  }
} );


//右カラム
registerBlockType( 'cocoon-blocks/column-right', {

  apiVersion: 2,
  title: __( '右カラム', THEME_NAME ),
  parent: [
    'cocoon-blocks/column-2',
    'cocoon-blocks/column-3',
  ],
  icon: <FontAwesomeIcon icon={['far', 'square']} />,
  category: THEME_NAME + '-layout',
  description: __( 'カラム右側に表示される内容内容を入力。', THEME_NAME ),
  supports: {
    inserter: false,
  },

  edit( { attributes, setAttributes } ) {
    return (
      <Fragment>
        <div className="column-right">
          <InnerBlocks templateLock={ false } />
        </div>
      </Fragment>
    );
  },

  save( { attributes } ) {
    return (
      <div className="column-right">
        <InnerBlocks.Content />
      </div>
    );
  }
} );
