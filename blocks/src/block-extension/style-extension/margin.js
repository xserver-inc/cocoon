import { addFilter } from '@wordpress/hooks';
import { createHigherOrderComponent } from '@wordpress/compose';
import { Fragment } from '@wordpress/element';
import { InspectorControls } from '@wordpress/block-editor';
import { PanelBody, SelectControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

import classnames from 'classnames';
import { THEME_NAME, BLOCK_SERIES } from '../../helpers';

// custom attributesの追加
function addCustomAttributes( settings ) {
  settings.attributes = Object.assign( settings.attributes, {
    extraBottomMargin: {
      type: 'string',
      default: '',
    },
  } );
  return settings;
}
addFilter(
  'blocks.registerBlockType',
  'cocoon-blocks/layout-custom-attributes',
  addCustomAttributes
);

// Edit拡張
const addCustomEdit = createHigherOrderComponent( ( BlockEdit ) => {
  return ( props ) => {
    if ( props.isSelected ) {
      const { setAttributes, isSelected, attributes } = props;
      const { className, extraBottomMargin } = attributes;

      var title = '';
      if ( props.name.includes( BLOCK_SERIES ) ) {
        title = __( 'ブロック下の余白量', THEME_NAME );
      } else {
        title = __( '[C] ブロック下の余白量', THEME_NAME );
      }

      return (
        <Fragment>
          <BlockEdit { ...props } />
          { isSelected && (
            <InspectorControls>
              <PanelBody title={ title } initialOpen={ false }>
                <SelectControl
                  value={ extraBottomMargin }
                  options={ [
                    {
                      label: __( '-- 余白量を選択して下さい --', THEME_NAME ),
                      value: '',
                      disabled: true,
                    },
                    { label: '0em', value: '0em' },
                    { label: '0.5em', value: '0.5em' },
                    { label: '1em', value: '1em' },
                    { label: '1.5em', value: '1.5em' },
                    { label: '2em', value: '2em' },
                    { label: '2.5em', value: '2.5em' },
                    { label: '3em', value: '3em' },
                    { label: '4em', value: '4em' },
                    { label: '5em', value: '5em' },
                    { label: '6em', value: '6em' },
                  ] }
                  onChange={ ( value ) =>
                    setAttributes( { extraBottomMargin: value } )
                  }
                  __nextHasNoMarginBottom
                />
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
  'cocoon-blocks/layout-custom-edit',
  addCustomEdit
);

// 親ブロックへのクラス適用
const applyAttributesToBlock = createHigherOrderComponent(
  ( BlockListBlock ) => {
    return ( props ) => {
      const { attributes, className, isValid } = props;

      if ( isValid ) {
        const { extraBottomMargin } = attributes;

        return (
          <BlockListBlock
            { ...props }
            className={ classnames( className, {
              [ 'is-style-bottom-margin-' + extraBottomMargin ]:
                !! extraBottomMargin,
              [ 'has-bottom-margin' ]: extraBottomMargin,
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
  'cocoon-blocks/layout-apply-attributes',
  applyAttributesToBlock
);

// Save拡張
const addCustomSave = ( props, blockType, attributes ) => {
  const { className } = props;
  const { extraBottomMargin } = attributes;

  props.className = classnames( className, {
    [ 'is-style-bottom-margin-' + extraBottomMargin ]: !! extraBottomMargin,
    [ 'has-bottom-margin' ]: extraBottomMargin,
  } );

  return Object.assign( props );
};
addFilter(
  'blocks.getSaveContent.extraProps',
  'cocoon-blocks/layout-custom-save',
  addCustomSave
);
