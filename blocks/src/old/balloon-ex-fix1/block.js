/**
 * Cocoon Blocks
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */

import {THEME_NAME, BLOCK_CLASS} from '../../helpers';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import classnames from 'classnames';

const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;
const { InnerBlocks, RichText, MediaUpload, InspectorControls } = wp.editor;
const { Button, PanelBody, SelectControl, BaseControl } = wp.components;
const { Fragment } = wp.element;
const DEFAULT_NAME = __( '未入力', THEME_NAME );

//classの取得
function getClasses(index, style, position, iconstyle) {
  const classes = classnames(
    {
      [ 'speech-wrap' ]: true,
      [ `sbs-${ style  }` ]: !! style ,
      [ `sbp-${ position  }` ]: !! position ,
      [ `sbis-${ iconstyle  }` ]: !! iconstyle ,
      [ 'cf' ]: true,
      [ 'block-box' ]: true,
    }
  );
  return classes;
}

function getBalloonIcon(id) {
  //console.log(id);
  gbSpeechBalloons.map((balloon, index) => {
    if (balloon.id == id) {
      //console.log(balloon.icon);
      const i = balloon.icon;
      console.log(i);
      return i;
    }
  });
  return '';
}

function getBalloonName(id) {
  gbSpeechBalloons.map((balloon, index) => {
    if (balloon.id == id) {
      const n = balloon.name;
      return n;
    }
  });
  return '';
}

registerBlockType( 'cocoon-blocks/balloon-ex-box', {

  title: __( '吹き出しEX', THEME_NAME ),
  icon: <FontAwesomeIcon icon={['far', 'comments']} />,
  category: THEME_NAME + '-block',
  description: __( '登録されている吹き出しのオプションを変更できます。', THEME_NAME ),
  keywords: [ 'balloon', 'box' ],

  attributes: {
    name: {
      type: 'id',
      default: '',
    },
    name: {
      type: 'string',
      default: '',
    },
    index: {
      type: 'string',
      default: '0',
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
    icon: {
      type: 'string',
      default: '',
    },
    iconid: {
      type: 'number',
      default: 0,
    },
  },

  edit( { attributes, setAttributes } ) {
    const { id, name, index, style, position, iconstyle, icon, iconid } = attributes;
    let defIcon = '';
    if (gbSpeechBalloons[index]) {
      defIcon = gbSpeechBalloons[index].icon;
    }
    let defName = '';
    if (gbSpeechBalloons[index]) {
      defName = gbSpeechBalloons[index].name;
    }

    const renderIcon = ( obj ) => {
      return (
        <Button className="image-button" onClick={ obj.open } style={ { padding: 0 } }>
          <img src={ icon ? icon : defIcon } alt={name ? name : defName} className={ `speech-icon-image wp-image-${ iconid }` } />
        </Button>
      );
    };

    //console.log(gbSpeechBalloons);
    var balloons = [];
    gbSpeechBalloons.map((balloon, index) => {
      //console.log(balloon);
      if (gbSpeechBalloons[index].visible == '1') {
        balloons.push({
          value: balloon.id,
          label: balloon.title,
        });
      }
    });
    //console.log(balloons);

    return (
      <Fragment>
        <InspectorControls>
          <PanelBody title={ __( 'スタイル設定', THEME_NAME ) }>

            <SelectControl
              label={ __( '人物', THEME_NAME ) }
              value={ id }
              onChange={ ( value ) => setAttributes( { icon: getBalloonIcon(value), name: getBalloonName(value) } ) }
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
        </InspectorControls>

        <div
          className={ getClasses(index, style, position, iconstyle) }>
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
                value={ name ? name : defName }
                placeholder={DEFAULT_NAME}
                onChange={ ( value ) => setAttributes( { name: value } ) }
              />
            </div>
          </div>
          <div className="speech-balloon">
            <InnerBlocks />
          </div>
        </div>

      </Fragment>
    );
  },

  save( { attributes } ) {
    const { name, index, style, position, iconstyle, icon } = attributes;
    let defIcon = '';
    if (gbSpeechBalloons[index]) {
      defIcon = gbSpeechBalloons[index].icon;
    }
    let defName = '';
    if (gbSpeechBalloons[index]) {
      defName = gbSpeechBalloons[index].name;
    }

    return (
        <div
          className={ getClasses(index, style, position, iconstyle) }>
          <div className="speech-person">
            <figure className="speech-icon">
              <img
                src={icon ? icon : defIcon}
                alt={name ? name : defName}
                className="speech-icon-image"
              />
            </figure>
            <div className="speech-name">
              <RichText.Content
                value={ name ? name : defName }
              />
            </div>
          </div>
          <div className="speech-balloon">
            <InnerBlocks.Content />
          </div>
        </div>
    );
  }
} );