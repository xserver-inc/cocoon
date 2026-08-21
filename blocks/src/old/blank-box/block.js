/**
 * Cocoon Blocks
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */

import { THEME_NAME, BLOCK_CLASS } from '../../helpers';
import { createLegacyStyleDeprecation } from '../../style-attribute-compat';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import classnames from 'classnames';

import { __ } from '@wordpress/i18n';
import { registerBlockType } from '@wordpress/blocks';
import {
  InnerBlocks,
  RichText,
  InspectorControls,
  useBlockProps,
} from '@wordpress/block-editor';
const { PanelBody, SelectControl, BaseControl } = wp.components;
import { Fragment } from '@wordpress/element';
const DEFAULT_MSG = __(
  'こちらをクリックして設定変更。この入力は公開ページで反映されません。',
  THEME_NAME
);
const DEFAULT_STYLE = 'blank-box';

const BLOCK_ATTRIBUTES = {
  content: {
    type: 'string',
    selector: 'div',
    default: DEFAULT_MSG,
  },
  boxStyle: {
    type: 'string',
    default: DEFAULT_STYLE,
  },
  style: {
    type: 'object',
  },
};

const BLOCK_SUPPORTS = { html: false, inserter: false };

// deprecatedの履歴スキーマを現行定義から独立させ、将来の変更波及を防ぎます。
const LEGACY_API_VERSION = 3;
const LEGACY_DEFAULT_STYLE = 'blank-box';
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
};
const LEGACY_BLOCK_SUPPORTS = { html: false, inserter: false };

function save( { attributes } ) {
  const blockProps = useBlockProps.save( {
    className: attributes.boxStyle + BLOCK_CLASS,
  } );
  return (
    <div { ...blockProps }>
      <InnerBlocks.Content />
    </div>
  );
}

// 旧style属性を使って、保存済みの歴史的HTMLだけを再現します。
function legacySave( { attributes } ) {
  const blockProps = useBlockProps.save( {
    className: attributes.style + BLOCK_CLASS,
  } );
  return (
    <div { ...blockProps }>
      <InnerBlocks.Content />
    </div>
  );
}

registerBlockType( 'cocoon-blocks/blank-box', {
  apiVersion: 3,
  title: __( '白抜きボックス', THEME_NAME ),
  icon: 'dismiss',
  category: THEME_NAME + '-old',
  description: __(
    'コンテンツを囲むだけのブランクボックスを表示します。',
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
      attributeName: 'boxStyle',
      defaultValue: LEGACY_DEFAULT_STYLE,
    } ),
  ],

  edit( { attributes, setAttributes } ) {
    const { content, boxStyle, alignment } = attributes;

    function onChange( event ) {
      setAttributes( { boxStyle: event.target.value } );
    }

    function onChangeContent( newContent ) {
      setAttributes( { content: newContent } );
    }

    return (
      <Fragment>
        <InspectorControls>
          <PanelBody title={ __( 'スタイル設定', THEME_NAME ) }>
            <SelectControl
              label={ __( 'タイプ', THEME_NAME ) }
              value={ boxStyle }
              onChange={ ( value ) => setAttributes( { boxStyle: value } ) }
              options={ [
                {
                  value: 'blank-box',
                  label: __( 'デフォルト', THEME_NAME ),
                },
                {
                  value: 'blank-box bb-yellow',
                  label: __( '黄色', THEME_NAME ),
                },
                {
                  value: 'blank-box bb-red',
                  label: __( '赤色', THEME_NAME ),
                },
                {
                  value: 'blank-box bb-blue',
                  label: __( '青色', THEME_NAME ),
                },
                {
                  value: 'blank-box bb-green',
                  label: __( '緑色', THEME_NAME ),
                },
              ] }
              __nextHasNoMarginBottom={ true }
              __next40pxDefaultSize={ true } // 新しいデフォルトサイズに対応
            />
          </PanelBody>
        </InspectorControls>

        <div className={ attributes.boxStyle + BLOCK_CLASS }>
          <span className={ 'box-block-msg' }>
            <RichText value={ content } placeholder={ DEFAULT_MSG } />
          </span>
          <InnerBlocks />
        </div>
      </Fragment>
    );
  },

  save,
} );
