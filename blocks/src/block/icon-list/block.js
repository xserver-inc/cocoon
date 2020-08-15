/**
 * Cocoon Blocks
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */

import { THEME_NAME, LIST_ICONS } from '../../helpers';
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
  BaseControl,
  Button,
  withFallbackStyles,
} = wp.components;

const {
  Component,
  Fragment,
} = wp.element;

const {
  compose
} = wp.compose;

const {
  times,
} = lodash;

const ALLOWED_BLOCKS = [ 'core/list' ];

const FallbackStyles = withFallbackStyles((node, ownProps) => {
  const {
    textColor,
    backgroundColor,
    borderColor,
    iconColor,
    fontSize,
    customFontSize,
  } = ownProps.attributes;
  const editableNode = node.querySelector('[contenteditable="true"]');
  //verify if editableNode is available, before using getComputedStyle.
  const computedStyles = editableNode ? getComputedStyle(editableNode) : null;
  return {
    fallbackBackgroundColor: backgroundColor || !computedStyles ? undefined : computedStyles.backgroundColor,
    fallbackTextColor: textColor || !computedStyles ? undefined : computedStyles.color,
    fallbackBorderColor: borderColor || !computedStyles ? undefined : computedStyles.color,
    fallbackIconColor: iconColor || !computedStyles ? undefined : computedStyles.color,
    fallbackFontSize: fontSize || customFontSize || !computedStyles ? undefined : parseInt( computedStyles.fontSize ) || undefined,
  }
});

class CocoonIconListBoxBlock extends Component {
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
      iconColor,
      setIconColor,
      fallbackBackgroundColor,
      fallbackTextColor,
      fallbackBorderColor,
      fallbackIconColor,
      fallbackFontSize,
      fontSize,
      setFontSize,
    } = this.props;

    const {
      title,
      icon,
    } = attributes;

    return (
      <Fragment>
        <InspectorControls>
          <PanelBody title={ __( 'スタイル設定', THEME_NAME ) }>

            <BaseControl label={ __( 'アイコン', THEME_NAME ) }>
              <div className="icon-setting-buttons">
                { times( LIST_ICONS.length, ( index ) => {
                  return (
                    <Button
                      isDefault
                      isPrimary={ icon === LIST_ICONS[index].value }
                      className={LIST_ICONS[index].label}
                      onClick={ () => {
                        setAttributes( { icon: LIST_ICONS[index].value } );
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
                label: __( 'アイコン色', THEME_NAME ),
                onChange: setIconColor,
                value: iconColor.color,
              },
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
            'iconlist-box': true,
            'blank-box': true,
            [ icon ]: !! icon,
            'block-box': true,
            'has-text-color': textColor.color,
            'has-background': backgroundColor.color,
            'has-border-color': borderColor.color,
            'has-icon-color': iconColor.color,
            [backgroundColor.class]: backgroundColor.class,
            [textColor.class]: textColor.class,
            [borderColor.class]: borderColor.class,
            [iconColor.class]: iconColor.class,
            [fontSize.class]: fontSize.class,
          })
         }>
          <div className="iconlist-title">
            <RichText
                value={ title }
                placeholder={__( 'タイトルがある場合は入力', THEME_NAME )}
                onChange={ ( value ) => setAttributes( { title: value } ) }
              />
          </div>
          <InnerBlocks
          template={[
              [ 'core/list' ]
          ]}
          templateLock="all"
          allowedBlocks={ ALLOWED_BLOCKS }
           />
        </div>

      </Fragment>
    );
  }
}

registerBlockType( 'cocoon-blocks/iconlist-box', {

  title: __( 'アイコンリスト', THEME_NAME ),
  icon: <FontAwesomeIcon icon={['far', 'list-alt']} />,
  category: THEME_NAME + '-block',
  description: __( 'リストポイントにアイコンを適用した非順序リストです。', THEME_NAME ),
  keywords: [ 'icon', 'list', 'box' ],

  attributes: {
    title: {
      type: 'string',
      default: '',
    },
    icon: {
      type: 'string',
      default: 'list-caret-right',
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
    iconColor: {
      type: 'string',
    },
    customIconColor: {
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
    withColors('backgroundColor', {textColor: 'color', borderColor: 'border-color', iconColor: 'icon-color'}),
    withFontSizes('fontSize'),
    FallbackStyles,
  ])(CocoonIconListBoxBlock),
  save: props => {
    const {
      title,
      icon,
      backgroundColor,
      customBackgroundColor,
      textColor,
      customTextColor,
      borderColor,
      customBorderColor,
      iconColor,
      customIconColor,
      fontSize,
      customFontSize,
    } = props.attributes;

    const backgroundClass = getColorClassName( 'background-color', backgroundColor );
    const textClass = getColorClassName( 'color', textColor );
    const borderClass = getColorClassName( 'border-color', borderColor );
    const iconClass = getColorClassName( 'icon-color', iconColor );
    const fontSizeClass = getFontSizeClass( fontSize );


    const className = classnames( {
      'iconlist-box': true,
      'blank-box': true,
      [ icon ]: !! icon,
      'block-box': true,
      'has-text-color': textColor || customTextColor,
      'has-background': backgroundColor || customBackgroundColor,
      'has-border-color': borderColor || customBorderColor,
      'has-icon-color': iconColor || customIconColor,
      [ textClass ]: textClass,
      [ backgroundClass ]: backgroundClass,
      [ borderClass ]: borderClass,
      [ iconClass ]: iconClass,
      [ fontSizeClass ]: fontSizeClass,
    } );

    return (
      <div className={ className }>
        <div className="iconlist-title">
          <RichText.Content
            value={ title }
          />
        </div>
        <InnerBlocks.Content />
      </div>
    );
  },

  deprecated: deprecated,
});