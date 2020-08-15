/**
 * Cocoon Blocks
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */

import {THEME_NAME, BLOCK_CLASS, colorValueToSlug} from '../../helpers';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import classnames from 'classnames';

const { __ } = wp.i18n;
const { registerBlockType, createBlock } = wp.blocks;
const { InnerBlocks, RichText, InspectorControls, PanelColorSettings, ContrastChecker } = wp.editor;
const { PanelBody, SelectControl, BaseControl, ToggleControl } = wp.components;
const { Fragment } = wp.element;
const DEFAULT_MSG = __( 'マイクロコピーバルーン', THEME_NAME );
const MICRO_COPY_CLASS = 'micro-copy';

//classの取得
function getClasses(style, isCircle, color) {
  const classes = classnames(
    {
      [ 'micro-balloon' ]: true,
      [ style ]: !! style,
      [ `mc-${ colorValueToSlug(color) }` ]: !! colorValueToSlug(color),
      [ 'mc-circle' ]: !! isCircle,
      [ MICRO_COPY_CLASS ]: true,
      [ 'block-box' ]: true,
    }
  );
  return classes;
}

// function getCircleClass(isCircle) {
//   return isCircle ? ' mc-circle' : '';
// }

registerBlockType( 'cocoon-blocks/micro-balloon-2', {

  title: __( 'マイクロバルーン', THEME_NAME ),
  icon: 'admin-comments',
  category: THEME_NAME + '-micro',
  description: __( 'コンバージョンリンク（ボタン）の直上もしくは直下にテキストバルーン表示して、コンバージョン率アップを図るためのマイクロコピーです。', THEME_NAME ),
  keywords: [ 'micro', 'copy', 'balloon' ],

  attributes: {
    content: {
      type: 'string',
      default: DEFAULT_MSG,
    },
    style: {
      type: 'string',
      default: 'micro-top',
    },
    color: {
      type: 'string',
      default: '',
    },
    isCircle: {
      type: 'boolean',
      default: false,
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
        blocks: [ 'cocoon-blocks/micro-text' ],
        transform: ( attributes ) => {
          return createBlock( 'cocoon-blocks/micro-text', attributes );
        },
      }
    ]
  },

  edit( { attributes, setAttributes } ) {
    const { content, style, isCircle, color } = attributes;
    //let circleClass = isCircle ? ' mc-circle' : '';
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

            <ToggleControl
              label={ __( '円形にする', THEME_NAME ) }
              checked={ isCircle }
              onChange={ ( value ) => setAttributes( { isCircle: value } ) }
            />

          </PanelBody>

          <PanelColorSettings
            title={ __( '色設定', THEME_NAME ) }
            initialOpen={ true }
            colorSettings={ [
              {
                value: color,
                onChange: ( value ) => setAttributes( { color: value } ),
                label: __( '色設定', THEME_NAME ),
              },
            ] }
          >
            <ContrastChecker
              color={ color }
            />
          </PanelColorSettings>

        </InspectorControls>

        <div className={ getClasses(style, isCircle, color) }>
          <RichText
            value={ content }
            onChange={ ( value ) => setAttributes( { content: value } ) }
          />
        </div>
      </Fragment>
    );
  },

  save( { attributes } ) {
    const { content, style, isCircle, color } = attributes;
    return (
      <div className={ getClasses(style, isCircle, color) }>
        <RichText.Content
          value={ content }
        />
      </div>
    );
  }
} );