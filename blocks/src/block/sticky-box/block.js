/**
 * Cocoon Blocks
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */

import {THEME_NAME, CLICK_POINT_MSG, BLOCK_CLASS} from '../../helpers';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import classnames from 'classnames';

const { __ } = wp.i18n;
const { registerBlockType, createBlock } = wp.blocks;
const { InnerBlocks, RichText, InspectorControls } = wp.editor;
const { PanelBody, SelectControl, BaseControl } = wp.components;
const { Fragment } = wp.element;

//classの取得
function getClasses(style) {
  const classes = classnames(
    {
      [ 'blank-box' ]: true,
      [ 'sticky' ]: true,
      [ style ]: !! style,
      [ 'block-box' ]: true,
    }
  );
  return classes;
}

registerBlockType( 'cocoon-blocks/sticky-box', {

  title: __( '付箋風ボックス', THEME_NAME ),
  icon: <FontAwesomeIcon icon={['far', 'sticky-note']} />,
  category: THEME_NAME + '-block',
  description: __( '目立つ濃いめの色で付箋風にメッセージを伝えるためのボックスです。', THEME_NAME ),
  keywords: [ 'sticky', 'box' ],

  attributes: {
    content: {
      type: 'string',
      default: CLICK_POINT_MSG,
    },
    style: {
      type: 'string',
      default: '',
    },
  },
  transforms: {
    to: [
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
        blocks: [ 'cocoon-blocks/icon-box' ],
        transform: ( attributes, innerBlocks ) => {
          return createBlock( 'cocoon-blocks/icon-box', {}, innerBlocks );
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
    const { content, style } = attributes;

    return (
      <Fragment>
        <InspectorControls>
          <PanelBody title={ __( 'スタイル設定', THEME_NAME ) }>

            <SelectControl
              label={ __( 'タイプ', THEME_NAME ) }
              value={ style }
              onChange={ ( value ) => setAttributes( { style: value } ) }
              options={ [
                {
                  value: '',
                  label: __( '灰色', THEME_NAME ),
                },
                {
                  value: 'st-yellow',
                  label: __( '黄色', THEME_NAME ),
                },
                {
                  value: 'st-red',
                  label: __( '赤色', THEME_NAME ),
                },
                {
                  value: 'st-blue',
                  label: __( '青色', THEME_NAME ),
                },
                {
                  value: 'st-green',
                  label: __( '緑色', THEME_NAME ),
                },
              ] }
            />

          </PanelBody>
        </InspectorControls>

        <div className={ classnames(getClasses(style), className) }>
          <InnerBlocks />
        </div>
      </Fragment>
    );
  },

  save( { attributes } ) {
    const { content, style } = attributes;
    return (
      <div className={ getClasses(style) }>
        <InnerBlocks.Content />
      </div>
    );
  }
} );
