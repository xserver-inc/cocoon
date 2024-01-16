/**
 * Cocoon Blocks
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */

import { THEME_NAME, LAYOUT_BLOCK_CLASS } from '../../helpers';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { faSquare } from '@fortawesome/free-regular-svg-icons';
import classnames from 'classnames';

import { __ } from '@wordpress/i18n';
import { registerBlockType } from '@wordpress/blocks';
import { InnerBlocks, useBlockProps } from '@wordpress/block-editor';
import { Fragment } from '@wordpress/element';

//左カラム
registerBlockType( 'cocoon-blocks/column-left', {
  apiVersion: 2,
  title: __( '左カラム', THEME_NAME ),
  parent: [ 'cocoon-blocks/column-2', 'cocoon-blocks/column-3' ],
  icon: <FontAwesomeIcon icon={ faSquare } />,
  category: THEME_NAME + '-layout',
  description: __( 'カラム左側に表示される内容内容を入力。', THEME_NAME ),
  supports: {
    inserter: false,
  },
  example: {},

  edit( { className } ) {
    const classes = classnames( className, {
      'column-left': true,
    } );

    const blockProps = useBlockProps( {
      className: classes,
    } );

    return (
      <Fragment>
        <div { ...blockProps }>
          <InnerBlocks templateLock={ false } />
        </div>
      </Fragment>
    );
  },

  save( { className } ) {
    const classes = classnames( className, {
      'column-left': true,
    } );

    const blockProps = useBlockProps.save( {
      className: classes,
    } );

    return (
      <div { ...blockProps }>
        <InnerBlocks.Content />
      </div>
    );
  },
} );

//中央カラム
registerBlockType( 'cocoon-blocks/column-center', {
  apiVersion: 2,
  title: __( '中央カラム', THEME_NAME ),
  parent: [ 'cocoon-blocks/column-2', 'cocoon-blocks/column-3' ],
  icon: <FontAwesomeIcon icon={ faSquare } />,
  category: THEME_NAME + '-layout',
  description: __( 'カラム中央に表示される内容内容を入力。', THEME_NAME ),
  supports: {
    inserter: false,
  },
  example: {},

  edit( { className } ) {
    const classes = classnames( className, {
      'column-center': true,
    } );

    const blockProps = useBlockProps( {
      className: classes,
    } );

    return (
      <Fragment>
        <div { ...blockProps }>
          <InnerBlocks templateLock={ false } />
        </div>
      </Fragment>
    );
  },

  save( { className } ) {
    const classes = classnames( className, {
      'column-center': true,
    } );

    const blockProps = useBlockProps.save( {
      className: classes,
    } );

    return (
      <div { ...blockProps }>
        <InnerBlocks.Content />
      </div>
    );
  },
} );

//右カラム
registerBlockType( 'cocoon-blocks/column-right', {
  apiVersion: 2,
  title: __( '右カラム', THEME_NAME ),
  parent: [ 'cocoon-blocks/column-2', 'cocoon-blocks/column-3' ],
  icon: <FontAwesomeIcon icon={ faSquare } />,
  category: THEME_NAME + '-layout',
  description: __( 'カラム右側に表示される内容内容を入力。', THEME_NAME ),
  supports: {
    inserter: false,
  },
  example: {},

  edit( { className } ) {
    const classes = classnames( className, {
      'column-right': true,
    } );

    const blockProps = useBlockProps( {
      className: classes,
    } );

    return (
      <Fragment>
        <div { ...blockProps }>
          <InnerBlocks templateLock={ false } />
        </div>
      </Fragment>
    );
  },

  save( { className } ) {
    const classes = classnames( className, {
      'column-right': true,
    } );

    const blockProps = useBlockProps.save( {
      className: classes,
    } );

    return (
      <div { ...blockProps }>
        <InnerBlocks.Content />
      </div>
    );
  },
} );
