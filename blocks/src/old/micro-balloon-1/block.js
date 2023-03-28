/**
 * Cocoon Blocks
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */

import { THEME_NAME, BLOCK_CLASS } from '../../helpers';

import { __ } from '@wordpress/i18n';
import { registerBlockType } from '@wordpress/blocks';
const { InnerBlocks, RichText, InspectorControls } = wp.editor;
const { PanelBody, SelectControl, BaseControl, ToggleControl } = wp.components;
import { Fragment } from '@wordpress/element';
const DEFAULT_MSG = __( 'マイクロコピーバルーン', THEME_NAME );
const MICRO_COPY_CLASS = ' micro-copy';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';

function getCircleClass( isCircle ) {
  return isCircle ? ' mc-circle' : '';
}

registerBlockType( 'cocoon-blocks/micro-balloon-1', {
  title: __( 'マイクロバルーン', THEME_NAME ),
  icon: 'dismiss',
  category: THEME_NAME + '-old',
  description: __(
    'コンバージョンリンク（ボタン）の直上もしくは直下にテキストバルーン表示して、コンバージョン率アップを図るためのマイクロコピーです。',
    THEME_NAME
  ),

  attributes: {
    content: {
      type: 'string',
      selector: 'div',
      default: DEFAULT_MSG,
    },
    style: {
      type: 'string',
      default: ' micro-top',
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
    inserter: false,
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
                  value: ' micro-top',
                  label: __( '下寄り', THEME_NAME ),
                },
                {
                  value: ' micro-bottom',
                  label: __( '上寄り', THEME_NAME ),
                },
              ] }
            />

            <SelectControl
              label={ __( '色設定', THEME_NAME ) }
              value={ color }
              onChange={ ( value ) => setAttributes( { color: value } ) }
              options={ [
                {
                  value: '',
                  label: __( 'デフォルト', THEME_NAME ),
                },
                {
                  value: ' mc-yellow',
                  label: __( '黄色', THEME_NAME ),
                },
                {
                  value: ' mc-red',
                  label: __( '赤色', THEME_NAME ),
                },
                {
                  value: ' mc-blue',
                  label: __( '青色', THEME_NAME ),
                },
                {
                  value: ' mc-green',
                  label: __( '緑色', THEME_NAME ),
                },
              ] }
            />

            <ToggleControl
              label={ __( '円形にする', THEME_NAME ) }
              checked={ isCircle }
              onChange={ ( value ) => setAttributes( { isCircle: value } ) }
            />
          </PanelBody>
        </InspectorControls>

        <div
          className={
            'micro-balloon' +
            style +
            color +
            getCircleClass( isCircle ) +
            MICRO_COPY_CLASS +
            BLOCK_CLASS
          }
        >
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
    //let circleClass = isCircle ? ' mc-circle' : '';
    return (
      <div
        className={
          'micro-balloon' +
          style +
          color +
          getCircleClass( isCircle ) +
          MICRO_COPY_CLASS +
          BLOCK_CLASS
        }
      >
        <RichText.Content value={ content } />
      </div>
    );
  },
} );
