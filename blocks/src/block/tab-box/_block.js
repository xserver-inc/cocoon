/**
 * Cocoon Blocks
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */

import {THEME_NAME, BLOCK_CLASS, colorValueToSlug} from '../../helpers';
import classnames from 'classnames';

const { __ } = wp.i18n;
const { registerBlockType, createBlock } = wp.blocks;
const { InnerBlocks, RichText, InspectorControls, PanelColorSettings, ContrastChecker } = wp.editor;
const { PanelBody, SelectControl, BaseControl } = wp.components;
const { Fragment } = wp.element;
const DEFAULT_MSG = __( 'こちらをクリックして設定変更。この入力は公開ページで反映されません。', THEME_NAME );

//classの取得
function getClasses(style, color) {
  const classes = classnames(
    {
      'blank-box': true,
      'bb-tab': true,
      [ style ]: !! style,
      [ `bb-${ colorValueToSlug(color) }` ]: !! colorValueToSlug(color),
      [ 'block-box' ]: true,
    }
  );
  return classes;
}

registerBlockType( 'cocoon-blocks/tab-box-1', {

  title: __( 'タブボックス', THEME_NAME ),
  icon: 'category',
  category: THEME_NAME + '-block',
  description: __( 'タブにメッセージ内容を伝えるための文字が書かれているボックスです。', THEME_NAME ),
  keywords: [ 'tab', 'box' ],

  attributes: {
    content: {
      type: 'string',
      default: DEFAULT_MSG,
    },
    style: {
      type: 'string',
      default: 'bb-check',
    },
    color: {
      type: 'string',
      default: '',
    },
  },
  // transforms: {
  //   to: [
  //     {
  //       type: 'block',
  //       blocks: [ 'cocoon-blocks/sticky-box' ],
  //       transform: ( attributes ) => {
  //         return createBlock( 'cocoon-blocks/sticky-box', attributes );
  //       },
  //     },
  //     {
  //       type: 'block',
  //       blocks: [ 'cocoon-blocks/blank-box-1' ],
  //       transform: ( attributes ) => {
  //         return createBlock( 'cocoon-blocks/blank-box-1', attributes );
  //       },
  //     },
  //     // {
  //     //   type: 'block',
  //     //   blocks: [ 'cocoon-blocks/icon-box' ],
  //     //   transform: ( attributes ) => {
  //     //     return createBlock( 'cocoon-blocks/icon-box', attributes );
  //     //   },
  //     // },
  //     // {
  //     //   type: 'block',
  //     //   blocks: [ 'cocoon-blocks/info-box' ],
  //     //   transform: ( attributes ) => {
  //     //     return createBlock( 'cocoon-blocks/info-box', attributes );
  //     //   },
  //     // },
  //   ],
  // },

  edit( { attributes, setAttributes } ) {
    const { content, style, color } = attributes;

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
                  value: 'bb-check',
                  label: __( 'チェック', THEME_NAME ),
                },
                {
                  value: 'bb-comment',
                  label: __( 'コメント', THEME_NAME ),
                },
                {
                  value: 'bb-point',
                  label: __( 'ポイント', THEME_NAME ),
                },
                {
                  value: 'bb-tips',
                  label: __( 'ティップス', THEME_NAME ),
                },
                {
                  value: 'bb-hint',
                  label: __( 'ヒント', THEME_NAME ),
                },
                {
                  value: 'bb-pickup',
                  label: __( 'ピックアップ', THEME_NAME ),
                },
                {
                  value: 'bb-bookmark',
                  label: __( 'ブックマーク', THEME_NAME ),
                },
                {
                  value: 'bb-memo',
                  label: __( 'メモ', THEME_NAME ),
                },
                {
                  value: 'bb-download',
                  label: __( 'ダウンロード', THEME_NAME ),
                },
                {
                  value: 'bb-break',
                  label: __( 'ブレイク', THEME_NAME ),
                },
                {
                  value: 'bb-amazon',
                  label: __( 'Amazon', THEME_NAME ),
                },
                {
                  value: 'bb-ok',
                  label: __( 'OK', THEME_NAME ),
                },
                {
                  value: 'bb-ng',
                  label: __( 'NG', THEME_NAME ),
                },
                {
                  value: 'bb-good',
                  label: __( 'GOOD', THEME_NAME ),
                },
                {
                  value: 'bb-bad',
                  label: __( 'BAD', THEME_NAME ),
                },
                {
                  value: 'bb-profile',
                  label: __( 'プロフィール', THEME_NAME ),
                },
              ] }
            />
          </PanelBody>

          <PanelColorSettings
            title={ __( '色設定', THEME_NAME ) }
            initialOpen={ true }
            colorSettings={ [
              {
                value: color,
                onChange: ( value ) => setAttributes( { color: value } ),
                label: __( '色設定', THEME_NAME ),
              },
            ] }
          >
          <ContrastChecker
            color={ color }
          />
          </PanelColorSettings>

        </InspectorControls>

        <div className={ getClasses(style, color) }>
          <span className={'box-block-msg'}>
            <RichText
              value={ content }
              placeholder={ DEFAULT_MSG }
            />
          </span>
          <InnerBlocks />
        </div>
      </Fragment>
    );
  },

  save( { attributes } ) {
    const { content, style, color } = attributes;
    return (
      <div className={ getClasses(style, color) }>
        <InnerBlocks.Content />
      </div>
    );
  }
} );