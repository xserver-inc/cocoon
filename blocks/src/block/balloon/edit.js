import { THEME_NAME, getBalloonClasses, isSameBalloon, isBalloonExist } from '../../helpers';
import { __ } from '@wordpress/i18n';
import {
  InspectorControls,
  InnerBlocks,
  RichText,
  withColors,
  PanelColorSettings,
  withFontSizes,
  MediaUpload,
  useBlockProps,
} from '@wordpress/block-editor';
import {
  PanelBody,
  SelectControl,
  Button,
} from '@wordpress/components';
import { Fragment } from '@wordpress/element';
import { compose } from '@wordpress/compose';
import classnames from 'classnames';

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

export function BalloonEdit( props ) {
  const {
    attributes,
    setAttributes,
    className,
    backgroundColor,
    setBackgroundColor,
    textColor,
    setTextColor,
    borderColor,
    setBorderColor,
    fontSize,
  } = props;

  let {
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

  const classes = getBalloonClasses(id, style, position, iconstyle);
  const blockProps = useBlockProps({
    className: classes,
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
      </InspectorControls>

      <div { ...blockProps }>
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

export default compose([
  withColors('backgroundColor', {textColor: 'color', borderColor: 'border-color'}),
  withFontSizes('fontSize'),
])(BalloonEdit);