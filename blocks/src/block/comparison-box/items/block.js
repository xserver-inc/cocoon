/**
 * Cocoon Blocks
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */

import { THEME_NAME } from '../../../helpers';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { faSquare } from '@fortawesome/free-regular-svg-icons';

import { __ } from '@wordpress/i18n';
import { registerBlockType } from '@wordpress/blocks';
import {
  InnerBlocks,
  useBlockProps,
  __experimentalUseInnerBlocksProps as useInnerBlocksProps,
} from '@wordpress/block-editor';
import { Fragment } from '@wordpress/element';
import classnames from 'classnames';

const ALLOWED_BLOCKS = [ 'cocoon-blocks/iconlist-box' ];
const TEMPLATE = [ 'cocoon-blocks/iconlist-box' ];

//左カラム
registerBlockType( 'cocoon-blocks/comparison-left', {
  apiVersion: 2,
  title: __( '左側', THEME_NAME ),
  parent: [ 'cocoon-blocks/comparison-box' ],
  icon: <FontAwesomeIcon icon={ faSquare } />,
  category: 'cocoon-block',
  description: __( '比較ボックスの左。', THEME_NAME ),
  supports: {
    inserter: false,
  },

  edit( { attributes, setAttributes, className } ) {
    const innerBlocksProps = useInnerBlocksProps( {
      allowedBlocks: ALLOWED_BLOCKS,
      template: TEMPLATE,
    } );

    return <div { ...innerBlocksProps } />;
  },

  save( { attributes } ) {
    return <InnerBlocks.Content />;
  },
} );

//右カラム
registerBlockType( 'cocoon-blocks/comparison-right', {
  apiVersion: 2,
  title: __( '右側', THEME_NAME ),
  parent: [ 'cocoon-blocks/comparison-box' ],
  icon: <FontAwesomeIcon icon={ faSquare } />,
  category: 'cocoon-block',
  description: __( '比較ボックスの右。', THEME_NAME ),
  supports: {
    inserter: false,
  },

  edit( { attributes, setAttributes } ) {
    const innerBlocksProps = useInnerBlocksProps( {
      allowedBlocks: ALLOWED_BLOCKS,
      template: TEMPLATE,
    } );

    return <div { ...innerBlocksProps } />;
  },

  save( { attributes } ) {
    return <InnerBlocks.Content />;
  },
} );
