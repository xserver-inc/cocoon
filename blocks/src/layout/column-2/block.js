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
import {
  InnerBlocks,
  InspectorControls,
  useBlockProps,
} from '@wordpress/block-editor';
import { PanelBody, SelectControl } from '@wordpress/components';
import { Fragment } from '@wordpress/element';

const ALLOWED_BLOCKS = [
  'cocoon-blocks/column-left',
  'cocoon-blocks/column-right',
];
const TEMPLATE = [
  [
    'cocoon-blocks/column-left',
    { placeholder: __( '左側に入力する内容', THEME_NAME ) },
  ],
  [
    'cocoon-blocks/column-right',
    { placeholder: __( '右側に入力する内容', THEME_NAME ) },
  ],
];

//classの取得
function getClasses( ratio ) {
  const classes = classnames( {
    [ 'column-wrap' ]: true,
    [ 'column-2' ]: true,
    [ ratio ]: !! ratio,
    [ LAYOUT_BLOCK_CLASS ]: true,
  } );
  return classes;
}

registerBlockType( 'cocoon-blocks/column-2', {
  apiVersion: 2,
  title: __( '2カラム', THEME_NAME ),
  icon: (
    <svg
      enable-background="new 0 0 24 24"
      height="512"
      viewBox="0 0 24 24"
      width="512"
      xmlns="http://www.w3.org/2000/svg"
    >
      <path d="m22.5 24h-8c-.827 0-1.5-.673-1.5-1.5v-21c0-.827.673-1.5 1.5-1.5h8c.827 0 1.5.673 1.5 1.5v21c0 .827-.673 1.5-1.5 1.5zm-8-23c-.276 0-.5.224-.5.5v21c0 .276.224.5.5.5h8c.276 0 .5-.224.5-.5v-21c0-.276-.224-.5-.5-.5z" />
      <path d="m9.5 24h-8c-.827 0-1.5-.673-1.5-1.5v-21c0-.827.673-1.5 1.5-1.5h8c.827 0 1.5.673 1.5 1.5v21c0 .827-.673 1.5-1.5 1.5zm-8-23c-.276 0-.5.224-.5.5v21c0 .276.224.5.5.5h8c.276 0 .5-.224.5-.5v-21c0-.276-.224-.5-.5-.5z" />
    </svg>
  ),
  category: THEME_NAME + '-layout',
  description: __(
    '本文を左右カラムに分けます。オプションでカラム比率を変更できます。',
    THEME_NAME
  ),
  keywords: [ 'column', '2' ],

  attributes: {
    ratio: {
      type: 'string',
      default: 'column-2-2-1-1',
    },
  },

  edit( { attributes, setAttributes, className } ) {
    const { ratio } = attributes;
    const classes = classnames( className, {
      [ 'column-wrap' ]: true,
      [ 'column-2' ]: true,
      [ ratio ]: !! ratio,
      [ LAYOUT_BLOCK_CLASS ]: true,
    } );

    const blockProps = useBlockProps( {
      className: classes,
    } );

    // const innerBlocksProps = useInnerBlocksProps( {
    //   allowedBlocks: ALLOWED_BLOCKS,
    //   template: TEMPLATE,
    //   templateLock: "all",
    //   orientation: 'horizontal',
    // } );

    return (
      <Fragment>
        <InspectorControls>
          <PanelBody title={ __( 'スタイル設定', THEME_NAME ) }>
            <SelectControl
              label={ __( 'カラム比率', THEME_NAME ) }
              value={ ratio }
              onChange={ ( value ) => setAttributes( { ratio: value } ) }
              options={ [
                {
                  value: 'column-2-2-1-1',
                  label: __( '1:1（｜□｜□｜）', THEME_NAME ),
                },
                {
                  value: 'column-2-3-1-2',
                  label: __( '1:2（｜□｜□□｜）', THEME_NAME ),
                },
                {
                  value: 'column-2-3-2-1',
                  label: __( '2:1（｜□□｜□｜）', THEME_NAME ),
                },
                {
                  value: 'column-2-4-1-3',
                  label: __( '1:3（｜□｜□□□｜））', THEME_NAME ),
                },
                {
                  value: 'column-2-4-3-1',
                  label: __( '3:1,（｜□□□｜□｜）', THEME_NAME ),
                },
              ] }
              __nextHasNoMarginBottom={ true }
            />
          </PanelBody>
        </InspectorControls>
        <div { ...blockProps }>
          <InnerBlocks
            template={ [
              [
                'cocoon-blocks/column-left',
                { placeholder: __( '左側に入力する内容', THEME_NAME ) },
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

  save( { attributes, className } ) {
    const { ratio } = attributes;
    const classes = classnames( className, {
      [ 'column-wrap' ]: true,
      [ 'column-2' ]: true,
      [ ratio ]: !! ratio,
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
