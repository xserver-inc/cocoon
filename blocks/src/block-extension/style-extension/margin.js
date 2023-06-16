import { addFilter } from '@wordpress/hooks';
import { createHigherOrderComponent } from '@wordpress/compose';
import { Fragment } from '@wordpress/element';
import { InspectorControls } from '@wordpress/block-editor';
import { PanelBody, Button, RangeControl } from '@wordpress/components';
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

const marks = [
  {
    value: 0,
    label: '0em',
  },
  {
    value: 10,
    label: '10em',
  },
  {
    value: 20,
    label: '20em',
  },
];

// Edit拡張
const addCustomEdit = createHigherOrderComponent( ( BlockEdit ) => {
  return ( props ) => {
    if ( props.isSelected ) {
      const { setAttributes, isSelected, attributes } = props;
      const { className, extraBottomMargin } = attributes;

      var title = '';
      if ( props.name.includes( BLOCK_SERIES ) ) {
        title = __( '余白', THEME_NAME );
      } else {
        title = __( '[C] 余白', THEME_NAME );
      }

      return (
        <Fragment>
          <BlockEdit { ...props } />
          { isSelected && (
            <InspectorControls>
              <PanelBody title={ title } initialOpen={ false }>
                { /* <div class="__clearBtn">
                  <button
                    type="button"
                    class="components-button is-small"
                    onClick={ () => setAttributes( { extraBottomMargin: '' } ) }
                  >
                    <span class="dashicons dashicons-editor-removeformatting"></span>
                    { __( 'ブロック下部余白をクリア', THEME_NAME ) }
                  </button>
                </div> */ }
                <RangeControl
                  label={ __( 'ブロック下部', THEME_NAME ) }
                  value={ extraBottomMargin }
                  onChange={ ( value ) => {
                    if ( value === -1 ) {
                      setAttributes( { extraBottomMargin: '' } );
                    } else {
                      setAttributes( { extraBottomMargin: value + 'em' } );
                    }
                  } }
                  min={ -1 }
                  max={ 20 }
                  step={ 0.1 }
                  allowReset={ true }
                  resetFallbackValue={ -1 }
                  initialPosition={ 0 }
                  marks={ marks }
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
