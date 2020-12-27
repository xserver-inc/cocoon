/**
 * Cocoon Blocks
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */

import { THEME_NAME, CLICK_POINT_MSG, fullFallbackStyles } from '../../helpers';
import { deprecated } from './deprecated';
import { transforms } from './transforms';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import classnames from 'classnames';


const { __ } = wp.i18n;
const {
  registerBlockType,
} = wp.blocks;
const {
  InspectorControls,
  InnerBlocks,
  RichText,
  withColors,
  getColorClassName,
  PanelColorSettings,
  getFontSizeClass,
  withFontSizes,
  FontSizePicker,
  ContrastChecker,
} = wp.editor;
const {
  PanelBody,
  PanelColor,
  ColorPalette,
  SelectControl,
  TextareaControl,
  ToggleControl
} = wp.components;

const {
  Component,
  Fragment,
} = wp.element;

const {
  compose
} = wp.compose;


class CocoonTabBoxBlock extends Component {
  constructor() {
    super(...arguments);
  }

  render() {
    const {
      attributes,
      setAttributes,
      mergeBlocks,
      onReplace,
      className,
      backgroundColor,
      setBackgroundColor,
      textColor,
      setTextColor,
      borderColor,
      setBorderColor,
      fallbackBackgroundColor,
      fallbackTextColor,
      fallbackBorderColor,
      fallbackFontSize,
      fontSize,
      setFontSize,
    } = this.props;

    const {
      content,
      label,
    } = attributes;

    return (
      <Fragment>
        <InspectorControls>
          <PanelBody title={ __( 'スタイル設定', THEME_NAME ) }>

            <SelectControl
              label={ __( 'ラベル', THEME_NAME ) }
              value={ label }
              onChange={ ( value ) => setAttributes( { label: value } ) }
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
            colorSettings={[
              {
                label: __( '枠の色', THEME_NAME ),
                onChange: setBorderColor,
                value: borderColor.color,
              },
              {
                label: __( '背景色', THEME_NAME ),
                onChange: setBackgroundColor,
                value: backgroundColor.color,
              },
              {
                label: __( '文字色', THEME_NAME ),
                onChange: setTextColor,
                value: textColor.color,
              },
            ]}
          />
          {/*
          <PanelBody title={ __( '文字サイズ', THEME_NAME ) } className="blocks-font-size">
            <FontSizePicker
              fallbackFontSize={ fallbackFontSize }
              value={ fontSize.size }
              onChange={ setFontSize }
            />
          </PanelBody>
          */}
        </InspectorControls>

        <div className={
          classnames(className, {
            'blank-box': true,
            'bb-tab': true,
            [ label ]: !! label,
            'block-box': true,
            'has-text-color': textColor.color,
            'has-background': backgroundColor.color,
            'has-border-color': borderColor.color,
            [backgroundColor.class]: backgroundColor.class,
            [textColor.class]: textColor.class,
            [borderColor.class]: borderColor.class,
            [fontSize.class]: fontSize.class,
          })
         }>
          <InnerBlocks />
        </div>

      </Fragment>
    );
  }
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
      default: CLICK_POINT_MSG,
    },
    label: {
      type: 'string',
      default: 'bb-check',
    },
    backgroundColor: {
      type: 'string',
    },
    customBackgroundColor: {
      type: 'string',
    },
    textColor: {
      type: 'string',
    },
    customTextColor: {
      type: 'string',
    },
    borderColor: {
      type: 'string',
    },
    customBorderColor: {
      type: 'string',
    },
    fontSize: {
      type: 'string',
    },
    customFontSize: {
      type: 'string',
    },
  },

  edit: compose([
    withColors('backgroundColor', {textColor: 'color', borderColor: 'border-color'}),
    withFontSizes('fontSize'),
    fullFallbackStyles,
  ])(CocoonTabBoxBlock),
  save: props => {
    const {
      label,
      backgroundColor,
      customBackgroundColor,
      textColor,
      customTextColor,
      borderColor,
      customBorderColor,
      fontSize,
      customFontSize,
    } = props.attributes;

    const backgroundClass = getColorClassName( 'background-color', backgroundColor );
    const textClass = getColorClassName( 'color', textColor );
    const borderClass = getColorClassName( 'border-color', borderColor );
    const fontSizeClass = getFontSizeClass( fontSize );


    const className = classnames( {
      'blank-box': true,
      'bb-tab': true,
      [ label ]: !! label,
      'block-box': true,
      'has-text-color': textColor || customTextColor,
      'has-background': backgroundColor || customBackgroundColor,
      'has-border-color': borderColor || customBorderColor,
      [ textClass ]: textClass,
      [ backgroundClass ]: backgroundClass,
      [ borderClass ]: borderClass,
      [ fontSizeClass ]: fontSizeClass,
    } );

    return (
      <div className={ className }>
        <InnerBlocks.Content />
      </div>
    );
  },

  deprecated: deprecated,

  transforms: transforms,
});
