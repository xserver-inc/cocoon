const NUMERIC_STYLE_KEY_PATTERN = /^(0|[1-9]\d*)$/;

// `[object Object]` がクラス名の一部として現れる実際の破損パターンだけを対象にします。
const BROKEN_CLASS_VALUE_PATTERN = /(?:^|\s)(?:sbs-)?\[object Object\](?=\s|$)/;

// WordPress 7.0 が文字列をオブジェクトへ展開して作った連番キーだけを判定します。
const isNumericStyleKey = ( key ) => NUMERIC_STYLE_KEY_PATTERN.test( key );

// 文字列スプレッド由来の0..n連番だけを、ソートを使わず安全に取り出します。
const getLegacyStyleCharacters = ( style ) => {
  if ( ! style || typeof style !== 'object' || Array.isArray( style ) ) {
    return undefined;
  }

  const numericKeyCount =
    Object.keys( style ).filter( isNumericStyleKey ).length;
  if ( numericKeyCount === 0 ) {
    return undefined;
  }

  const characters = [];

  // 0から件数分を直接走査し、巨大indexや欠番をO(n)で拒否します。
  for ( let index = 0; index < numericKeyCount; index++ ) {
    const key = String( index );
    if ( ! Object.prototype.hasOwnProperty.call( style, key ) ) {
      return undefined;
    }

    const character = style[ key ];
    if ( typeof character !== 'string' || character.length !== 1 ) {
      return undefined;
    }

    characters.push( character );
  }

  return characters;
};

// HTMLの最初のルート要素から、引用符を考慮して開始タグだけを取り出します。
const getFirstRootOpeningTag = ( html ) => {
  if ( typeof html !== 'string' || html.length === 0 ) {
    return '';
  }

  let searchFrom = 0;

  while ( searchFrom < html.length ) {
    const tagStart = html.indexOf( '<', searchFrom );
    if ( tagStart === -1 ) {
      return '';
    }

    // コメントやDOCTYPEなどはルート要素として扱わず、次の開始タグを探します。
    if ( html.startsWith( '<!--', tagStart ) ) {
      const commentEnd = html.indexOf( '-->', tagStart + 4 );
      if ( commentEnd === -1 ) {
        return '';
      }
      searchFrom = commentEnd + 3;
      continue;
    }

    const firstCharacter = html[ tagStart + 1 ];
    if ( ! firstCharacter || ! /[A-Za-z]/.test( firstCharacter ) ) {
      searchFrom = tagStart + 1;
      continue;
    }

    let quote = '';
    for ( let index = tagStart + 1; index < html.length; index++ ) {
      const character = html[ index ];

      if ( quote ) {
        if ( character === quote ) {
          quote = '';
        }
        continue;
      }

      if ( character === '"' || character === "'" ) {
        quote = character;
      } else if ( character === '>' ) {
        return html.slice( tagStart, index + 1 );
      }
    }

    return '';
  }

  return '';
};

// 別属性の引用値内にある「class=」を誤認しないよう、開始タグの属性を順に走査します。
const getRootClassValue = ( html ) => {
  const openingTag = getFirstRootOpeningTag( html );
  if ( ! openingTag ) {
    return '';
  }

  let index = 1;

  // 要素名の終端まで進み、以降を属性名・属性値の組として読み取ります。
  while (
    index < openingTag.length &&
    ! /[\s/>]/.test( openingTag[ index ] )
  ) {
    index++;
  }

  while ( index < openingTag.length ) {
    while ( /\s/.test( openingTag[ index ] ) ) {
      index++;
    }

    if ( openingTag[ index ] === '>' || openingTag[ index ] === '/' ) {
      break;
    }

    const nameStart = index;
    while (
      index < openingTag.length &&
      ! /[\s=/>]/.test( openingTag[ index ] )
    ) {
      index++;
    }
    const attributeName = openingTag.slice( nameStart, index ).toLowerCase();

    while ( /\s/.test( openingTag[ index ] ) ) {
      index++;
    }

    let attributeValue = '';
    if ( openingTag[ index ] === '=' ) {
      index++;
      while ( /\s/.test( openingTag[ index ] ) ) {
        index++;
      }

      const quote = openingTag[ index ];
      if ( quote === '"' || quote === "'" ) {
        index++;
        const valueStart = index;
        while ( index < openingTag.length && openingTag[ index ] !== quote ) {
          index++;
        }
        attributeValue = openingTag.slice( valueStart, index );
        if ( openingTag[ index ] === quote ) {
          index++;
        }
      } else {
        const valueStart = index;
        while (
          index < openingTag.length &&
          ! /[\s>]/.test( openingTag[ index ] )
        ) {
          index++;
        }
        attributeValue = openingTag.slice( valueStart, index );
      }
    }

    if ( attributeName === 'class' ) {
      return attributeValue;
    }
  }

  return '';
};

// WordPressの版によるcontext形状の違いを吸収して、元のブロックHTMLを取得します。
const getOriginalBlockContent = ( context ) => {
  const originalContent = context?.block?.originalContent;

  if ( typeof originalContent === 'string' && originalContent.length > 0 ) {
    return originalContent;
  }

  return context?.blockNode?.innerHTML ?? '';
};

// ルート要素のclass属性にWordPress 7.0由来の壊れた値があるかを判定します。
const hasBrokenRootClass = ( context ) =>
  BROKEN_CLASS_VALUE_PATTERN.test(
    getRootClassValue( getOriginalBlockContent( context ) )
  );

/**
 * 投稿コメントに旧Cocoonが使用していたstyle属性が残っているかを判定します。
 *
 * @param {Object} attributes   投稿コメントから読み込んだ属性
 * @param {Array}  _innerBlocks 未使用の内部ブロック
 * @param {Object} context      WordPressが渡す解析中ブロックの情報
 * @return {boolean} 旧文字列またはWordPress 7.0でオブジェクト化された形式ならtrue
 */
export const isLegacyStyle = ( attributes, _innerBlocks, context ) => {
  const { style } = attributes || {};

  if ( typeof style === 'string' ) {
    return true;
  }

  // 権限変更でstyle自体が削除されても、ルートHTMLに破損跡が残る場合は復旧対象にします。
  if ( hasBrokenRootClass( context ) ) {
    return true;
  }

  if ( ! style || typeof style !== 'object' || Array.isArray( style ) ) {
    return false;
  }

  return getLegacyStyleCharacters( style ) !== undefined;
};

/**
 * 旧 Cocoon の style 文字列、または WordPress 7.0 で壊れた style オブジェクトから
 * Cocoon 独自のスタイル名を復元します。
 *
 * @param {string|Object} style        旧 style 属性
 * @param {string}        defaultValue 復元できない場合の初期値
 * @return {string} 復元した Cocoon のスタイル名
 */
export const recoverLegacyStyleValue = ( style, defaultValue ) => {
  if ( typeof style === 'string' ) {
    return style;
  }

  if ( ! style || typeof style !== 'object' || Array.isArray( style ) ) {
    return defaultValue;
  }

  const characters = getLegacyStyleCharacters( style );
  if ( ! characters ) {
    return defaultValue;
  }

  return characters.join( '' );
};

/**
 * WordPress 標準の style オブジェクトから、Cocoon旧属性由来の連番キーだけを除去します。
 *
 * @param {Object} style 旧 style 属性
 * @return {Object|undefined} WordPress 標準として残す style 属性
 */
export const cleanCoreStyle = ( style ) => {
  if ( ! style || typeof style !== 'object' || Array.isArray( style ) ) {
    return undefined;
  }

  // css・color・spacing など、WordPress 標準の非数値キーはそのまま保持します。
  const coreStyleEntries = Object.entries( style ).filter(
    ( [ key ] ) => ! isNumericStyleKey( key )
  );

  if ( coreStyleEntries.length === 0 ) {
    return undefined;
  }

  return Object.fromEntries( coreStyleEntries );
};

/**
 * 旧 style 属性を新しい Cocoon 専用属性へ移し、WordPress 標準 style も保持します。
 *
 * @param {Object} attributes    旧ブロック属性
 * @param {string} attributeName 新しい Cocoon 専用属性名
 * @param {string} defaultValue  Cocoon スタイルの初期値
 * @return {Object} 現行形式へ移行した属性
 */
export const migrateLegacyStyleAttribute = (
  attributes,
  attributeName,
  defaultValue
) => {
  const { style, ...migratedAttributes } = attributes;
  const coreStyle = cleanCoreStyle( style );

  migratedAttributes[ attributeName ] = recoverLegacyStyleValue(
    style,
    defaultValue
  );

  if ( coreStyle ) {
    migratedAttributes.style = coreStyle;
  }

  return migratedAttributes;
};

/**
 * 現行 block.json から Cocoon 専用属性を外し、旧 style スキーマを組み立てます。
 *
 * @param {Object}          attributes    現行ブロック属性の定義
 * @param {string}          attributeName Cocoon 専用属性名
 * @param {string|string[]} styleType     旧 style 属性の型
 * @param {string}          defaultValue  文字列型の場合の初期値
 * @return {Object} deprecated 用の属性定義
 */
export const createLegacyStyleAttributes = (
  attributes,
  attributeName,
  styleType,
  defaultValue
) => {
  const legacyAttributes = { ...attributes };
  delete legacyAttributes[ attributeName ];

  legacyAttributes.style = { type: styleType };
  if (
    styleType === 'string' ||
    ( Array.isArray( styleType ) && styleType.includes( 'string' ) )
  ) {
    legacyAttributes.style.default = defaultValue;
  }

  return legacyAttributes;
};

/**
 * 挿入不可の旧ブロック向けに、style属性を安全に改名するdeprecated定義を組み立てます。
 *
 * @param {Object}   settings               現行のブロック設定
 * @param {number}   settings.apiVersion    ブロックAPIのバージョン
 * @param {Object}   settings.attributes    現行の属性定義
 * @param {Object}   settings.supports      現行のsupports
 * @param {Function} settings.legacySave    旧HTMLを再現する固定save
 * @param {string}   settings.attributeName Cocoon専用の新属性名
 * @param {string}   settings.defaultValue  旧style属性の初期値
 * @return {Object} deprecated 配列へ渡す定義
 */
export const createLegacyStyleDeprecation = ( {
  apiVersion,
  attributes,
  supports,
  legacySave,
  attributeName,
  defaultValue,
} ) => {
  // 破損した当時は customCSS が有効だったため、has-custom-css を含むHTMLを再現します。
  const legacySupports = { ...supports };
  delete legacySupports.customCSS;

  return {
    apiVersion,
    attributes: createLegacyStyleAttributes(
      attributes,
      attributeName,
      [ 'string', 'object' ],
      defaultValue
    ),
    supports: legacySupports,
    isEligible: isLegacyStyle,
    migrate: ( legacyAttributes ) =>
      migrateLegacyStyleAttribute(
        legacyAttributes,
        attributeName,
        defaultValue
      ),
    save: legacySave,
  };
};
