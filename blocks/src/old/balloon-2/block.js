/**
 * Cocoon Blocks
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */

import {THEME_NAME, BLOCK_CLASS, getBalloonClasses, isSameBalloon, isBalloonExist} from '../../helpers';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import classnames from 'classnames';

const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;
const { InnerBlocks, RichText, InspectorControls } = wp.editor;
const { PanelBody, SelectControl, BaseControl } = wp.components;
const { Fragment } = wp.element;
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

registerBlockType( 'cocoon-blocks/balloon-box-2', {

  title: __( '吹き出し', THEME_NAME ),
  icon: 'dismiss',
  category: THEME_NAME + '-old',
  description: __( '登録されている吹き出しを挿入できます。', THEME_NAME ),
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
  },
  supports: {
    inserter: false,
  },

  edit( { attributes, setAttributes } ) {
    var { name, index, id, icon, style, position, iconstyle } = attributes;
    if (!speechBalloons[index]) {
      index = 0;
    }
    //新規作成時
    if (!icon && index == '0' && speechBalloons[0]) {
        id = speechBalloons[0].id;
        icon = speechBalloons[0].icon;
        style = speechBalloons[0].style;
        position = speechBalloons[0].position;
        iconstyle = speechBalloons[0].iconstyle;
        if (!name) {
          name = speechBalloons[0].name;
        }
        setAttributes( { name: name, index: index, id: id, icon: icon, style: style, position: position, iconstyle: iconstyle } );
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

    //console.log(speechBalloons);
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
    //console.log(balloons);

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

          </PanelBody>
        </InspectorControls>

        <div
          className={ getBalloonClasses(id, style, position, iconstyle) }>
          <div className="speech-person">
            <figure className="speech-icon">
              <img
                src={icon}
                alt={name}
                className="speech-icon-image"
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
          <div className="speech-balloon">
            <InnerBlocks />
          </div>
        </div>

      </Fragment>
    );
  },

  save( { attributes } ) {
    const { name, index, id, icon, style, position, iconstyle } = attributes;
    return (
        <div
          className={ getBalloonClasses(id, style, position, iconstyle) }>
          <div className="speech-person">
            <figure className="speech-icon">
              <img
                src={icon}
                alt={name}
                className="speech-icon-image"
              />
            </figure>
            <div className="speech-name">
              <RichText.Content
                value={ name }
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