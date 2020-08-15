/**
 * Cocoon Blocks
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */

import { THEME_NAME, CLICK_POINT_MSG, fullFallbackStyles, codeBlockEscape, CODE_LANGUAGES } from '../../helpers';
import { deprecated } from './deprecated';
import { transforms } from './transforms';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import classnames from 'classnames';

function getCssToLabelName(css){
  var $res = '';
  CODE_LANGUAGES.map((item ,index) => {
    if (item['value'] == css) {
      //console.log(item);
      //console.log(item['label']);
      $res = item['label'];
      return $res;
    }
  })
  //console.log('aaaaaaaaaaaaa');
  return $res;
}

//console.log(getCssToLabelName('php'));

const { __ } = wp.i18n;
const {
  registerBlockType,
} = wp.blocks;
const {
  InspectorControls,
  InnerBlocks,
  RichText,
  PlainText,
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


class CocoonCodeBlock extends Component {
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

    let {
      content,
      language,
    } = attributes;

    return (
      <Fragment>
        {/*
        <InspectorControls>
          <PanelColorSettings
            title={ __( '色設定（無ハイライト用）', THEME_NAME ) }
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
          <PanelBody title={ __( '文字サイズ', THEME_NAME ) } className="blocks-font-size">
            <FontSizePicker
              fallbackFontSize={ fallbackFontSize }
              value={ fontSize.size }
              onChange={ setFontSize }
            />
          </PanelBody>
        </InspectorControls>
        */}

        <SelectControl
          label={ __( '言語', THEME_NAME ) }
          className='language-select-control'
          value={ language }
          onChange={ ( value ) => setAttributes( {
            language: value,
          } ) }
          options={ CODE_LANGUAGES }
        />


          <PlainText
            tagName="pre"
            className={ classnames(className, {
              'code-box': true,
              'block-box': true,
              [ language ]: !! language,
              'auto': ! language,
              'has-text-color': textColor.color,
              'has-background': backgroundColor.color,
              'has-border-color': borderColor.color,
              [backgroundColor.class]: backgroundColor.class,
              [textColor.class]: textColor.class,
              [borderColor.class]: borderColor.class,
              [fontSize.class]: fontSize.class,
            }) }
            value={ content }
            onChange={ ( value ) => setAttributes( { content: value } ) }
            placeholder={  __( 'コードを入力', THEME_NAME ) }
          />
        {/*<RichText
          tagName="pre"
          value={ content.replace( /\n/g, '<br>' ) }
          onChange={ ( value ) => {
            setAttributes( {
              content: value.replace( /<br ?\/?>/g, '\n' ),
            } );
          } }
          placeholder={ __( 'コードを入力', THEME_NAME ) }
          wrapperClassName={ className }
          onMerge={ mergeBlocks }
        />*/}

      </Fragment>
    );
  }
}

registerBlockType( 'cocoon-blocks/code-box', {

  title: __( 'コード', THEME_NAME ),
  icon: <FontAwesomeIcon icon={['fas', 'code']} />,
  category: THEME_NAME + '-block',
  description: __( 'highlight.js用のコード出力ブロック。', THEME_NAME ),
  keywords: [ 'code', 'box' ],

  attributes: {
    content: {
      type: 'string',
      default: '',
    },
    language: {
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
  ])(CocoonCodeBlock),
  save: props => {
    const {
      content,
      language,
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
      'code-box': true,
      'block-box': true,
       [ language ]: !! language,
      'auto': ! language,
      'has-text-color': textColor || customTextColor,
      'has-background': backgroundColor || customBackgroundColor,
      'has-border-color': borderColor || customBorderColor,
      [ textClass ]: textClass,
      [ backgroundClass ]: backgroundClass,
      [ borderClass ]: borderClass,
      [ fontSizeClass ]: fontSizeClass,
    } );

    return (
      <RichText.Content
        tagName="pre"
        className={ className }
        value={ codeBlockEscape(content) }
        aria-label={ getCssToLabelName(language) }
      />
    );
  },

  //deprecated: deprecated,

  //transforms: transforms,
});