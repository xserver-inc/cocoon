/**
 * Cocoon Blocks
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */

import {THEME_NAME, LAYOUT_BLOCK_CLASS} from '../../helpers';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import classnames from 'classnames';

const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;
const { InnerBlocks, InspectorControls } = wp.editor;
const { PanelBody, SelectControl, Path, SVG } = wp.components;
const { Fragment } = wp.element;

const ALLOWED_BLOCKS = [ 'cocoon-blocks/column-left', 'cocoon-blocks/column-center', 'cocoon-blocks/column-right' ];

//classの取得
function getClasses() {
  const classes = classnames(
    {
      [ 'column-wrap' ]: true,
      [ 'column-3' ]: true,
      [ LAYOUT_BLOCK_CLASS ]: true,
    }
  );
  return classes;
}

registerBlockType( 'cocoon-blocks/column-3', {

  title: __( '3カラム', THEME_NAME ),
  icon: <svg width="24" height="24" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" role="img" aria-hidden="true" focusable="false"><path fill="none" d="M0 0h24v24H0V0z"></path><g><path d="M21 4H3L2 5v14l1 1h18l1-1V5l-1-1zM8 18H4V6h4v12zm6 0h-4V6h4v12zm6 0h-4V6h4v12z"></path></g></svg>,
  category: THEME_NAME + '-layout',
  description: __( '本文を左・中央・右カラムに分けます。', THEME_NAME ),
  keywords: [ 'column', '3' ],


  edit( { attributes, setAttributes, className } ) {
    return (
      <Fragment>
        <div className={ classnames(getClasses(), className) }>
          <InnerBlocks
          template={[
              [ 'cocoon-blocks/column-left', { placeholder: __( '左側に入力する内容', THEME_NAME ) } ],
              [ 'cocoon-blocks/column-center', { placeholder: __( '中央に入力する内容', THEME_NAME ) } ],
              [ 'cocoon-blocks/column-right', { placeholder: __( '右側に入力する内容', THEME_NAME ) } ]
          ]}
          templateLock="all"
          allowedBlocks={ ALLOWED_BLOCKS }
           />
        </div>
      </Fragment>
    );
  },

  save( { attributes } ) {
    return (
      <div className={ getClasses() }>
        <InnerBlocks.Content />
      </div>
    );
  }
} );