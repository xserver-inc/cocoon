/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, {
/******/ 				configurable: false,
/******/ 				enumerable: true,
/******/ 				get: getter
/******/ 			});
/******/ 		}
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "";
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = 11);
/******/ })
/************************************************************************/
/******/ ({

/***/ 1:
/*!******************************************!*\
  !*** ./node_modules/classnames/index.js ***!
  \******************************************/
/*! dynamic exports provided */
/*! exports used: default */
/***/ (function(module, exports, __webpack_require__) {

eval("var __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;/*!\n  Copyright (c) 2018 Jed Watson.\n  Licensed under the MIT License (MIT), see\n  http://jedwatson.github.io/classnames\n*/\n/* global define */\n\n(function () {\n\t'use strict';\n\n\tvar hasOwn = {}.hasOwnProperty;\n\n\tfunction classNames() {\n\t\tvar classes = [];\n\n\t\tfor (var i = 0; i < arguments.length; i++) {\n\t\t\tvar arg = arguments[i];\n\t\t\tif (!arg) continue;\n\n\t\t\tvar argType = typeof arg;\n\n\t\t\tif (argType === 'string' || argType === 'number') {\n\t\t\t\tclasses.push(arg);\n\t\t\t} else if (Array.isArray(arg)) {\n\t\t\t\tif (arg.length) {\n\t\t\t\t\tvar inner = classNames.apply(null, arg);\n\t\t\t\t\tif (inner) {\n\t\t\t\t\t\tclasses.push(inner);\n\t\t\t\t\t}\n\t\t\t\t}\n\t\t\t} else if (argType === 'object') {\n\t\t\t\tif (arg.toString === Object.prototype.toString) {\n\t\t\t\t\tfor (var key in arg) {\n\t\t\t\t\t\tif (hasOwn.call(arg, key) && arg[key]) {\n\t\t\t\t\t\t\tclasses.push(key);\n\t\t\t\t\t\t}\n\t\t\t\t\t}\n\t\t\t\t} else {\n\t\t\t\t\tclasses.push(arg.toString());\n\t\t\t\t}\n\t\t\t}\n\t\t}\n\n\t\treturn classes.join(' ');\n\t}\n\n\tif (typeof module !== 'undefined' && module.exports) {\n\t\tclassNames.default = classNames;\n\t\tmodule.exports = classNames;\n\t} else if (true) {\n\t\t// register as 'classnames', consistent with npm package name\n\t\t!(__WEBPACK_AMD_DEFINE_ARRAY__ = [], __WEBPACK_AMD_DEFINE_RESULT__ = (function () {\n\t\t\treturn classNames;\n\t\t}).apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__),\n\t\t\t\t__WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__));\n\t} else {\n\t\twindow.classNames = classNames;\n\t}\n}());\n//# sourceURL=[module]\n//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiMS5qcyIsInNvdXJjZXMiOlsid2VicGFjazovLy8uL25vZGVfbW9kdWxlcy9jbGFzc25hbWVzL2luZGV4LmpzPzFkNmUiXSwic291cmNlc0NvbnRlbnQiOlsiLyohXG4gIENvcHlyaWdodCAoYykgMjAxOCBKZWQgV2F0c29uLlxuICBMaWNlbnNlZCB1bmRlciB0aGUgTUlUIExpY2Vuc2UgKE1JVCksIHNlZVxuICBodHRwOi8vamVkd2F0c29uLmdpdGh1Yi5pby9jbGFzc25hbWVzXG4qL1xuLyogZ2xvYmFsIGRlZmluZSAqL1xuXG4oZnVuY3Rpb24gKCkge1xuXHQndXNlIHN0cmljdCc7XG5cblx0dmFyIGhhc093biA9IHt9Lmhhc093blByb3BlcnR5O1xuXG5cdGZ1bmN0aW9uIGNsYXNzTmFtZXMoKSB7XG5cdFx0dmFyIGNsYXNzZXMgPSBbXTtcblxuXHRcdGZvciAodmFyIGkgPSAwOyBpIDwgYXJndW1lbnRzLmxlbmd0aDsgaSsrKSB7XG5cdFx0XHR2YXIgYXJnID0gYXJndW1lbnRzW2ldO1xuXHRcdFx0aWYgKCFhcmcpIGNvbnRpbnVlO1xuXG5cdFx0XHR2YXIgYXJnVHlwZSA9IHR5cGVvZiBhcmc7XG5cblx0XHRcdGlmIChhcmdUeXBlID09PSAnc3RyaW5nJyB8fCBhcmdUeXBlID09PSAnbnVtYmVyJykge1xuXHRcdFx0XHRjbGFzc2VzLnB1c2goYXJnKTtcblx0XHRcdH0gZWxzZSBpZiAoQXJyYXkuaXNBcnJheShhcmcpKSB7XG5cdFx0XHRcdGlmIChhcmcubGVuZ3RoKSB7XG5cdFx0XHRcdFx0dmFyIGlubmVyID0gY2xhc3NOYW1lcy5hcHBseShudWxsLCBhcmcpO1xuXHRcdFx0XHRcdGlmIChpbm5lcikge1xuXHRcdFx0XHRcdFx0Y2xhc3Nlcy5wdXNoKGlubmVyKTtcblx0XHRcdFx0XHR9XG5cdFx0XHRcdH1cblx0XHRcdH0gZWxzZSBpZiAoYXJnVHlwZSA9PT0gJ29iamVjdCcpIHtcblx0XHRcdFx0aWYgKGFyZy50b1N0cmluZyA9PT0gT2JqZWN0LnByb3RvdHlwZS50b1N0cmluZykge1xuXHRcdFx0XHRcdGZvciAodmFyIGtleSBpbiBhcmcpIHtcblx0XHRcdFx0XHRcdGlmIChoYXNPd24uY2FsbChhcmcsIGtleSkgJiYgYXJnW2tleV0pIHtcblx0XHRcdFx0XHRcdFx0Y2xhc3Nlcy5wdXNoKGtleSk7XG5cdFx0XHRcdFx0XHR9XG5cdFx0XHRcdFx0fVxuXHRcdFx0XHR9IGVsc2Uge1xuXHRcdFx0XHRcdGNsYXNzZXMucHVzaChhcmcudG9TdHJpbmcoKSk7XG5cdFx0XHRcdH1cblx0XHRcdH1cblx0XHR9XG5cblx0XHRyZXR1cm4gY2xhc3Nlcy5qb2luKCcgJyk7XG5cdH1cblxuXHRpZiAodHlwZW9mIG1vZHVsZSAhPT0gJ3VuZGVmaW5lZCcgJiYgbW9kdWxlLmV4cG9ydHMpIHtcblx0XHRjbGFzc05hbWVzLmRlZmF1bHQgPSBjbGFzc05hbWVzO1xuXHRcdG1vZHVsZS5leHBvcnRzID0gY2xhc3NOYW1lcztcblx0fSBlbHNlIGlmICh0eXBlb2YgZGVmaW5lID09PSAnZnVuY3Rpb24nICYmIHR5cGVvZiBkZWZpbmUuYW1kID09PSAnb2JqZWN0JyAmJiBkZWZpbmUuYW1kKSB7XG5cdFx0Ly8gcmVnaXN0ZXIgYXMgJ2NsYXNzbmFtZXMnLCBjb25zaXN0ZW50IHdpdGggbnBtIHBhY2thZ2UgbmFtZVxuXHRcdGRlZmluZSgnY2xhc3NuYW1lcycsIFtdLCBmdW5jdGlvbiAoKSB7XG5cdFx0XHRyZXR1cm4gY2xhc3NOYW1lcztcblx0XHR9KTtcblx0fSBlbHNlIHtcblx0XHR3aW5kb3cuY2xhc3NOYW1lcyA9IGNsYXNzTmFtZXM7XG5cdH1cbn0oKSk7XG5cblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL25vZGVfbW9kdWxlcy9jbGFzc25hbWVzL2luZGV4LmpzXG4vLyBtb2R1bGUgaWQgPSAxXG4vLyBtb2R1bGUgY2h1bmtzID0gMCJdLCJtYXBwaW5ncyI6IkFBQUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTsiLCJzb3VyY2VSb290IjoiIn0=\n//# sourceURL=webpack-internal:///1\n");

/***/ }),

/***/ 11:
/*!***********************!*\
  !*** ./src/blocks.js ***!
  \***********************/
/*! no exports provided */
/*! all exports used */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("Object.defineProperty(__webpack_exports__, \"__esModule\", { value: true });\n/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__block_test_box_block_js__ = __webpack_require__(/*! ./block/test-box/block.js */ 36);\n/**\r\n * Cocoon Blocks\r\n * @author: yhira\r\n * @link: https://wp-cocoon.com/\r\n * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later\r\n */\n\n//ブロック\n\n\n// import './block/icon-box/block.js';\n// import './block/info-box/block.js';\n// import './block/blank-box/block.js';\n// import './block/sticky-box/block.js';\n// import './block/tab-box/block.js';\n// import './block/balloon/block.js';\n// //import './block/balloon-ex/block.js';\n// import './block/blogcard/block.js';\n// import './block/button/block.js';\n// import './block/button-wrap/block.js';\n// import './block/toggle-box/block.js';\n// import './block/search-box/block.js';\n// import './block/timeline/block.js';\n// import './block/icon-list/block.js';\n\n// //デフォルトブロックの拡張\n// import './custom/code/block.js';\n// //import './block/code-box/block.js';\n// // import './block/hoc-color-palette-demo/block.js';\n\n// //汎用ブロック\n// import './block-universal/caption-box/block.js';\n// import './block-universal/tab-caption-box/block.js';\n// import './block-universal/label-box/block.js';\n\n// //マイクロコピー\n// import './micro/micro-text/block.js';\n// import './micro/micro-balloon/block.js';\n\n// //レイアウト\n// import './layout/column-children/block.js';\n// import './layout/column-2/block.js';\n// import './layout/column-3/block.js';\n\n// //ショートコード\n// // import './shortcode/affi/block.js';\n\n// //文字色変更など\n// import './toolbutton/bold.js';\n// import './toolbutton/red.js';\n// import './toolbutton/bold-red.js';\n// import './toolbutton/blue.js';\n// import './toolbutton/bold-blue.js';\n// import './toolbutton/green.js';\n// import './toolbutton/bold-green.js';\n// import './toolbutton/keyboard-key.js';\n// import './toolbutton/ruby.js';\n// //import './toolbutton/html.js';\n\n// //マーカー\n// import './toolbutton/marker-yellow.js';\n// import './toolbutton/marker-under-yellow.js';\n// import './toolbutton/marker-red.js';\n// import './toolbutton/marker-under-red.js';\n// import './toolbutton/marker-blue.js';\n// import './toolbutton/marker-under-blue.js';\n\n// //バッジ\n// import './toolbutton/badge-orange.js';\n// import './toolbutton/badge-red.js';\n// import './toolbutton/badge-pink.js';\n// import './toolbutton/badge-purple.js';\n// import './toolbutton/badge-blue.js';\n// import './toolbutton/badge-green.js';\n// import './toolbutton/badge-yellow.js';\n// import './toolbutton/badge-brown.js';\n// import './toolbutton/badge-grey.js';\n\n// //ドロップダウン\n// import './toolbutton/dropdown-letters.js';\n// import './toolbutton/dropdown-markers.js';\n// import './toolbutton/dropdown-badges.js';\n// import './toolbutton/dropdown-font-sizes.js';\n// import './toolbutton/dropdown-shortcodes.js';\n// import './toolbutton/dropdown-templates.js';\n// import './toolbutton/dropdown-affiliates.js';\n// import './toolbutton/dropdown-rankings.js';\n\n\n// //旧バージョン（現在は非表示）\n// //ブロックエディター出現時の情報のないときに誤って作成したもの\n// //出来れば同一ブロックに統一して統合したいけど、やり方がよくわかっていない。\n// import './old/micro-balloon/block.js';\n// import './old/micro-balloon-1/block.js';\n// import './old/balloon/block.js';\n// import './old/balloon-1/block.js';\n// import './old/balloon-2/block.js';\n// import './old/balloon-ex/block.js';\n// import './old/blank-box/block.js';\n// import './old/tab-box/block.js';\n// import './old/toggle-box/block.js';\n// import './old/caption-box/block.js';\n// import './old/tab-caption-box/block.js';\n// import './old/label-box/block.js';\n// import './old/button/block.js';\n// // import './old/button-1/block.js';\n// import './old/button-wrap/block.js';\n// // import './old/button-wrap-1/block.js';\n\n// // import './demo/test/block.js';\n// // import './demo/info-box-drop/block.js';\n// // import './demo/blank-box-demo/block.js';\n// // import './demo/test-as-shortcode-text/block.js';\n// // import './demo/test-severside-as-shortcode-input/block.js';\n// // import './demo/test-get-id/block.js';//# sourceURL=[module]\n//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiMTEuanMiLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vLi9zcmMvYmxvY2tzLmpzPzdiNWIiXSwic291cmNlc0NvbnRlbnQiOlsiLyoqXHJcbiAqIENvY29vbiBCbG9ja3NcclxuICogQGF1dGhvcjogeWhpcmFcclxuICogQGxpbms6IGh0dHBzOi8vd3AtY29jb29uLmNvbS9cclxuICogQGxpY2Vuc2U6IGh0dHA6Ly93d3cuZ251Lm9yZy9saWNlbnNlcy9ncGwtMi4wLmh0bWwgR1BMIHYyIG9yIGxhdGVyXHJcbiAqL1xuXG4vL+ODluODreODg+OCr1xuXG5pbXBvcnQgJy4vYmxvY2svdGVzdC1ib3gvYmxvY2suanMnO1xuLy8gaW1wb3J0ICcuL2Jsb2NrL2ljb24tYm94L2Jsb2NrLmpzJztcbi8vIGltcG9ydCAnLi9ibG9jay9pbmZvLWJveC9ibG9jay5qcyc7XG4vLyBpbXBvcnQgJy4vYmxvY2svYmxhbmstYm94L2Jsb2NrLmpzJztcbi8vIGltcG9ydCAnLi9ibG9jay9zdGlja3ktYm94L2Jsb2NrLmpzJztcbi8vIGltcG9ydCAnLi9ibG9jay90YWItYm94L2Jsb2NrLmpzJztcbi8vIGltcG9ydCAnLi9ibG9jay9iYWxsb29uL2Jsb2NrLmpzJztcbi8vIC8vaW1wb3J0ICcuL2Jsb2NrL2JhbGxvb24tZXgvYmxvY2suanMnO1xuLy8gaW1wb3J0ICcuL2Jsb2NrL2Jsb2djYXJkL2Jsb2NrLmpzJztcbi8vIGltcG9ydCAnLi9ibG9jay9idXR0b24vYmxvY2suanMnO1xuLy8gaW1wb3J0ICcuL2Jsb2NrL2J1dHRvbi13cmFwL2Jsb2NrLmpzJztcbi8vIGltcG9ydCAnLi9ibG9jay90b2dnbGUtYm94L2Jsb2NrLmpzJztcbi8vIGltcG9ydCAnLi9ibG9jay9zZWFyY2gtYm94L2Jsb2NrLmpzJztcbi8vIGltcG9ydCAnLi9ibG9jay90aW1lbGluZS9ibG9jay5qcyc7XG4vLyBpbXBvcnQgJy4vYmxvY2svaWNvbi1saXN0L2Jsb2NrLmpzJztcblxuLy8gLy/jg4fjg5Xjgqnjg6vjg4jjg5bjg63jg4Pjgq/jga7mi6HlvLVcbi8vIGltcG9ydCAnLi9jdXN0b20vY29kZS9ibG9jay5qcyc7XG4vLyAvL2ltcG9ydCAnLi9ibG9jay9jb2RlLWJveC9ibG9jay5qcyc7XG4vLyAvLyBpbXBvcnQgJy4vYmxvY2svaG9jLWNvbG9yLXBhbGV0dGUtZGVtby9ibG9jay5qcyc7XG5cbi8vIC8v5rGO55So44OW44Ot44OD44KvXG4vLyBpbXBvcnQgJy4vYmxvY2stdW5pdmVyc2FsL2NhcHRpb24tYm94L2Jsb2NrLmpzJztcbi8vIGltcG9ydCAnLi9ibG9jay11bml2ZXJzYWwvdGFiLWNhcHRpb24tYm94L2Jsb2NrLmpzJztcbi8vIGltcG9ydCAnLi9ibG9jay11bml2ZXJzYWwvbGFiZWwtYm94L2Jsb2NrLmpzJztcblxuLy8gLy/jg57jgqTjgq/jg63jgrPjg5Tjg7xcbi8vIGltcG9ydCAnLi9taWNyby9taWNyby10ZXh0L2Jsb2NrLmpzJztcbi8vIGltcG9ydCAnLi9taWNyby9taWNyby1iYWxsb29uL2Jsb2NrLmpzJztcblxuLy8gLy/jg6zjgqTjgqLjgqbjg4hcbi8vIGltcG9ydCAnLi9sYXlvdXQvY29sdW1uLWNoaWxkcmVuL2Jsb2NrLmpzJztcbi8vIGltcG9ydCAnLi9sYXlvdXQvY29sdW1uLTIvYmxvY2suanMnO1xuLy8gaW1wb3J0ICcuL2xheW91dC9jb2x1bW4tMy9ibG9jay5qcyc7XG5cbi8vIC8v44K344On44O844OI44Kz44O844OJXG4vLyAvLyBpbXBvcnQgJy4vc2hvcnRjb2RlL2FmZmkvYmxvY2suanMnO1xuXG4vLyAvL+aWh+Wtl+iJsuWkieabtOOBquOBqVxuLy8gaW1wb3J0ICcuL3Rvb2xidXR0b24vYm9sZC5qcyc7XG4vLyBpbXBvcnQgJy4vdG9vbGJ1dHRvbi9yZWQuanMnO1xuLy8gaW1wb3J0ICcuL3Rvb2xidXR0b24vYm9sZC1yZWQuanMnO1xuLy8gaW1wb3J0ICcuL3Rvb2xidXR0b24vYmx1ZS5qcyc7XG4vLyBpbXBvcnQgJy4vdG9vbGJ1dHRvbi9ib2xkLWJsdWUuanMnO1xuLy8gaW1wb3J0ICcuL3Rvb2xidXR0b24vZ3JlZW4uanMnO1xuLy8gaW1wb3J0ICcuL3Rvb2xidXR0b24vYm9sZC1ncmVlbi5qcyc7XG4vLyBpbXBvcnQgJy4vdG9vbGJ1dHRvbi9rZXlib2FyZC1rZXkuanMnO1xuLy8gaW1wb3J0ICcuL3Rvb2xidXR0b24vcnVieS5qcyc7XG4vLyAvL2ltcG9ydCAnLi90b29sYnV0dG9uL2h0bWwuanMnO1xuXG4vLyAvL+ODnuODvOOCq+ODvFxuLy8gaW1wb3J0ICcuL3Rvb2xidXR0b24vbWFya2VyLXllbGxvdy5qcyc7XG4vLyBpbXBvcnQgJy4vdG9vbGJ1dHRvbi9tYXJrZXItdW5kZXIteWVsbG93LmpzJztcbi8vIGltcG9ydCAnLi90b29sYnV0dG9uL21hcmtlci1yZWQuanMnO1xuLy8gaW1wb3J0ICcuL3Rvb2xidXR0b24vbWFya2VyLXVuZGVyLXJlZC5qcyc7XG4vLyBpbXBvcnQgJy4vdG9vbGJ1dHRvbi9tYXJrZXItYmx1ZS5qcyc7XG4vLyBpbXBvcnQgJy4vdG9vbGJ1dHRvbi9tYXJrZXItdW5kZXItYmx1ZS5qcyc7XG5cbi8vIC8v44OQ44OD44K4XG4vLyBpbXBvcnQgJy4vdG9vbGJ1dHRvbi9iYWRnZS1vcmFuZ2UuanMnO1xuLy8gaW1wb3J0ICcuL3Rvb2xidXR0b24vYmFkZ2UtcmVkLmpzJztcbi8vIGltcG9ydCAnLi90b29sYnV0dG9uL2JhZGdlLXBpbmsuanMnO1xuLy8gaW1wb3J0ICcuL3Rvb2xidXR0b24vYmFkZ2UtcHVycGxlLmpzJztcbi8vIGltcG9ydCAnLi90b29sYnV0dG9uL2JhZGdlLWJsdWUuanMnO1xuLy8gaW1wb3J0ICcuL3Rvb2xidXR0b24vYmFkZ2UtZ3JlZW4uanMnO1xuLy8gaW1wb3J0ICcuL3Rvb2xidXR0b24vYmFkZ2UteWVsbG93LmpzJztcbi8vIGltcG9ydCAnLi90b29sYnV0dG9uL2JhZGdlLWJyb3duLmpzJztcbi8vIGltcG9ydCAnLi90b29sYnV0dG9uL2JhZGdlLWdyZXkuanMnO1xuXG4vLyAvL+ODieODreODg+ODl+ODgOOCpuODs1xuLy8gaW1wb3J0ICcuL3Rvb2xidXR0b24vZHJvcGRvd24tbGV0dGVycy5qcyc7XG4vLyBpbXBvcnQgJy4vdG9vbGJ1dHRvbi9kcm9wZG93bi1tYXJrZXJzLmpzJztcbi8vIGltcG9ydCAnLi90b29sYnV0dG9uL2Ryb3Bkb3duLWJhZGdlcy5qcyc7XG4vLyBpbXBvcnQgJy4vdG9vbGJ1dHRvbi9kcm9wZG93bi1mb250LXNpemVzLmpzJztcbi8vIGltcG9ydCAnLi90b29sYnV0dG9uL2Ryb3Bkb3duLXNob3J0Y29kZXMuanMnO1xuLy8gaW1wb3J0ICcuL3Rvb2xidXR0b24vZHJvcGRvd24tdGVtcGxhdGVzLmpzJztcbi8vIGltcG9ydCAnLi90b29sYnV0dG9uL2Ryb3Bkb3duLWFmZmlsaWF0ZXMuanMnO1xuLy8gaW1wb3J0ICcuL3Rvb2xidXR0b24vZHJvcGRvd24tcmFua2luZ3MuanMnO1xuXG5cbi8vIC8v5pen44OQ44O844K444On44Oz77yI54++5Zyo44Gv6Z2e6KGo56S677yJXG4vLyAvL+ODluODreODg+OCr+OCqOODh+OCo+OCv+ODvOWHuuePvuaZguOBruaDheWgseOBruOBquOBhOOBqOOBjeOBq+iqpOOBo+OBpuS9nOaIkOOBl+OBn+OCguOBrlxuLy8gLy/lh7rmnaXjgozjgbDlkIzkuIDjg5bjg63jg4Pjgq/jgavntbHkuIDjgZfjgabntbHlkIjjgZfjgZ/jgYTjgZHjganjgIHjgoTjgormlrnjgYzjgojjgY/jgo/jgYvjgaPjgabjgYTjgarjgYTjgIJcbi8vIGltcG9ydCAnLi9vbGQvbWljcm8tYmFsbG9vbi9ibG9jay5qcyc7XG4vLyBpbXBvcnQgJy4vb2xkL21pY3JvLWJhbGxvb24tMS9ibG9jay5qcyc7XG4vLyBpbXBvcnQgJy4vb2xkL2JhbGxvb24vYmxvY2suanMnO1xuLy8gaW1wb3J0ICcuL29sZC9iYWxsb29uLTEvYmxvY2suanMnO1xuLy8gaW1wb3J0ICcuL29sZC9iYWxsb29uLTIvYmxvY2suanMnO1xuLy8gaW1wb3J0ICcuL29sZC9iYWxsb29uLWV4L2Jsb2NrLmpzJztcbi8vIGltcG9ydCAnLi9vbGQvYmxhbmstYm94L2Jsb2NrLmpzJztcbi8vIGltcG9ydCAnLi9vbGQvdGFiLWJveC9ibG9jay5qcyc7XG4vLyBpbXBvcnQgJy4vb2xkL3RvZ2dsZS1ib3gvYmxvY2suanMnO1xuLy8gaW1wb3J0ICcuL29sZC9jYXB0aW9uLWJveC9ibG9jay5qcyc7XG4vLyBpbXBvcnQgJy4vb2xkL3RhYi1jYXB0aW9uLWJveC9ibG9jay5qcyc7XG4vLyBpbXBvcnQgJy4vb2xkL2xhYmVsLWJveC9ibG9jay5qcyc7XG4vLyBpbXBvcnQgJy4vb2xkL2J1dHRvbi9ibG9jay5qcyc7XG4vLyAvLyBpbXBvcnQgJy4vb2xkL2J1dHRvbi0xL2Jsb2NrLmpzJztcbi8vIGltcG9ydCAnLi9vbGQvYnV0dG9uLXdyYXAvYmxvY2suanMnO1xuLy8gLy8gaW1wb3J0ICcuL29sZC9idXR0b24td3JhcC0xL2Jsb2NrLmpzJztcblxuLy8gLy8gaW1wb3J0ICcuL2RlbW8vdGVzdC9ibG9jay5qcyc7XG4vLyAvLyBpbXBvcnQgJy4vZGVtby9pbmZvLWJveC1kcm9wL2Jsb2NrLmpzJztcbi8vIC8vIGltcG9ydCAnLi9kZW1vL2JsYW5rLWJveC1kZW1vL2Jsb2NrLmpzJztcbi8vIC8vIGltcG9ydCAnLi9kZW1vL3Rlc3QtYXMtc2hvcnRjb2RlLXRleHQvYmxvY2suanMnO1xuLy8gLy8gaW1wb3J0ICcuL2RlbW8vdGVzdC1zZXZlcnNpZGUtYXMtc2hvcnRjb2RlLWlucHV0L2Jsb2NrLmpzJztcbi8vIC8vIGltcG9ydCAnLi9kZW1vL3Rlc3QtZ2V0LWlkL2Jsb2NrLmpzJztcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL3NyYy9ibG9ja3MuanNcbi8vIG1vZHVsZSBpZCA9IDExXG4vLyBtb2R1bGUgY2h1bmtzID0gMCJdLCJtYXBwaW5ncyI6IkFBQUE7QUFBQTtBQUFBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBIiwic291cmNlUm9vdCI6IiJ9\n//# sourceURL=webpack-internal:///11\n");

/***/ }),

/***/ 34:
/*!****************************!*\
  !*** external "wp.blocks" ***!
  \****************************/
/*! dynamic exports provided */
/*! exports used: registerBlockType */
/***/ (function(module, exports) {

module.exports = wp.blocks;

/***/ }),

/***/ 35:
/*!*****************************!*\
  !*** external "wp.element" ***!
  \*****************************/
/*! dynamic exports provided */
/*! exports used: Fragment */
/***/ (function(module, exports) {

module.exports = wp.element;

/***/ }),

/***/ 36:
/*!*************************************!*\
  !*** ./src/block/test-box/block.js ***!
  \*************************************/
/*! no exports provided */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_classnames__ = __webpack_require__(/*! classnames */ 1);\n/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_classnames___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_0_classnames__);\n/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__wordpress_blocks__ = __webpack_require__(/*! @wordpress/blocks */ 34);\n/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__wordpress_blocks___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_1__wordpress_blocks__);\n/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2__wordpress_block_editor__ = __webpack_require__(/*! @wordpress/block-editor */ 7);\n/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2__wordpress_block_editor___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_2__wordpress_block_editor__);\n/* harmony import */ var __WEBPACK_IMPORTED_MODULE_3__wordpress_element__ = __webpack_require__(/*! @wordpress/element */ 35);\n/* harmony import */ var __WEBPACK_IMPORTED_MODULE_3__wordpress_element___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_3__wordpress_element__);\n/**\r\n * Cocoon Blocks\r\n * @author: yhira\r\n * @link: https://wp-cocoon.com/\r\n * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later\r\n */\n\n\n\n// const { __ } = wp.i18n;\n// const { registerBlockType, createBlock } = wp.blocks;\n// const { InnerBlocks, RichText, InspectorControls, useBlockProps } = wp.blockEditor;\n// const { PanelBody, SelectControl, BaseControl } = wp.components;\n// const { Fragment } = wp.element;\n\n\n\n\n\nObject(__WEBPACK_IMPORTED_MODULE_1__wordpress_blocks__[\"registerBlockType\"])('cocoon-blocks/test-box', {\n\n  apiVersion: 2,\n  title: 'テストボックス',\n  // icon: <FontAwesomeIcon icon={['fas', 'exclamation-circle']} />,\n  category: 'cocoon-block',\n  description: 'テストボックスです。',\n  keywords: ['test', 'box'],\n\n  edit: function edit(_ref) {\n    var attributes = _ref.attributes,\n        className = _ref.className;\n\n    var classes = __WEBPACK_IMPORTED_MODULE_0_classnames___default()('test', className);\n    var blockProps = Object(__WEBPACK_IMPORTED_MODULE_2__wordpress_block_editor__[\"useBlockProps\"])({\n      className: classes\n    });\n\n    return wp.element.createElement(\n      __WEBPACK_IMPORTED_MODULE_3__wordpress_element__[\"Fragment\"],\n      null,\n      wp.element.createElement(\n        'div',\n        blockProps,\n        wp.element.createElement(__WEBPACK_IMPORTED_MODULE_2__wordpress_block_editor__[\"InnerBlocks\"], null)\n      )\n    );\n  },\n  save: function save(_ref2) {\n    var attributes = _ref2.attributes;\n\n    var classes = 'test';\n    var blockProps = __WEBPACK_IMPORTED_MODULE_2__wordpress_block_editor__[\"useBlockProps\"].save({\n      className: classes\n    });\n    return wp.element.createElement(\n      'div',\n      blockProps,\n      wp.element.createElement(__WEBPACK_IMPORTED_MODULE_2__wordpress_block_editor__[\"InnerBlocks\"].Content, null)\n    );\n  }\n});//# sourceURL=[module]\n//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiMzYuanMiLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vLi9zcmMvYmxvY2svdGVzdC1ib3gvYmxvY2suanM/MzJlOSJdLCJzb3VyY2VzQ29udGVudCI6WyIvKipcclxuICogQ29jb29uIEJsb2Nrc1xyXG4gKiBAYXV0aG9yOiB5aGlyYVxyXG4gKiBAbGluazogaHR0cHM6Ly93cC1jb2Nvb24uY29tL1xyXG4gKiBAbGljZW5zZTogaHR0cDovL3d3dy5nbnUub3JnL2xpY2Vuc2VzL2dwbC0yLjAuaHRtbCBHUEwgdjIgb3IgbGF0ZXJcclxuICovXG5cbmltcG9ydCBjbGFzc25hbWVzIGZyb20gJ2NsYXNzbmFtZXMnO1xuXG4vLyBjb25zdCB7IF9fIH0gPSB3cC5pMThuO1xuLy8gY29uc3QgeyByZWdpc3RlckJsb2NrVHlwZSwgY3JlYXRlQmxvY2sgfSA9IHdwLmJsb2Nrcztcbi8vIGNvbnN0IHsgSW5uZXJCbG9ja3MsIFJpY2hUZXh0LCBJbnNwZWN0b3JDb250cm9scywgdXNlQmxvY2tQcm9wcyB9ID0gd3AuYmxvY2tFZGl0b3I7XG4vLyBjb25zdCB7IFBhbmVsQm9keSwgU2VsZWN0Q29udHJvbCwgQmFzZUNvbnRyb2wgfSA9IHdwLmNvbXBvbmVudHM7XG4vLyBjb25zdCB7IEZyYWdtZW50IH0gPSB3cC5lbGVtZW50O1xuXG5pbXBvcnQgeyByZWdpc3RlckJsb2NrVHlwZSB9IGZyb20gJ0B3b3JkcHJlc3MvYmxvY2tzJztcbmltcG9ydCB7IElubmVyQmxvY2tzLCB1c2VCbG9ja1Byb3BzIH0gZnJvbSAnQHdvcmRwcmVzcy9ibG9jay1lZGl0b3InO1xuaW1wb3J0IHsgRnJhZ21lbnQgfSBmcm9tICdAd29yZHByZXNzL2VsZW1lbnQnO1xuXG5yZWdpc3RlckJsb2NrVHlwZSgnY29jb29uLWJsb2Nrcy90ZXN0LWJveCcsIHtcblxuICBhcGlWZXJzaW9uOiAyLFxuICB0aXRsZTogJ+ODhuOCueODiOODnOODg+OCr+OCuScsXG4gIC8vIGljb246IDxGb250QXdlc29tZUljb24gaWNvbj17WydmYXMnLCAnZXhjbGFtYXRpb24tY2lyY2xlJ119IC8+LFxuICBjYXRlZ29yeTogJ2NvY29vbi1ibG9jaycsXG4gIGRlc2NyaXB0aW9uOiAn44OG44K544OI44Oc44OD44Kv44K544Gn44GZ44CCJyxcbiAga2V5d29yZHM6IFsndGVzdCcsICdib3gnXSxcblxuICBlZGl0OiBmdW5jdGlvbiBlZGl0KF9yZWYpIHtcbiAgICB2YXIgYXR0cmlidXRlcyA9IF9yZWYuYXR0cmlidXRlcyxcbiAgICAgICAgY2xhc3NOYW1lID0gX3JlZi5jbGFzc05hbWU7XG5cbiAgICB2YXIgY2xhc3NlcyA9IGNsYXNzbmFtZXMoJ3Rlc3QnLCBjbGFzc05hbWUpO1xuICAgIHZhciBibG9ja1Byb3BzID0gdXNlQmxvY2tQcm9wcyh7XG4gICAgICBjbGFzc05hbWU6IGNsYXNzZXNcbiAgICB9KTtcblxuICAgIHJldHVybiB3cC5lbGVtZW50LmNyZWF0ZUVsZW1lbnQoXG4gICAgICBGcmFnbWVudCxcbiAgICAgIG51bGwsXG4gICAgICB3cC5lbGVtZW50LmNyZWF0ZUVsZW1lbnQoXG4gICAgICAgICdkaXYnLFxuICAgICAgICBibG9ja1Byb3BzLFxuICAgICAgICB3cC5lbGVtZW50LmNyZWF0ZUVsZW1lbnQoSW5uZXJCbG9ja3MsIG51bGwpXG4gICAgICApXG4gICAgKTtcbiAgfSxcbiAgc2F2ZTogZnVuY3Rpb24gc2F2ZShfcmVmMikge1xuICAgIHZhciBhdHRyaWJ1dGVzID0gX3JlZjIuYXR0cmlidXRlcztcblxuICAgIHZhciBjbGFzc2VzID0gJ3Rlc3QnO1xuICAgIHZhciBibG9ja1Byb3BzID0gdXNlQmxvY2tQcm9wcy5zYXZlKHtcbiAgICAgIGNsYXNzTmFtZTogY2xhc3Nlc1xuICAgIH0pO1xuICAgIHJldHVybiB3cC5lbGVtZW50LmNyZWF0ZUVsZW1lbnQoXG4gICAgICAnZGl2JyxcbiAgICAgIGJsb2NrUHJvcHMsXG4gICAgICB3cC5lbGVtZW50LmNyZWF0ZUVsZW1lbnQoSW5uZXJCbG9ja3MuQ29udGVudCwgbnVsbClcbiAgICApO1xuICB9XG59KTtcblxuXG4vLy8vLy8vLy8vLy8vLy8vLy9cbi8vIFdFQlBBQ0sgRk9PVEVSXG4vLyAuL3NyYy9ibG9jay90ZXN0LWJveC9ibG9jay5qc1xuLy8gbW9kdWxlIGlkID0gMzZcbi8vIG1vZHVsZSBjaHVua3MgPSAwIl0sIm1hcHBpbmdzIjoiQUFBQTtBQUFBO0FBQUE7QUFBQTtBQUFBO0FBQUE7QUFBQTtBQUFBO0FBQUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0EiLCJzb3VyY2VSb290IjoiIn0=\n//# sourceURL=webpack-internal:///36\n");

/***/ }),

/***/ 7:
/*!*********************************!*\
  !*** external "wp.blockEditor" ***!
  \*********************************/
/*! dynamic exports provided */
/*! exports used: InnerBlocks, useBlockProps */
/***/ (function(module, exports) {

module.exports = wp.blockEditor;

/***/ })

/******/ });