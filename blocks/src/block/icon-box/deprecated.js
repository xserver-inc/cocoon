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

const DEFAULT_BOX_STYLE = 'information-box';

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
  const classes = classnames( 'common-icon-box', 'block-box', {
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
    'common-icon-box': true,
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
        default: 'information-box',
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
          blocks: [ 'cocoon-blocks/tab-box-1' ],
          transform: ( attributes, innerBlocks ) => {
            return createBlock( 'cocoon-blocks/tab-box-1', {}, innerBlocks );
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
                    value: 'information-box',
                    label: __( '補足情報(i)', THEME_NAME ),
                  },
                  {
                    value: 'question-box',
                    label: __( '補足情報(?)', THEME_NAME ),
                  },
                  {
                    value: 'alert-box',
                    label: __( '補足情報(!)', THEME_NAME ),
                  },
                  {
                    value: 'memo-box',
                    label: __( 'メモ', THEME_NAME ),
                  },
                  {
                    value: 'comment-box',
                    label: __( 'コメント', THEME_NAME ),
                  },
                  {
                    value: 'ok-box',
                    label: __( 'OK', THEME_NAME ),
                  },
                  {
                    value: 'ng-box',
                    label: __( 'NG', THEME_NAME ),
                  },
                  {
                    value: 'good-box',
                    label: __( 'GOOD', THEME_NAME ),
                  },
                  {
                    value: 'bad-box',
                    label: __( 'BAD', THEME_NAME ),
                  },
                  {
                    value: 'profile-box',
                    label: __( 'プロフィール', THEME_NAME ),
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
