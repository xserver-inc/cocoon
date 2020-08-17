/**
 * Cocoon Blocks
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */

import { THEME_NAME, CLICK_POINT_MSG, fullFallbackStyles, getBalloonClasses, isSameBalloon, isBalloonExist } from '../../helpers';
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
  MediaUpload,
} = wp.editor;
const {
  PanelBody,
  PanelColor,
  ColorPalette,
  SelectControl,
  Button,
} = wp.components;

const {
  Component,
  Fragment,
} = wp.element;

const {
  compose
} = wp.compose;

const DEFAULT_NAME = __( '未入力', THEME_NAME );

const defaultIconUrl = gbSettings['speechBalloonDefaultIconUrl'] ? gbSettings['speechBalloonDefaultIconUrl'] : '';

let speechBalloons = gbSpeechBalloons;

if (!isBalloonExist(speechBalloons)) {
  speechBalloons = [{
    name: '',
    id: '0',
    icon: defaultIconUrl,
    style: 'cb',
    position: 'l',
    iconstyle: 'stn',
    visible: '1',
  }];
}
// console.log(speechBalloons);
// console.log(isBalloonExist(speechBalloons));

class CocoonBalloonBoxBlock extends Component {
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

    var {
      name,
      index,
      id,
      icon,
      style,
      position,
      iconstyle,
      iconid,
    } = attributes;


    //新規作成時
    if (!icon && index == '0' && speechBalloons.length > 0) {

        speechBalloons.some((balloon, index) => {
          //console.log(balloon);
          if (balloon.visible == '1') {
            id = balloon.id;
            icon = balloon.icon;
            style = balloon.style;
            position = balloon.position;
            iconstyle = balloon.iconstyle;
            if (!name) {
              name = balloon.name;
            }
            setAttributes( { name: name, index: index, id: id, icon: icon, style: style, position: position, iconstyle: iconstyle } );
            return true;
          }
        });


    }

    //新規作成以外
    if (speechBalloons[index]) {
      if (isSameBalloon(index, id, icon, style, position, iconstyle)) {

        id = speechBalloons[index].id;
        icon = speechBalloons[index].icon;
        style = speechBalloons[index].style;
        position = speechBalloons[index].position;
        iconstyle = speechBalloons[index].iconstyle;
        if (!name) {
          name = speechBalloons[index].name;
        }
        setAttributes( { index: index, id: id, icon: icon, style: style, position: position, iconstyle: iconstyle } );
      }
    }

    const renderIcon = ( obj ) => {
      return (
        <Button className="image-button" onClick={ obj.open } style={ { padding: 0 } }>
          <img src={ icon ? icon : speechBalloons[index].icon } alt={icon ? '' : speechBalloons[index].name} className={ `speech-icon-image wp-image-${ iconid }` } />
        </Button>
      );
    };

    var balloons = [];
    speechBalloons.map((balloon, index) => {
      //console.log(balloon);
      if (speechBalloons[index].visible == '1') {
        balloons.push({
          value: index,
          label: balloon.title,
        });
      }
    });

    return (
      <Fragment>
        <InspectorControls>
          <PanelBody title={ __( 'スタイル設定', THEME_NAME ) }>

            <SelectControl
              label={ __( '人物', THEME_NAME ) }
              value={ index }
              onChange={ ( value ) => setAttributes( {
                index: value,
                name: speechBalloons[value].name,
                id: speechBalloons[value].id,
                icon: speechBalloons[value].icon,
                style: speechBalloons[value].style,
                position: speechBalloons[value].position,
                iconstyle: speechBalloons[value].iconstyle } ) }
              options={ balloons }
            />

            <SelectControl
              label={ __( '吹き出しスタイル', THEME_NAME ) }
              value={ style }
              onChange={ ( value ) => setAttributes( { style: value } ) }
              options={ [
                {
                  value: 'stn',
                  label: __( 'デフォルト', THEME_NAME ),
                },
                {
                  value: 'flat',
                  label: __( 'フラット', THEME_NAME ),
                },
                {
                  value: 'line',
                  label: __( 'LINE風', THEME_NAME ),
                },
                {
                  value: 'think',
                  label: __( '考え事', THEME_NAME ),
                },
              ] }
            />

            <SelectControl
              label={ __( '人物位置', THEME_NAME ) }
              value={ position }
              onChange={ ( value ) => setAttributes( { position: value } ) }
              options={ [
                {
                  value: 'l',
                  label: __( '左', THEME_NAME ),
                },
                {
                  value: 'r',
                  label: __( '右', THEME_NAME ),
                },
              ] }
            />

            <SelectControl
              label={ __( 'アイコンスタイル', THEME_NAME ) }
              value={ iconstyle }
              onChange={ ( value ) => setAttributes( { iconstyle: value } ) }
              options={ [
                {
                  value: 'sn',
                  label: __( '四角（枠線なし）', THEME_NAME ),
                },
                {
                  value: 'sb',
                  label: __( '四角（枠線あり）', THEME_NAME ),
                },
                {
                  value: 'cn',
                  label: __( '丸（枠線なし）', THEME_NAME ),
                },
                {
                  value: 'cb',
                  label: __( '丸（枠線あり）', THEME_NAME ),
                },
              ] }
            />

          </PanelBody>

          <PanelColorSettings
            title={ __( '吹き出し色設定', THEME_NAME ) }
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


        <div
          className={ getBalloonClasses(id, style, position, iconstyle) }>
          <div className="speech-person">
            <figure className="speech-icon">
              <MediaUpload
                onSelect={ ( media ) => {
                  let newicon = !! media.sizes.thumbnail ? media.sizes.thumbnail.url : media.url;
                  //console.log(newicon);
                  setAttributes( { icon: newicon, iconid: media.id } );
                } }
                type="image"
                value={ iconid }
                render={ renderIcon }
              />
            </figure>
            <div className="speech-name">
              <RichText
                value={ name }
                placeholder={DEFAULT_NAME}
                onChange={ ( value ) => setAttributes( { name: value } ) }
              />
            </div>
          </div>
          <div className={ classnames(className, {
              'speech-balloon': true,
              'has-text-color': textColor.color,
              'has-background': backgroundColor.color,
              'has-border-color': borderColor.color,
              [backgroundColor.class]: backgroundColor.class,
              [textColor.class]: textColor.class,
              [borderColor.class]: borderColor.class,
              [fontSize.class]: fontSize.class,
          }) }>
            <InnerBlocks />
          </div>
        </div>

      </Fragment>
    );
  }
}

registerBlockType( 'cocoon-blocks/balloon-ex-box-1', {

  title: __( '吹き出し', THEME_NAME ),
  icon: <FontAwesomeIcon icon={['far', 'comment']} />,
  category: THEME_NAME + '-block',
  description: __( '登録されている吹き出しのオプションを変更できます。', THEME_NAME ),
  keywords: [ 'balloon', 'box' ],

  attributes: {
    name: {
      type: 'string',
      default: '',
    },
    index: {
      type: 'string',
      default: '0',
    },
    id: {
      type: 'string',
      default: '',
    },
    icon: {
      type: 'string',
      default: '',
    },
    style: {
      type: 'string',
      default: 'stn',
    },
    position: {
      type: 'string',
      default: 'l',
    },
    iconstyle: {
      type: 'string',
      default: 'cb',
    },
    iconid: {
      type: 'number',
      default: 0,
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
  ])(CocoonBalloonBoxBlock),
  save: props => {
    const {
      name,
      index,
      id,
      icon,
      style,
      position,
      iconstyle,
      iconid,
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
      'speech-balloon': true,
      'has-text-color': textColor || customTextColor,
      'has-background': backgroundColor || customBackgroundColor,
      'has-border-color': borderColor || customBorderColor,
      [ textClass ]: textClass,
      [ backgroundClass ]: backgroundClass,
      [ borderClass ]: borderClass,
      [ fontSizeClass ]: fontSizeClass,
    } );

    return (
      <div
        className={ getBalloonClasses(id, style, position, iconstyle) }>
        <div className="speech-person">
          <figure className="speech-icon">
            <img
              src={ icon }
              alt={ name }
              className="speech-icon-image"
            />
          </figure>
          <div className="speech-name">
            <RichText.Content
              value={ name }
            />
          </div>
        </div>
        <div className={ className }>
          <InnerBlocks.Content />
        </div>
      </div>
    );
  },

  //deprecated: deprecated,
});