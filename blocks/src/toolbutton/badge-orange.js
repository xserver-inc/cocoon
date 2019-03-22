/**
 * Cocoon Blocks
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */

// const { Fragment } = wp.element;
const { __ } = wp.i18n;
// const { registerBlockType } = wp.blocks;
const { registerFormatType, toggleFormat } = wp.richText;
// const { RichTextShortcut, RichTextToolbarButton, InspectorControls } = wp.editor;
// const { PanelBody, SelectControl, BaseControl } = wp.components;
const THEME_NAME = 'cocoon';
const FORMAT_TYPE_NAME = 'cocoon-blocks/badge';
// import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';

registerFormatType( FORMAT_TYPE_NAME, {
  title: __( 'オレンジ', THEME_NAME ),
  tagName: 'span',
  className: 'badge',
  // attributes: {
  //   color: {
  //     type: 'string',
  //     default: 'blank-box',
  //   },
  // },
  // edit( { /*attributes, setAttributes,*/ isActive, value, onChange } ) {
  //   //const { color } = attributes;
  //   // const onToggle = () => onChange( toggleFormat( value, { type: FORMAT_TYPE_NAME } ) );
  //   const onToggle = () => {
  //     let color = 'test';
  //     return onChange( toggleFormat( value, {
  //       type: FORMAT_TYPE_NAME,
  //       attributes: {
  //         color: color,
  //       }
  //     } ) );
  //   };
  //   console.log(value);
  //   return (
  //     <Fragment>
  //       <InspectorControls>
  //         <PanelBody title={ __( 'スタイル設定', THEME_NAME ) }>

  //           <SelectControl
  //             label={ __( 'タイプ', THEME_NAME ) }
  //             // value={ color }
  //             // onChange={ ( value ) => setAttributes( { color: value } ) }
  //             options={ [
  //               {
  //                 value: 'blank-box',
  //                 label: __( '灰色', THEME_NAME ),
  //               },
  //               {
  //                 value: 'blank-box bb-yellow',
  //                 label: __( '黄色', THEME_NAME ),
  //               },
  //               {
  //                 value: 'blank-box bb-red',
  //                 label: __( '赤色', THEME_NAME ),
  //               },
  //               {
  //                 value: 'blank-box bb-blue',
  //                 label: __( '青色', THEME_NAME ),
  //               },
  //               {
  //                 value: 'blank-box bb-green',
  //                 label: __( '緑色', THEME_NAME ),
  //               },
  //             ] }
  //           />

  //         </PanelBody>
  //       </InspectorControls>

  //       <RichTextShortcut
  //         type='primary'
  //         character=''
  //         onUse={ onToggle }
  //       />
  //       <RichTextToolbarButton
  //         title={ __( 'バッジ', THEME_NAME ) }
  //         icon={ <FontAwesomeIcon icon="square" /> }
  //         onClick={ onToggle }
  //         isActive={ isActive }
  //         shortcutType='primary'
  //         shortcutCharacter=''
  //       />
  //     </Fragment>
  //   );
  // },
} );