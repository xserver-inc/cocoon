import { addFilter } from '@wordpress/hooks';
import { useDebounce, createHigherOrderComponent } from '@wordpress/compose';
import { Fragment, useState } from '@wordpress/element';
import { InspectorControls } from '@wordpress/block-editor';
import { PanelBody, Button } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

import classnames from 'classnames';
import { THEME_NAME } from '../../helpers';

// 拡張対象ブロック
const allowedBlocks = [ 'core/paragraph', 'core/list' ];

// custom attributesの追加
function addCustomAttributes( settings ) {
  if ( allowedBlocks.includes( settings.name ) ) {
    settings.attributes = Object.assign( settings.attributes, {
      extraBorder: {
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
      const { className, extraBorder } = attributes;

      const extraBorders = [
        {
          style: '',
          buttonText: __( 'デフォルト', THEME_NAME ),
        },
        {
          style: 'border-solid',
          buttonText: __( '実線', THEME_NAME ),
        },
        {
          style: 'border-double',
          buttonText: __( '二重線', THEME_NAME ),
        },
        {
          style: 'border-dashed',
          buttonText: __( '破線', THEME_NAME ),
        },
        {
          style: 'border-dotted',
          buttonText: __( '点線', THEME_NAME ),
        },
        {
          style: 'border-thin-and-thick',
          buttonText: __( '薄太', THEME_NAME ),
        },
        {
          style: 'border-convex',
          buttonText: __( '微凸', THEME_NAME ),
        },
        {
          style: 'border-radius-s-solid',
          buttonText: __( '実線(角丸小)', THEME_NAME ),
        },
        {
          style: 'border-radius-s-double',
          buttonText: __( '二重線(角丸小)', THEME_NAME ),
        },
        {
          style: 'border-radius-s-dashed',
          buttonText: __( '破線(角丸小)', THEME_NAME ),
        },
        {
          style: 'border-radius-s-dotted',
          buttonText: __( '点線(角丸小)', THEME_NAME ),
        },
        {
          style: 'border-radius-s-thin-and-thick',
          buttonText: __( '薄太(角丸小)', THEME_NAME ),
        },
        {
          style: 'border-radius-s-convex',
          buttonText: __( '微凸(角丸小)', THEME_NAME ),
        },
        {
          style: 'border-radius-l-solid',
          buttonText: __( '実線(角丸大)', THEME_NAME ),
        },
        {
          style: 'border-radius-l-double',
          buttonText: __( '二重線(角丸大)', THEME_NAME ),
        },
        {
          style: 'border-radius-l-dashed',
          buttonText: __( '破線(角丸大)', THEME_NAME ),
        },
        {
          style: 'border-radius-l-dotted',
          buttonText: __( '点線(角丸大)', THEME_NAME ),
        },
        {
          style: 'border-radius-l-thin-and-thick',
          buttonText: __( '薄太(角丸大)', THEME_NAME ),
        },
        {
          style: 'border-radius-l-convex',
          buttonText: __( '微凸(角丸大)', THEME_NAME ),
        },
        {
          style: 'blank-box-gray',
          buttonText: __( '白抜き灰', THEME_NAME ),
        },
        {
          style: 'blank-box-red',
          buttonText: __( '白抜き赤', THEME_NAME ),
        },
        {
          style: 'blank-box-pink',
          buttonText: __( '白抜きピンク', THEME_NAME ),
        },
        {
          style: 'blank-box-navy',
          buttonText: __( '白抜き紺', THEME_NAME ),
        },
        {
          style: 'blank-box-blue',
          buttonText: __( '白抜き青', THEME_NAME ),
        },
        {
          style: 'blank-box-purple',
          buttonText: __( '白抜き紫', THEME_NAME ),
        },
        {
          style: 'blank-box-orange',
          buttonText: __( '白抜きオレンジ', THEME_NAME ),
        },
        {
          style: 'blank-box-yellow',
          buttonText: __( '白抜き黄', THEME_NAME ),
        },
        {
          style: 'blank-box-green',
          buttonText: __( '白抜き緑', THEME_NAME ),
        },
      ];

      return (
        <Fragment>
          <BlockEdit { ...props } />

          { isSelected && (
            <InspectorControls>
              <PanelBody
                title={ __( '[C] ボーダー', THEME_NAME ) }
                initialOpen={ false }
              >
                <div class="__clearBtn">
                  <button
                    type="button"
                    class="components-button is-small"
                    onClick={ () => setAttributes( { extraBorder: '' } ) }
                  >
                    <span class="dashicons dashicons-editor-removeformatting"></span>
                    { __( 'ボーダーをクリア', THEME_NAME ) }
                  </button>
                </div>
                <div className="block-editor-block-styles">
                  <div className="block-editor-block-styles__variants style-buttons">
                    { extraBorders.map( ( border, index ) => {
                      return (
                        <div
                          className={ classnames( '__btnBox', {
                            [ '__btnBoxDefault display-none' ]: ! border.style,
                          } ) }
                        >
                          <Button
                            id={ 'cocoon-dorder-border01-button-' + index }
                            className={ classnames(
                              'display-none',
                              'block-editor-block-styles__item',
                              {
                                'is-active': border.style === extraBorder,
                              }
                            ) }
                            variant="secondary"
                            label={ border.buttonText }
                            onClick={ () => {
                              if ( border.style === extraBorder ) {
                                setAttributes( { extraBorder: '' } );
                              } else {
                                setAttributes( { extraBorder: border.style } );
                              }
                            } }
                          ></Button>
                          <label
                            for={ 'cocoon-dorder-border01-button-' + index }
                            class="__labelBtn"
                            data-selected={
                              border.style === extraBorder ? true : false
                            }
                          >
                            <span class="__prevWrap editor-styles-wrapper">
                              <span
                                className={ classnames( '__prev', {
                                  [ 'is-style-' + border.style ]:
                                    !! border.style,
                                  [ 'has-border' ]: border.style,
                                } ) }
                              ></span>
                            </span>
                            <span class="__prevTitle">
                              { border.buttonText }
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
                    onClick={ () => setAttributes( { extraBorder: '' } ) }
                  >
                    <span class="dashicons dashicons-editor-removeformatting"></span>
                    { __( 'ボーダーをクリア', THEME_NAME ) }
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
        const { extraBorder } = attributes;

        return (
          <BlockListBlock
            { ...props }
            className={ classnames( {
              [ className ]: !! className,
              [ 'is-style-' + extraBorder ]: !! extraBorder,
              [ 'has-border' ]: extraBorder,
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
    const { extraBorder } = attributes;

    if ( className || extraBorder ) {
      props.className = classnames( {
        [ className ]: !! className,
        [ 'is-style-' + extraBorder ]: !! extraBorder,
        [ 'has-border' ]: extraBorder,
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
