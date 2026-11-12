import { createElement, Fragment } from '@wordpress/element';
import fs from 'fs';
import path from 'path';
import vm from 'vm';

const source = fs.readFileSync(
  path.join( __dirname, '../dist/legacy-jsx-runtime.js' ), 'utf8'
);

// 配布用JSと実際のReact要素生成APIを使った旧WordPressの再現
function loadRuntime( existing ) {
  const context = { window: { wp: { element: { createElement, Fragment } } } };
  if ( existing ) context.window.ReactJSXRuntime = existing;
  vm.runInNewContext( source, context );
  return context.window.ReactJSXRuntime;
}

describe( '旧WordPress向けのJSXランタイム', () => {
  test( '既存の標準ランタイムを変更しない', () => {
    const existing = { jsx: () => null };
    expect( loadRuntime( existing ) ).toBe( existing );
  } );

  test( '属性と日本語の子要素を保持し、入力属性を変更しない', () => {
    const props = { className: 'btn', children: '日本語' };
    const actual = loadRuntime().jsx( 'a', props, 'button-key' );
    expect( actual.type ).toBe( 'a' );
    expect( actual.key ).toBe( 'button-key' );
    expect( actual.props ).toEqual( props );
    expect( props ).not.toHaveProperty( 'key' );
  } );

  test.each( [
    [ 'props-key', 'props-key' ],
    [ undefined, 'argument-key' ],
    [ null, 'null' ],
  ] )( '属性側のkey=%sと引数keyの優先順位を維持する', ( key, expected ) => {
    const actual = loadRuntime().jsx( 'div', { key }, 'argument-key' );
    expect( actual.key ).toBe( expected );
  } );

  test( 'refと既定属性を保持する', () => {
    const Component = () => null;
    Component.defaultProps = { title: '既定値' };
    const ref = { current: null };
    const actual = loadRuntime().jsx( Component, { ref } );
    expect( actual.ref ).toBe( ref );
    expect( actual.props.title ).toBe( '既定値' );
  } );

  test( 'Fragmentと複数の子要素を保持する', () => {
    const runtime = loadRuntime();
    const children = [ runtime.jsx( 'span', { children: '左' }, 'left' ), runtime.jsx( 'span', { children: '右' }, 'right' ) ];
    const actual = runtime.jsxs( runtime.Fragment, { children } );
    expect( actual.type ).toBe( Fragment );
    expect( actual.props.children ).toBe( children );
  } );

  test( 'JSX属性がない要素も生成できる', () => {
    expect( loadRuntime().jsx( 'hr', null ).props ).toEqual( {} );
  } );
} );
