/**
 * Cocoon Blocks
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */

const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;
const { InnerBlocks/*, RichText, InspectorControls*/ } = wp.editor;
// const { PanelBody, SelectControl, BaseControl } = wp.components;
const { Fragment } = wp.element;
const THEME_NAME = 'cocoon';
//const DEFAULT_MSG = __( 'キーワード', THEME_NAME );
const BLOCK_CLASS = ' layout-box';

registerBlockType( 'cocoon-blocks/column-2-1-1', {

  title: __( '2カラム（1:1）', THEME_NAME ),
  icon: 'grid-view',
  category: THEME_NAME + '-layout',

  attributes: {
    content: {
      type: 'string',
      //default: DEFAULT_MSG,
    },
  },

  edit( { attributes, setAttributes } ) {
    return (
      <Fragment>
        <div className={"column-wrap column-2" + BLOCK_CLASS}>
          <div className="column-left">
            <InnerBlocks />
          </div>
          <div className="column-right">
            <InnerBlocks />
          </div>
        </div>
      </Fragment>
    );
  },

  save( { attributes } ) {
    return (
      <div className={"column-wrap column-2" + BLOCK_CLASS}>
        <div className="column-left">
          <InnerBlocks.Content />
        </div>
        <div className="column-right">
          <InnerBlocks.Content />
        </div>
      </div>
    );
  }
} );