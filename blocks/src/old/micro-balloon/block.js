/**
 * Cocoon Blocks
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */

import { THEME_NAME, BLOCK_CLASS } from '../../helpers';
import { createLegacyStyleDeprecation } from '../../style-attribute-compat';

import { __ } from '@wordpress/i18n';
import { registerBlockType } from '@wordpress/blocks';
import {
  RichText,
  InspectorControls,
  useBlockProps,
} from '@wordpress/block-editor';
const { PanelBody, SelectControl, BaseControl, ToggleControl } = wp.components;
import { Fragment } from '@wordpress/element';
const DEFAULT_MSG = __( 'マイクロコピーバルーン', THEME_NAME );
const MICRO_COPY_CLASS = ' micro-copy';
const DEFAULT_STYLE = 'micro-balloon';

function getCircleClass( isCircle ) {
  return isCircle ? ' mc-circle' : '';
}

const BLOCK_ATTRIBUTES = {
  content: {
    type: 'string',
    selector: 'div',
    default: DEFAULT_MSG,
  },
  balloonStyle: {
    type: 'string',
    default: DEFAULT_STYLE,
  },
  style: {
    type: 'object',
  },
  color: {
    type: 'string',
    default: '',
  },
  isCircle: {
    type: 'boolean',
    default: false,
  },
};

const BLOCK_SUPPORTS = {
  html: false,
  align: [ 'left', 'center', 'right' ],
  customClassName: true,
  inserter: false,
};

// deprecatedの履歴スキーマを現行定義から独立させ、将来の変更波及を防ぎます。
const LEGACY_API_VERSION = 3;
const LEGACY_DEFAULT_STYLE = 'micro-balloon';
const LEGACY_BLOCK_ATTRIBUTES = {
  content: {
    type: 'string',
    selector: 'div',
    default: DEFAULT_MSG,
  },
  style: {
    type: 'string',
    default: LEGACY_DEFAULT_STYLE,
  },
  color: {
    type: 'string',
    default: '',
  },
  isCircle: {
    type: 'boolean',
    default: false,
  },
};
const LEGACY_BLOCK_SUPPORTS = {
  html: false,
  align: [ 'left', 'center', 'right' ],
  customClassName: true,
  inserter: false,
};

function save( { attributes } ) {
  const { content, balloonStyle, isCircle, color } = attributes;
  const blockProps = useBlockProps.save( {
    className:
      balloonStyle +
      color +
      getCircleClass( isCircle ) +
      MICRO_COPY_CLASS +
      BLOCK_CLASS,
  } );
  return (
    <div { ...blockProps }>
      <RichText.Content value={ content } />
    </div>
  );
}

// 旧style属性を使い、破損時を含む歴史的HTMLを現行saveから独立して再現します。
function legacySave( { attributes } ) {
  const { content, style, isCircle, color } = attributes;
  const blockProps = useBlockProps.save( {
    className:
      style +
      color +
      ( isCircle ? ' mc-circle' : '' ) +
      MICRO_COPY_CLASS +
      BLOCK_CLASS,
  } );
  return (
    <div { ...blockProps }>
      <RichText.Content value={ content } />
    </div>
  );
}

registerBlockType( 'cocoon-blocks/micro-balloon', {
  apiVersion: 3,
  title: __( 'マイクロバルーン', THEME_NAME ),
  icon: 'dismiss',
  category: THEME_NAME + '-old',
  description: __(
    'コンバージョンリンク（ボタン）の直上もしくは直下にテキストバルーン表示して、コンバージョン率アップを図るためのマイクロコピーです。',
    THEME_NAME
  ),

  attributes: BLOCK_ATTRIBUTES,
  supports: BLOCK_SUPPORTS,

  deprecated: [
    createLegacyStyleDeprecation( {
      apiVersion: LEGACY_API_VERSION,
      attributes: LEGACY_BLOCK_ATTRIBUTES,
      supports: LEGACY_BLOCK_SUPPORTS,
      legacySave,
      attributeName: 'balloonStyle',
      defaultValue: LEGACY_DEFAULT_STYLE,
    } ),
  ],

  edit( { attributes, setAttributes } ) {
    const { content, balloonStyle, isCircle, color } = attributes;
    //let circleClass = isCircle ? ' mc-circle' : '';
    return (
      <Fragment>
        <InspectorControls>
          <PanelBody title={ __( 'スタイル設定', THEME_NAME ) }>
            <SelectControl
              label={ __( 'タイプ', THEME_NAME ) }
              value={ balloonStyle }
              onChange={ ( value ) => setAttributes( { balloonStyle: value } ) }
              options={ [
                {
                  value: 'micro-balloon',
                  label: __( '下寄り', THEME_NAME ),
                },
                {
                  value: 'micro-balloon micro-bottom',
                  label: __( '上寄り', THEME_NAME ),
                },
              ] }
              __nextHasNoMarginBottom={ true }
              __next40pxDefaultSize={ true } // 新しいデフォルトサイズに対応
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
              __nextHasNoMarginBottom={ true }
              __next40pxDefaultSize={ true } // 新しいデフォルトサイズに対応
            />

            <ToggleControl
              __nextHasNoMarginBottom={ true }
              label={ __( '円形にする', THEME_NAME ) }
              checked={ isCircle }
              onChange={ ( value ) => setAttributes( { isCircle: value } ) }
            />
          </PanelBody>
        </InspectorControls>

        <div
          className={
            balloonStyle +
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

  save,
} );
