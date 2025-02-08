/**
 * Cocoon Blocks
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */

import { THEME_NAME, BLOCK_CLASS, isBalloonExist } from '../../helpers';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import classnames from 'classnames';

import { __ } from '@wordpress/i18n';
import { registerBlockType } from '@wordpress/blocks';
import { InnerBlocks, RichText, MediaUpload, InspectorControls } from '@wordpress/block-editor';
const { Button, PanelBody, SelectControl, BaseControl } = wp.components;
import { Fragment } from '@wordpress/element';
const DEFAULT_NAME = __( '未入力', THEME_NAME );

const defaultIconUrl = gbSettings[ 'speechBalloonDefaultIconUrl' ]
  ? gbSettings[ 'speechBalloonDefaultIconUrl' ]
  : '';

let speechBalloons = gbSpeechBalloons;

if ( ! isBalloonExist( speechBalloons ) ) {
  speechBalloons = [
    {
      name: '',
      id: '0',
      icon: defaultIconUrl,
      style: 'cb',
      position: 'l',
      iconstyle: 'stn',
      visible: '1',
    },
  ];
}

//classの取得
function getClasses( index, style, position, iconstyle ) {
  const classes = classnames( {
    [ 'speech-wrap' ]: true,
    [ `sb-id-${ speechBalloons[ index ].id }` ]: !! speechBalloons[ index ].id,
    [ `sbs-${ style }` ]: !! style,
    [ `sbp-${ position }` ]: !! position,
    [ `sbis-${ iconstyle }` ]: !! iconstyle,
    [ 'cf' ]: true,
    [ 'block-box' ]: true,
  } );
  return classes;
}

registerBlockType( 'cocoon-blocks/balloon-ex-box', {
  title: __( '吹き出しEX', THEME_NAME ),
  icon: 'dismiss',
  category: THEME_NAME + '-old',
  description: __(
    '登録されている吹き出しのオプションを変更できます。',
    THEME_NAME
  ),
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
  supports: {
    inserter: false,
  },

  edit( { attributes, setAttributes } ) {
    var { name, index, style, position, iconstyle, icon, iconid } = attributes;
    if ( ! speechBalloons[ index ] ) {
      index = 0;
    }

    const renderIcon = ( obj ) => {
      // console.log(icon);
      // console.log(speechBalloons[index].icon);
      // console.log((icon === speechBalloons[index].icon) ? icon : speechBalloons[index].icon);
      return (
        <Button
          className="image-button"
          onClick={ obj.open }
          style={ { padding: 0 } }
        >
          <img
            src={ icon ? icon : speechBalloons[ index ].icon }
            alt={ icon ? '' : speechBalloons[ index ].name }
            className={ `speech-icon-image wp-image-${ iconid }` }
          />
        </Button>
      );
    };

    //console.log(speechBalloons);
    var balloons = [];
    speechBalloons.map( ( balloon, index ) => {
      //console.log(balloon);
      if ( speechBalloons[ index ].visible == '1' ) {
        balloons.push( {
          value: index,
          label: balloon.title,
        } );
      }
    } );
    //console.log(balloons);

    return (
      <Fragment>
        <InspectorControls>
          <PanelBody title={ __( 'スタイル設定', THEME_NAME ) }>
            <SelectControl
              label={ __( '人物', THEME_NAME ) }
              value={ index }
              onChange={ ( value ) =>
                setAttributes( { index: value, icon: '' } )
              }
              options={ balloons }
              __nextHasNoMarginBottom={ true }
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
              __nextHasNoMarginBottom={ true }
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
              __nextHasNoMarginBottom={ true }
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
              __nextHasNoMarginBottom={ true }
            />
          </PanelBody>
        </InspectorControls>

        <div className={ getClasses( index, style, position, iconstyle ) }>
          <div className="speech-person">
            <figure className="speech-icon">
              <MediaUpload
                onSelect={ ( media ) => {
                  let newicon = !! media.sizes.thumbnail
                    ? media.sizes.thumbnail.url
                    : media.url;
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
                value={ name ? name : speechBalloons[ index ].name }
                placeholder={ DEFAULT_NAME }
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
    var { name, index, style, position, iconstyle, icon } = attributes;
    if ( ! speechBalloons[ index ] ) {
      index = 0;
    }
    return (
      <div className={ getClasses( index, style, position, iconstyle ) }>
        <div className="speech-person">
          <figure className="speech-icon">
            <img
              src={ icon ? icon : speechBalloons[ index ].icon }
              alt={ icon ? '' : speechBalloons[ index ].name }
              className="speech-icon-image"
            />
          </figure>
          <div className="speech-name">
            <RichText.Content
              value={ name ? name : speechBalloons[ index ].name }
            />
          </div>
        </div>
        <div className="speech-balloon">
          <InnerBlocks.Content />
        </div>
      </div>
    );
  },
} );
