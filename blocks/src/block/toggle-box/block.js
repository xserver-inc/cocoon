/**
 * Cocoon Blocks
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */

import { THEME_NAME, fullFallbackStyles, getDateID } from '../../helpers';
import { deprecated } from './deprecated';
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

const DEFAULT_MSG = __( 'トグルボックス見出し', THEME_NAME );

class CocoonToggleBoxBlock extends Component {
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
      dateID,
    } = attributes;

    (dateID == '') ? setAttributes( { dateID: getDateID() } ) : dateID;

    return (
      <Fragment>
        <InspectorControls>

          <PanelColorSettings
            title={ __( '色設定', THEME_NAME ) }
            colorSettings={[
              {
                label: __( 'ボーダー色', THEME_NAME ),
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
            'toggle-wrap': true,
            'toggle-box': true,
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
          <input id={"toggle-checkbox-" + dateID} className="toggle-checkbox" type="checkbox" />
          <label className="toggle-button" for={"toggle-checkbox-" + dateID}>
            <RichText
              value={ content }
              onChange={ ( value ) => setAttributes( { content: value } ) }
            />
          </label>
          <div className="toggle-content">
            <InnerBlocks />
          </div>
        </div>

      </Fragment>
    );
  }
}

registerBlockType( 'cocoon-blocks/toggle-box-1', {

  title: __( 'トグルボックス', THEME_NAME ),
  icon: 'randomize',
  category: THEME_NAME + '-block',
  description: __( 'クリックすることでコンテンツ内容の表示を切り替えることができるボックスです。', THEME_NAME ),
  keywords: [ 'toggle', 'box' ],

  attributes: {
    content: {
      type: 'string',
      selector: 'div',
      default: DEFAULT_MSG,
    },
    dateID: {
      type: 'string',
      default: '',
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
    reusable: false,
  },

  edit: compose([
    withColors('backgroundColor', {textColor: 'color', borderColor: 'border-color'}),
    withFontSizes('fontSize'),
    fullFallbackStyles,
  ])(CocoonToggleBoxBlock),
  save: props => {
    const {
      content,
      dateID,
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
      'toggle-wrap': true,
      'toggle-box': true,
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
        <input id={"toggle-checkbox-" + dateID} className="toggle-checkbox" type="checkbox" />
        <label className="toggle-button" for={"toggle-checkbox-" + dateID}>
          <RichText.Content
            value={ content }
          />
        </label>
        <div className="toggle-content">
          <InnerBlocks.Content />
        </div>
      </div>
    );
  },

  deprecated: deprecated,
});