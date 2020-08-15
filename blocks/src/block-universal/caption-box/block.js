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

const CAPTION_BOX_CLASS = 'caption-box';
const DEFAULT_MSG = __( '見出し', THEME_NAME );

class CocoonCaptionBoxBlock extends Component {
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
      icon,
    } = attributes;

    return (
      <Fragment>
        <InspectorControls>

          <PanelBody title={ __( 'スタイル設定', THEME_NAME ) }>
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

        <div className={ classnames(className, {
              [ CAPTION_BOX_CLASS ]: true,
              'block-box': true,
              'has-text-color': textColor.color,
              'has-background': backgroundColor.color,
              'has-border-color': borderColor.color,
              [backgroundColor.class]: backgroundColor.class,
              [textColor.class]: textColor.class,
              [borderColor.class]: borderColor.class,
              [fontSize.class]: fontSize.class,
          }) }>
          <div className={
            classnames('caption-box-label', 'block-box-label', 'box-label', icon)
          }>
            <span className={
              classnames('caption-box-label-text', 'block-box-label-text', 'box-label-text')
            }>
              <RichText
                value={ content }
                onChange={ ( value ) => setAttributes( { content: value } ) }
                placeholder={ DEFAULT_MSG }
              />
            </span>
          </div>
          <div className={classnames('caption-box-content', 'block-box-content', 'box-content')}>
            <InnerBlocks />
          </div>
        </div>

      </Fragment>
    );
  }
}

registerBlockType( 'cocoon-blocks/caption-box-1', {

  title: __( '見出しボックス', THEME_NAME ),
  icon: <FontAwesomeIcon icon={['far', 'window-maximize']} />,
  category: THEME_NAME + '-universal-block',
  description: __( 'ボックス「見出し」を入力できる汎用ボックスです。', THEME_NAME ),
  keywords: [ 'caption', 'box' ],

  attributes: {
    content: {
      type: 'string',
      default: DEFAULT_MSG,
    },
    icon: {
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

  edit: compose([
    withColors('backgroundColor', {textColor: 'color', borderColor: 'border-color'}),
    withFontSizes('fontSize'),
    fullFallbackStyles,
  ])(CocoonCaptionBoxBlock),
  save: props => {
    const {
      content,
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
      [ CAPTION_BOX_CLASS ]: true,
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
        <div className={
          classnames('caption-box-label', 'block-box-label', 'box-label', icon)
        }>
          <span className={
            classnames('caption-box-label-text', 'block-box-label-text', 'box-label-text')
          }>
            <RichText.Content
              value={ content }
            />
          </span>
        </div>
        <div className={classnames('caption-box-content', 'block-box-content', 'box-content')}>
          <InnerBlocks.Content />
        </div>
      </div>
    );
  },

  deprecated: deprecated,

  transforms: transforms,
});