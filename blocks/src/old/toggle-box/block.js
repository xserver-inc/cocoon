/**
 * Cocoon Blocks
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */

import {THEME_NAME, BLOCK_CLASS, getDateID} from '../../helpers';

const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;
const { InnerBlocks, RichText, InspectorControls } = wp.editor;
const { PanelBody, SelectControl, BaseControl } = wp.components;
const { Fragment } = wp.element;
const DEFAULT_MSG = __( 'トグルボックス見出し', THEME_NAME );

registerBlockType( 'cocoon-blocks/toggle-box', {

  title: __( 'トグルボックス', THEME_NAME ),
  icon: 'dismiss',
  category: THEME_NAME + '-old',
  description: __( 'クリックすることでコンテンツ内容の表示を切り替えることができるボックスです。', THEME_NAME ),

  attributes: {
    content: {
      type: 'string',
      selector: 'div',
      default: DEFAULT_MSG,
    },
    color: {
      type: 'string',
      default: 'toggle-wrap',
    },
    dateID: {
      type: 'string',
      default: '',
    },
  },
  supports: {
    inserter: false,
  },

  edit( { attributes, setAttributes } ) {
    const { content, color, dateID } = attributes;
    //dateID = getDateID();
    (dateID == '') ? setAttributes( { dateID: getDateID() } ) : dateID;

    function onChangeContent(newContent){
      setAttributes( { content: newContent } );
    }

    return (
      <Fragment>
        <InspectorControls>
          <PanelBody title={ __( 'スタイル設定', THEME_NAME ) }>

            <SelectControl
              label={ __( '色設定', THEME_NAME ) }
              value={ color }
              onChange={ ( value ) => setAttributes( { color: value } ) }
              options={ [
                {
                  value: 'toggle-wrap',
                  label: __( 'デフォルト', THEME_NAME ),
                },
                {
                  value: 'toggle-wrap tb-yellow',
                  label: __( '黄色', THEME_NAME ),
                },
                {
                  value: 'toggle-wrap tb-red',
                  label: __( '赤色', THEME_NAME ),
                },
                {
                  value: 'toggle-wrap tb-blue',
                  label: __( '青色', THEME_NAME ),
                },
                {
                  value: 'toggle-wrap tb-green',
                  label: __( '緑色', THEME_NAME ),
                },
              ] }
            />

          </PanelBody>
        </InspectorControls>

        <div className={color + BLOCK_CLASS}>
          <input id={"toggle-checkbox-" + dateID} className="toggle-checkbox" type="checkbox" />
          <label className="toggle-button" for={"toggle-checkbox-" + dateID}>
            <RichText
              value={ content }
              onChange={ onChangeContent }
              placeholder={ DEFAULT_MSG }
            />
          </label>
          <div className="toggle-content">
            <InnerBlocks />
          </div>
        </div>
      </Fragment>
    );
  },

  save( { attributes } ) {
    const { content, color, dateID } = attributes;
    return (
      <div className={color + BLOCK_CLASS}>
        <input id={"toggle-checkbox-" + dateID} className="toggle-checkbox" type="checkbox" />
        <label className="toggle-button" for={"toggle-checkbox-" + dateID}>
          <RichText.Content
            value={ content }
          />
        </label>
        <div className="toggle-content">
          <InnerBlocks.Content />
        </div>
      </div>
    );
  }
} );