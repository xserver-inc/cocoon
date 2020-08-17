/**
 * Cocoon Blocks
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */

import {THEME_NAME, BLOCK_CLASS, getDateID, colorValueToSlug} from '../../helpers';
import classnames from 'classnames';

const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;
const { InnerBlocks, RichText, InspectorControls, PanelColorSettings, ContrastChecker } = wp.editor;
const { PanelBody, SelectControl, BaseControl } = wp.components;
const { Fragment } = wp.element;
const DEFAULT_MSG = __( 'トグルボックス見出し', THEME_NAME );

//classの取得
function getClasses(color) {
  const classes = classnames(
    {
      'toggle-wrap': true,
      [ `tb-${ colorValueToSlug(color) }` ]: !! colorValueToSlug(color),
      [ 'block-box' ]: true,
    }
  );
  return classes;
}

registerBlockType( 'cocoon-blocks/toggle-box-1', {

  title: __( 'トグルボックス', THEME_NAME ),
  icon: 'randomize',
  category: THEME_NAME + '-block',
  description: __( 'クリックすることでコンテンツ内容の表示を切り替えることができるボックスです。', THEME_NAME ),
  keywords: [ 'toggle', 'box' ],

  attributes: {
    content: {
      type: 'string',
      selector: 'div',
      default: DEFAULT_MSG,
    },
    color: {
      type: 'string',
      default: '',
    },
    dateID: {
      type: 'string',
      default: '',
    },
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

          <PanelColorSettings
            title={ __( '色設定', THEME_NAME ) }
            initialOpen={ true }
            colorSettings={ [
              {
                value: color,
                onChange: ( value ) => setAttributes( { color: value } ),
                label: __( '色設定', THEME_NAME ),
              },
            ] }
          >
            <ContrastChecker
              color={ color }
            />
          </PanelColorSettings>
        </InspectorControls>

        <div className={ getClasses(color) }>
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
      <div className={ getClasses(color) }>
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