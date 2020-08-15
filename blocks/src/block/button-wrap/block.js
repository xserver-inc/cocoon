/**
 * Cocoon Blocks
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */

import { THEME_NAME, BUTTON_BLOCK, fullFallbackStyles } from '../../helpers';
import { deprecated } from './deprecated';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import classnames from 'classnames';


const { __ } = wp.i18n;
const {
  registerBlockType,
} = wp.blocks;
const {
  InspectorControls,
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


class CocoonButtonWrapBlock extends Component {
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
      tag,
      size,
      url,
      isCircle,
      isShine,
    } = attributes;

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
                  value: 'btn-wrap-s',
                  label: __( '小', THEME_NAME ),
                },
                {
                  value: 'btn-wrap-m',
                  label: __( '中', THEME_NAME ),
                },
                {
                  value: 'btn-wrap-l',
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

          <PanelBody title={ __( '文字サイズ', THEME_NAME ) } className="blocks-font-size">
            <FontSizePicker
              fallbackFontSize={ fallbackFontSize }
              value={ fontSize.size }
              onChange={ setFontSize }
            />
          </PanelBody>

          <PanelColorSettings
            title={ __( '色設定', THEME_NAME ) }
            colorSettings={[
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
              {
                label: __( 'ボーダー色', THEME_NAME ),
                onChange: setBorderColor,
                value: borderColor.color,
              },
            ]}
          />
        </InspectorControls>

        <span className={'button-wrap-msg'}>
          <RichText
            value={ content }
          />
        </span>
        <div
          className={ classnames(className, {
              [ 'btn-wrap' ]: true,
              [ 'btn-wrap-block' ]: true,
              [ BUTTON_BLOCK ]: true,
              [ size ]: size,
              [ 'btn-wrap-circle' ]: !! isCircle,
              [ 'btn-wrap-shine' ]: !! isShine,
              'has-text-color': textColor.color,
              'has-background': backgroundColor.color,
              'has-border-color': borderColor.color,
              [backgroundColor.class]: backgroundColor.class,
              [textColor.class]: textColor.class,
              [borderColor.class]: borderColor.class,
              [fontSize.class]: fontSize.class,
            }) }
          dangerouslySetInnerHTML={{__html: tag}}
        >
        </div>

      </Fragment>
    );
  }
}

registerBlockType( 'cocoon-blocks/button-wrap-1', {

  title: __( '囲みボタン', THEME_NAME ),
  icon: <FontAwesomeIcon icon={['fas', 'ticket-alt']} />,
  category: THEME_NAME + '-block',
  description: __( 'アスリートタグ等のタグを変更できないリンクをボタン化します。', THEME_NAME ),
  keywords: [ 'button', 'btn', 'wrap' ],

  attributes: {
    content: {
      type: 'string',
      default: __( 'こちらをクリックしてリンクタグを設定エリア入力してください。この入力は公開ページで反映されません。', THEME_NAME ),
    },
    tag: {
      type: 'string',
      default: '',
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
  ])(CocoonButtonWrapBlock),
  save: props => {
    const {
      tag,
      size,
      isCircle,
      isShine,
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
      [ 'btn-wrap' ]: true,
      [ 'btn-wrap-block' ]: true,
      [ BUTTON_BLOCK ]: true,
      [ size ]: size,
      [ 'btn-wrap-circle' ]: !! isCircle,
      [ 'btn-wrap-shine' ]: !! isShine,
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
        <RichText.Content
          value={ tag }
        />
      </div>
    );
  },

  deprecated: deprecated,
});