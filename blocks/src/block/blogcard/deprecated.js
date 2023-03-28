/**
 * Cocoon Blocks
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */

import { THEME_NAME } from '../../helpers';
import classnames from 'classnames';

import { __ } from '@wordpress/i18n';
const { InspectorControls } = wp.editor;
const { PanelBody, SelectControl } = wp.components;
import { Fragment } from '@wordpress/element';

export default [
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

    edit( { attributes, setAttributes, className } ) {
      const { content, style } = attributes;

      function onChange( event ) {
        setAttributes( { style: event.target.value } );
      }

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
      let { content } = attributes;
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
