/**
 * Cocoon Blocks
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */

import {THEME_NAME, BLOCK_CLASS} from '../../helpers.js';

const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;
const { InnerBlocks, RichText, InspectorControls } = wp.editor;
const { PanelBody, SelectControl, BaseControl } = wp.components;
const { Fragment } = wp.element;
const DEFAULT_MSG = __( 'こちらをクリックして設定変更。この入力は公開ページで反映されません。', THEME_NAME );

registerBlockType( 'cocoon-blocks/balloon-box', {

  title: __( '吹き出し', THEME_NAME ),
  icon: 'format-chat',
  category: THEME_NAME + '-block',
  description: __( 'コンテンツを囲むだけのブラックボックスを表示します。', THEME_NAME ),

  attributes: {
    content: {
      type: 'string',
      selector: 'div',
      default: DEFAULT_MSG,
    },
    style: {
      type: 'string',
      default: 'blank-box',
    },
  },

  edit( { attributes, setAttributes } ) {
    const { content, style, alignment } = attributes;

    console.log(speechBaloons);
    var items = [1,2,3,4];
    items.push( 5,6,7,8 );
    console.log(items);
    var balloons = [];
    speechBaloons.map((balloon) => {
      console.log(balloon);
      balloons.push({
              value: balloon.id,
              label: balloon.title,
            });
    });
    console.log(balloons);

    function onChange(event){
      setAttributes({style: event.target.value});
    }

    function onChangeContent(newContent){
      setAttributes( { content: newContent } );
    }

    return (
      <Fragment>
        <InspectorControls>
          <PanelBody title={ __( 'スタイル設定', THEME_NAME ) }>

            <SelectControl
              label={ __( 'タイプ', THEME_NAME ) }
              value={ style }
              onChange={ ( value ) => setAttributes( { style: value } ) }
              options={ balloons }
            />

          </PanelBody>
        </InspectorControls>

        <div className={attributes.style + BLOCK_CLASS}>
          <span className={'box-block-msg'}>
            <RichText
              value={ content }
              placeholder={ DEFAULT_MSG }
            />
          </span>
          <InnerBlocks />
        </div>
      </Fragment>
    );
  },

  save( { attributes } ) {
    const { content } = attributes;
    return (
      <div className={attributes.style + BLOCK_CLASS}>
        <InnerBlocks.Content />
      </div>
    );
  }
} );