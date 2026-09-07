/**
 * Cocoon Blocks
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */

import { THEME_NAME } from '../../helpers';
import {
  isLegacyStyle,
  migrateLegacyStyleAttribute,
} from '../../style-attribute-compat';
import classnames from 'classnames';

import { __ } from '@wordpress/i18n';
// RichText が未 import だと、後方互換（deprecated）版の save 評価時に
// 「RichText is not defined」エラーになるため、明示的に読み込む。
import {
  RichText,
  InspectorControls,
  useBlockProps,
} from '@wordpress/block-editor';
const { PanelBody, SelectControl } = wp.components;
import { Fragment } from '@wordpress/element';

const DEFAULT_CARD_STYLE = 'blogcard-type bct-none';

// WordPress 7.0で破損が起きる直前の属性定義を、現行block.jsonから独立して保持します。
const LEGACY_STYLE_ATTRIBUTES = {
  content: {
    type: 'string',
    source: 'html',
    selector: 'div',
    default: '',
  },
  style: {
    type: [ 'string', 'object' ],
    default: DEFAULT_CARD_STYLE,
  },
};

// deprecatedの検証条件が将来のsupports変更に追随しないよう、当時の値を固定します。
const LEGACY_SUPPORTS = {
  anchor: true,
  html: false,
};

// 現行直前のHTMLを再現し、正常な旧記事とWordPress 7.0で壊れた記事の両方を検出します。
function saveLegacyStyle( { attributes } ) {
  const { content, style } = attributes;
  const blockProps = useBlockProps.save( {
    className: style,
  } );

  return (
    <div { ...blockProps }>
      <RichText.Content
        value={ content
          .replace( /<\/p><p>/g, '</p>\n<p>' )
          .replace( /^<p>/g, '\n<p>' )
          .replace( /<\/p>$/g, '</p>\n' )
          .replace( /\s+<p>/g, '\n<p>' )
          .replace( /<\p>\s+/g, '<p>\n' )
          .replace( /<br>/g, '\n<br>\n' )
          .replace( /^/g, '\n' )
          .replace( /$/g, '\n' )
          .replace( /\n /g, '\n' )
          .replace( /\n\n/g, '\n' ) }
      />
    </div>
  );
}

// どの旧形式からでも、deprecatedを経由せず現行属性へ直接移行します。
const migrateStyle = ( attributes ) =>
  migrateLegacyStyleAttribute( attributes, 'cardStyle', DEFAULT_CARD_STYLE );

const legacyStyle = {
  apiVersion: 3,
  supports: LEGACY_SUPPORTS,
  attributes: LEGACY_STYLE_ATTRIBUTES,
  isEligible: isLegacyStyle,
  migrate: migrateStyle,
  save: saveLegacyStyle,
};

export default [
  legacyStyle,
  {
    attributes: {
      content: {
        type: 'string',
        source: 'html',
        selector: 'div',
        default: '',
      },
      style: {
        type: 'string',
        default: 'blogcard-type bct-none',
      },
    },
    migrate: migrateStyle,

    edit( { attributes, setAttributes, className } ) {
      const { content, style } = attributes;

      function onChangeContent( newContent ) {
        setAttributes( { content: newContent } );
      }

      return (
        <Fragment>
          <InspectorControls>
            <PanelBody title={ __( 'スタイル設定', THEME_NAME ) }>
              <SelectControl
                label={ __( 'ラベル', THEME_NAME ) }
                value={ style }
                onChange={ ( value ) => setAttributes( { style: value } ) }
                options={ [
                  {
                    value: 'blogcard-type bct-none',
                    label: __( 'なし', THEME_NAME ),
                  },
                  {
                    value: 'blogcard-type bct-related',
                    label: __( '関連記事', THEME_NAME ),
                  },
                  {
                    value: 'blogcard-type bct-reference',
                    label: __( '参考記事', THEME_NAME ),
                  },
                  {
                    value: 'blogcard-type bct-reference-link',
                    label: __( '参考リンク', THEME_NAME ),
                  },
                  {
                    value: 'blogcard-type bct-popular',
                    label: __( '人気記事', THEME_NAME ),
                  },
                  {
                    value: 'blogcard-type bct-together',
                    label: __( 'あわせて読みたい', THEME_NAME ),
                  },
                  {
                    value: 'blogcard-type bct-detail',
                    label: __( '詳細はこちら', THEME_NAME ),
                  },
                  {
                    value: 'blogcard-type bct-check',
                    label: __( 'チェック', THEME_NAME ),
                  },
                  {
                    value: 'blogcard-type bct-pickup',
                    label: __( 'ピックアップ', THEME_NAME ),
                  },
                  {
                    value: 'blogcard-type bct-official',
                    label: __( '公式サイト', THEME_NAME ),
                  },
                  {
                    value: 'blogcard-type bct-dl',
                    label: __( 'ダウンロード', THEME_NAME ),
                  },
                ] }
                __nextHasNoMarginBottom={ true }
                __next40pxDefaultSize={ true } // 新しいデフォルトサイズに対応
              />
            </PanelBody>
          </InspectorControls>

          <div className={ classnames( style, className ) }>
            <RichText
              onChange={ onChangeContent }
              value={ content }
              multiline="p"
            />
          </div>
        </Fragment>
      );
    },

    save( { attributes } ) {
      const { content } = attributes;
      // content = '\n' + content + '\n';
      //console.log(content);
      return (
        <div className={ attributes.style }>
          <RichText.Content
            value={ content
              .replace( /<\/p><p>/g, '</p>\n<p>' )
              .replace( /^<p>/g, '\n<p>' )
              .replace( /<\/p>$/g, '</p>\n' ) }
            multiline={ 'p' }
          />
        </div>
      );
    },
  },
];
