/**
 * Cocoon Blocks
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */

import {THEME_NAME, BLOCK_CLASS} from '../../helpers.js';

const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;
const { InnerBlocks, RichText, InspectorControls } = wp.editor;
const { PanelBody, SelectControl, BaseControl } = wp.components;
const { Fragment } = wp.element;
const DEFAULT_MSG = __( 'こちらをクリックして設定変更。この入力は公開ページで反映されません。', THEME_NAME );

registerBlockType( 'cocoon-blocks/info-box', {

  title: __( '案内ボックス', THEME_NAME ),
  icon: 'info',
  category: THEME_NAME + '-block',
  description: __( 'ボックスの背景色により、直感的にメッセージ内容を伝えるためのボックスです。', THEME_NAME ),

  attributes: {
    content: {
      type: 'string',
      source: 'html',
      selector: 'div',
      default: DEFAULT_MSG,
    },
    style: {
      type: 'string',
      default: 'primary-box',
    },
  },

  edit( { attributes, setAttributes } ) {
    const { content, style, alignment } = attributes;

    function onChange(event){
      setAttributes({style: event.target.value});
    }

    function onChangeContent(newContent){
      setAttributes( { content: newContent } );
    }

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
            />

          </PanelBody>
        </InspectorControls>

        <div className={attributes.style + BLOCK_CLASS}>
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
    const { content } = attributes;
    return (
      <div className={attributes.style + BLOCK_CLASS}>
        <InnerBlocks.Content />
      </div>
    );
  }
} );