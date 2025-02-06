/**
 * Cocoon Blocks
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */

import { THEME_NAME, BLOCK_CLASS } from '../../helpers';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { faAddressCard } from '@fortawesome/free-regular-svg-icons';
import classnames from 'classnames';

import { __ } from '@wordpress/i18n';
import { registerBlockType } from '@wordpress/blocks';
import { RichText, InspectorControls } from '@wordpress/block-editor';
const { PanelBody, SelectControl, BaseControl } = wp.components;
import { Fragment } from '@wordpress/element';

//classの取得
function getClasses( style ) {
  const classes = classnames( {
    'blogcard-type': true,
    [ style ]: !! style,
  } );
  return classes;
}

registerBlockType( 'cocoon-blocks/blogcard', {
  title: __( 'ブログカード', THEME_NAME ),
  icon: <FontAwesomeIcon icon={ faAddressCard } />,
  category: THEME_NAME + '-block',
  description: __(
    'ブログカード表示用の入力ブロックを表示します。URLは複数入力可能です。',
    THEME_NAME
  ),

  attributes: {
    content: {
      type: 'string',
      default: '',
    },
    style: {
      type: 'string',
      default: 'bct-none',
    },
  },

  edit( { attributes, setAttributes } ) {
    const { content, style } = attributes;

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
                  value: 'bct-none',
                  label: __( 'なし', THEME_NAME ),
                },
                {
                  value: 'bct-related',
                  label: __( '関連記事', THEME_NAME ),
                },
                {
                  value: 'bct-reference',
                  label: __( '参考記事', THEME_NAME ),
                },
                {
                  value: 'bct-popular',
                  label: __( '人気記事', THEME_NAME ),
                },
                {
                  value: 'bct-together',
                  label: __( 'あわせて読みたい', THEME_NAME ),
                },
                {
                  value: 'bct-detail',
                  label: __( '詳細はこちら', THEME_NAME ),
                },
                {
                  value: 'bct-check',
                  label: __( 'チェック', THEME_NAME ),
                },
                {
                  value: 'bct-pickup',
                  label: __( 'ピックアップ', THEME_NAME ),
                },
                {
                  value: 'bct-official',
                  label: __( '公式サイト', THEME_NAME ),
                },
              ] }
              __nextHasNoMarginBottom={ true }
            />
          </PanelBody>
        </InspectorControls>

        <div className={ getClasses( style ) }>
          <RichText
            onChange={ ( value ) => setAttributes( { content: value } ) }
            value={ content }
            multiline="p"
          />
        </div>
      </Fragment>
    );
  },

  save( { attributes } ) {
    const { content, style } = attributes;
    return (
      <div className={ getClasses( style ) }>
        <RichText.Content value={ content } multiline={ 'p' } />
      </div>
    );
  },
} );
