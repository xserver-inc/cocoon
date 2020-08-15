/**
 * Cocoon Blocks
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */

import {THEME_NAME, BLOCK_CLASS} from '../../helpers';
import classnames from 'classnames';

const { __ } = wp.i18n;
const { registerBlockType, createBlock } = wp.blocks;
const { InnerBlocks, RichText, InspectorControls } = wp.editor;
const { PanelBody, SelectControl, BaseControl } = wp.components;
const { Fragment } = wp.element;
const DEFAULT_MSG = __( 'マイクロコピーテキスト', THEME_NAME );
const MICRO_COPY_CLASS = 'micro-copy';

//classの取得
function getClasses(style) {
  const classes = classnames(
    {
      [ MICRO_COPY_CLASS ]: true,
      [ style ]: !! style,
      [ 'block-box' ]: true,
    }
  );
  return classes;
}

registerBlockType( 'cocoon-blocks/micro-text', {

  title: __( 'マイクロテキスト', THEME_NAME ),
  description: __( 'コンバージョンリンク（ボタン）の直上もしくは直下に小さくテキスト表示して、コンバージョン率アップを図るためのマイクロコピーです。', THEME_NAME );
  icon: 'editor-textcolor',
  category: THEME_NAME + '-micro',
  keywords: [ 'micro', 'copy', 'text' ],

  attributes: {
    content: {
      type: 'string',
      selector: 'div',
      default: DEFAULT_MSG,
    },
    style: {
      type: 'string',
      default: 'micro-top',
    },
  },
  supports: {
    align: [ 'left', 'center', 'right' ],
    customClassName: true,
  },
  transforms: {
    to: [
      {
        type: 'block',
        blocks: [ 'cocoon-blocks/micro-balloon-2' ],
        transform: ( attributes ) => {
          return createBlock( 'cocoon-blocks/micro-balloon-2', attributes );
        },
      }
    ]
  },

  edit( { attributes, setAttributes } ) {
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
                  value: 'micro-top',
                  label: __( '下寄り', THEME_NAME ),
                },
                {
                  value: 'micro-bottom',
                  label: __( '上寄り', THEME_NAME ),
                },
              ] }
            />

          </PanelBody>
        </InspectorControls>

        <div className={ getClasses(style) }>
          <RichText
            value={ content }
            onChange={ ( value ) => setAttributes( { content: value } ) }
          />
        </div>
      </Fragment>
    );
  },

  save( { attributes } ) {
    const { content, style } = attributes;
    return (
      <div className={ getClasses(style) }>
        <RichText.Content
          value={ content }
        />
      </div>
    );
  }
} );