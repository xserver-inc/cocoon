/**
 * Cocoon Blocks
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */

import {THEME_NAME, BLOCK_CLASS} from '../../helpers';
import classnames from 'classnames';

const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;
const { RichText, InspectorControls } = wp.editor;
const { PanelBody, SelectControl, BaseControl, TextControl } = wp.components;
const { Fragment } = wp.element;
const BUTTON_BLOCK = ' button-block';

registerBlockType( 'cocoon-blocks/button', {

  title: __( 'ボタン', THEME_NAME ),
  icon: 'dismiss',
  category: THEME_NAME + '-old',
  description: __( '一般的なリンクボタンを作成します。', THEME_NAME ),

  attributes: {
    content: {
      type: 'string',
      default: __( 'ボタン', THEME_NAME ),
    },
    url: {
      type: 'string',
      default: '',
    },
    target: {
      type: 'string',
      default: '_self',
    },
    color: {
      type: 'string',
      default: 'btn btn-red',
    },
    size: {
      type: 'string',
      default: '',
    },
  },
  supports: {
    align: [ 'left', 'center', 'right' ],
    customClassName: true,
    inserter: false,
  },

  edit( { attributes, setAttributes } ) {
    const { content, color, size, url, target } = attributes;

    // function onChange(event){
    //   setAttributes({color: event.target.value});
    // }

    // function onChangeContent(newContent){
    //   setAttributes( { content: newContent } );
    // }

    return (
      <Fragment>
        <InspectorControls>
          <PanelBody title={ __( 'ボタン設定', THEME_NAME ) }>

            <TextControl
              label={ __( 'URL', THEME_NAME ) }
              value={ url }
              onChange={ ( value ) => setAttributes( { url: value } ) }
            />

            <SelectControl
              label={ __( 'リンクの開き方', THEME_NAME ) }
              value={ target }
              onChange={ ( value ) => setAttributes( { target: value } ) }
              options={ [
                {
                  value: '_self',
                  label: __( '現在のタブで開く', THEME_NAME ),
                },
                {
                  value: '_blank',
                  label: __( '新しいタブで開く', THEME_NAME ),
                },
              ] }
            />

            <SelectControl
              label={ __( '色設定', THEME_NAME ) }
              value={ color }
              onChange={ ( value ) => setAttributes( { color: value } ) }
              options={ [
                {
                  value: 'btn btn-red',
                  label: __( 'レッド', THEME_NAME ),
                },
                {
                  value: 'btn btn-pink',
                  label: __( 'ピンク', THEME_NAME ),
                },
                {
                  value: 'btn btn-purple',
                  label: __( 'パープル', THEME_NAME ),
                },
                {
                  value: 'btn btn-deep',
                  label: __( 'ディープパープル', THEME_NAME ),
                },
                {
                  value: 'btn btn-indigo',
                  label: __( 'インディゴ[紺色]', THEME_NAME ),
                },
                {
                  value: 'btn btn-blue',
                  label: __( 'ブルー', THEME_NAME ),
                },
                {
                  value: 'btn btn-blue',
                  label: __( 'ライトブルー', THEME_NAME ),
                },
                {
                  value: 'btn btn-cyan',
                  label: __( 'シアン', THEME_NAME ),
                },
                {
                  value: 'btn btn-teal',
                  label: __( 'ティール[緑色がかった青]', THEME_NAME ),
                },
                {
                  value: 'btn btn-green',
                  label: __( 'グリーン', THEME_NAME ),
                },
                {
                  value: 'btn btn-light-green',
                  label: __( 'ライトグリーン', THEME_NAME ),
                },
                {
                  value: 'btn btn-lime',
                  label: __( 'ライム', THEME_NAME ),
                },
                {
                  value: 'btn btn-yellow',
                  label: __( 'イエロー', THEME_NAME ),
                },
                {
                  value: 'btn btn-amber',
                  label: __( 'アンバー[琥珀色]', THEME_NAME ),
                },
                {
                  value: 'btn btn-orange',
                  label: __( 'オレンジ', THEME_NAME ),
                },
                {
                  value: 'btn btn-deep-orange',
                  label: __( 'ディープオレンジ', THEME_NAME ),
                },
                {
                  value: 'btn btn-brown',
                  label: __( 'ブラウン', THEME_NAME ),
                },
                {
                  value: 'btn btn-grey',
                  label: __( 'グレー', THEME_NAME ),
                },
              ] }
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
                  value: ' btn-m',
                  label: __( '中', THEME_NAME ),
                },
                {
                  value: ' btn-l',
                  label: __( '大', THEME_NAME ),
                },
              ] }
            />

          </PanelBody>
        </InspectorControls>

        <div className={BUTTON_BLOCK}>
          <span
            className={color + size}
            href={ url }
            target={ target }
          >
            <RichText
              value={ content }
              onChange={ ( value ) => setAttributes( { content: value } ) }
            />
          </span>
        </div>

      </Fragment>
    );
  },

  save( { attributes } ) {
    const { content, color, size, url, target } = attributes;
    return (
      <div className={BUTTON_BLOCK}>
        <a
          href={ url }
          className={color + size}
          target={ target }
        >
          <RichText.Content
            value={ content }
          />
        </a>
      </div>
    );
  }
} );