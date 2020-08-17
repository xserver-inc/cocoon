/**
 * Cocoon Blocks
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */

import {THEME_NAME, BLOCK_CLASS, colorValueToSlug} from '../../helpers';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import classnames from 'classnames';

const { __ } = wp.i18n;
const { registerBlockType, createBlock } = wp.blocks;
const { InnerBlocks, RichText, InspectorControls, PanelColorSettings, ContrastChecker } = wp.editor;
//const { PanelBody, SelectControl, BaseControl } = wp.components;
const { Fragment } = wp.element;
const DEFAULT_MSG = __( 'こちらをクリックして設定変更。この入力は公開ページで反映されません。', THEME_NAME );

//classの取得
function getClasses(borderColor) {
  const classes = classnames(
    {
      'blank-box': true,
      [ `bb-${ colorValueToSlug(borderColor) }` ]: !! colorValueToSlug(borderColor),
      [ 'block-box' ]: true,
    }
  );
  return classes;
}

registerBlockType( 'cocoon-blocks/blank-box-1', {

  title: __( '白抜きボックス', THEME_NAME ),
  icon: <FontAwesomeIcon icon={['far', 'square']} />,
  category: THEME_NAME + '-block',
  description: __( 'コンテンツを囲むだけのブランクボックスを表示します。', THEME_NAME ),
  keywords: [ 'blank', 'box' ],

  attributes: {
    content: {
      type: 'string',
      default: DEFAULT_MSG,
    },
    borderColor: {
      type: 'string',
      default: '',
    },
  },
  // transforms: {
  //   to: [
  //     {
  //       type: 'block',
  //       blocks: [ 'cocoon-blocks/sticky-box' ],
  //       transform: ( attributes ) => {
  //         return createBlock( 'cocoon-blocks/sticky-box', attributes );
  //       },
  //     },
  //     {
  //       type: 'block',
  //       blocks: [ 'cocoon-blocks/tab-box-1' ],
  //       transform: ( attributes ) => {
  //         return createBlock( 'cocoon-blocks/tab-box-1', attributes );
  //       },
  //     },
  //     // {
  //     //   type: 'block',
  //     //   blocks: [ 'cocoon-blocks/icon-box' ],
  //     //   transform: ( attributes ) => {
  //     //     return createBlock( 'cocoon-blocks/icon-box', attributes );
  //     //   },
  //     // },
  //     // {
  //     //   type: 'block',
  //     //   blocks: [ 'cocoon-blocks/info-box' ],
  //     //   transform: ( attributes ) => {
  //     //     return createBlock( 'cocoon-blocks/info-box', attributes );
  //     //   },
  //     // },
  //   ],
  // },

  edit( { attributes, setAttributes } ) {
    const { content, borderColor } = attributes;

    return (
      <Fragment>
        <InspectorControls>

          <PanelColorSettings
            title={ __( '色設定', THEME_NAME ) }
            initialOpen={ true }
            colorSettings={ [
              {
                value: borderColor,
                onChange: ( value ) => setAttributes( { borderColor: value } ),
                label: __( 'ボーダー色', THEME_NAME ),
              },
            ] }
          >
            <ContrastChecker
              borderColor={ borderColor }
            />
          </PanelColorSettings>

        </InspectorControls>

        <div className={ getClasses(borderColor) }>
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
    const { content, borderColor } = attributes;
    return (
      <div className={ getClasses(borderColor) }>
        <InnerBlocks.Content />
      </div>
    );
  }
} );