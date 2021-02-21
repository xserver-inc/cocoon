/**
 * Cocoon Blocks
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */

import { THEME_NAME, BUTTON_BLOCK, colorValueToSlug, keyColor } from '../../helpers';
import classnames from 'classnames';

const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;
const { RichText, InspectorControls, PanelColorSettings, ContrastChecker } = wp.editor;
const { PanelBody, SelectControl, BaseControl, TextControl, ToggleControl } = wp.components;
const { Fragment } = wp.element;

//classの取得
function getClasses(color, size, isCircle, isShine) {
  const classes = classnames(
    {
      'btn': true,
      [ `btn-${ colorValueToSlug(color) }` ]: !! colorValueToSlug(color),
      [ size ]: size,
      [ 'btn-circle' ]: !! isCircle,
      [ 'btn-shine' ]: !! isShine,
    }
  );
  return classes;
}

registerBlockType( 'cocoon-blocks/button-1', {

  title: __( 'ボタン', THEME_NAME ),
  icon: 'dismiss',
  category: THEME_NAME + '-old',
  description: __( '一般的なリンクボタンを作成します。', THEME_NAME ),
  keywords: [ 'button', 'btn' ],

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
    customClassName: true,
  },

  edit( { attributes, setAttributes } ) {
    const { content, color, size, url, target, isCircle, isShine } = attributes;

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
              label={ __( 'サイズ', THEME_NAME ) }
              value={ size }
              onChange={ ( value ) => setAttributes( { size: value } ) }
              options={ [
                {
                  value: 'btn-s',
                  label: __( '小', THEME_NAME ),
                },
                {
                  value: 'btn-m',
                  label: __( '中', THEME_NAME ),
                },
                {
                  value: 'btn-l',
                  label: __( '大', THEME_NAME ),
                },
              ] }
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
            <ContrastChecker
              color={ color }
            />
          </PanelColorSettings>

        </InspectorControls>

        <div className={BUTTON_BLOCK}>
          <span
            className={ getClasses(color, size, isCircle, isShine) }
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
    const { content, color, size, url, target, isCircle, isShine } = attributes;
    return (
      <div className={BUTTON_BLOCK}>
        <a
          href={ url }
          className={ getClasses(color, size, isCircle, isShine) }
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
