/**
 * Cocoon Blocks
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */

 import classnames from 'classnames';

 // const { __ } = wp.i18n;
 // const { registerBlockType, createBlock } = wp.blocks;
 // const { InnerBlocks, RichText, InspectorControls, useBlockProps } = wp.blockEditor;
 // const { PanelBody, SelectControl, BaseControl } = wp.components;
 // const { Fragment } = wp.element;

 import { registerBlockType } from '@wordpress/blocks';
 import { InnerBlocks,  useBlockProps } from '@wordpress/block-editor';
 import { Fragment } from '@wordpress/element';


 registerBlockType( 'cocoon-blocks/test-box', {

   apiVersion: 2,
   title: 'テストボックス',
   // icon: <FontAwesomeIcon icon={['fas', 'exclamation-circle']} />,
   category: 'cocoon-block',
   description: 'テストボックスです。',
   keywords: [ 'test', 'box' ],

   edit( { attributes, className } ) {
     const classes = classnames('test', className);
     const blockProps = useBlockProps({
       className: classes,
     });

     return (
       <Fragment>

         <div { ...blockProps }>
           <InnerBlocks />
         </div>
       </Fragment>
     );
   },

   save( { attributes } ) {
     const classes = 'test';
     const blockProps = useBlockProps.save({
       className: classes,
     });
     return (
       <div { ...blockProps }>
         <InnerBlocks.Content />
       </div>
     );
   },

 });
