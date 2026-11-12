import { createElement, Fragment } from '@wordpress/element';

// WordPress 6.5以前の公開APIを使ったJSX要素の生成
function jsx( type, props, key ) {
  // 属性側のkeyが未設定の場合だけの引数keyの補完
  const elementProps = key !== undefined && props?.key === undefined
    ? { ...props, key }
    : props;
  return createElement( type, elementProps );
}

// WordPress本体やGutenbergが提供する標準ランタイムの優先
if ( ! window.ReactJSXRuntime ) {
  window.ReactJSXRuntime = { jsx, jsxs: jsx, Fragment };
}
