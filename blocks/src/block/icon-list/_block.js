/**
 * Cocoon Blocks
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */

import { THEME_NAME, BLOCK_CLASS, LIST_ICONS, colorValueToSlug } from '../../helpers';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import classnames from 'classnames';

const { times } = lodash;
const { __ } = wp.i18n;
//const { applyFormat, removeFormat, getActiveFormat } = window.wp.richText;
const { registerBlockType } = wp.blocks;
const { InnerBlocks, RichText, InspectorControls, PanelColorSettings, ContrastChecker/*, getColorObjectByColorValue*/ } = wp.editor;
const { PanelBody, SelectControl, BaseControl, Button } = wp.components;
//const { select } = wp.data;
const { Fragment } = wp.element;
const ALLOWED_BLOCKS = [ 'core/list' ];

//classの取得
function getClasses(icon, iconColor, borderColor) {
  const classes = classnames(
    {
      'iconlist-box': true,
      [ icon ]: !! icon,
      [ `iic-${ colorValueToSlug(iconColor) }` ]: !! colorValueToSlug(iconColor),
      [ `blank-box bb-${ colorValueToSlug(borderColor) }` ]: !! colorValueToSlug(borderColor),
      [ 'block-box' ]: true,
    }
  );
  return classes;
}

registerBlockType( 'cocoon-blocks/iconlist-box', {

  title: __( 'アイコンリスト', THEME_NAME ),
  icon: <FontAwesomeIcon icon={['far', 'list-alt']} />,
  category: THEME_NAME + '-block',
  description: __( 'リストポイントにアイコンを適用した非順序リストです。', THEME_NAME ),
  keywords: [ 'icon', 'list', 'box' ],

  attributes: {
    title: {
      type: 'string',
      default: '',
    },
    iconColor: {
      type: 'string',
      default: '',
    },
    borderColor: {
      type: 'string',
      default: '',
    },
    icon: {
      type: 'string',
      default: 'list-caret-right',
    },
  },

  edit( { attributes, setAttributes } ) {
    const { title, icon, iconColor, borderColor } = attributes;
    //console.log(borderColor);

    // const borderColorStyles = {
    //   borderColor: borderColor || undefined,
    // };
    return (
      <Fragment>
        <InspectorControls>
          <PanelBody title={ __( 'スタイル設定', THEME_NAME ) }>

            <BaseControl label={ __( 'アイコン', THEME_NAME ) }>
              <div className="icon-setting-buttons">
                { times( LIST_ICONS.length, ( index ) => {
                  return (
                    <Button
                      isDefault
                      isPrimary={ icon === LIST_ICONS[index].value }
                      className={LIST_ICONS[index].label}
                      onClick={ () => {
                        setAttributes( { icon: LIST_ICONS[index].value } );
                      } }
                    >
                    </Button>
                  );
                } ) }
              </div>
            </BaseControl>
          </PanelBody>

          <PanelColorSettings
            title={ __( '色設定', THEME_NAME ) }
            initialOpen={ true }
            colorSettings={ [
              {
                value: iconColor,
                onChange: ( value ) => setAttributes( { iconColor: value } ),
                label: __( 'アイコン色', THEME_NAME ),
              },
              {
                value: borderColor,
                onChange: ( value ) => setAttributes( { borderColor: value } ),
                label: __( 'ボーダー色', THEME_NAME ),
              },
            ] }
          >
          <ContrastChecker
            iconColor={ iconColor }
            borderColor={ borderColor }
          />
          </PanelColorSettings>

        </InspectorControls>
        <div
          className={ getClasses(icon, iconColor, borderColor) }
          //style={ borderColorStyles }
        >
          <div className="iconlist-title">
            <RichText
                value={ title }
                placeholder={__( 'タイトルがある場合は入力', THEME_NAME )}
                onChange={ ( value ) => setAttributes( { title: value } ) }
              />
          </div>
          <InnerBlocks
          template={[
              [ 'core/list' ]
          ]}
          templateLock="all"
          allowedBlocks={ ALLOWED_BLOCKS }
           />
        </div>
      </Fragment>
    );
  },

  save( { attributes } ) {
    const { title, icon, iconColor, borderColor } = attributes;

    return (
      <div className={ getClasses(icon, iconColor, borderColor) }>
        <div className="iconlist-title">
          <RichText.Content
            value={ title }
          />
        </div>
        <InnerBlocks.Content />
      </div>
    );
  }
} );