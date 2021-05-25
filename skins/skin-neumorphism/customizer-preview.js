/*テーマカスタマイザーでのリアルタイムプレビュー用設定*/
(function( $ ) {
	var root = $( ':root' );
	wp.customize( 'color_bg', function( value ) {

		value.bind( function( color ) {

			var keyColor = generateNewColor ( color, -0.7 );
			var UsColor = generateNewColor ( color, 0.07 );
			var HkColor = generateNewColor ( color, 0.18 );
			var lightColor = generateNewColor ( color, 0.1 );
			var darkColor = generateNewColor ( color, -0.15 );

			mergeStyle({
				'--color-bg': color,
				'--color-ki': keyColor,
				'--color-us': UsColor,
				'--color-hk': HkColor,
				'--color-shadow-light': lightColor,
				'--color-shadow-dark': darkColor
			});
		});
	});

	function mergeStyle( newStylesObj ) {
		var mergeStylesObj = {};

		var currentStyles = root.attr( 'style' );
		var currentStylesArr = currentStyles ? currentStyles.split( ';' ) : [];

		for ( var i = 0; i < currentStylesArr.length; i++ ) {
			if ( currentStylesArr[i]) {
				var style = currentStylesArr[i].split( ':' );
				var property = style[0].replace( /\s+/g, '' );
				var value = style[1].replace( /\s+/g, '' );
				mergeStylesObj[property] = value;
			}
		}

		Object.keys( newStylesObj ).forEach( function( key ) {
			var property = key.replace( /\s+/g, '' );
			var value = newStylesObj[key].replace( /\s+/g, '' );
			mergeStylesObj[property] = value;
		});

		var mergeStylesStr = '';

		Object.keys( mergeStylesObj ).forEach( function( key ) {
			var style = key + ':' + mergeStylesObj[key] + ';';
			mergeStylesStr += style;
		});

		root.attr( 'style', mergeStylesStr );
	}

	/*影色作成*/
	function generateNewColor( hex, luminance ) {

		hex = hex.slice( 1 );

		if ( 3 === hex.length ) {
			hex = hex.substr( 0, 1 ) + hex.substr( 0, 1 ) + hex.substr( 1, 1 ) + hex.substr( 1, 1 ) + hex.substr( 2, 1 ) + hex.substr( 2, 1 );
		}

		var newHex = '#';

		for ( var i = 0; 3 > i; i++ ) {
			var colorPair = parseInt( hex.substr( i * 2, 2 ), 16 );
			colorPair += colorPair * luminance;
			colorPair = Math.round( Math.min( 245, Math.max( 10, colorPair ) ) ).toString( 16 );
			colorPair = ( '00' + colorPair ).substr( colorPair.length );
			newHex += colorPair;
		}

		return newHex;
	}
}( jQuery ));