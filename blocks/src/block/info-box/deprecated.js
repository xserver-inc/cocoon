/**
 * Cocoon Blocks
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */

import { THEME_NAME, CLICK_POINT_MSG } from '../../helpers';
import {
  isLegacyStyle,
  migrateLegacyStyleAttribute,
} from '../../style-attribute-compat';
import classnames from 'classnames';

import { __ } from '@wordpress/i18n';
const { createBlock } = wp.blocks;
import {
  InnerBlocks,
  InspectorControls,
  useBlockProps,
} from '@wordpress/block-editor';
const { PanelBody, SelectControl } = wp.components;
import { Fragment } from '@wordpress/element';

const DEFAULT_BOX_STYLE = 'primary-box';

// WordPress 7.0で破損が起きる直前の属性定義を、現行block.jsonから独立して保持します。
const LEGACY_STYLE_ATTRIBUTES = {
  style: {
    type: [ 'string', 'object' ],
    default: DEFAULT_BOX_STYLE,
  },
};

// deprecatedの検証条件が将来のsupports変更に追随しないよう、当時の値を固定します。
const LEGACY_SUPPORTS = {
  anchor: true,
  html: false,
};

// 現行直前のHTMLを再現し、正常な旧記事とWordPress 7.0で壊れた記事の両方を検出します。
function saveLegacyStyle( { attributes } ) {
  const { style } = attributes;
  const classes = classnames( 'block-box', {
    [ style ]: !! style,
  } );
  const blockProps = useBlockProps.save( {
    className: classes,
  } );

  return (
    <div { ...blockProps }>
      <InnerBlocks.Content />
    </div>
  );
}

// どの旧形式からでも、deprecatedを経由せず現行属性へ直接移行します。
const migrateStyle = ( attributes ) =>
  migrateLegacyStyleAttribute( attributes, 'boxStyle', DEFAULT_BOX_STYLE );

const legacyStyle = {
  apiVersion: 3,
  supports: LEGACY_SUPPORTS,
  attributes: LEGACY_STYLE_ATTRIBUTES,
  isEligible: isLegacyStyle,
  migrate: migrateStyle,
  save: saveLegacyStyle,
};

//classの取得
function getClasses( style ) {
  const classes = classnames( {
    [ style ]: !! style,
    'block-box': true,
  } );
  return classes;
}

export default [
  legacyStyle,
  {
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
    migrate: migrateStyle,

    edit( { attributes, setAttributes, className } ) {
      const { style } = attributes;

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
                __nextHasNoMarginBottom={ true }
                __next40pxDefaultSize={ true } // 新しいデフォルトサイズに対応
              />
            </PanelBody>
          </InspectorControls>

          <div className={ classnames( getClasses( style ), className ) }>
            <InnerBlocks />
          </div>
        </Fragment>
      );
    },

    save( { attributes } ) {
      const { style } = attributes;
      return (
        <div className={ getClasses( style ) }>
          <InnerBlocks.Content />
        </div>
      );
    },
  },
];
