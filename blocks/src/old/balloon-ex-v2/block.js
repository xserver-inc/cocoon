/**
 * Cocoon Blocks
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */

import {THEME_NAME, BLOCK_CLASS} from '../../helpers';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';

const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;
const { InnerBlocks, RichText, InspectorControls } = wp.editor;
const { PanelBody, SelectControl, BaseControl } = wp.components;
const { Fragment } = wp.element;
const DEFAULT_NAME = __( '未入力', THEME_NAME );

registerBlockType( 'cocoon-blocks/balloon-ex-box', {

  title: __( '吹き出しEX', THEME_NAME ),
  icon: <FontAwesomeIcon icon={['far', 'comments']} />,
  category: THEME_NAME + '-block',
  description: __( '登録されている吹き出しのオプションを変更できます。', THEME_NAME ),

  attributes: {
    name: {
      type: 'string',
      selector: 'div',
      default: '',
    },
    index: {
      type: 'string',
      default: '0',
    },
    style: {
      type: 'string',
      default: '',
    },
    position: {
      type: 'string',
      default: '',
    },
    iconstyle: {
      type: 'string',
      default: '',
    },
  },

  edit( { attributes, setAttributes } ) {
    const { name, index, style, position, iconstyle } = attributes;

    //console.log(gbSpeechBalloons);
    var balloons = [];
    gbSpeechBalloons.map((balloon, index) => {
      //console.log(balloon);
      if (gbSpeechBalloons[index].visible == '1') {
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
              value={ index ? index : gbSpeechBalloons[index].index }
              onChange={ ( value ) => setAttributes( { index: value } ) }
              options={ balloons }
            />

            <SelectControl
              label={ __( '吹き出しスタイル', THEME_NAME ) }
              value={ style ? style : gbSpeechBalloons[index].style }
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
              value={ position ? position : gbSpeechBalloons[index].position }
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
              value={ iconstyle ? iconstyle : gbSpeechBalloons[index].iconstyle }
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
          className={
            "speech-wrap sb-id-" + gbSpeechBalloons[index].id +
            " sbs-" + style +
            " sbp-" + position +
            " sbis-" + iconstyle +
            " cf" +
            BLOCK_CLASS
          }>
          <div className="speech-person">
            <figure className="speech-icon">
              <img
                src={gbSpeechBalloons[index].icon}
                alt={gbSpeechBalloons[index].name}
                className="speech-icon-image"
              />
            </figure>
            <div className="speech-name">
              <RichText
                value={ name ? name : gbSpeechBalloons[index].name }
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
    const { name, index, style, position, iconstyle } = attributes;
    return (
        <div
          className={
            "speech-wrap sb-id-" + gbSpeechBalloons[index].id +
            " sbs-" + style +
            " sbp-" + position +
            " sbis-" + iconstyle +
            " cf" +
            BLOCK_CLASS
          }>
          <div className="speech-person">
            <figure className="speech-icon">
              <img
                src={gbSpeechBalloons[index].icon}
                alt={gbSpeechBalloons[index].name}
                className="speech-icon-image"
              />
            </figure>
            <div className="speech-name">
              <RichText.Content
                value={ name ? name : gbSpeechBalloons[index].name }
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