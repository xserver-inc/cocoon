/**
 * Cocoon Blocks
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */

import { THEME_NAME, LAYOUT_BLOCK_CLASS } from '../../helpers';
import classnames from 'classnames';

import { __ } from '@wordpress/i18n';
import { registerBlockType } from '@wordpress/blocks';
import { InnerBlocks, useBlockProps } from '@wordpress/block-editor';
import { Fragment } from '@wordpress/element';

const ALLOWED_BLOCKS = [
  'cocoon-blocks/column-left',
  'cocoon-blocks/column-center',
  'cocoon-blocks/column-right',
];

registerBlockType( 'cocoon-blocks/column-3', {
  apiVersion: 2,
  title: __( '3カラム', THEME_NAME ),
  icon: (
    <svg
      enable-background="new 0 0 24 24"
      height="512"
      viewBox="0 0 24 24"
      width="512"
      xmlns="http://www.w3.org/2000/svg"
    >
      <path d="m22.5 24h-3c-.827 0-1.5-.673-1.5-1.5v-21c0-.827.673-1.5 1.5-1.5h3c.827 0 1.5.673 1.5 1.5v21c0 .827-.673 1.5-1.5 1.5zm-3-23c-.275 0-.5.224-.5.5v21c0 .276.225.5.5.5h3c.275 0 .5-.224.5-.5v-21c0-.276-.225-.5-.5-.5z" />
      <path d="m13.5 24h-3c-.827 0-1.5-.673-1.5-1.5v-21c0-.827.673-1.5 1.5-1.5h3c.827 0 1.5.673 1.5 1.5v21c0 .827-.673 1.5-1.5 1.5zm-3-23c-.275 0-.5.224-.5.5v21c0 .276.225.5.5.5h3c.275 0 .5-.224.5-.5v-21c0-.276-.225-.5-.5-.5z" />
      <path d="m4.5 24h-3c-.827 0-1.5-.673-1.5-1.5v-21c0-.827.673-1.5 1.5-1.5h3c.827 0 1.5.673 1.5 1.5v21c0 .827-.673 1.5-1.5 1.5zm-3-23c-.275 0-.5.224-.5.5v21c0 .276.225.5.5.5h3c.275 0 .5-.224.5-.5v-21c0-.276-.225-.5-.5-.5z" />
    </svg>
  ),
  category: THEME_NAME + '-layout',
  description: __( '本文を左・中央・右カラムに分けます。', THEME_NAME ),
  keywords: [ 'column', '3' ],

  edit( { className } ) {
    const classes = classnames( className, {
      [ 'column-wrap' ]: true,
      [ 'column-3' ]: true,
      [ LAYOUT_BLOCK_CLASS ]: true,
    } );

    const blockProps = useBlockProps( {
      className: classes,
    } );

    return (
      <Fragment>
        <div { ...blockProps }>
          <InnerBlocks
            template={ [
              [
                'cocoon-blocks/column-left',
                { placeholder: __( '左側に入力する内容', THEME_NAME ) },
              ],
              [
                'cocoon-blocks/column-center',
                { placeholder: __( '中央に入力する内容', THEME_NAME ) },
              ],
              [
                'cocoon-blocks/column-right',
                { placeholder: __( '右側に入力する内容', THEME_NAME ) },
              ],
            ] }
            templateLock="all"
            allowedBlocks={ ALLOWED_BLOCKS }
          />
        </div>
      </Fragment>
    );
  },

  save( { className } ) {
    const classes = classnames( className, {
      [ 'column-wrap' ]: true,
      [ 'column-3' ]: true,
      [ LAYOUT_BLOCK_CLASS ]: true,
    } );

    const blockProps = useBlockProps.save( {
      className: classes,
    } );

    return (
      <div { ...blockProps }>
        <InnerBlocks.Content />
      </div>
    );
  },
} );
