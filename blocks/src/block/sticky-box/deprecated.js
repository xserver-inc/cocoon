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

const DEFAULT_BOX_STYLE = '';
const LEGACY_API_VERSION = 3;
const LEGACY_SUPPORTS = { anchor: true, html: false };

// 現行直前のHTMLを再現し、正常な旧記事とWordPress 7.0で壊れた記事の両方を検出します。
function saveLegacyStyle( { attributes } ) {
  const { style } = attributes;
  const classes = classnames( 'blank-box', 'block-box', 'sticky', {
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

// styleが消えた後も保存HTMLだけに残る破損クラスとCSS識別クラスを固定再現します。
function saveOrphanedCustomCSS( { attributes } ) {
  const { className } = attributes;
  const classes = classnames(
    'blank-box',
    'block-box',
    'sticky',
    '[object Object]',
    className,
    'has-custom-css'
  );
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

// edit_css権限の変更で空になったstyleがコメントから消えた破損HTMLを再現します。
const orphanedStyleAttributes = {
  style: { type: 'object', default: {} },
  className: { type: 'string' },
};

// 通常の旧記事をクラス補正で誤捕捉しないよう、この専用定義ではクラスを手動再現します。
const orphanedStyleSupports = {
  ...LEGACY_SUPPORTS,
  customClassName: false,
  customCSS: false,
};

// コアのクラス補正でclassNameへ移された破損トークンだけを除去し、他のクラスは保持します。
const migrateBrokenStickyStyle = ( attributes ) => {
  const migratedAttributes = migrateStyle( attributes );

  const originalClassName =
    typeof migratedAttributes.className === 'string'
      ? migratedAttributes.className
      : '';
  const cleanedClassName = originalClassName
    .replace( /(?:^|\s)\[object Object\](?=\s|$)/g, ' ' )
    .replace( /\s+/g, ' ' )
    .trim();

  const classNames = cleanedClassName ? cleanedClassName.split( /\s+/ ) : [];
  if ( classNames.length > 0 ) {
    migratedAttributes.className = classNames.join( ' ' );
  } else {
    delete migratedAttributes.className;
  }

  return migratedAttributes;
};

const migrateOrphanedStyle = ( attributes ) => {
  const migratedAttributes = migrateBrokenStickyStyle( attributes );

  // CSSが消えた孤立形式だけは、元HTMLに残るコア識別クラスを維持します。
  if ( ! migratedAttributes.style?.css ) {
    const classNames = migratedAttributes.className
      ? migratedAttributes.className.split( /\s+/ )
      : [];
    if ( ! classNames.includes( 'has-custom-css' ) ) {
      classNames.push( 'has-custom-css' );
    }
    migratedAttributes.className = classNames.join( ' ' );
  }

  return migratedAttributes;
};

const orphanedCustomCSSStyle = {
  apiVersion: LEGACY_API_VERSION,
  supports: orphanedStyleSupports,
  attributes: orphanedStyleAttributes,
  isEligible: isLegacyStyle,
  migrate: migrateOrphanedStyle,
  save: saveOrphanedCustomCSS,
};

const legacyStyle = {
  apiVersion: LEGACY_API_VERSION,
  supports: LEGACY_SUPPORTS,
  attributes: {
    style: {
      type: [ 'string', 'object' ],
      default: DEFAULT_BOX_STYLE,
    },
  },
  isEligible: isLegacyStyle,
  migrate: migrateBrokenStickyStyle,
  save: saveLegacyStyle,
};

//classの取得
function getClasses( style ) {
  const classes = classnames( {
    'blank-box': true,
    sticky: true,
    [ style ]: !! style,
    'block-box': true,
  } );
  return classes;
}

export default [
  orphanedCustomCSSStyle,
  legacyStyle,
  {
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
