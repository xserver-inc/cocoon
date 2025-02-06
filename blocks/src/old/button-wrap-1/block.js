/**
 * Cocoon Blocks
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */

import {
  THEME_NAME,
  BUTTON_BLOCK,
  colorValueToSlug,
  keyColor,
} from '../../helpers';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import classnames from 'classnames';

import { __ } from '@wordpress/i18n';
import { registerBlockType } from '@wordpress/blocks';
import { RichText, InspectorControls, PanelColorSettings, ContrastChecker } from '@wordpress/block-editor';
const {
  PanelBody,
  SelectControl,
  BaseControl,
  TextareaControl,
  ToggleControl,
} = wp.components;
import { Fragment } from '@wordpress/element';

//classの取得
function getClasses( color, size, isCircle, isShine ) {
  const classes = classnames( {
    [ 'btn-wrap' ]: true,
    [ `btn-wrap-${ colorValueToSlug( color ) }` ]: !! colorValueToSlug( color ),
    [ size ]: size,
    [ BUTTON_BLOCK ]: true,
    [ 'btn-wrap-circle' ]: !! isCircle,
    [ 'btn-wrap-shine' ]: !! isShine,
  } );
  return classes;
}

registerBlockType( 'cocoon-blocks/button-wrap-1', {
  title: __( '囲みボタン', THEME_NAME ),
  icon: 'dismiss',
  category: THEME_NAME + '-old',
  description: __(
    'アスリートタグ等のタグを変更できないリンクをボタン化します。',
    THEME_NAME
  ),
  keywords: [ 'button', 'btn', 'wrap' ],

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
      default: keyColor,
    },
    size: {
      type: 'string',
      default: '',
    },
    isCircle: {
      type: 'boolean',
      default: false,
    },
    isShine: {
      type: 'boolean',
      default: false,
    },
  },
  supports: {
    align: [ 'left', 'center', 'right' ],
  },

  edit( { attributes, setAttributes } ) {
    const { content, color, size, tag, isCircle, isShine } = attributes;

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

            <ToggleControl
              label={ __( '円形にする', THEME_NAME ) }
              checked={ isCircle }
              onChange={ ( value ) => setAttributes( { isCircle: value } ) }
            />

            <ToggleControl
              label={ __( '光らせる', THEME_NAME ) }
              checked={ isShine }
              onChange={ ( value ) => setAttributes( { isShine: value } ) }
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
            <ContrastChecker color={ color } />
          </PanelColorSettings>
        </InspectorControls>
        <span className={ 'button-wrap-msg' }>
          <RichText value={ content } />
        </span>
        <div
          className={ getClasses( color, size, isCircle, isShine ) }
          dangerouslySetInnerHTML={ { __html: tag } }
        ></div>
      </Fragment>
    );
  },

  save( { attributes } ) {
    const { content, color, size, tag, isCircle, isShine } = attributes;
    return (
      <div className={ getClasses( color, size, isCircle, isShine ) }>
        <RichText.Content value={ tag } />
      </div>
    );
  },
} );
