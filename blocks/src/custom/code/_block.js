/**
 * Cocoon Blocks
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 * @reference: https://www.webopixel.net/wordpress/1499.html
 * @reference: https://gist.github.com/k-ishiwata/bc1698839c9755ad84eac5a13988f02f
 */
import { THEME_NAME, CODE_LANGUAGES } from '../../helpers';
const { assign } = lodash;
const { __ } = wp.i18n;
const { Fragment } = wp.element;
const { addFilter } = wp.hooks;
const {
  PanelBody,
  SelectControl
} = wp.components;

const {
  InspectorControls,
} = window.wp.editor;

const { createHigherOrderComponent } = wp.compose;

const isValidBlockType = ( name ) => {
  const validBlockTypes = [
    'core/code', //ソースコード
  ];
  return validBlockTypes.includes( name );
};

export const addBlockControl = createHigherOrderComponent( ( BlockEdit ) => {
  let languageValue = '';

  return ( props ) => {
    // isValidBlockType で指定したブロックが選択されたら表示
    if ( isValidBlockType( props.name ) && props.isSelected ) {
      //console.log(props);
      // すでにオプション選択されていたら
      if (props.attributes.language) {
        languageValue = props.attributes.language;
      }
      return (
        <Fragment>
          <BlockEdit { ...props } />
          <InspectorControls>
            <PanelBody title={ __( '言語設定', THEME_NAME ) } initialOpen={ false } className="language-controle">
              <SelectControl
                value={ languageValue }
                options={ CODE_LANGUAGES }
                onChange={ ( value ) => {
                  let newClassName = value;
                  // 高度な設定で入力している場合は追加する
                  if (props.attributes.className) {
                    // 付与されているclassを取り出す
                    let inputClassName = props.attributes.className;
                    // スペース区切りを配列に
                    inputClassName = inputClassName.split(' ');
                    // 選択されていたオプションの値を削除
                    let filterClassName = inputClassName.filter(function(name) {
                      return name !== languageValue;
                    });
                    // 新しく選択したオプションを追加
                    filterClassName.push(value);
                    // 配列を文字列に
                    newClassName = filterClassName.join(' ');
                  }
                  console.log(newClassName);
                  languageValue = value;
                  props.setAttributes({
                    className: newClassName,
                    language: value
                  });
                } }
              />
            </PanelBody>
          </InspectorControls>
        </Fragment>
      );
    }
    return <BlockEdit { ...props } />;
  };
}, 'addCustomCodeBlockControls' );
addFilter( 'editor.BlockEdit', 'cocoon-blocks/code-control', addBlockControl );

export function addAttribute( settings ) {
  if ( isValidBlockType( settings.name ) ) {
    settings.attributes = assign( settings.attributes, {
      language: {
        type: 'string',
      },
    } );
  }
  return settings;
}
addFilter( 'blocks.registerBlockType', 'cocoon-blocks/code-add-attr', addAttribute );

export function addSaveProps( extraProps, blockType, attributes ) {
  if ( isValidBlockType( blockType.name ) ) {
    // なしを選択した場合はlanguage削除
    if (attributes.language === '') {
      delete attributes.language;
    }
  }
  return extraProps;
}
addFilter( 'blocks.getSaveContent.extraProps', 'cocoon-blocks/code-props', addSaveProps );