/**
 * Cocoon Blocks
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */

import classnames from 'classnames';

import { __ } from '@wordpress/i18n';
import {
  Fill,
  ToolbarButton,
  withFallbackStyles,
  CheckboxControl,
} from '@wordpress/components';
import {
  getColorObjectByColorValue,
  getColorObjectByAttributeValues,
  getColorClassName,
} from '@wordpress/block-editor';
import { displayShortcut } from '@wordpress/keycodes';


const { getComputedStyle } = window;

export const THEME_NAME = 'cocoon';
export const BLOCK_CLASS = ' block-box';
export const BUTTON_BLOCK = 'button-block';
export const LAYOUT_BLOCK_CLASS = 'layout-box';
export const PARAGRAPH_CLASS = ' paragraph';
export const CLICK_POINT_MSG = __(
  'こちらをクリックして設定変更。この入力は公開ページで反映されません。',
  THEME_NAME
);
export const BLOCK_SERIES = 'cocoon-blocks';

export const keyColor = gbColors[ 'keyColor' ];

//日時をもとにしたID作成
export function getDateID() {
  //Dateオブジェクトを利用
  var d = new Date();
  var year = d.getFullYear();
  var month = d.getMonth() + 1;
  var month = month < 10 ? '0' + month : month;
  var day = d.getDate() < 10 ? '0' + d.getDate() : d.getDate();
  var hour = d.getHours() < 10 ? '0' + d.getHours() : d.getHours();
  var min = d.getMinutes() < 10 ? '0' + d.getMinutes() : d.getMinutes();
  var sec = d.getSeconds() < 10 ? '0' + d.getSeconds() : d.getSeconds();
  var dateID = '' + year + month + day + hour + min + sec;
  return dateID;
}

//アイコンクラス文字列を取得
export function getIconClass( icon ) {
  return icon ? ' ' + icon : '';
}

//バルーンclassの取得
export function getBalloonClasses( id, style, position, iconstyle ) {
  const classes = classnames( {
    [ 'speech-wrap' ]: true,
    [ `sb-id-${ id }` ]: !! id,
    [ `sbs-${ style }` ]: !! style,
    [ `sbp-${ position }` ]: !! position,
    [ `sbis-${ iconstyle }` ]: !! iconstyle,
    [ 'cf' ]: true,
    [ 'block-box' ]: true,
  } );
  return classes;
}

//オブジェクト吹き出しと保存した吹き出しの情報が同じか
export function isSameBalloon( index, id, icon, style, position, iconstyle ) {
  if ( gbSpeechBalloons[ index ] ) {
    if (
      gbSpeechBalloons[ index ].id == id &&
      gbSpeechBalloons[ index ].icon == icon &&
      gbSpeechBalloons[ index ].style == style &&
      gbSpeechBalloons[ index ].position == position &&
      gbSpeechBalloons[ index ].iconstyle == iconstyle
    ) {
      return true;
    }
  }
  return false;
}

export const fullFallbackStyles = withFallbackStyles( ( node, ownProps ) => {
  const { textColor, backgroundColor, borderColor, fontSize, customFontSize } =
    ownProps.attributes;
  const editableNode = node.querySelector( '[contenteditable="true"]' );
  //verify if editableNode is available, before using getComputedStyle.
  const computedStyles = editableNode ? getComputedStyle( editableNode ) : null;
  return {
    fallbackBackgroundColor:
      backgroundColor || ! computedStyles
        ? undefined
        : computedStyles.backgroundColor,
    fallbackTextColor:
      textColor || ! computedStyles ? undefined : computedStyles.color,
    fallbackBorderColor:
      borderColor || ! computedStyles ? undefined : computedStyles.color,
    fallbackFontSize:
      fontSize || customFontSize || ! computedStyles
        ? undefined
        : parseInt( computedStyles.fontSize ) || undefined,
  };
} );

//現在のカラーパレットのスラッグを取得
export function getCurrentColorSlug( colors, color ) {
  const object = getColorObjectByColorValue( colors, color );
  let slug = 'key-color';
  if ( object ) {
    slug = object.slug;
  }
  return slug;
}

//現在のカラーパレットのスラッグを取得
export function getCurrentColorCode( colors, slug ) {
  //console.log(colors);
  const object = getColorObjectByAttributeValues( colors, slug, keyColor );
  //console.log(object);
  let color = keyColor;
  if ( object ) {
    color = object.color;
  }
  return color;
}

//HTMLエスケープ
export function htmlEscape( str ) {
  if ( ! str ) return;
  return str.replace( /[<>&"'`]/g, function ( match ) {
    const escape = {
      '<': '&lt;',
      '>': '&gt;',
      '&': '&amp;',
      '"': '&quot;',
      "'": '&#39;',
      '`': '&#x60;',
    };
    return escape[ match ];
  } );
}

//吹き出しが存在しているかどうか
export function isBalloonExist( speechBalloons ) {
  var res = false;
  if ( speechBalloons && speechBalloons[ 0 ] ) {
    speechBalloons.some( function ( balloon ) {
      if ( balloon.visible == 1 ) {
        res = true;
      }
    } );
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
export function colorValueToSlug( color ) {
  switch ( color ) {
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
    case '#dddddd':
      return 'light-grey';
      break;
    case '#ddd':
      return 'light-grey';
      break;
    case '#949495':
      return 'grey';
      break;
    case '#666666':
      return 'dark-grey';
      break;
    case '#666':
      return 'dark-grey';
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

export function LetterToolbarButton( {
  name,
  shortcutType,
  shortcutCharacter,
  ...props
} ) {
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
      <ToolbarButton { ...props } shortcut={ shortcut } />
    </Fill>
  );
}

export function MarkerToolbarButton( {
  name,
  shortcutType,
  shortcutCharacter,
  ...props
} ) {
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
      <ToolbarButton { ...props } shortcut={ shortcut } />
    </Fill>
  );
}

export function BadgeToolbarButton( {
  name,
  shortcutType,
  shortcutCharacter,
  ...props
} ) {
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
      <ToolbarButton { ...props } shortcut={ shortcut } />
    </Fill>
  );
}

export function FontSizeToolbarButton( {
  name,
  shortcutType,
  shortcutCharacter,
  ...props
} ) {
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
      <ToolbarButton { ...props } shortcut={ shortcut } />
    </Fill>
  );
}

export function AffiliateToolbarButton( {
  name,
  shortcutType,
  shortcutCharacter,
  ...props
} ) {
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
      <ToolbarButton { ...props } shortcut={ shortcut } />
    </Fill>
  );
}

export function TemplateToolbarButton( {
  name,
  shortcutType,
  shortcutCharacter,
  ...props
} ) {
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
      <ToolbarButton { ...props } shortcut={ shortcut } />
    </Fill>
  );
}

export function ShortcodeToolbarButton( {
  name,
  shortcutType,
  shortcutCharacter,
  ...props
} ) {
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
      <ToolbarButton { ...props } shortcut={ shortcut } />
    </Fill>
  );
}

export function RankingToolbarButton( {
  name,
  shortcutType,
  shortcutCharacter,
  ...props
} ) {
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
      <ToolbarButton { ...props } shortcut={ shortcut } />
    </Fill>
  );
}

//コードブロックの言語配列
export const CODE_LANGUAGES = gbCodeLanguages;

/**
 * カンマ区切りの数字を羅列した文字列に数字を追加する関数
 * @param {string} data 数字を羅列した文字列
 * @param {integer} num 追加する数字
 */
export function AddNumToData( data, num ) {
  let newData = '';
  if ( data == '' ) {
    newData = String( num );
  } else {
    let dataArray = data.split( ',' );
    let found = false;
    for ( var i = 0; i < dataArray.length; i++ ) {
      if ( dataArray[ i ] == num ) {
        found = true;
        break;
      }
    }
    if ( found == false ) {
      dataArray.push( num );
    }
    newData = dataArray.join( ',' );
  }
  return newData;
}

/**
 * カンマ区切りの数字を羅列した文字列から数字を削除する関数
 * @param {string} data 数字を羅列した文字列
 * @param {integer} num 削除する数字
 */
export function DeleteNumFromData( data, num ) {
  let dataArray = data.split( ',' );
  for ( var i = 0; i < dataArray.length; i++ ) {
    if ( dataArray[ i ] == num ) {
      dataArray.splice( i, 1 );
    }
  }
  return dataArray.join( ',' );
}

/**
 * カテゴリ検索用のCheckboxControlを作成する関数
 * @param {boolean} isChecked チェックボックスの選択状態
 * @param {string} label チェックボックスのラベル
 * @param {function} onChange チェック状態変更時のコールバック関数
 */
export function CreateCategory( isChecked, label, onChange ) {
  return (
    <CheckboxControl
      label={ label }
      checked={ isChecked }
      onChange={ ( isChecked ) => {
        onChange( isChecked );
      } }
    />
  );
}

/**
 * 検索文字列に応じてカテゴリのリストを作成する関数
 * @param {entityRecords}} catData wpcoreから取得したカテゴリ情報一覧
 * @param {string} input 検索文字列
 * @param {string} data 現在のカテゴリ一覧(カンマ区切り文字列)
 * @param {function} updateAttr attributes更新用コールバック関数
 */
export function CreateCategoryList( catData, input, data, updateAttr ) {
  if ( catData == null ) return null;

  let control = [];

  let dataArray = data.split( ',' );
  catData.forEach( ( record ) => {
    let isChecked = false;
    for ( var i = 0; i < dataArray.length; i++ ) {
      if ( dataArray[ i ] == String( record.id ) ) {
        isChecked = true;
        break;
      }
    }
    //検索文字列がある場合、該当しないカテゴリは追加しない
    if ( input != '' && record.name.indexOf( input ) == -1 ) {
      return;
    }
    control.push(
      CreateCategory( isChecked, record.name, function ( isChecked ) {
        var newData = '';
        if ( isChecked == true ) {
          newData = AddNumToData( data, String( record.id ) );
        } else {
          newData = DeleteNumFromData( data, String( record.id ) );
        }
        updateAttr( newData );
      } )
    );
  } );

  return (
    <div className="category-checkbox-list">
      {control}
    </div>
  );
}

export function hexToRgba(hex, alpha = 1) {
  // 先頭の#を取り除く
  hex = hex.replace('#', '');

  // 3桁のカラーコードを6桁に変換
  if (hex.length === 3) {
    hex = hex.split('').map(function(h) {
      return h + h;
    }).join('');
  }

  // 16進数を10進数に変換してRGBの形式に
  var r = parseInt(hex.substring(0, 2), 16);
  var g = parseInt(hex.substring(2, 4), 16);
  var b = parseInt(hex.substring(4, 6), 16);

  return `rgba(${r}, ${g}, ${b}, ${alpha})`;
}

// キャンバス用のユニークなIDを取得する
export function getCanvasId() {
  return `canvas-${Math.random().toString(36).substr(2, 9)}`;
}

// 配列の値の総計
export function arrayValueTotal(array) {
  return array.reduce((accumulator, currentValue) => {
    // 空文字をチェックし、空なら0に置き換える
    const value = isNaN(currentValue) ? 0 : Number(currentValue);
    return accumulator + value;
  }, 0);
}

// // 配列の値の総計
// export function radarValueTotal(array) {
//   return ' [ ' + __( '総計: ', THEME_NAME ) +  arrayValueTotal(array) + ' ]'
// }

// フォントの高さを取得
export function getChartJsFontHeight(fontSize) {
  // 一時的な要素を作成
  let element = document.createElement('span');

  // 要素のスタイルを設定
  element.style.fontFamily = "'Helvetica Neue', 'Helvetica', 'Arial', sans-serif'";
  element.style.fontSize = fontSize;
  element.style.position = 'absolute';
  element.style.visibility = 'hidden';
  element.style.whiteSpace = 'nowrap';

  // 高さを測定するための文字列を挿入
  element.innerText = 'Hg'; // 「H」と「g」は高い文字と低い文字の代表例

  // DOM に追加
  document.body.appendChild(element);

  // 高さを取得
  let height = element.offsetHeight;

  // 要素をDOMから削除
  document.body.removeChild(element);

  return height;
}