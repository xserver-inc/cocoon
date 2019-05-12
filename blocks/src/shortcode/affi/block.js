/**
 * Cocoon Blocks
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */

import {THEME_NAME} from '../../helpers.js';

const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;
const { InnerBlocks } = wp.editor;
const { Fragment } = wp.element;

//左カラム
registerBlockType( 'cocoon-blocks/affi', {

  title: __( 'アフィリエイトタグタグ', THEME_NAME ),
  icon: 'grid-view',
  category: THEME_NAME + '-shortcode',
  description: __( 'アフィリエイトタグ', THEME_NAME ),

  edit( { attributes, setAttributes } ) {
    return (
        '[affi id=1]'
    );
  },

  save( { attributes } ) {
    return (
      '[affi id=1]'
    );
  }
} );
