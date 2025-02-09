/**
 * Cocoon Blocks
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */

import { THEME_NAME, BLOCK_CLASS, isBalloonExist } from '../../helpers';

import { __ } from '@wordpress/i18n';
const { registerBlockType, createBlock } = wp.blocks;
import { InnerBlocks, RichText, InspectorControls } from '@wordpress/block-editor';
const { PanelBody, SelectControl, BaseControl } = wp.components;
import { Fragment } from '@wordpress/element';
const DEFAULT_NAME = __( '未入力', THEME_NAME );
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';

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

registerBlockType( 'cocoon-blocks/balloon-box', {
  title: __( '吹き出し', THEME_NAME ),
  icon: 'dismiss',
  category: THEME_NAME + '-old',
  description: __( '登録されている吹き出しを挿入できます。', THEME_NAME ),

  attributes: {
    content: {
      type: 'string',
      selector: 'div',
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
    var { content, index } = attributes;
    if ( ! speechBalloons[ index ] ) {
      index = 0;
    }

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

        <div
          className={
            'speech-wrap sb-id-' +
            speechBalloons[ index ].id +
            ' sbs-' +
            speechBalloons[ index ].style +
            ' sbp-' +
            speechBalloons[ index ].position +
            ' sbis-' +
            speechBalloons[ index ].iconindex +
            ' cf' +
            BLOCK_CLASS
          }
        >
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
                value={ content ? content : speechBalloons[ index ].name }
                placeholder={ DEFAULT_NAME }
                onChange={ ( value ) => setAttributes( { content: value } ) }
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
    var { content, index } = attributes;
    if ( ! speechBalloons[ index ] ) {
      index = 0;
    }
    return (
      <div
        className={
          'speech-wrap sb-id-' +
          speechBalloons[ index ].id +
          ' sbs-' +
          speechBalloons[ index ].style +
          ' sbp-' +
          speechBalloons[ index ].position +
          ' sbis-' +
          speechBalloons[ index ].iconindex +
          ' cf' +
          BLOCK_CLASS
        }
      >
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
              value={ content ? content : speechBalloons[ index ].name }
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
