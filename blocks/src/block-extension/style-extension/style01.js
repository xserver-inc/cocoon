import { addFilter } from '@wordpress/hooks';
import { useDebounce, createHigherOrderComponent } from '@wordpress/compose';
import { Fragment, useState } from '@wordpress/element';
import { InspectorControls } from '@wordpress/block-editor';
import { PanelBody, Button } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

import classnames from 'classnames';
import { THEME_NAME } from '../../helpers';

// 拡張対象ブロック
const allowedBlocks = [ 'core/paragraph' ];

// custom attributesの追加
function addCustomAttributes( settings ) {
  if ( allowedBlocks.includes( settings.name ) ) {
    settings.attributes = Object.assign( settings.attributes, {
      extraStyle: {
        type: 'string',
        default: '',
      },
    } );
  }
  return settings;
}
addFilter(
  'blocks.registerBlockType',
  'cocoon-blocks/style-custom-attributes',
  addCustomAttributes
);

// Edit拡張
const addCustomEdit = createHigherOrderComponent( ( BlockEdit ) => {
  return ( props ) => {
    if ( allowedBlocks.includes( props.name ) && props.isSelected ) {
      const { setAttributes, isSelected, attributes } = props;
      const { className, extraStyle } = attributes;

      const extraStyles = [
        {
          style: '',
          buttonText: __( 'デフォルト', THEME_NAME ),
        },
        {
          style: 'light-background-box',
          buttonText: __( '薄背景', THEME_NAME ),
        },
        {
          style: 'stripe-box',
          buttonText: __( 'ストライプ', THEME_NAME ),
        },
        {
          style: 'section-paper-box',
          buttonText: __( '方眼紙', THEME_NAME ),
        },
        {
          style: 'checkered-box',
          buttonText: __( 'チェック', THEME_NAME ),
        },
        {
          style: 'stitch-box',
          buttonText: __( 'ステッチ', THEME_NAME ),
        },
        {
          style: 'square-brackets-box',
          buttonText: __( 'かぎ括弧', THEME_NAME ),
        },
        {
          style: 'parenthetic-box',
          buttonText: __( '角括弧', THEME_NAME ),
        },
        {
          style: 'cross-line',
          buttonText: __( '交差線', THEME_NAME ),
        },
        {
          style: 'p-style-08',
          buttonText: __( 'ずれた二重線', THEME_NAME ),
        },
        {
          style: 'triangle-box',
          buttonText: __( '角三角', THEME_NAME ),
        },
        {
          style: 'clip-box',
          buttonText: __( 'クリップ', THEME_NAME ),
        },
        {
          style: 'stapler-box',
          buttonText: __( 'ホチキス', THEME_NAME ),
        },
        {
          style: 'stapler-top-left-box',
          buttonText: __( 'ホチキス左上', THEME_NAME ),
        },
        {
          style: 'hole-punch-box',
          buttonText: __( '穴あけパンチ', THEME_NAME ),
        },
        {
          style: 'handwritten-box',
          buttonText: __( '手書き風', THEME_NAME ),
        },
        {
          style: 'border-top-box',
          buttonText: __( '上線', THEME_NAME ),
        },
        {
          style: 'border-left-box',
          buttonText: __( '付箋', THEME_NAME ),
        },
        {
          style: 'balloon-left-box',
          buttonText: __( '左吹き出し', THEME_NAME ),
        },
        {
          style: 'balloon-right-box',
          buttonText: __( '右吹き出し', THEME_NAME ),
        },
        {
          style: 'balloon-top-box',
          buttonText: __( '上吹き出し', THEME_NAME ),
        },
        {
          style: 'balloon-bottom-box',
          buttonText: __( '下吹き出し', THEME_NAME ),
        },
        {
          style: 'information-box',
          buttonText: __( '情報', THEME_NAME ),
        },
        {
          style: 'question-box',
          buttonText: __( '質問', THEME_NAME ),
        },
        {
          style: 'alert-box',
          buttonText: __( 'アラート', THEME_NAME ),
        },
        {
          style: 'memo-box',
          buttonText: __( 'メモ', THEME_NAME ),
        },
        {
          style: 'comment-box',
          buttonText: __( 'コメント', THEME_NAME ),
        },
        {
          style: 'ok-box',
          buttonText: __( 'OK', THEME_NAME ),
        },
        {
          style: 'ng-box',
          buttonText: __( 'NG', THEME_NAME ),
        },
        {
          style: 'good-box',
          buttonText: __( 'GOOD', THEME_NAME ),
        },
        {
          style: 'bad-box',
          buttonText: __( 'BAD', THEME_NAME ),
        },
        {
          style: 'profile-box',
          buttonText: __( 'プロフィール', THEME_NAME ),
        },
        {
          style: 'primary-box',
          buttonText: __( 'プライマリー', THEME_NAME ),
        },
        {
          style: 'success-box',
          buttonText: __( 'サクセス', THEME_NAME ),
        },
        {
          style: 'info-box',
          buttonText: __( 'インフォ', THEME_NAME ),
        },
        {
          style: 'warning-box',
          buttonText: __( 'ワーニング', THEME_NAME ),
        },
        {
          style: 'danger-box',
          buttonText: __( 'デンジャー', THEME_NAME ),
        },
        {
          style: 'secondary-box',
          buttonText: __( 'セカンダリー', THEME_NAME ),
        },
        {
          style: 'light-box',
          buttonText: __( 'ライト', THEME_NAME ),
        },
        {
          style: 'dark-box',
          buttonText: __( 'ダーク', THEME_NAME ),
        },
        {
          style: 'sticky-gray',
          buttonText: __( '付箋灰', THEME_NAME ),
        },
        {
          style: 'sticky-red',
          buttonText: __( '付箋赤', THEME_NAME ),
        },
        {
          style: 'sticky-blue',
          buttonText: __( '付箋青', THEME_NAME ),
        },
        {
          style: 'sticky-yellow',
          buttonText: __( '付箋黄', THEME_NAME ),
        },
        {
          style: 'sticky-green',
          buttonText: __( '付箋緑', THEME_NAME ),
        },
      ];

      return (
        <Fragment>
          <BlockEdit { ...props } />

          { isSelected && (
            <InspectorControls>
              <PanelBody
                title={ __( '[C] スタイル', THEME_NAME ) }
                initialOpen={ false }
              >
                <div class="__clearBtn">
                  <button
                    type="button"
                    class="components-button is-small"
                    onClick={ () => setAttributes( { extraStyle: '' } ) }
                  >
                    <span class="dashicons dashicons-editor-removeformatting"></span>
                    { __( 'スタイルをクリア', THEME_NAME ) }
                  </button>
                </div>
                <div className="block-editor-block-styles">
                  <div className="block-editor-block-styles__variants style-buttons">
                    { extraStyles.map( ( style, index ) => {
                      return (
                        <div
                          className={ classnames( '__btnBox', {
                            [ '__btnBoxDefault display-none' ]: ! style.style,
                          } ) }
                        >
                          <Button
                            id={ 'cocoon-dorder-style01-button-' + index }
                            className={ classnames(
                              'display-none',
                              'block-editor-block-styles__item',
                              {
                                'is-active': style.style === extraStyle,
                              }
                            ) }
                            variant="secondary"
                            label={ style.buttonText }
                            onClick={ () => {
                              if ( style.style === extraStyle ) {
                                setAttributes( { extraStyle: '' } );
                              } else {
                                setAttributes( { extraStyle: style.style } );
                              }
                            } }
                          ></Button>
                          <label
                            for={ 'cocoon-dorder-style01-button-' + index }
                            class="__labelBtn"
                            data-selected={
                              style.style === extraStyle ? true : false
                            }
                          >
                            <span class="__prevWrap editor-styles-wrapper">
                              <span
                                className={ classnames( '__prev', {
                                  [ 'is-style-' + style.style ]: !! style.style,
                                  [ 'has-box-style' ]: style.style,
                                } ) }
                              ></span>
                            </span>
                            <span class="__prevTitle">
                              { style.buttonText }
                            </span>
                          </label>
                        </div>
                      );
                    } ) }
                  </div>
                </div>
                <div class="__clearBtn">
                  <button
                    type="button"
                    class="components-button is-small"
                    onClick={ () => setAttributes( { extraStyle: '' } ) }
                  >
                    <span class="dashicons dashicons-editor-removeformatting"></span>
                    { __( 'スタイルをクリア', THEME_NAME ) }
                  </button>
                </div>
              </PanelBody>
            </InspectorControls>
          ) }
        </Fragment>
      );
    }

    return <BlockEdit { ...props } />;
  };
}, 'addCustomEdit' );
addFilter(
  'editor.BlockEdit',
  'cocoon-blocks/style-custom-edit',
  addCustomEdit
);

// 親ブロックへのクラス適用
const applyAttributesToBlock = createHigherOrderComponent(
  ( BlockListBlock ) => {
    return ( props ) => {
      const { attributes, className, name, isValid } = props;

      if ( isValid && allowedBlocks.includes( name ) ) {
        const { extraStyle } = attributes;

        return (
          <BlockListBlock
            { ...props }
            className={ classnames( {
              [ className ]: !! className,
              [ 'is-style-' + extraStyle ]: !! extraStyle,
              [ 'has-box-style' ]: extraStyle,
            } ) }
          />
        );
      }

      return <BlockListBlock { ...props } />;
    };
  },
  'applyAttributesToBlock'
);
addFilter(
  'editor.BlockListBlock',
  'cocoon-blocks/style-apply-attributes',
  applyAttributesToBlock
);

// Save拡張
const addCustomSave = ( props, blockType, attributes ) => {
  if ( allowedBlocks.includes( blockType.name ) ) {
    const { className } = props;
    const { extraStyle } = attributes;

    if ( className || extraStyle ) {
      props.className = classnames( {
        [ className ]: !! className,
        [ 'is-style-' + extraStyle ]: !! extraStyle,
        [ 'has-box-style' ]: extraStyle,
      } );
    }

    return Object.assign( props );
  }
  return props;
};
addFilter(
  'blocks.getSaveContent.extraProps',
  'cocoon-blocks/style-custom-save',
  addCustomSave
);
