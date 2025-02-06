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
import { InnerBlocks, RichText, InspectorControls } from '@wordpress/block-editor';
const { PanelBody, SelectControl, BaseControl } = wp.components;
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
function getClasses( index ) {
  const classes = classnames( {
    [ 'speech-wrap' ]: true,
    [ `sb-id-${ speechBalloons[ index ].id }` ]: !! speechBalloons[ index ].id,
    [ `sbs-${ speechBalloons[ index ].style }` ]:
      !! speechBalloons[ index ].style,
    [ `sbp-${ speechBalloons[ index ].position }` ]:
      !! speechBalloons[ index ].position,
    [ `sbis-${ speechBalloons[ index ].iconstyle }` ]:
      !! speechBalloons[ index ].iconstyle,
    [ 'cf' ]: true,
    [ 'block-box' ]: true,
  } );
  return classes;
}

registerBlockType( 'cocoon-blocks/balloon-box-1', {
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
  },
  supports: {
    inserter: false,
  },

  edit( { attributes, setAttributes } ) {
    var { name, index } = attributes;
    if ( ! speechBalloons[ index ] ) {
      index = 0;
    }

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
              onChange={ ( value ) => setAttributes( { index: value } ) }
              options={ balloons }
              __nextHasNoMarginBottom={ true }
            />
          </PanelBody>
        </InspectorControls>

        <div className={ getClasses( index ) }>
          <div className="speech-person">
            <figure className="speech-icon">
              <img
                src={ speechBalloons[ index ].icon }
                alt={ speechBalloons[ index ].name }
                className="speech-icon-image"
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
    var { name, index } = attributes;
    if ( ! speechBalloons[ index ] ) {
      index = 0;
    }
    return (
      <div className={ getClasses( index ) }>
        <div className="speech-person">
          <figure className="speech-icon">
            <img
              src={ speechBalloons[ index ].icon }
              alt={ speechBalloons[ index ].name }
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
