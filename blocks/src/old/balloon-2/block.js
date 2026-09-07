/**
 * Cocoon Blocks
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */

import {
  THEME_NAME,
  BLOCK_CLASS,
  getBalloonClasses,
  isSameBalloon,
  isBalloonExist,
} from '../../helpers';
import { createLegacyStyleDeprecation } from '../../style-attribute-compat';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import classnames from 'classnames';

import { __ } from '@wordpress/i18n';
import { registerBlockType } from '@wordpress/blocks';
import {
  InnerBlocks,
  RichText,
  InspectorControls,
  useBlockProps,
} from '@wordpress/block-editor';
const { PanelBody, SelectControl, BaseControl } = wp.components;
import { Fragment } from '@wordpress/element';
const DEFAULT_NAME = __( '未入力', THEME_NAME );

const defaultIconUrl = gbSettings.speechBalloonDefaultIconUrl
  ? gbSettings.speechBalloonDefaultIconUrl
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

const DEFAULT_STYLE = 'stn';

const BLOCK_ATTRIBUTES = {
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
  balloonStyle: {
    type: 'string',
    default: DEFAULT_STYLE,
  },
  style: {
    type: 'object',
  },
  position: {
    type: 'string',
    default: 'l',
  },
  iconstyle: {
    type: 'string',
    default: 'cb',
  },
};

const BLOCK_SUPPORTS = { html: false, inserter: false };

// deprecatedの履歴スキーマを現行定義から独立させ、将来の変更波及を防ぎます。
const LEGACY_API_VERSION = 3;
const LEGACY_DEFAULT_STYLE = 'stn';
const LEGACY_BLOCK_ATTRIBUTES = {
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
    default: LEGACY_DEFAULT_STYLE,
  },
  position: {
    type: 'string',
    default: 'l',
  },
  iconstyle: {
    type: 'string',
    default: 'cb',
  },
};
const LEGACY_BLOCK_SUPPORTS = { html: false, inserter: false };

function save( { attributes } ) {
  const { name, id, icon, balloonStyle, position, iconstyle } = attributes;
  const blockProps = useBlockProps.save( {
    className: getBalloonClasses( id, balloonStyle, position, iconstyle ),
  } );
  return (
    <div { ...blockProps }>
      <div className="speech-person">
        <figure className="speech-icon">
          <img src={ icon } alt={ name } className="speech-icon-image" />
        </figure>
        <div className="speech-name">
          <RichText.Content value={ name } />
        </div>
      </div>
      <div className="speech-balloon">
        <InnerBlocks.Content />
      </div>
    </div>
  );
}

// 旧style属性の文字列化も含め、保存済みHTMLのクラス構成を固定します。
function legacySave( { attributes } ) {
  const { name, id, icon, style, position, iconstyle } = attributes;
  const legacyClasses = classnames( {
    'speech-wrap': true,
    [ `sb-id-${ id }` ]: !! id,
    [ `sbs-${ style }` ]: !! style,
    [ `sbp-${ position }` ]: !! position,
    [ `sbis-${ iconstyle }` ]: !! iconstyle,
    cf: true,
    'block-box': true,
  } );
  const blockProps = useBlockProps.save( { className: legacyClasses } );
  return (
    <div { ...blockProps }>
      <div className="speech-person">
        <figure className="speech-icon">
          <img src={ icon } alt={ name } className="speech-icon-image" />
        </figure>
        <div className="speech-name">
          <RichText.Content value={ name } />
        </div>
      </div>
      <div className="speech-balloon">
        <InnerBlocks.Content />
      </div>
    </div>
  );
}

registerBlockType( 'cocoon-blocks/balloon-box-2', {
  apiVersion: 3,
  title: __( '吹き出し', THEME_NAME ),
  icon: 'dismiss',
  category: THEME_NAME + '-old',
  description: __( '登録されている吹き出しを挿入できます。', THEME_NAME ),
  keywords: [ 'balloon', 'box' ],

  attributes: BLOCK_ATTRIBUTES,
  supports: BLOCK_SUPPORTS,

  deprecated: [
    createLegacyStyleDeprecation( {
      apiVersion: LEGACY_API_VERSION,
      attributes: LEGACY_BLOCK_ATTRIBUTES,
      supports: LEGACY_BLOCK_SUPPORTS,
      legacySave,
      attributeName: 'balloonStyle',
      defaultValue: LEGACY_DEFAULT_STYLE,
    } ),
  ],

  edit( { attributes, setAttributes } ) {
    let { name, index, id, icon, balloonStyle, position, iconstyle } =
      attributes;
    if ( ! speechBalloons[ index ] ) {
      index = 0;
    }
    //新規作成時
    if ( ! icon && index == '0' && speechBalloons[ 0 ] ) {
      id = speechBalloons[ 0 ].id;
      icon = speechBalloons[ 0 ].icon;
      balloonStyle = speechBalloons[ 0 ].style;
      position = speechBalloons[ 0 ].position;
      iconstyle = speechBalloons[ 0 ].iconstyle;
      if ( ! name ) {
        name = speechBalloons[ 0 ].name;
      }
      setAttributes( {
        name,
        index,
        id,
        icon,
        balloonStyle,
        position,
        iconstyle,
      } );
    }
    //新規作成以外
    if ( speechBalloons[ index ] ) {
      if (
        isSameBalloon( index, id, icon, balloonStyle, position, iconstyle )
      ) {
        id = speechBalloons[ index ].id;
        icon = speechBalloons[ index ].icon;
        balloonStyle = speechBalloons[ index ].style;
        position = speechBalloons[ index ].position;
        iconstyle = speechBalloons[ index ].iconstyle;
        if ( ! name ) {
          name = speechBalloons[ index ].name;
        }
        setAttributes( {
          index,
          id,
          icon,
          balloonStyle,
          position,
          iconstyle,
        } );
      }
    }

    //console.log(speechBalloons);
    const balloons = [];
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
                setAttributes( {
                  index: value,
                  name: speechBalloons[ value ].name,
                  id: speechBalloons[ value ].id,
                  icon: speechBalloons[ value ].icon,
                  balloonStyle: speechBalloons[ value ].style,
                  position: speechBalloons[ value ].position,
                  iconstyle: speechBalloons[ value ].iconstyle,
                } )
              }
              options={ balloons }
              __nextHasNoMarginBottom={ true }
              __next40pxDefaultSize={ true } // 新しいデフォルトサイズに対応
            />
          </PanelBody>
        </InspectorControls>

        <div
          className={ getBalloonClasses(
            id,
            balloonStyle,
            position,
            iconstyle
          ) }
        >
          <div className="speech-person">
            <figure className="speech-icon">
              <img src={ icon } alt={ name } className="speech-icon-image" />
            </figure>
            <div className="speech-name">
              <RichText
                value={ name }
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

  save,
} );
