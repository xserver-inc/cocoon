/**
 * Cocoon Blocks
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */

import { THEME_NAME, BLOCK_CLASS } from '../../helpers';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import classnames from 'classnames';

import { __ } from '@wordpress/i18n';
import { registerBlockType } from '@wordpress/blocks';
import { RichText, InspectorControls } from '@wordpress/block-editor';
const { PanelBody, SelectControl, BaseControl, TextareaControl } =
  wp.components;
import { Fragment } from '@wordpress/element';
const BUTTON_BLOCK = ' button-block';

registerBlockType( 'cocoon-blocks/button-wrap', {
  title: __( '囲みボタン', THEME_NAME ),
  icon: 'dismiss',
  category: THEME_NAME + '-old',
  description: __(
    'アスリートタグ等のタグを変更できないリンクをボタン化します。',
    THEME_NAME
  ),

  attributes: {
    content: {
      type: 'string',
      default: __(
        'こちらをクリックしてリンクタグを設定エリア入力してください。この入力は公開ページで反映されません。',
        THEME_NAME
      ),
    },
    tag: {
      type: 'string',
      default: '',
    },
    color: {
      type: 'string',
      default: 'btn-wrap btn-wrap-red',
    },
    size: {
      type: 'string',
      default: '',
    },
  },
  supports: {
    align: [ 'left', 'center', 'right' ],
    inserter: false,
  },

  edit( { attributes, setAttributes } ) {
    const { content, color, size, tag } = attributes;

    // function unescapeHTML(html) {
    //   var escapeEl = document.createElement(‘textarea’);
    //   escapeEl.innerHTML = html;
    //   return escapeEl.textContent;
    // }

    return (
      <Fragment>
        <InspectorControls>
          <PanelBody title={ __( '囲みボタン設定', THEME_NAME ) }>
            <TextareaControl
              label={ __( 'リンクタグ・ショートコード', THEME_NAME ) }
              value={ tag }
              onChange={ ( value ) => setAttributes( { tag: value } ) }
            />

            <SelectControl
              label={ __( '色設定', THEME_NAME ) }
              value={ color }
              onChange={ ( value ) => setAttributes( { color: value } ) }
              options={ [
                {
                  value: 'btn-wrap btn-wrap-red',
                  label: __( 'レッド', THEME_NAME ),
                },
                {
                  value: 'btn-wrap btn-wrap-pink',
                  label: __( 'ピンク', THEME_NAME ),
                },
                {
                  value: 'btn-wrap btn-wrap-purple',
                  label: __( 'パープル', THEME_NAME ),
                },
                {
                  value: 'btn-wrap btn-wrap-deep',
                  label: __( 'ディープパープル', THEME_NAME ),
                },
                {
                  value: 'btn-wrap btn-wrap-indigo',
                  label: __( 'インディゴ[紺色]', THEME_NAME ),
                },
                {
                  value: 'btn-wrap btn-wrap-blue',
                  label: __( 'ブルー', THEME_NAME ),
                },
                {
                  value: 'btn-wrap btn-wrap-blue',
                  label: __( 'ライトブルー', THEME_NAME ),
                },
                {
                  value: 'btn-wrap btn-wrap-cyan',
                  label: __( 'シアン', THEME_NAME ),
                },
                {
                  value: 'btn-wrap btn-wrap-teal',
                  label: __( 'ティール[緑色がかった青]', THEME_NAME ),
                },
                {
                  value: 'btn-wrap btn-wrap-green',
                  label: __( 'グリーン', THEME_NAME ),
                },
                {
                  value: 'btn-wrap btn-wrap-light-green',
                  label: __( 'ライトグリーン', THEME_NAME ),
                },
                {
                  value: 'btn-wrap btn-wrap-lime',
                  label: __( 'ライム', THEME_NAME ),
                },
                {
                  value: 'btn-wrap btn-wrap-yellow',
                  label: __( 'イエロー', THEME_NAME ),
                },
                {
                  value: 'btn-wrap btn-wrap-amber',
                  label: __( 'アンバー[琥珀色]', THEME_NAME ),
                },
                {
                  value: 'btn-wrap btn-wrap-orange',
                  label: __( 'オレンジ', THEME_NAME ),
                },
                {
                  value: 'btn-wrap btn-wrap-deep-orange',
                  label: __( 'ディープオレンジ', THEME_NAME ),
                },
                {
                  value: 'btn-wrap btn-wrap-brown',
                  label: __( 'ブラウン', THEME_NAME ),
                },
                {
                  value: 'btn-wrap btn-wrap-grey',
                  label: __( 'グレー', THEME_NAME ),
                },
              ] }
              __nextHasNoMarginBottom={ true }
            />

            <SelectControl
              label={ __( 'サイズ', THEME_NAME ) }
              value={ size }
              onChange={ ( value ) => setAttributes( { size: value } ) }
              options={ [
                {
                  value: '',
                  label: __( '小', THEME_NAME ),
                },
                {
                  value: ' btn-wrap-m',
                  label: __( '中', THEME_NAME ),
                },
                {
                  value: ' btn-wrap-l',
                  label: __( '大', THEME_NAME ),
                },
              ] }
              __nextHasNoMarginBottom={ true }
            />
          </PanelBody>
        </InspectorControls>
        <span className={ 'button-wrap-msg' }>
          <RichText value={ content } />
        </span>
        <div
          className={ color + size + BUTTON_BLOCK }
          dangerouslySetInnerHTML={ { __html: tag } }
        ></div>
      </Fragment>
    );
  },

  save( { attributes } ) {
    const { content, color, size, tag } = attributes;
    return (
      <div className={ color + size + BUTTON_BLOCK }>
        <RichText.Content value={ tag } />
      </div>
    );
  },
} );
