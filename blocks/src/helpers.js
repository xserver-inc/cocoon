/**
 * Cocoon Blocks
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */

import classnames from 'classnames';
// import domReady from '@wordpress/dom-ready';

// domReady( function() {
//   const addClasses = function () {
//     console.log(jQuery('.block-editor-writing-flow'));
//   };
//   // setTimeout(addClasses, 100);
//   // let classList = document.getElementsByClassName("block-editor__typewriter");
//   // console.log(classList.length);
//   // const addClasses = function () {
//     // console.log('2');
//     // console.log(jQuery('.block-editor-writing-flow'));
//     // add body class

//     //do something after DOM loads.
//     // console.log('domReady');
//     // // document.getElementById("wpbody").classList.add("test");
//     // let classList = document.getElementsByClassName('block-editor__typewriter');
//     // console.log(document.getElementById("wpwrap"));
//     // console.log([ ... classList]);
//     // for (var i = 0; i < classList[0].length; i++) {
//     //   console.log(classList[0][i]); //second console output
//     //   classList[0][i].add('ex2-1', 'ex2-2');
//     // }
//     // console.log(classList.length);
//     // [ ... classList].forEach(e =>
//     //   console.log(e);
//     // )
//     // classList.add('ex2-1', 'ex2-2');
// } );

const { __ } = wp.i18n;
const {
  Fill,
  ToolbarButton,
  withFallbackStyles,
} = wp.components;
const {
  getColorObjectByColorValue,
  getColorObjectByAttributeValues,
  getColorClassName
} = wp.editor;
const {
  displayShortcut
} = wp.keycodes;
const {
  find
} = lodash;
const {
  getComputedStyle
} = window;

export const THEME_NAME = 'cocoon';
export const BLOCK_CLASS = ' block-box';
export const BUTTON_BLOCK = 'button-block';
export const LAYOUT_BLOCK_CLASS = 'layout-box';
export const PARAGRAPH_CLASS = ' paragraph';
export const CLICK_POINT_MSG = __( 'こちらをクリックして設定変更。この入力は公開ページで反映されません。', THEME_NAME );

export const keyColor = gbColors['keyColor'];

//日時をもとにしたID作成
export function getDateID(){
  //Dateオブジェクトを利用
  var d = new Date();
  var year  = d.getFullYear();
  var month = d.getMonth() + 1;
  var month = ( month          < 10 ) ? '0' + month          : month;
  var day   = ( d.getDate()    < 10 ) ? '0' + d.getDate()    : d.getDate();
  var hour  = ( d.getHours()   < 10 ) ? '0' + d.getHours()   : d.getHours();
  var min   = ( d.getMinutes() < 10 ) ? '0' + d.getMinutes() : d.getMinutes();
  var sec   = ( d.getSeconds() < 10 ) ? '0' + d.getSeconds() : d.getSeconds();
  var dateID = '' + year + month + day + hour + min + sec;
  return dateID;
}

//アイコンクラス文字列を取得
export function getIconClass(icon){
  return icon ? (' ' + icon) : '';
}

//バルーンclassの取得
export function getBalloonClasses(id, style, position, iconstyle) {
  const classes = classnames(
    {
      [ 'speech-wrap' ]: true,
      [ `sb-id-${ id }` ]: !! id,
      [ `sbs-${ style  }` ]: !! style ,
      [ `sbp-${ position  }` ]: !! position ,
      [ `sbis-${ iconstyle  }` ]: !! iconstyle ,
      [ 'cf' ]: true,
      [ 'block-box' ]: true,
    }
  );
  return classes;
}

//オブジェクト吹き出しと保存した吹き出しの情報が同じか
export function isSameBalloon(index, id, icon, style, position, iconstyle) {
  if (gbSpeechBalloons[index]) {
    if (
      gbSpeechBalloons[index].id == id &&
      gbSpeechBalloons[index].icon == icon &&
      gbSpeechBalloons[index].style == style &&
      gbSpeechBalloons[index].position == position &&
      gbSpeechBalloons[index].iconstyle == iconstyle

    ) {
      return true;
    }
  }
  return false;
}



export const fullFallbackStyles = withFallbackStyles((node, ownProps) => {
  const {
    textColor,
    backgroundColor,
    borderColor,
    fontSize,
    customFontSize,
  } = ownProps.attributes;
  const editableNode = node.querySelector('[contenteditable="true"]');
  //verify if editableNode is available, before using getComputedStyle.
  const computedStyles = editableNode ? getComputedStyle(editableNode) : null;
  return {
    fallbackBackgroundColor: backgroundColor || !computedStyles ? undefined : computedStyles.backgroundColor,
    fallbackTextColor: textColor || !computedStyles ? undefined : computedStyles.color,
    fallbackBorderColor: borderColor || !computedStyles ? undefined : computedStyles.color,
    fallbackFontSize: fontSize || customFontSize || !computedStyles ? undefined : parseInt( computedStyles.fontSize ) || undefined,
  }
});

//現在のカラーパレットのスラッグを取得
export function getCurrentColorSlug(colors, color){
  const object = getColorObjectByColorValue(colors, color);
  let slug = 'key-color';
  if (object) {
    slug = object.slug;
  }
  return slug;
}

//現在のカラーパレットのスラッグを取得
export function getCurrentColorCode(colors, slug){
  //console.log(colors);
  const object = getColorObjectByAttributeValues(colors, slug, keyColor);
  //console.log(object);
  let color = keyColor;
  if (object) {
    color = object.color;
  }
  return color;
}

//HTMLエスケープ
export function htmlEscape(str) {
  if (!str) return;
  return str.replace(/[<>&"'`]/g, function(match) {
    const escape = {
      '<': '&lt;',
      '>': '&gt;',
      '&': '&amp;',
      '"': '&quot;',
      "'": '&#39;',
      '`': '&#x60;'
    };
    return escape[match];
  });
}

//吹き出しが存在しているかどうか
export function isBalloonExist(speechBalloons) {
  var res = false;
  if (speechBalloons && speechBalloons[0]){
    speechBalloons.some(function(balloon){
      if(balloon.visible == 1){
        res = true;
      }
    });
  }
  return res;
}

// //コードブロックエスケープ
// export function codeBlockEscape(str) {
//   //HTMLエスケープ
//   str = htmlEscape(str);
//   //ショートコードエスケープ
//   str = str.replace(/\[[^<>&\/\[\]\x00-\x20=]+.*?\]/g, '[$&]');
//   return str;
// }

//色からスラッグを取得
export function colorValueToSlug(color){
  switch (color) {
    case keyColor:
      return 'key-color';
      break;
    case '#e60033':
      return 'red';
      break;
    case '#e95295':
      return 'pink';
      break;
    case '#884898':
      return 'purple';
      break;
    case '#55295b':
      return 'deep';
      break;
    case '#1e50a2':
      return 'indigo';
      break;
    case '#0095d9':
      return 'blue';
      break;
    case '#2ca9e1':
      return 'light-blue';
      break;
    case '#00a3af':
      return 'cyan';
      break;
    case '#007b43':
      return 'teal';
      break;
    case '#3eb370':
      return 'green';
      break;
    case '#8bc34a':
      return 'light-green';
      break;
    case '#c3d825':
      return 'lime';
      break;
    case '#ffd900':
      return 'yellow';
      break;
    case '#ffc107':
      return 'amber';
      break;
    case '#f39800':
      return 'orange';
      break;
    case '#ea5506':
      return 'deep-orange';
      break;
    case '#954e2a':
      return 'brown';
      break;
    case '#949495':
      return 'grey';
      break;
    case '#333333':
      return 'black';
      break;
    case '#333':
      return 'black';
      break;
    case '#ffffff':
      return 'white';
      break;
    case '#fff':
      return 'white';
      break;
    default:
      //return 'color--' + color.replace('#', '');
      break;
  }
}

export const ICONS = [
  {
    value: '',
    label: 'fab-none',
  },
  {
    value: 'fab-info-circle',
    label: __( 'fab-info-circle', THEME_NAME ),
  },
  {
    value: 'fab-question-circle',
    label: __( 'fab-question-circle', THEME_NAME ),
  },
  {
    value: 'fab-exclamation-circle',
    label: __( 'fab-exclamation-circle', THEME_NAME ),
  },
  {
    value: 'fab-pencil',
    label: __( 'fab-pencil', THEME_NAME ),
  },
  {
    value: 'fab-edit',
    label: __( 'fab-edit', THEME_NAME ),
  },
  {
    value: 'fab-comment',
    label: __( 'fab-comment', THEME_NAME ),
  },
  {
    value: 'fab-ok',
    label: __( 'fab-ok', THEME_NAME ),
  },
  {
    value: 'fab-bad',
    label: __( 'fab-bad', THEME_NAME ),
  },
  {
    value: 'fab-thumbs-up',
    label: __( 'fab-thumbs-up', THEME_NAME ),
  },
  {
    value: 'fab-thumbs-down',
    label: __( 'fab-thumbs-down', THEME_NAME ),
  },
  {
    value: 'fab-check',
    label: __( 'fab-check', THEME_NAME ),
  },
  {
    value: 'fab-star',
    label: __( 'fab-star', THEME_NAME ),
  },
  {
    value: 'fab-bell',
    label: __( 'fab-bell', THEME_NAME ),
  },
  {
    value: 'fab-trophy',
    label: __( 'fab-trophy', THEME_NAME ),
  },
  {
    value: 'fab-lightbulb',
    label: __( 'fab-lightbulb', THEME_NAME ),
  },
  {
    value: 'fab-graduation-cap',
    label: __( 'fab-graduation-cap', THEME_NAME ),
  },
  {
    value: 'fab-bolt',
    label: __( 'fab-bolt', THEME_NAME ),
  },
  {
    value: 'fab-bookmark',
    label: __( 'fab-bookmark', THEME_NAME ),
  },
  {
    value: 'fab-book',
    label: __( 'fab-book', THEME_NAME ),
  },
  {
    value: 'fab-download',
    label: __( 'fab-download', THEME_NAME ),
  },
  {
    value: 'fab-coffee',
    label: __( 'fab-coffee', THEME_NAME ),
  },
  {
    value: 'fab-amazon',
    label: __( 'fab-amazon', THEME_NAME ),
  },
  {
    value: 'fab-user',
    label: __( 'fab-user', THEME_NAME ),
  },
  {
    value: 'fab-envelope',
    label: __( 'fab-envelope', THEME_NAME ),
  },
  {
    value: 'fab-flag',
    label: __( 'fab-flag', THEME_NAME ),
  },
  {
    value: 'fab-ban',
    label: __( 'fab-ban', THEME_NAME ),
  },
  {
    value: 'fab-calendar',
    label: __( 'fab-calendar', THEME_NAME ),
  },
  {
    value: 'fab-clock',
    label: __( 'fab-clock', THEME_NAME ),
  },
  {
    value: 'fab-cutlery',
    label: __( 'fab-cutlery', THEME_NAME ),
  },
  {
    value: 'fab-heart',
    label: __( 'fab-heart', THEME_NAME ),
  },
  {
    value: 'fab-camera',
    label: __( 'fab-camera', THEME_NAME ),
  },
  {
    value: 'fab-search',
    label: __( 'fab-search', THEME_NAME ),
  },
  {
    value: 'fab-file-text',
    label: __( 'fab-file-text', THEME_NAME ),
  },
  {
    value: 'fab-folder',
    label: __( 'fab-folder', THEME_NAME ),
  },
  {
    value: 'fab-tag',
    label: __( 'fab-tag', THEME_NAME ),
  },
  {
    value: 'fab-car',
    label: __( 'fab-car', THEME_NAME ),
  },
  {
    value: 'fab-truck',
    label: __( 'fab-truck', THEME_NAME ),
  },
  {
    value: 'fab-bicycle',
    label: __( 'fab-bicycle', THEME_NAME ),
  },
  {
    value: 'fab-motorcycle',
    label: __( 'fab-motorcycle', THEME_NAME ),
  },
  {
    value: 'fab-bus',
    label: __( 'fab-bus', THEME_NAME ),
  },
  {
    value: 'fab-plane',
    label: __( 'fab-plane', THEME_NAME ),
  },
  {
    value: 'fab-train',
    label: __( 'fab-train', THEME_NAME ),
  },
  {
    value: 'fab-subway',
    label: __( 'fab-subway', THEME_NAME ),
  },
  {
    value: 'fab-taxi',
    label: __( 'fab-taxi', THEME_NAME ),
  },
  {
    value: 'fab-ship',
    label: __( 'fab-ship', THEME_NAME ),
  },
  {
    value: 'fab-jpy',
    label: __( 'fab-jpy', THEME_NAME ),
  },
  {
    value: 'fab-usd',
    label: __( 'fab-usd', THEME_NAME ),
  },
  {
    value: 'fab-eur',
    label: __( 'fab-eur', THEME_NAME ),
  },
  {
    value: 'fab-btc',
    label: __( 'fab-btc', THEME_NAME ),
  },
  {
    value: 'fab-apple',
    label: __( 'fab-apple', THEME_NAME ),
  },
  {
    value: 'fab-android',
    label: __( 'fab-android', THEME_NAME ),
  },
  {
    value: 'fab-wordpress',
    label: __( 'fab-wordpress', THEME_NAME ),
  },
];

















// export const LABEL_ICONS = [
//   {
//     value: '',
//     label: 'fab-none',
//   },
//   {
//     value: 'fa-info-circle',
//     label: __( 'fab-info-circle', THEME_NAME ),
//   },
//   {
//     value: 'fa-question-circle',
//     label: __( 'fab-question-circle', THEME_NAME ),
//   },
//   {
//     value: 'fa-exclamation-circle',
//     label: __( 'fab-exclamation-circle', THEME_NAME ),
//   },
//   {
//     value: 'fa-pencil',
//     label: __( 'fab-pencil', THEME_NAME ),
//   },
//   {
//     value: 'fa-edit',
//     label: __( 'fab-edit', THEME_NAME ),
//   },
//   {
//     value: 'fa-comment',
//     label: __( 'fab-comment', THEME_NAME ),
//   },
//   {
//     value: 'fa-ok',
//     label: __( 'fab-ok', THEME_NAME ),
//   },
//   {
//     value: 'fa-bad',
//     label: __( 'fab-bad', THEME_NAME ),
//   },
//   {
//     value: 'fa-thumbs-up',
//     label: __( 'fab-thumbs-up', THEME_NAME ),
//   },
//   {
//     value: 'fa-thumbs-down',
//     label: __( 'fab-thumbs-down', THEME_NAME ),
//   },
//   {
//     value: 'fa-check',
//     label: __( 'fab-check', THEME_NAME ),
//   },
//   {
//     value: 'fa-star',
//     label: __( 'fab-star', THEME_NAME ),
//   },
//   {
//     value: 'fa-bell',
//     label: __( 'fab-bell', THEME_NAME ),
//   },
//   {
//     value: 'fa-trophy',
//     label: __( 'fab-trophy', THEME_NAME ),
//   },
//   {
//     value: 'fa-lightbulb',
//     label: __( 'fab-lightbulb', THEME_NAME ),
//   },
//   {
//     value: 'fa-graduation-cap',
//     label: __( 'fab-graduation-cap', THEME_NAME ),
//   },
//   {
//     value: 'fa-bolt',
//     label: __( 'fab-bolt', THEME_NAME ),
//   },
//   {
//     value: 'fa-bookmark',
//     label: __( 'fab-bookmark', THEME_NAME ),
//   },
//   {
//     value: 'fa-book',
//     label: __( 'fab-book', THEME_NAME ),
//   },
//   {
//     value: 'fa-download',
//     label: __( 'fab-download', THEME_NAME ),
//   },
//   {
//     value: 'fa-coffee',
//     label: __( 'fab-coffee', THEME_NAME ),
//   },
//   {
//     value: 'fa-amazon',
//     label: __( 'fab-amazon', THEME_NAME ),
//   },
//   {
//     value: 'fa-user',
//     label: __( 'fab-user', THEME_NAME ),
//   },
//   {
//     value: 'fa-envelope',
//     label: __( 'fab-envelope', THEME_NAME ),
//   },
//   {
//     value: 'fa-flag',
//     label: __( 'fab-flag', THEME_NAME ),
//   },
//   {
//     value: 'fa-ban',
//     label: __( 'fab-ban', THEME_NAME ),
//   },
//   {
//     value: 'fa-calendar',
//     label: __( 'fab-calendar', THEME_NAME ),
//   },
//   {
//     value: 'fa-clock',
//     label: __( 'fab-clock', THEME_NAME ),
//   },
//   {
//     value: 'fa-cutlery',
//     label: __( 'fab-cutlery', THEME_NAME ),
//   },
//   {
//     value: 'fa-heart',
//     label: __( 'fab-heart', THEME_NAME ),
//   },
//   {
//     value: 'fa-camera',
//     label: __( 'fab-camera', THEME_NAME ),
//   },
//   {
//     value: 'fa-search',
//     label: __( 'fab-search', THEME_NAME ),
//   },
//   {
//     value: 'fa-file-text',
//     label: __( 'fab-file-text', THEME_NAME ),
//   },
//   {
//     value: 'fa-folder',
//     label: __( 'fab-folder', THEME_NAME ),
//   },
//   {
//     value: 'fa-tag',
//     label: __( 'fab-tag', THEME_NAME ),
//   },
//   {
//     value: 'fa-car',
//     label: __( 'fab-car', THEME_NAME ),
//   },
//   {
//     value: 'fa-truck',
//     label: __( 'fab-truck', THEME_NAME ),
//   },
//   {
//     value: 'fa-bicycle',
//     label: __( 'fab-bicycle', THEME_NAME ),
//   },
//   {
//     value: 'fa-motorcycle',
//     label: __( 'fab-motorcycle', THEME_NAME ),
//   },
//   {
//     value: 'fa-bus',
//     label: __( 'fab-bus', THEME_NAME ),
//   },
//   {
//     value: 'fa-plane',
//     label: __( 'fab-plane', THEME_NAME ),
//   },
//   {
//     value: 'fa-train',
//     label: __( 'fab-train', THEME_NAME ),
//   },
//   {
//     value: 'fa-subway',
//     label: __( 'fab-subway', THEME_NAME ),
//   },
//   {
//     value: 'fa-taxi',
//     label: __( 'fab-taxi', THEME_NAME ),
//   },
//   {
//     value: 'fa-ship',
//     label: __( 'fab-ship', THEME_NAME ),
//   },
//   {
//     value: 'fa-jpy',
//     label: __( 'fab-jpy', THEME_NAME ),
//   },
//   {
//     value: 'fa-usd',
//     label: __( 'fab-usd', THEME_NAME ),
//   },
//   {
//     value: 'fa-eur',
//     label: __( 'fab-eur', THEME_NAME ),
//   },
//   {
//     value: 'fa-btc',
//     label: __( 'fab-btc', THEME_NAME ),
//   },
//   {
//     value: 'fa-apple',
//     label: __( 'fab-apple', THEME_NAME ),
//   },
//   {
//     value: 'fa-android',
//     label: __( 'fab-android', THEME_NAME ),
//   },
//   {
//     value: 'fa-wordpress',
//     label: __( 'fab-wordpress', THEME_NAME ),
//   },
// ];
























export const LIST_ICONS = [
  {
    value: 'list-caret-right',
    label: __( 'fab-caret-right', THEME_NAME ),
  },
  {
    value: 'list-check',
    label: __( 'fab-check', THEME_NAME ),
  },
  {
    value: 'list-check-circle',
    label: __( 'fab-check-circle', THEME_NAME ),
  },
  {
    value: 'list-check-circle-o',
    label: __( 'fab-check-circle-o', THEME_NAME ),
  },
  {
    value: 'list-check-square',
    label: __( 'fab-check-square', THEME_NAME ),
  },
  {
    value: 'list-check-square-o',
    label: __( 'fab-check-square-o', THEME_NAME ),
  },
  {
    value: 'list-caret-square-o-right',
    label: __( 'fab-caret-square-o-right', THEME_NAME ),
  },
  {
    value: 'list-arrow-right',
    label: __( 'fab-arrow-right', THEME_NAME ),
  },
  {
    value: 'list-angle-right',
    label: __( 'fab-angle-right', THEME_NAME ),
  },
  {
    value: 'list-angle-double-right',
    label: __( 'fab-angle-double-right', THEME_NAME ),
  },
  {
    value: 'list-arrow-circle-right',
    label: __( 'fab-arrow-circle-right', THEME_NAME ),
  },
  {
    value: 'list-arrow-circle-o-right',
    label: __( 'fab-arrow-circle-o-right', THEME_NAME ),
  },
  {
    value: 'list-play-circle',
    label: __( 'fab-play-circle', THEME_NAME ),
  },
  {
    value: 'list-play-circle-o',
    label: __( 'fab-play-circle-o', THEME_NAME ),
  },
  {
    value: 'list-chevron-right',
    label: __( 'fab-chevron-right', THEME_NAME ),
  },
  {
    value: 'list-chevron-circle-right',
    label: __( 'fab-chevron-circle-right', THEME_NAME ),
  },
  {
    value: 'list-hand-o-right',
    label: __( 'fab-hand-o-right', THEME_NAME ),
  },
  {
    value: 'list-star',
    label: __( 'fab-star', THEME_NAME ),
  },
  {
    value: 'list-star-o',
    label: __( 'fab-star-o', THEME_NAME ),
  },
  {
    value: 'list-heart',
    label: __( 'fab-heart', THEME_NAME ),
  },
  {
    value: 'list-heart-o',
    label: __( 'fab-heart-o', THEME_NAME ),
  },
  {
    value: 'list-square',
    label: __( 'fab-square', THEME_NAME ),
  },
  {
    value: 'list-square-o',
    label: __( 'fab-square-o', THEME_NAME ),
  },
  {
    value: 'list-circle',
    label: __( 'fab-circle', THEME_NAME ),
  },
  {
    value: 'list-circle-o',
    label: __( 'fab-circle-o', THEME_NAME ),
  },
  {
    value: 'list-dot-circle-o',
    label: __( 'fab-dot-circle-o', THEME_NAME ),
  },
  {
    value: 'list-plus',
    label: __( 'fab-plus', THEME_NAME ),
  },
  {
    value: 'list-plus-circle',
    label: __( 'fab-plus-circle', THEME_NAME ),
  },
  {
    value: 'list-plus-square',
    label: __( 'fab-plus-square', THEME_NAME ),
  },
  {
    value: 'list-plus-square-o',
    label: __( 'fab-plus-square-o', THEME_NAME ),
  },
  {
    value: 'list-minus',
    label: __( 'fab-minus', THEME_NAME ),
  },
  {
    value: 'list-minus-circle',
    label: __( 'fab-minus-circle', THEME_NAME ),
  },
  {
    value: 'list-minus-square',
    label: __( 'fab-minus-square', THEME_NAME ),
  },
  {
    value: 'list-minus-square-o',
    label: __( 'fab-minus-square-o', THEME_NAME ),
  },
  {
    value: 'list-times',
    label: __( 'fab-times', THEME_NAME ),
  },
  {
    value: 'list-times-circle',
    label: __( 'fab-times-circle', THEME_NAME ),
  },
  {
    value: 'list-times-circle-o',
    label: __( 'fab-times-circle-o', THEME_NAME ),
  },
  {
    value: 'list-window-close',
    label: __( 'fab-window-close', THEME_NAME ),
  },
  {
    value: 'list-window-close-o',
    label: __( 'fab-window-close-o', THEME_NAME ),
  },
  {
    value: 'list-paw',
    label: __( 'fab-paw', THEME_NAME ),
  },
];

export function LetterToolbarButton( { name, shortcutType, shortcutCharacter, ...props } ) {
  let shortcut;
  let fillName = 'Letter.ToolbarControls';

  if ( name ) {
    fillName += `.${ name }`;
  }

  if ( shortcutType && shortcutCharacter ) {
    shortcut = displayShortcut[ shortcutType ]( shortcutCharacter );
  }

  return (
    <Fill name={ fillName }>
      <ToolbarButton
        { ...props }
        shortcut={ shortcut }
      />
    </Fill>
  );
}

export function MarkerToolbarButton( { name, shortcutType, shortcutCharacter, ...props } ) {
  let shortcut;
  let fillName = 'Marker.ToolbarControls';

  if ( name ) {
    fillName += `.${ name }`;
  }

  if ( shortcutType && shortcutCharacter ) {
    shortcut = displayShortcut[ shortcutType ]( shortcutCharacter );
  }

  return (
    <Fill name={ fillName }>
      <ToolbarButton
        { ...props }
        shortcut={ shortcut }
      />
    </Fill>
  );
}

export function BadgeToolbarButton( { name, shortcutType, shortcutCharacter, ...props } ) {
  let shortcut;
  let fillName = 'Badge.ToolbarControls';

  if ( name ) {
    fillName += `.${ name }`;
  }

  if ( shortcutType && shortcutCharacter ) {
    shortcut = displayShortcut[ shortcutType ]( shortcutCharacter );
  }

  return (
    <Fill name={ fillName }>
      <ToolbarButton
        { ...props }
        shortcut={ shortcut }
      />
    </Fill>
  );
}


export function FontSizeToolbarButton( { name, shortcutType, shortcutCharacter, ...props } ) {
  let shortcut;
  let fillName = 'FontSize.ToolbarControls';

  if ( name ) {
    fillName += `.${ name }`;
  }

  if ( shortcutType && shortcutCharacter ) {
    shortcut = displayShortcut[ shortcutType ]( shortcutCharacter );
  }

  return (
    <Fill name={ fillName }>
      <ToolbarButton
        { ...props }
        shortcut={ shortcut }
      />
    </Fill>
  );
}


export function AffiliateToolbarButton( { name, shortcutType, shortcutCharacter, ...props } ) {
  let shortcut;
  let fillName = 'Affiliate.ToolbarControls';

  if ( name ) {
    fillName += `.${ name }`;
  }

  if ( shortcutType && shortcutCharacter ) {
    shortcut = displayShortcut[ shortcutType ]( shortcutCharacter );
  }

  return (
    <Fill name={ fillName }>
      <ToolbarButton
        { ...props }
        shortcut={ shortcut }
      />
    </Fill>
  );
}


export function TemplateToolbarButton( { name, shortcutType, shortcutCharacter, ...props } ) {
  let shortcut;
  let fillName = 'Template.ToolbarControls';

  if ( name ) {
    fillName += `.${ name }`;
  }

  if ( shortcutType && shortcutCharacter ) {
    shortcut = displayShortcut[ shortcutType ]( shortcutCharacter );
  }

  return (
    <Fill name={ fillName }>
      <ToolbarButton
        { ...props }
        shortcut={ shortcut }
      />
    </Fill>
  );
}


export function ShortcodeToolbarButton( { name, shortcutType, shortcutCharacter, ...props } ) {
  let shortcut;
  let fillName = 'Shortcode.ToolbarControls';

  if ( name ) {
    fillName += `.${ name }`;
  }

  if ( shortcutType && shortcutCharacter ) {
    shortcut = displayShortcut[ shortcutType ]( shortcutCharacter );
  }

  return (
    <Fill name={ fillName }>
      <ToolbarButton
        { ...props }
        shortcut={ shortcut }
      />
    </Fill>
  );
}


export function RankingToolbarButton( { name, shortcutType, shortcutCharacter, ...props } ) {
  let shortcut;
  let fillName = 'Ranking.ToolbarControls';

  if ( name ) {
    fillName += `.${ name }`;
  }

  if ( shortcutType && shortcutCharacter ) {
    shortcut = displayShortcut[ shortcutType ]( shortcutCharacter );
  }

  return (
    <Fill name={ fillName }>
      <ToolbarButton
        { ...props }
        shortcut={ shortcut }
      />
    </Fill>
  );
}

//コードブロックの言語配列
export const CODE_LANGUAGES = gbCodeLanguages;

// export const CODE_LANGUAGES = [
//   {
//     value: '',
//     label: __( '自動判別', THEME_NAME ),
//   },
//   {
//     value: 'nohighlight',
//     label: __( 'ハイライト表示しない', THEME_NAME ),
//   },
//   {
//     value: 'plaintext',
//     label: __( 'プレーンテキスト', THEME_NAME ),
//   },
//   {
//     value: '1c',
//     label: __( '1C', THEME_NAME ),
//   },
//   {
//     value: 'abnf',
//     label: __( 'ABNF', THEME_NAME ),
//   },
//   {
//     value: 'accesslog',
//     label: __( 'Access logs', THEME_NAME ),
//   },
//   {
//     value: 'ada',
//     label: __( 'Ada', THEME_NAME ),
//   },
//   {
//     value: 'armasm',
//     label: __( 'ARM assembler', THEME_NAME ),
//   },
//   {
//     value: 'avrasm',
//     label: __( 'AVR assembler', THEME_NAME ),
//   },
//   {
//     value: 'actionscript',
//     label: __( 'ActionScript', THEME_NAME ),
//   },
//   {
//     value: 'apache',
//     label: __( 'Apache', THEME_NAME ),
//   },
//   {
//     value: 'applescript',
//     label: __( 'AppleScript', THEME_NAME ),
//   },
//   {
//     value: 'asciidoc',
//     label: __( 'AsciiDoc', THEME_NAME ),
//   },
//   {
//     value: 'aspectj',
//     label: __( 'AspectJ', THEME_NAME ),
//   },
//   {
//     value: 'autohotkey',
//     label: __( 'AutoHotkey', THEME_NAME ),
//   },
//   {
//     value: 'autoit',
//     label: __( 'AutoIt', THEME_NAME ),
//   },
//   {
//     value: 'awk',
//     label: __( 'Awk', THEME_NAME ),
//   },
//   {
//     value: 'axapta',
//     label: __( 'Axapta', THEME_NAME ),
//   },
//   {
//     value: 'bash',
//     label: __( 'Bash', THEME_NAME ),
//   },
//   {
//     value: 'basic',
//     label: __( 'Basic', THEME_NAME ),
//   },
//   {
//     value: 'bnf',
//     label: __( 'BNF', THEME_NAME ),
//   },
//   {
//     value: 'brainfuck',
//     label: __( 'Brainfuck', THEME_NAME ),
//   },
//   {
//     value: 'cs',
//     label: __( 'C#', THEME_NAME ),
//   },
//   {
//     value: 'cpp',
//     label: __( 'C++', THEME_NAME ),
//   },
//   {
//     value: 'cal',
//     label: __( 'C/AL', THEME_NAME ),
//   },
//   {
//     value: 'cos',
//     label: __( 'Cache Object Script', THEME_NAME ),
//   },
//   {
//     value: 'cmake',
//     label: __( 'CMake', THEME_NAME ),
//   },
//   {
//     value: 'coq',
//     label: __( 'Coq', THEME_NAME ),
//   },
//   {
//     value: 'csp',
//     label: __( 'CSP', THEME_NAME ),
//   },
//   {
//     value: 'css',
//     label: __( 'CSS', THEME_NAME ),
//   },
//   {
//     value: 'capnproto',
//     label: __( 'Cap’n Proto', THEME_NAME ),
//   },
//   {
//     value: 'clojure',
//     label: __( 'Clojure', THEME_NAME ),
//   },
//   {
//     value: 'coffeescript',
//     label: __( 'CoffeeScript', THEME_NAME ),
//   },
//   {
//     value: 'crmsh',
//     label: __( 'Crmsh', THEME_NAME ),
//   },
//   {
//     value: 'crystal',
//     label: __( 'Crystal', THEME_NAME ),
//   },
//   {
//     value: 'd',
//     label: __( 'D', THEME_NAME ),
//   },
//   {
//     value: 'dns',
//     label: __( 'DNS Zone file', THEME_NAME ),
//   },
//   {
//     value: 'dos',
//     label: __( 'DOS', THEME_NAME ),
//   },
//   {
//     value: 'dart',
//     label: __( 'Dart', THEME_NAME ),
//   },
//   {
//     value: 'delphi',
//     label: __( 'Delphi', THEME_NAME ),
//   },
//   {
//     value: 'diff',
//     label: __( 'Diff', THEME_NAME ),
//   },
//   {
//     value: 'django',
//     label: __( 'Django', THEME_NAME ),
//   },
//   {
//     value: 'dockerfile',
//     label: __( 'Dockerfile', THEME_NAME ),
//   },
//   {
//     value: 'dsconfig',
//     label: __( 'dsconfig', THEME_NAME ),
//   },
//   {
//     value: 'dts',
//     label: __( 'DTS (Device Tree)', THEME_NAME ),
//   },
//   {
//     value: 'dust',
//     label: __( 'Dust', THEME_NAME ),
//   },
//   {
//     value: 'ebnf',
//     label: __( 'EBNF', THEME_NAME ),
//   },
//   {
//     value: 'elixir',
//     label: __( 'Elixir', THEME_NAME ),
//   },
//   {
//     value: 'elm',
//     label: __( 'Elm', THEME_NAME ),
//   },
//   {
//     value: 'erlang',
//     label: __( 'Erlang', THEME_NAME ),
//   },
//   {
//     value: 'excel',
//     label: __( 'Excel', THEME_NAME ),
//   },
//   {
//     value: 'fsharp',
//     label: __( 'F#', THEME_NAME ),
//   },
//   {
//     value: 'fix',
//     label: __( 'FIX', THEME_NAME ),
//   },
//   {
//     value: 'fortran',
//     label: __( 'Fortran', THEME_NAME ),
//   },
//   {
//     value: 'gcode',
//     label: __( 'G-Code', THEME_NAME ),
//   },
//   {
//     value: 'gams',
//     label: __( 'Gams', THEME_NAME ),
//   },
//   {
//     value: 'gauss',
//     label: __( 'GAUSS', THEME_NAME ),
//   },
//   {
//     value: 'gherkin',
//     label: __( 'Gherkin', THEME_NAME ),
//   },
//   {
//     value: 'go',
//     label: __( 'Go', THEME_NAME ),
//   },
//   {
//     value: 'golo',
//     label: __( 'Golo', THEME_NAME ),
//   },
//   {
//     value: 'gradle',
//     label: __( 'Gradle', THEME_NAME ),
//   },
//   {
//     value: 'groovy',
//     label: __( 'Groovy', THEME_NAME ),
//   },
//   {
//     value: 'html',
//     label: __( 'HTML', THEME_NAME ),
//   },
//   {
//     value: 'http',
//     label: __( 'HTTP', THEME_NAME ),
//   },
//   {
//     value: 'haml',
//     label: __( 'Haml', THEME_NAME ),
//   },
//   {
//     value: 'handlebars',
//     label: __( 'Handlebars', THEME_NAME ),
//   },
//   {
//     value: 'haskell',
//     label: __( 'Haskell', THEME_NAME ),
//   },
//   {
//     value: 'haxe',
//     label: __( 'Haxe', THEME_NAME ),
//   },
//   {
//     value: 'hy',
//     label: __( 'Hy', THEME_NAME ),
//   },
//   {
//     value: 'ini',
//     label: __( 'Ini', THEME_NAME ),
//   },
//   {
//     value: 'inform7',
//     label: __( 'Inform7', THEME_NAME ),
//   },
//   {
//     value: 'irpf90',
//     label: __( 'IRPF90', THEME_NAME ),
//   },
//   {
//     value: 'json',
//     label: __( 'JSON', THEME_NAME ),
//   },
//   {
//     value: 'java',
//     label: __( 'Java', THEME_NAME ),
//   },
//   {
//     value: 'javascript',
//     label: __( 'JavaScript', THEME_NAME ),
//   },
//   {
//     value: 'leaf',
//     label: __( 'Leaf', THEME_NAME ),
//   },
//   {
//     value: 'lasso',
//     label: __( 'Lasso', THEME_NAME ),
//   },
//   {
//     value: 'less',
//     label: __( 'Less', THEME_NAME ),
//   },
//   {
//     value: 'ldif',
//     label: __( 'LDIF', THEME_NAME ),
//   },
//   {
//     value: 'lisp',
//     label: __( 'Lisp', THEME_NAME ),
//   },
//   {
//     value: 'livecodeserver',
//     label: __( 'LiveCode Server', THEME_NAME ),
//   },
//   {
//     value: 'livescript',
//     label: __( 'LiveScript', THEME_NAME ),
//   },
//   {
//     value: 'lua',
//     label: __( 'Lua', THEME_NAME ),
//   },
//   {
//     value: 'makefile',
//     label: __( 'Makefile', THEME_NAME ),
//   },
//   {
//     value: 'markdown',
//     label: __( 'Markdown', THEME_NAME ),
//   },
//   {
//     value: 'mathematica',
//     label: __( 'Mathematica', THEME_NAME ),
//   },
//   {
//     value: 'matlab',
//     label: __( 'Matlab', THEME_NAME ),
//   },
//   {
//     value: 'maxima',
//     label: __( 'Maxima', THEME_NAME ),
//   },
//   {
//     value: 'mel',
//     label: __( 'Maya Embedded Language', THEME_NAME ),
//   },
//   {
//     value: 'mercury',
//     label: __( 'Mercury', THEME_NAME ),
//   },
//   {
//     value: 'mizar',
//     label: __( 'Mizar', THEME_NAME ),
//   },
//   {
//     value: 'mojolicious',
//     label: __( 'Mojolicious', THEME_NAME ),
//   },
//   {
//     value: 'monkey',
//     label: __( 'Monkey', THEME_NAME ),
//   },
//   {
//     value: 'moonscript',
//     label: __( 'Moonscript', THEME_NAME ),
//   },
//   {
//     value: 'n1ql',
//     label: __( 'N1QL', THEME_NAME ),
//   },
//   {
//     value: 'nsis',
//     label: __( 'NSIS', THEME_NAME ),
//   },
//   {
//     value: 'nginx',
//     label: __( 'Nginx', THEME_NAME ),
//   },
//   {
//     value: 'nimrod',
//     label: __( 'Nimrod', THEME_NAME ),
//   },
//   {
//     value: 'nix',
//     label: __( 'Nix', THEME_NAME ),
//   },
//   {
//     value: 'ocaml',
//     label: __( 'OCaml', THEME_NAME ),
//   },
//   {
//     value: 'objectivec',
//     label: __( 'Objective C', THEME_NAME ),
//   },
//   {
//     value: 'glsl',
//     label: __( 'OpenGL Shading Language', THEME_NAME ),
//   },
//   {
//     value: 'openscad',
//     label: __( 'OpenSCAD', THEME_NAME ),
//   },
//   {
//     value: 'ruleslanguage',
//     label: __( 'Oracle Rules Language', THEME_NAME ),
//   },
//   {
//     value: 'oxygene',
//     label: __( 'Oxygene', THEME_NAME ),
//   },
//   {
//     value: 'pf',
//     label: __( 'PF', THEME_NAME ),
//   },
//   {
//     value: 'php',
//     label: __( 'PHP', THEME_NAME ),
//   },
//   {
//     value: 'parser3',
//     label: __( 'Parser3', THEME_NAME ),
//   },
//   {
//     value: 'perl',
//     label: __( 'Perl', THEME_NAME ),
//   },
//   {
//     value: 'pony',
//     label: __( 'Pony', THEME_NAME ),
//   },
//   {
//     value: 'powershell',
//     label: __( 'PowerShell', THEME_NAME ),
//   },
//   {
//     value: 'processing',
//     label: __( 'Processing', THEME_NAME ),
//   },
//   {
//     value: 'prolog',
//     label: __( 'Prolog', THEME_NAME ),
//   },
//   {
//     value: 'protobuf',
//     label: __( 'Protocol Buffers', THEME_NAME ),
//   },
//   {
//     value: 'puppet',
//     label: __( 'Puppet', THEME_NAME ),
//   },
//   {
//     value: 'python',
//     label: __( 'Python', THEME_NAME ),
//   },
//   {
//     value: 'profile',
//     label: __( 'Python profiler results', THEME_NAME ),
//   },
//   {
//     value: 'k',
//     label: __( 'Q', THEME_NAME ),
//   },
//   {
//     value: 'qml',
//     label: __( 'QML', THEME_NAME ),
//   },
//   {
//     value: 'r',
//     label: __( 'R', THEME_NAME ),
//   },
//   {
//     value: 'rib',
//     label: __( 'RenderMan RIB', THEME_NAME ),
//   },
//   {
//     value: 'rsl',
//     label: __( 'RenderMan RSL', THEME_NAME ),
//   },
//   {
//     value: 'graph',
//     label: __( 'Roboconf', THEME_NAME ),
//   },
//   {
//     value: 'ruby',
//     label: __( 'Ruby', THEME_NAME ),
//   },
//   {
//     value: 'rust',
//     label: __( 'Rust', THEME_NAME ),
//   },
//   {
//     value: 'scss',
//     label: __( 'SCSS', THEME_NAME ),
//   },
//   {
//     value: 'sql',
//     label: __( 'SQL', THEME_NAME ),
//   },
//   {
//     value: 'p21',
//     label: __( 'STEP Part 21', THEME_NAME ),
//   },
//   {
//     value: 'scala',
//     label: __( 'Scala', THEME_NAME ),
//   },
//   {
//     value: 'scheme',
//     label: __( 'Scheme', THEME_NAME ),
//   },
//   {
//     value: 'scilab',
//     label: __( 'Scilab', THEME_NAME ),
//   },
//   {
//     value: 'shell',
//     label: __( 'Shell', THEME_NAME ),
//   },
//   {
//     value: 'smali',
//     label: __( 'Smali', THEME_NAME ),
//   },
//   {
//     value: 'smalltalk',
//     label: __( 'Smalltalk', THEME_NAME ),
//   },
//   {
//     value: 'stan',
//     label: __( 'Stan', THEME_NAME ),
//   },
//   {
//     value: 'stata',
//     label: __( 'Stata', THEME_NAME ),
//   },
//   {
//     value: 'stylus',
//     label: __( 'Stylus', THEME_NAME ),
//   },
//   {
//     value: 'subunit',
//     label: __( 'SubUnit', THEME_NAME ),
//   },
//   {
//     value: 'swift',
//     label: __( 'Swift', THEME_NAME ),
//   },
//   {
//     value: 'tap',
//     label: __( 'Test Anything Protocol', THEME_NAME ),
//   },
//   {
//     value: 'tcl',
//     label: __( 'Tcl', THEME_NAME ),
//   },
//   {
//     value: 'tex',
//     label: __( 'TeX', THEME_NAME ),
//   },
//   {
//     value: 'thrift',
//     label: __( 'Thrift', THEME_NAME ),
//   },
//   {
//     value: 'tp',
//     label: __( 'TP', THEME_NAME ),
//   },
//   {
//     value: 'twig',
//     label: __( 'Twig', THEME_NAME ),
//   },
//   {
//     value: 'typescript',
//     label: __( 'TypeScript', THEME_NAME ),
//   },
//   {
//     value: 'vbnet',
//     label: __( 'VB.Net', THEME_NAME ),
//   },
//   {
//     value: 'vbscript',
//     label: __( 'VBScript', THEME_NAME ),
//   },
//   {
//     value: 'vhdl',
//     label: __( 'VHDL', THEME_NAME ),
//   },
//   {
//     value: 'vala',
//     label: __( 'Vala', THEME_NAME ),
//   },
//   {
//     value: 'verilog',
//     label: __( 'Verilog', THEME_NAME ),
//   },
//   {
//     value: 'vim',
//     label: __( 'Vim Script', THEME_NAME ),
//   },
//   {
//     value: 'x86asm',
//     label: __( 'x86 Assembly', THEME_NAME ),
//   },
//   {
//     value: 'xl',
//     label: __( 'XL', THEME_NAME ),
//   },
//   {
//     value: 'xml',
//     label: __( 'XML', THEME_NAME ),
//   },
//   {
//     value: 'xpath',
//     label: __( 'XQuery', THEME_NAME ),
//   },
//   {
//     value: 'zephir',
//     label: __( 'Zephir', THEME_NAME ),
//   },
// ];
