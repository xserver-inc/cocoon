/*!
 * Copyright (c) 2020 kusamura
 * https://web.monogusa-note.com/
 * Released under the GPL license.
 * see http://www.gnu.org/licenses/gpl-2.0.html
 *
 * 参考: https://www.webopixel.net/wordpress/1499.html
 *
 * 改変: yhira
 * 以下のコードは参考URL内のコードを参考に一部yhiraがコードを編集しています。元のコードを見るには参考URL先を参照してください
 */
/**
 * Cocoon Blocks
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 * @reference: https://www.webopixel.net/wordpress/1499.html
 */

import { THEME_NAME, CODE_LANGUAGES } from '../../helpers';
import { __ } from '@wordpress/i18n';
//コンポーネントの読み込み
import { Fragment } from '@wordpress/element';
const { addFilter } = wp.hooks;
const { PanelBody, SelectControl } = wp.components;
import { InspectorControls } from '@wordpress/block-editor';
const { createHigherOrderComponent } = wp.compose;

//サイドバーパネルの見出し
const TITLE = __( '言語', THEME_NAME );
//基本オプション
const baseOption = [ { label: __( '言語選択', THEME_NAME ), value: '' } ];
//拡張するブロックと各オプション
const myClassSetting = {
  'core/code': CODE_LANGUAGES,
};

//サイドバーの設定
export const addBlockControl = createHigherOrderComponent( ( BlockEdit ) => {
  let selectOption = '';
  return ( props ) => {
    const { className } = props.attributes;
    //myClassSettingで指定したブロックかどうか判定
    const isValidBlockType = ( name ) => {
      const validBlockTypes = Object.keys( myClassSetting );
      return validBlockTypes.includes( name );
    };
    //指定したブロックが選択されたら表示
    if ( isValidBlockType( props.name ) && props.isSelected ) {
      //baseOptionと結合後クラス名だけ抜き出して配列に
      let myClassNames = baseOption
        .concat( myClassSetting[ props.name ] )
        .map( ( { value } ) => value );
      //オプション選択されたクラス名があるか(複数の場合も想定)
      const myClassFind = () => {
        if ( ! className ) return '';
        let myClassSort = myClassNames.slice();
        //クラス名の多い順に並べ替える
        myClassSort.sort(
          ( a, b ) =>
            b.trim().split( /\s+/ ).length - a.trim().split( /\s+/ ).length
        );
        return (
          myClassSort.find( ( name ) => {
            const classArr = className.trim().split( /\s+/ );
            const searchArr = name.trim().split( /\s+/ );
            return searchArr.every( ( v ) => classArr.includes( v ) );
          } ) || ''
        );
      };
      //選択されたオプションを設定
      selectOption = myClassFind();
      //myClassSettingで複数のクラス名設定していた場合を想定して一旦くっつけて配列に戻す
      myClassNames = myClassNames.join( ' ' ).split( /\s+/ );
      //SelectControlのoptionsに設定する用
      const myClassOptions = baseOption.concat( myClassSetting[ props.name ] );
      return (
        <Fragment>
          <BlockEdit { ...props } />
          <InspectorControls>
            <PanelBody
              title={ TITLE }
              initialOpen={ true }
              className="my-classname-controle"
            >
              <SelectControl
                value={ selectOption }
                options={ myClassOptions }
                onChange={ ( changeOption ) => {
                  let newClassName = changeOption;
                  // 高度な設定で入力している場合は追加する
                  if ( className ) {
                    // 付与されているclassを取り出す
                    let inputClassName = className;
                    // スペース区切りを配列に
                    inputClassName = inputClassName.split( /\s+/ );
                    // 選択したオプション以外の自分用クラスを取り除く
                    let filterClassName = inputClassName.filter(
                      ( name ) => ! myClassNames.includes( name )
                    );
                    // 新しく選択したオプションを追加
                    filterClassName.push( changeOption );
                    // 配列を文字列に
                    newClassName = filterClassName.join( ' ' );
                  }
                  selectOption = changeOption;
                  props.setAttributes( { className: newClassName } );
                } }
                __nextHasNoMarginBottom={ true }
              />
            </PanelBody>
          </InspectorControls>
        </Fragment>
      );
    }
    return <BlockEdit { ...props } />;
  };
}, 'addMyCustomBlockControls' );
addFilter( 'editor.BlockEdit', 'cocoon-blocks/code-props', addBlockControl );
