/**
 * Cocoon Blocks
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */

import { THEME_NAME, CLICK_POINT_MSG } from '../../helpers';
import classnames from 'classnames';

import { __ } from '@wordpress/i18n';
const { createBlock } = wp.blocks;
const { InnerBlocks, InspectorControls } = wp.editor;
const { PanelBody, SelectControl } = wp.components;
import { Fragment } from '@wordpress/element';

//classの取得
function getClasses( style ) {
  const classes = classnames( {
    [ style ]: !! style,
    'common-icon-box': true,
    [ 'block-box' ]: true,
  } );
  return classes;
}

export default [
  {
    attributes: {
      content: {
        type: 'string',
        default: CLICK_POINT_MSG,
      },
      style: {
        type: 'string',
        default: 'information-box',
      },
    },
    transforms: {
      to: [
        {
          type: 'block',
          blocks: [ 'cocoon-blocks/sticky-box' ],
          transform: ( attributes, innerBlocks ) => {
            return createBlock( 'cocoon-blocks/sticky-box', {}, innerBlocks );
          },
        },
        {
          type: 'block',
          blocks: [ 'cocoon-blocks/blank-box-1' ],
          transform: ( attributes, innerBlocks ) => {
            return createBlock( 'cocoon-blocks/blank-box-1', {}, innerBlocks );
          },
        },
        {
          type: 'block',
          blocks: [ 'cocoon-blocks/tab-box-1' ],
          transform: ( attributes, innerBlocks ) => {
            return createBlock( 'cocoon-blocks/tab-box-1', {}, innerBlocks );
          },
        },
        {
          type: 'block',
          blocks: [ 'cocoon-blocks/info-box' ],
          transform: ( attributes, innerBlocks ) => {
            return createBlock( 'cocoon-blocks/info-box', {}, innerBlocks );
          },
        },
      ],
    },

    edit( { attributes, setAttributes, className } ) {
      const { content, style } = attributes;

      return (
        <Fragment>
          <InspectorControls>
            <PanelBody title={ __( 'スタイル設定', THEME_NAME ) }>
              <SelectControl
                label={ __( 'タイプ', THEME_NAME ) }
                value={ style }
                onChange={ ( value ) => setAttributes( { style: value } ) }
                options={ [
                  {
                    value: 'information-box',
                    label: __( '補足情報(i)', THEME_NAME ),
                  },
                  {
                    value: 'question-box',
                    label: __( '補足情報(?)', THEME_NAME ),
                  },
                  {
                    value: 'alert-box',
                    label: __( '補足情報(!)', THEME_NAME ),
                  },
                  {
                    value: 'memo-box',
                    label: __( 'メモ', THEME_NAME ),
                  },
                  {
                    value: 'comment-box',
                    label: __( 'コメント', THEME_NAME ),
                  },
                  {
                    value: 'ok-box',
                    label: __( 'OK', THEME_NAME ),
                  },
                  {
                    value: 'ng-box',
                    label: __( 'NG', THEME_NAME ),
                  },
                  {
                    value: 'good-box',
                    label: __( 'GOOD', THEME_NAME ),
                  },
                  {
                    value: 'bad-box',
                    label: __( 'BAD', THEME_NAME ),
                  },
                  {
                    value: 'profile-box',
                    label: __( 'プロフィール', THEME_NAME ),
                  },
                ] }
              />
            </PanelBody>
          </InspectorControls>

          <div className={ classnames( getClasses( style ), className ) }>
            <InnerBlocks />
          </div>
        </Fragment>
      );
    },

    save( { attributes } ) {
      const { content, style } = attributes;
      return (
        <div className={ getClasses( style ) }>
          <InnerBlocks.Content />
        </div>
      );
    },
  },
];
