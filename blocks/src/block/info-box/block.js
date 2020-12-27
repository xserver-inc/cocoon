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
      [ style ]: !! style,
      [ 'block-box' ]: true,
    }
  );
  return classes;
}

registerBlockType( 'cocoon-blocks/info-box', {

  title: __( '案内ボックス', THEME_NAME ),
  icon: <FontAwesomeIcon icon={['fas', 'info-circle']} />,
  category: THEME_NAME + '-block',
  description: __( 'ボックスの背景色により、直感的にメッセージ内容を伝えるためのボックスです。', THEME_NAME ),
  keywords: [ 'info', 'box' ],

  attributes: {
    content: {
      type: 'string',
      default: CLICK_POINT_MSG,
    },
    style: {
      type: 'string',
      default: 'primary-box',
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
        blocks: [ 'cocoon-blocks/icon-box' ],
        transform: ( attributes, innerBlocks ) => {
          return createBlock( 'cocoon-blocks/icon-box', {}, innerBlocks );
        },
      },
      {
        type: 'block',
        blocks: [ 'cocoon-blocks/tab-box-1' ],
        transform: ( attributes, innerBlocks ) => {
          return createBlock( 'cocoon-blocks/tab-box-1', {}, innerBlocks );
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
                  value: 'primary-box',
                  label: __( 'プライマリー（濃い水色）', THEME_NAME ),
                },
                {
                  value: 'secondary-box',
                  label: __( 'セカンダリー（濃い灰色）', THEME_NAME ),
                },
                {
                  value: 'info-box',
                  label: __( 'インフォ（薄い青）', THEME_NAME ),
                },
                {
                  value: 'success-box',
                  label: __( 'サクセス（薄い緑）', THEME_NAME ),
                },
                {
                  value: 'warning-box',
                  label: __( 'ワーニング（薄い黄色）', THEME_NAME ),
                },
                {
                  value: 'danger-box',
                  label: __( 'デンジャー（薄い赤色）', THEME_NAME ),
                },
                {
                  value: 'light-box',
                  label: __( 'ライト（白色）', THEME_NAME ),
                },
                {
                  value: 'dark-box',
                  label: __( 'ダーク（暗い灰色）', THEME_NAME ),
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
