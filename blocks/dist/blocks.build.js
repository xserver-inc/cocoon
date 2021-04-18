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
/******/ 	return __webpack_require__(__webpack_require__.s = 0);
/******/ })
/************************************************************************/
/******/ ([
/* 0 */
/*!***********************!*\
  !*** ./src/blocks.js ***!
  \***********************/
/*! dynamic exports provided */
/*! all exports used */
/***/ (function(module, exports) {

eval("/**\r\n * Cocoon Blocks\r\n * @author: yhira\r\n * @link: https://wp-cocoon.com/\r\n * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later\r\n */\n\n//ブロック\n// import './block/icon-box/block.js';\n// import './block/info-box/block.js';\n// import './block/blank-box/block.js';\n// import './block/sticky-box/block.js';\n// import './block/tab-box/block.js';\n// import './block/balloon/block.js';\n// //import './block/balloon-ex/block.js';\n// import './block/blogcard/block.js';\n// import './block/button/block.js';\n// import './block/button-wrap/block.js';\n// import './block/toggle-box/block.js';\n// import './block/search-box/block.js';\n// import './block/timeline/block.js';\n// import './block/icon-list/block.js';\n\n// //デフォルトブロックの拡張\n// import './custom/code/block.js';\n// //import './block/code-box/block.js';\n// // import './block/hoc-color-palette-demo/block.js';\n\n// //汎用ブロック\n// import './block-universal/caption-box/block.js';\n// import './block-universal/tab-caption-box/block.js';\n// import './block-universal/label-box/block.js';\n\n// //マイクロコピー\n// import './micro/micro-text/block.js';\n// import './micro/micro-balloon/block.js';\n\n// //レイアウト\n// import './layout/column-children/block.js';\n// import './layout/column-2/block.js';\n// import './layout/column-3/block.js';\n\n// //ショートコード\n// // import './shortcode/affi/block.js';\n\n// //文字色変更など\n// import './toolbutton/bold.js';\n// import './toolbutton/red.js';\n// import './toolbutton/bold-red.js';\n// import './toolbutton/blue.js';\n// import './toolbutton/bold-blue.js';\n// import './toolbutton/green.js';\n// import './toolbutton/bold-green.js';\n// import './toolbutton/keyboard-key.js';\n// import './toolbutton/ruby.js';\n// //import './toolbutton/html.js';\n\n// //マーカー\n// import './toolbutton/marker-yellow.js';\n// import './toolbutton/marker-under-yellow.js';\n// import './toolbutton/marker-red.js';\n// import './toolbutton/marker-under-red.js';\n// import './toolbutton/marker-blue.js';\n// import './toolbutton/marker-under-blue.js';\n\n// //バッジ\n// import './toolbutton/badge-orange.js';\n// import './toolbutton/badge-red.js';\n// import './toolbutton/badge-pink.js';\n// import './toolbutton/badge-purple.js';\n// import './toolbutton/badge-blue.js';\n// import './toolbutton/badge-green.js';\n// import './toolbutton/badge-yellow.js';\n// import './toolbutton/badge-brown.js';\n// import './toolbutton/badge-grey.js';\n\n// //ドロップダウン\n// import './toolbutton/dropdown-letters.js';\n// import './toolbutton/dropdown-markers.js';\n// import './toolbutton/dropdown-badges.js';\n// import './toolbutton/dropdown-font-sizes.js';\n// import './toolbutton/dropdown-shortcodes.js';\n// import './toolbutton/dropdown-templates.js';\n// import './toolbutton/dropdown-affiliates.js';\n// import './toolbutton/dropdown-rankings.js';\n\n\n// //旧バージョン（現在は非表示）\n// //ブロックエディター出現時の情報のないときに誤って作成したもの\n// //出来れば同一ブロックに統一して統合したいけど、やり方がよくわかっていない。\n// import './old/micro-balloon/block.js';\n// import './old/micro-balloon-1/block.js';\n// import './old/balloon/block.js';\n// import './old/balloon-1/block.js';\n// import './old/balloon-2/block.js';\n// import './old/balloon-ex/block.js';\n// import './old/blank-box/block.js';\n// import './old/tab-box/block.js';\n// import './old/toggle-box/block.js';\n// import './old/caption-box/block.js';\n// import './old/tab-caption-box/block.js';\n// import './old/label-box/block.js';\n// import './old/button/block.js';\n// // import './old/button-1/block.js';\n// import './old/button-wrap/block.js';\n// // import './old/button-wrap-1/block.js';\n\n// // import './demo/test/block.js';\n// // import './demo/info-box-drop/block.js';\n// // import './demo/blank-box-demo/block.js';\n// // import './demo/test-as-shortcode-text/block.js';\n// // import './demo/test-severside-as-shortcode-input/block.js';\n// // import './demo/test-get-id/block.js';//# sourceURL=[module]\n//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiMC5qcyIsInNvdXJjZXMiOlsid2VicGFjazovLy8uL3NyYy9ibG9ja3MuanM/N2I1YiJdLCJzb3VyY2VzQ29udGVudCI6WyIvKipcclxuICogQ29jb29uIEJsb2Nrc1xyXG4gKiBAYXV0aG9yOiB5aGlyYVxyXG4gKiBAbGluazogaHR0cHM6Ly93cC1jb2Nvb24uY29tL1xyXG4gKiBAbGljZW5zZTogaHR0cDovL3d3dy5nbnUub3JnL2xpY2Vuc2VzL2dwbC0yLjAuaHRtbCBHUEwgdjIgb3IgbGF0ZXJcclxuICovXG5cbi8v44OW44Ot44OD44KvXG4vLyBpbXBvcnQgJy4vYmxvY2svaWNvbi1ib3gvYmxvY2suanMnO1xuLy8gaW1wb3J0ICcuL2Jsb2NrL2luZm8tYm94L2Jsb2NrLmpzJztcbi8vIGltcG9ydCAnLi9ibG9jay9ibGFuay1ib3gvYmxvY2suanMnO1xuLy8gaW1wb3J0ICcuL2Jsb2NrL3N0aWNreS1ib3gvYmxvY2suanMnO1xuLy8gaW1wb3J0ICcuL2Jsb2NrL3RhYi1ib3gvYmxvY2suanMnO1xuLy8gaW1wb3J0ICcuL2Jsb2NrL2JhbGxvb24vYmxvY2suanMnO1xuLy8gLy9pbXBvcnQgJy4vYmxvY2svYmFsbG9vbi1leC9ibG9jay5qcyc7XG4vLyBpbXBvcnQgJy4vYmxvY2svYmxvZ2NhcmQvYmxvY2suanMnO1xuLy8gaW1wb3J0ICcuL2Jsb2NrL2J1dHRvbi9ibG9jay5qcyc7XG4vLyBpbXBvcnQgJy4vYmxvY2svYnV0dG9uLXdyYXAvYmxvY2suanMnO1xuLy8gaW1wb3J0ICcuL2Jsb2NrL3RvZ2dsZS1ib3gvYmxvY2suanMnO1xuLy8gaW1wb3J0ICcuL2Jsb2NrL3NlYXJjaC1ib3gvYmxvY2suanMnO1xuLy8gaW1wb3J0ICcuL2Jsb2NrL3RpbWVsaW5lL2Jsb2NrLmpzJztcbi8vIGltcG9ydCAnLi9ibG9jay9pY29uLWxpc3QvYmxvY2suanMnO1xuXG4vLyAvL+ODh+ODleOCqeODq+ODiOODluODreODg+OCr+OBruaLoeW8tVxuLy8gaW1wb3J0ICcuL2N1c3RvbS9jb2RlL2Jsb2NrLmpzJztcbi8vIC8vaW1wb3J0ICcuL2Jsb2NrL2NvZGUtYm94L2Jsb2NrLmpzJztcbi8vIC8vIGltcG9ydCAnLi9ibG9jay9ob2MtY29sb3ItcGFsZXR0ZS1kZW1vL2Jsb2NrLmpzJztcblxuLy8gLy/msY7nlKjjg5bjg63jg4Pjgq9cbi8vIGltcG9ydCAnLi9ibG9jay11bml2ZXJzYWwvY2FwdGlvbi1ib3gvYmxvY2suanMnO1xuLy8gaW1wb3J0ICcuL2Jsb2NrLXVuaXZlcnNhbC90YWItY2FwdGlvbi1ib3gvYmxvY2suanMnO1xuLy8gaW1wb3J0ICcuL2Jsb2NrLXVuaXZlcnNhbC9sYWJlbC1ib3gvYmxvY2suanMnO1xuXG4vLyAvL+ODnuOCpOOCr+ODreOCs+ODlOODvFxuLy8gaW1wb3J0ICcuL21pY3JvL21pY3JvLXRleHQvYmxvY2suanMnO1xuLy8gaW1wb3J0ICcuL21pY3JvL21pY3JvLWJhbGxvb24vYmxvY2suanMnO1xuXG4vLyAvL+ODrOOCpOOCouOCpuODiFxuLy8gaW1wb3J0ICcuL2xheW91dC9jb2x1bW4tY2hpbGRyZW4vYmxvY2suanMnO1xuLy8gaW1wb3J0ICcuL2xheW91dC9jb2x1bW4tMi9ibG9jay5qcyc7XG4vLyBpbXBvcnQgJy4vbGF5b3V0L2NvbHVtbi0zL2Jsb2NrLmpzJztcblxuLy8gLy/jgrfjg6fjg7zjg4jjgrPjg7zjg4lcbi8vIC8vIGltcG9ydCAnLi9zaG9ydGNvZGUvYWZmaS9ibG9jay5qcyc7XG5cbi8vIC8v5paH5a2X6Imy5aSJ5pu044Gq44GpXG4vLyBpbXBvcnQgJy4vdG9vbGJ1dHRvbi9ib2xkLmpzJztcbi8vIGltcG9ydCAnLi90b29sYnV0dG9uL3JlZC5qcyc7XG4vLyBpbXBvcnQgJy4vdG9vbGJ1dHRvbi9ib2xkLXJlZC5qcyc7XG4vLyBpbXBvcnQgJy4vdG9vbGJ1dHRvbi9ibHVlLmpzJztcbi8vIGltcG9ydCAnLi90b29sYnV0dG9uL2JvbGQtYmx1ZS5qcyc7XG4vLyBpbXBvcnQgJy4vdG9vbGJ1dHRvbi9ncmVlbi5qcyc7XG4vLyBpbXBvcnQgJy4vdG9vbGJ1dHRvbi9ib2xkLWdyZWVuLmpzJztcbi8vIGltcG9ydCAnLi90b29sYnV0dG9uL2tleWJvYXJkLWtleS5qcyc7XG4vLyBpbXBvcnQgJy4vdG9vbGJ1dHRvbi9ydWJ5LmpzJztcbi8vIC8vaW1wb3J0ICcuL3Rvb2xidXR0b24vaHRtbC5qcyc7XG5cbi8vIC8v44Oe44O844Kr44O8XG4vLyBpbXBvcnQgJy4vdG9vbGJ1dHRvbi9tYXJrZXIteWVsbG93LmpzJztcbi8vIGltcG9ydCAnLi90b29sYnV0dG9uL21hcmtlci11bmRlci15ZWxsb3cuanMnO1xuLy8gaW1wb3J0ICcuL3Rvb2xidXR0b24vbWFya2VyLXJlZC5qcyc7XG4vLyBpbXBvcnQgJy4vdG9vbGJ1dHRvbi9tYXJrZXItdW5kZXItcmVkLmpzJztcbi8vIGltcG9ydCAnLi90b29sYnV0dG9uL21hcmtlci1ibHVlLmpzJztcbi8vIGltcG9ydCAnLi90b29sYnV0dG9uL21hcmtlci11bmRlci1ibHVlLmpzJztcblxuLy8gLy/jg5Djg4Pjgrhcbi8vIGltcG9ydCAnLi90b29sYnV0dG9uL2JhZGdlLW9yYW5nZS5qcyc7XG4vLyBpbXBvcnQgJy4vdG9vbGJ1dHRvbi9iYWRnZS1yZWQuanMnO1xuLy8gaW1wb3J0ICcuL3Rvb2xidXR0b24vYmFkZ2UtcGluay5qcyc7XG4vLyBpbXBvcnQgJy4vdG9vbGJ1dHRvbi9iYWRnZS1wdXJwbGUuanMnO1xuLy8gaW1wb3J0ICcuL3Rvb2xidXR0b24vYmFkZ2UtYmx1ZS5qcyc7XG4vLyBpbXBvcnQgJy4vdG9vbGJ1dHRvbi9iYWRnZS1ncmVlbi5qcyc7XG4vLyBpbXBvcnQgJy4vdG9vbGJ1dHRvbi9iYWRnZS15ZWxsb3cuanMnO1xuLy8gaW1wb3J0ICcuL3Rvb2xidXR0b24vYmFkZ2UtYnJvd24uanMnO1xuLy8gaW1wb3J0ICcuL3Rvb2xidXR0b24vYmFkZ2UtZ3JleS5qcyc7XG5cbi8vIC8v44OJ44Ot44OD44OX44OA44Km44OzXG4vLyBpbXBvcnQgJy4vdG9vbGJ1dHRvbi9kcm9wZG93bi1sZXR0ZXJzLmpzJztcbi8vIGltcG9ydCAnLi90b29sYnV0dG9uL2Ryb3Bkb3duLW1hcmtlcnMuanMnO1xuLy8gaW1wb3J0ICcuL3Rvb2xidXR0b24vZHJvcGRvd24tYmFkZ2VzLmpzJztcbi8vIGltcG9ydCAnLi90b29sYnV0dG9uL2Ryb3Bkb3duLWZvbnQtc2l6ZXMuanMnO1xuLy8gaW1wb3J0ICcuL3Rvb2xidXR0b24vZHJvcGRvd24tc2hvcnRjb2Rlcy5qcyc7XG4vLyBpbXBvcnQgJy4vdG9vbGJ1dHRvbi9kcm9wZG93bi10ZW1wbGF0ZXMuanMnO1xuLy8gaW1wb3J0ICcuL3Rvb2xidXR0b24vZHJvcGRvd24tYWZmaWxpYXRlcy5qcyc7XG4vLyBpbXBvcnQgJy4vdG9vbGJ1dHRvbi9kcm9wZG93bi1yYW5raW5ncy5qcyc7XG5cblxuLy8gLy/ml6fjg5Djg7zjgrjjg6fjg7PvvIjnj77lnKjjga/pnZ7ooajnpLrvvIlcbi8vIC8v44OW44Ot44OD44Kv44Ko44OH44Kj44K/44O85Ye654++5pmC44Gu5oOF5aCx44Gu44Gq44GE44Go44GN44Gr6Kqk44Gj44Gm5L2c5oiQ44GX44Gf44KC44GuXG4vLyAvL+WHuuadpeOCjOOBsOWQjOS4gOODluODreODg+OCr+OBq+e1seS4gOOBl+OBpue1seWQiOOBl+OBn+OBhOOBkeOBqeOAgeOChOOCiuaWueOBjOOCiOOBj+OCj+OBi+OBo+OBpuOBhOOBquOBhOOAglxuLy8gaW1wb3J0ICcuL29sZC9taWNyby1iYWxsb29uL2Jsb2NrLmpzJztcbi8vIGltcG9ydCAnLi9vbGQvbWljcm8tYmFsbG9vbi0xL2Jsb2NrLmpzJztcbi8vIGltcG9ydCAnLi9vbGQvYmFsbG9vbi9ibG9jay5qcyc7XG4vLyBpbXBvcnQgJy4vb2xkL2JhbGxvb24tMS9ibG9jay5qcyc7XG4vLyBpbXBvcnQgJy4vb2xkL2JhbGxvb24tMi9ibG9jay5qcyc7XG4vLyBpbXBvcnQgJy4vb2xkL2JhbGxvb24tZXgvYmxvY2suanMnO1xuLy8gaW1wb3J0ICcuL29sZC9ibGFuay1ib3gvYmxvY2suanMnO1xuLy8gaW1wb3J0ICcuL29sZC90YWItYm94L2Jsb2NrLmpzJztcbi8vIGltcG9ydCAnLi9vbGQvdG9nZ2xlLWJveC9ibG9jay5qcyc7XG4vLyBpbXBvcnQgJy4vb2xkL2NhcHRpb24tYm94L2Jsb2NrLmpzJztcbi8vIGltcG9ydCAnLi9vbGQvdGFiLWNhcHRpb24tYm94L2Jsb2NrLmpzJztcbi8vIGltcG9ydCAnLi9vbGQvbGFiZWwtYm94L2Jsb2NrLmpzJztcbi8vIGltcG9ydCAnLi9vbGQvYnV0dG9uL2Jsb2NrLmpzJztcbi8vIC8vIGltcG9ydCAnLi9vbGQvYnV0dG9uLTEvYmxvY2suanMnO1xuLy8gaW1wb3J0ICcuL29sZC9idXR0b24td3JhcC9ibG9jay5qcyc7XG4vLyAvLyBpbXBvcnQgJy4vb2xkL2J1dHRvbi13cmFwLTEvYmxvY2suanMnO1xuXG4vLyAvLyBpbXBvcnQgJy4vZGVtby90ZXN0L2Jsb2NrLmpzJztcbi8vIC8vIGltcG9ydCAnLi9kZW1vL2luZm8tYm94LWRyb3AvYmxvY2suanMnO1xuLy8gLy8gaW1wb3J0ICcuL2RlbW8vYmxhbmstYm94LWRlbW8vYmxvY2suanMnO1xuLy8gLy8gaW1wb3J0ICcuL2RlbW8vdGVzdC1hcy1zaG9ydGNvZGUtdGV4dC9ibG9jay5qcyc7XG4vLyAvLyBpbXBvcnQgJy4vZGVtby90ZXN0LXNldmVyc2lkZS1hcy1zaG9ydGNvZGUtaW5wdXQvYmxvY2suanMnO1xuLy8gLy8gaW1wb3J0ICcuL2RlbW8vdGVzdC1nZXQtaWQvYmxvY2suanMnO1xuXG5cbi8vLy8vLy8vLy8vLy8vLy8vL1xuLy8gV0VCUEFDSyBGT09URVJcbi8vIC4vc3JjL2Jsb2Nrcy5qc1xuLy8gbW9kdWxlIGlkID0gMFxuLy8gbW9kdWxlIGNodW5rcyA9IDAiXSwibWFwcGluZ3MiOiJBQUFBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0EiLCJzb3VyY2VSb290IjoiIn0=\n//# sourceURL=webpack-internal:///0\n");

/***/ })
/******/ ]);