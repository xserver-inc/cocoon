/**
 * Cocoon Blocks
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */

import { THEME_NAME, ICONS, fullFallbackStyles } from '../../helpers';
import { deprecated } from './deprecated';
import { transforms } from './transforms';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import classnames from 'classnames';


const { times } = lodash;
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
  BaseControl,
  Button,
} = wp.components;

const {
  Component,
  Fragment,
} = wp.element;

const {
  compose
} = wp.compose;

const DEFAULT_MSG = __( 'マイクロコピーテキスト', THEME_NAME );
const MICRO_COPY_CLASS = 'micro-copy';

class CocoonMicroTextBlock extends Component {
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
      type,
      icon,
    } = attributes;

    return (
      <Fragment>
        <InspectorControls>
          <PanelBody title={ __( 'スタイル設定', THEME_NAME ) }>

            <SelectControl
              label={ __( 'タイプ', THEME_NAME ) }
              value={ type }
              onChange={ ( value ) => setAttributes( { type: value } ) }
              options={ [
                {
                  value: 'micro-top',
                  label: __( '下寄り', THEME_NAME ),
                },
                {
                  value: 'micro-bottom',
                  label: __( '上寄り', THEME_NAME ),
                },
              ] }
            />

            <BaseControl label={ __( 'アイコン', THEME_NAME ) }>
              <div className="icon-setting-buttons">
                { times( ICONS.length, ( index ) => {
                  return (
                    <Button
                      isDefault
                      isPrimary={ icon === ICONS[index].value }
                      className={ICONS[index].label}
                      onClick={ () => {
                        setAttributes( { icon: ICONS[index].value } );
                      } }
                    >
                    </Button>
                  );
                } ) }
              </div>
            </BaseControl>

          </PanelBody>

          <PanelColorSettings
            title={ __( '色設定', THEME_NAME ) }
            colorSettings={[
              {
                label: __( '文字色', THEME_NAME ),
                onChange: setTextColor,
                value: textColor.color,
              },
              // {
              //   label: __( '背景色', THEME_NAME ),
              //   onChange: setBackgroundColor,
              //   value: backgroundColor.color,
              // },
              // {
              //   label: __( 'ボーダー色', THEME_NAME ),
              //   onChange: setBorderColor,
              //   value: borderColor.color,
              // },
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
            [ 'micro-text' ]: true,
            [ MICRO_COPY_CLASS ]: true,
            [ type ]: !! type,
            'has-text-color': textColor.color,
            // 'has-background': backgroundColor.color,
            // 'has-border-color': borderColor.color,
            // [backgroundColor.class]: backgroundColor.class,
            [textColor.class]: textColor.class,
            // [borderColor.class]: borderColor.class,
            // [fontSize.class]: fontSize.class,
          })
         }>
          <span class="micro-text-content micro-content">
            { icon && <span class={classnames('micro-text-icon', 'micro-icon', icon)}></span> }
            <RichText
              value={ content }
              onChange={ ( value ) => setAttributes( { content: value } ) }
            />
          </span>
        </div>

      </Fragment>
    );
  }
}

registerBlockType( 'cocoon-blocks/micro-text', {

  title: __( 'マイクロテキスト', THEME_NAME ),
  icon: 'editor-textcolor',
  category: THEME_NAME + '-micro',
  description: __( 'コンバージョンリンク（ボタン）の直上もしくは直下に小さくテキスト表示して、コンバージョン率アップを図るためのマイクロコピーです。', THEME_NAME ),
  keywords: [ 'micro', 'copy', 'text' ],

  attributes: {
    content: {
      type: 'string',
      default: DEFAULT_MSG,
    },
    type: {
      type: 'string',
      default: 'micro-top',
    },
    icon: {
      type: 'string',
    },
    align: {
      type: 'string',
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
  supports: {
    align: [ 'left', 'center', 'right' ],
    customClassName: true,
  },

  edit: compose([
    withColors('backgroundColor', {textColor: 'color', borderColor: 'border-color'}),
    withFontSizes('fontSize'),
    fullFallbackStyles,
  ])(CocoonMicroTextBlock),
  save: props => {
    const {
      content,
      type,
      icon,
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
      [ 'micro-text' ]: true,
      [ MICRO_COPY_CLASS ]: true,
      [ type ]: !! type,
      'has-text-color': textColor || customTextColor,
      // 'has-background': backgroundColor || customBackgroundColor,
      // 'has-border-color': borderColor || customBorderColor,
      [ textClass ]: textClass,
      // [ backgroundClass ]: backgroundClass,
      // [ borderClass ]: borderClass,
      // [ fontSizeClass ]: fontSizeClass,
    } );

    return (
      <div className={ className }>
        <span class="micro-text-content micro-content">
          { icon && <span class={classnames('micro-text-icon', 'micro-icon', icon)}></span> }
          <RichText.Content
            value={ content }
          />
        </span>
      </div>
    );
  },

  deprecated: deprecated,

  transforms: transforms,
});