const fs = require( 'fs' );
const path = require( 'path' );
const React = require( 'react' );
const { renderToStaticMarkup } = require( 'react-dom/server.node' );
const postcss = require( 'postcss' );

// 区切り線を含む設定パネルと標準属性の一覧
const panelCases = [
	[ 'ナビカード', './block/navicard', 1 ],
	[ '新着記事', './block/new-list', 3 ],
	[ '人気記事', './block/popular-list', 2 ],
	[ 'タブ', './block/tab', 2 ],
];

// 設定パネルのReact要素を実体化するための依存分離
function installMocks( hasLegacyDivider ) {
	jest.doMock( '@wordpress/i18n', () => ( { __: ( text ) => text } ) );
	jest.doMock( '@wordpress/element', () => ( {
		Fragment: React.Fragment,
		RawHTML: ( { children } ) => children,
		useState: ( value ) => [ value, jest.fn() ],
		useEffect: jest.fn(),
	} ) );
	jest.doMock( '@wordpress/block-editor', () => ( {
		InspectorControls: 'mock-inspector-controls',
		InnerBlocks: 'mock-inner-blocks',
		useBlockProps: () => ( {} ),
	} ) );
	jest.doMock( '@wordpress/components', () => ( {
		...Object.fromEntries(
			[
				'SelectControl',
				'PanelBody',
				'TextControl',
				'ToggleControl',
				'SearchControl',
				'RangeControl',
				'Disabled',
				'Button',
			].map( ( name ) => [ name, `mock-${ name }` ] )
		),
		// 旧APIの存在に左右されない設定パネルの検証条件
		...( hasLegacyDivider
			? { __experimentalDivider: 'mock-legacy-divider' }
			: {} ),
	} ) );
	jest.doMock( '@wordpress/data', () => ( {
		useSelect: ( callback ) =>
			callback( () => ( { getEntityRecords: () => [] } ) ),
		useDispatch: () => ( {
			replaceInnerBlocks: jest.fn(),
			updateBlockAttributes: jest.fn(),
		} ),
	} ) );
	jest.doMock( '@wordpress/blocks', () => ( { createBlock: jest.fn() } ) );
	jest.doMock( './helpers', () => ( {
		THEME_NAME: 'cocoon',
		CreateCategoryList: () => null,
	} ) );
	jest.doMock( './server-side-render', () => ( {
		__esModule: true,
		default: 'mock-server-side-render',
	} ) );
}

// 未定義の部品を検出しながら区切り線を数えるReact要素木の探索
function collectSeparators( element, separators = [] ) {
	if ( Array.isArray( element ) ) {
		element.forEach( ( child ) => collectSeparators( child, separators ) );
	} else if ( React.isValidElement( element ) ) {
		expect( element.type ).toBeDefined();
		if ( typeof element.type === 'function' ) {
			collectSeparators( element.type( element.props ), separators );
		} else {
			if ( element.type === 'hr' ) separators.push( element );
			collectSeparators( element.props.children, separators );
		}
	}
	return separators;
}

describe.each( [ true, false ] )(
	'WordPressの旧Divider公開状態: %s',
	( hasLegacyDivider ) => {
		beforeEach( () => {
			jest.resetModules();
			installMocks( hasLegacyDivider );
			globalThis.gbNavMenus = [];
		} );
		afterEach( () => {
			delete globalThis.gbNavMenus;
		} );

		test.each( panelCases )(
			'%sの設定パネルを旧APIに依存せず表示できる',
			( label, directory, count ) => {
				const metadata = require( `${ directory }/block.json` );
				// ブロック定義の初期値を使った設定パネルの入力
				const attributes = Object.fromEntries(
					Object.entries( metadata.attributes ).map(
						( [ name, definition ] ) => [ name, definition.default ]
					)
				);
				const edit = require( `${ directory }/edit` ).default;
				const tree = edit( {
					attributes,
					setAttributes: jest.fn(),
					clientId: 'review-block',
				} );
				expect( collectSeparators( tree ) ).toHaveLength( count );
				// 既存のタブ描画にあるkey未指定警告だけの許容
				if ( directory === './block/tab' ) {
					expect( console.error.mock.calls ).toEqual(
						[ '<mock-PanelBody>', '<ul>' ].map( ( parent ) => [
							expect.stringContaining(
								'Each child in a list should have a unique "key" prop.'
							),
							expect.stringContaining( parent ),
							'',
							expect.any( String ),
						] )
					);
					expect( console ).toHaveErrored();
				}
			}
		);
	}
);

test( 'エディター装飾CSSがなくても区切り線の表示と意味を保持できる', () => {
	const Divider = require( './components/Divider' ).default;
	document.body.innerHTML = renderToStaticMarkup(
		React.createElement( Divider )
	);
	const divider = document.querySelector( 'hr' );
	expect( divider ).not.toBeNull();
	const styles = window.getComputedStyle( divider );
	expect( styles.borderTopWidth ).toBe( '0px' );
	expect( styles.borderBottomWidth ).toBe( '1px' );
	expect( styles.borderBottomStyle ).toBe( 'solid' );
	expect( styles.marginTop ).toBe( '0px' );
} );

describe( '入力欄の接尾要素のWordPress互換性', () => {
	afterEach( () => {
		document.head
			.querySelectorAll( 'style[data-cocoon-compat-test]' )
			.forEach( ( style ) => style.remove() );
		document.body.innerHTML = '';
	} );

	test.each( [
		[ 'Emotionを使用する旧版', 'em5sgkm6 css-legacy' ],
		[ 'CSS Modulesを使用する7.2', '_module-suffix' ],
	] )( '%sでも公開CSSの余白補正が有効になる', ( label, generatedClasses ) => {
		const css = fs.readFileSync(
			path.resolve( __dirname, '../../css/gutenberg-editor.css' ),
			'utf8'
		);
		const rules = [];
		// 配布CSSから接尾要素の補正ルールだけを取り出した表示条件
		postcss.parse( css ).walkRules( ( rule ) => {
			if (
				/components-input-control__suffix|em5sgkm6/.test(
					rule.selector
				)
			)
				rules.push( rule.toString() );
		} );
		const style = document.createElement( 'style' );
		style.dataset.cocoonCompatTest = '';
		style.textContent = rules.join( '\n' );
		document.head.appendChild( style );
		document.body.innerHTML = `<div class="components-input-control"><span class="components-input-control__suffix ${ generatedClasses }">px</span></div>`;
		expect(
			window.getComputedStyle( document.querySelector( 'span' ) )
				.marginBottom
		).toBe( '8px' );
	} );
} );
