/**
 * Cocoon Blocks
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */

import { THEME_NAME, BLOCK_CLASS, ICONS, getIconClass } from '../../helpers';

const { times } = lodash;
import { __ } from '@wordpress/i18n';
import { registerBlockType } from '@wordpress/blocks';
import { InnerBlocks, RichText, InspectorControls } from '@wordpress/block-editor';
const { PanelBody, SelectControl, BaseControl, Button } = wp.components;
import { Fragment } from '@wordpress/element';
const CAPTION_BOX_CLASS = 'label-box';
const DEFAULT_MSG = __( '見出し', THEME_NAME );

registerBlockType( 'cocoon-blocks/label-box', {
  title: __( 'ラベルボックス', THEME_NAME ),
  icon: 'dismiss',
  category: THEME_NAME + '-old',
  description: __(
    'ボックスに「ラベル見出し」入力できる汎用ボックスです。',
    THEME_NAME
  ),

  attributes: {
    content: {
      type: 'string',
      selector: 'div',
      default: DEFAULT_MSG,
    },
    color: {
      type: 'string',
      default: '',
    },
    icon: {
      type: 'string',
      default: '',
    },
  },
  supports: {
    inserter: false,
  },

  edit( { attributes, setAttributes } ) {
    const { content, color, icon } = attributes;

    return (
      <Fragment>
        <InspectorControls>
          <PanelBody title={ __( 'スタイル設定', THEME_NAME ) }>
            <SelectControl
              label={ __( '色設定', THEME_NAME ) }
              value={ color }
              onChange={ ( value ) => setAttributes( { color: value } ) }
              options={ [
                {
                  value: '',
                  label: __( 'デフォルト', THEME_NAME ),
                },
                {
                  value: ' lb-yellow',
                  label: __( '黄色', THEME_NAME ),
                },
                {
                  value: ' lb-red',
                  label: __( '赤色', THEME_NAME ),
                },
                {
                  value: ' lb-blue',
                  label: __( '青色', THEME_NAME ),
                },
                {
                  value: ' lb-green',
                  label: __( '緑色', THEME_NAME ),
                },
              ] }
              __nextHasNoMarginBottom={ true }
            />

            <BaseControl label={ __( 'アイコン', THEME_NAME ) }>
              <div className="icon-setting-buttons">
                { times( ICONS.length, ( index ) => {
                  return (
                    <Button
                      variant="secondary"
                      isPrimary={ icon === ICONS[ index ].value }
                      className={ ICONS[ index ].label }
                      onClick={ () => {
                        setAttributes( { icon: ICONS[ index ].value } );
                      } }
                    ></Button>
                  );
                } ) }
              </div>
            </BaseControl>
          </PanelBody>
        </InspectorControls>

        <div className={ CAPTION_BOX_CLASS + color + BLOCK_CLASS }>
          <div
            className={
              'label-box-label block-box-label' + getIconClass( icon )
            }
          >
            <span className={ 'label-box-label-text block-box-label-text' }>
              <RichText
                value={ content }
                onChange={ ( value ) => setAttributes( { content: value } ) }
                placeholder={ DEFAULT_MSG }
              />
            </span>
          </div>
          <div className="label-box-content">
            <InnerBlocks />
          </div>
        </div>
      </Fragment>
    );
  },

  save( { attributes } ) {
    const { content, color, icon } = attributes;
    return (
      <div className={ CAPTION_BOX_CLASS + color + BLOCK_CLASS }>
        <div
          className={ 'label-box-label block-box-label' + getIconClass( icon ) }
        >
          <span className={ 'label-box-label-text block-box-label-text' }>
            <RichText.Content value={ content } />
          </span>
        </div>
        <div className="label-box-content">
          <InnerBlocks.Content />
        </div>
      </div>
    );
  },
} );
