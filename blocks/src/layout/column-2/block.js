/**
 * Cocoon Blocks
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */

import {THEME_NAME, LAYOUT_BLOCK_CLASS} from '../../helpers';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import classnames from 'classnames';

import { __ } from '@wordpress/i18n';
import { registerBlockType } from '@wordpress/blocks';
import { InnerBlocks, InspectorControls, useBlockProps } from '@wordpress/block-editor';
import { PanelBody, SelectControl } from '@wordpress/components';
import { Fragment } from '@wordpress/element';

const ALLOWED_BLOCKS = [ 'cocoon-blocks/column-left', 'cocoon-blocks/column-right' ];

//classの取得
function getClasses(ratio) {
  const classes = classnames(
    {
      [ 'column-wrap' ]: true,
      [ 'column-2' ]: true,
      [ ratio ]: !! ratio,
      [ LAYOUT_BLOCK_CLASS ]: true,
    }
  );
  return classes;
}

registerBlockType( 'cocoon-blocks/column-2', {

  apiVersion: 2,
  name: 'cocoon-blocks/column-2',
  title: __( '2カラム', THEME_NAME ),
  icon: <FontAwesomeIcon icon="columns" />,
  category: THEME_NAME + '-layout',
  description: __( '本文を左右カラムに分けます。オプションでカラム比率を変更できます。', THEME_NAME ),
  keywords: [ 'column', '2' ],

  attributes: {
    ratio: {
      type: 'string',
      default: 'column-2-2-1-1',
    },
  },

  edit( { attributes, setAttributes, className } ) {
    const { ratio } = attributes;

    const classes = classnames('column-wrap', 'column-2', 'layout-box',
      {
        [ ratio ]: !! ratio,
        [ className ]: !! className,
      }
    );
    const blockProps = useBlockProps({
      className: classes,
    });
    return (
      <Fragment>
        <InspectorControls>
          <PanelBody title={ __( 'スタイル設定', THEME_NAME ) }>

            <SelectControl
              label={ __( 'カラム比率', THEME_NAME ) }
              value={ ratio }
              onChange={ ( value ) => setAttributes( { ratio: value } ) }
              options={ [
                {
                  value: 'column-2-2-1-1',
                  label: __( '1:1（｜□｜□｜）', THEME_NAME ),
                },
                {
                  value: 'column-2-3-1-2',
                  label: __( '1:2（｜□｜□□｜）', THEME_NAME ),
                },
                {
                  value: 'column-2-3-2-1',
                  label: __( '2:1（｜□□｜□｜）', THEME_NAME ),
                },
                {
                  value: 'column-2-4-1-3',
                  label: __( '1:3（｜□｜□□□｜））', THEME_NAME ),
                },
                {
                  value: 'column-2-4-3-1',
                  label: __( '3:1,（｜□□□｜□｜）', THEME_NAME ),
                },
              ] }
            />

          </PanelBody>
        </InspectorControls>
        <div { ...blockProps}>
          <InnerBlocks
          template={[
              [ 'cocoon-blocks/column-left', { placeholder: __( '左側に入力する内容', THEME_NAME ) } ],
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
    const { ratio } = attributes;
    const classes = classnames('column-wrap', 'column-2', 'layout-box',
      {
        [ ratio ]: !! ratio,
      }
    );
    const blockProps = useBlockProps.save({
      className: classes,
    });
    return (
      <div  className={getClasses(ratio)}>
        <InnerBlocks.Content />
      </div>
    );
  }
} );
