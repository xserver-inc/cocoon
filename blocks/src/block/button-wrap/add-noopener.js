const ATTRIBUTE_WHITESPACE = /[\t\n\f\r ]/;
const TAG_NAME_BOUNDARY = /[\t\n\f\r />]/;
const NOOPENER_ELEMENT_NAMES = new Set( [ 'a', 'area', 'form' ] );
const HTML_NAMESPACE_URI = 'http://www.w3.org/1999/xhtml';
const PROBE_ATTRIBUTE_NAME = 'data-cocoon-noopener-probe';

const NAMED_ATTRIBUTE_CHARACTER_REFERENCES = new Map( [
  [ '&Tab;', '\t' ],
  [ '&NewLine;', '\n' ],
  [ '&lowbar;', '_' ],
  [ '&UnderBar;', '_' ],
  [ '&sol;', '/' ],
  [ '&plus;', '+' ],
] );

// Unicodeの文字数を変えずHTML字句規則どおりASCII英大文字だけを小文字化する処理。
const toAsciiLowerCase = ( value ) =>
  value.replace( /[A-Z]/g, ( character ) =>
    String.fromCharCode( character.charCodeAt( 0 ) + 0x20 )
  );

// HTMLの属性値状態だけで引用符を開始し、開始タグ末尾を探す走査処理。
const findStartTagEnd = ( html, startIndex ) => {
  let index = startIndex + 1;

  while ( index < html.length && ! TAG_NAME_BOUNDARY.test( html[ index ] ) ) {
    index++;
  }

  let state = 'beforeAttributeName';
  for ( ; index < html.length; index++ ) {
    const character = html[ index ];

    if ( state === 'attributeValueDoubleQuoted' ) {
      if ( character === '"' ) {
        state = 'afterAttributeValueQuoted';
      }
      continue;
    }

    if ( state === 'attributeValueSingleQuoted' ) {
      if ( character === "'" ) {
        state = 'afterAttributeValueQuoted';
      }
      continue;
    }

    if ( state === 'attributeValueUnquoted' ) {
      if ( character === '>' ) {
        return index;
      }
      if ( ATTRIBUTE_WHITESPACE.test( character ) ) {
        state = 'beforeAttributeName';
      }
      continue;
    }

    if ( state === 'beforeAttributeValue' ) {
      if ( ATTRIBUTE_WHITESPACE.test( character ) ) {
        continue;
      }
      if ( character === '"' ) {
        state = 'attributeValueDoubleQuoted';
        continue;
      }
      if ( character === "'" ) {
        state = 'attributeValueSingleQuoted';
        continue;
      }
      if ( character === '>' ) {
        return index;
      }
      state = 'attributeValueUnquoted';
      continue;
    }

    if ( state === 'attributeName' ) {
      if ( character === '=' ) {
        state = 'beforeAttributeValue';
      } else if ( character === '>' ) {
        return index;
      } else if ( ATTRIBUTE_WHITESPACE.test( character ) ) {
        state = 'afterAttributeName';
      } else if ( character === '/' ) {
        state = 'selfClosingStartTag';
      }
      continue;
    }

    if ( state === 'afterAttributeName' ) {
      if ( ATTRIBUTE_WHITESPACE.test( character ) ) {
        continue;
      }
      if ( character === '=' ) {
        state = 'beforeAttributeValue';
        continue;
      }
      if ( character === '>' ) {
        return index;
      }
      state = character === '/' ? 'selfClosingStartTag' : 'attributeName';
      continue;
    }

    if ( state === 'afterAttributeValueQuoted' ) {
      if ( ATTRIBUTE_WHITESPACE.test( character ) ) {
        state = 'beforeAttributeName';
      } else if ( character === '/' ) {
        state = 'selfClosingStartTag';
      } else if ( character === '>' ) {
        return index;
      } else {
        state = 'attributeName';
      }
      continue;
    }

    if ( state === 'selfClosingStartTag' ) {
      if ( character === '>' ) {
        return index;
      }

      // self-closingにならないスラッシュ後もブラウザー同様に属性として再解釈する処理。
      state = 'beforeAttributeName';
      index--;
      continue;
    }

    if ( character === '>' ) {
      return index;
    }
    if ( ATTRIBUTE_WHITESPACE.test( character ) ) {
      continue;
    }
    state = character === '/' ? 'selfClosingStartTag' : 'attributeName';
  }

  return -1;
};

// 属性名・値・引用符・挿入位置を保持する開始タグ属性解析。
const parseStartTagAttributes = ( startTag ) => {
  const attributes = [];
  let index = 1;

  while (
    index < startTag.length &&
    ! TAG_NAME_BOUNDARY.test( startTag[ index ] )
  ) {
    index++;
  }

  while ( index < startTag.length ) {
    while (
      index < startTag.length &&
      ATTRIBUTE_WHITESPACE.test( startTag[ index ] )
    ) {
      index++;
    }

    if ( index >= startTag.length || startTag[ index ] === '>' ) {
      break;
    }

    if ( startTag[ index ] === '/' ) {
      if ( startTag[ index + 1 ] === '>' ) {
        break;
      }

      // self-closingにならないスラッシュを読み飛ばし後続属性を回収する処理。
      index++;
      continue;
    }

    const nameStart = index;
    while (
      index < startTag.length &&
      ! ATTRIBUTE_WHITESPACE.test( startTag[ index ] ) &&
      startTag[ index ] !== '>' &&
      startTag[ index ] !== '/'
    ) {
      // 属性名先頭の等号は名前に含め、2文字目以降の等号だけを値区切りとして扱う処理。
      if ( startTag[ index ] === '=' && index > nameStart ) {
        break;
      }

      index++;
    }

    const nameEnd = index;
    if ( nameEnd === nameStart ) {
      index++;
      continue;
    }

    const name = toAsciiLowerCase( startTag.slice( nameStart, nameEnd ) );

    while (
      index < startTag.length &&
      ATTRIBUTE_WHITESPACE.test( startTag[ index ] )
    ) {
      index++;
    }

    if ( startTag[ index ] !== '=' ) {
      attributes.push( {
        name,
        nameEnd,
        valueStart: null,
        valueEnd: null,
        quote: null,
        attributeEnd: nameEnd,
      } );
      continue;
    }

    index++;
    while (
      index < startTag.length &&
      ATTRIBUTE_WHITESPACE.test( startTag[ index ] )
    ) {
      index++;
    }

    const quote =
      startTag[ index ] === '"' || startTag[ index ] === "'"
        ? startTag[ index ]
        : null;

    if ( quote ) {
      index++;
    }

    const valueStart = index;
    if ( quote ) {
      while ( index < startTag.length && startTag[ index ] !== quote ) {
        index++;
      }
    } else {
      while (
        index < startTag.length &&
        ! ATTRIBUTE_WHITESPACE.test( startTag[ index ] ) &&
        startTag[ index ] !== '>'
      ) {
        index++;
      }
    }

    const valueEnd = index;
    if ( quote && startTag[ index ] === quote ) {
      index++;
    }

    attributes.push( {
      name,
      nameEnd,
      valueStart,
      valueEnd,
      quote,
      attributeEnd: index,
    } );
  }

  return attributes;
};

// 有効な数値文字参照だけを判定用文字列へ戻す変換処理。
const decodeNumericCharacterReference = ( match, digits, radix ) => {
  const codePoint = Number.parseInt( digits, radix );

  if (
    ! Number.isInteger( codePoint ) ||
    codePoint <= 0 ||
    codePoint > 0x10ffff ||
    ( codePoint >= 0xd800 && codePoint <= 0xdfff )
  ) {
    return match;
  }

  return String.fromCodePoint( codePoint );
};

// 属性値の意味判定に必要な数値・名前付き文字参照の復号処理。
const decodeAttributeCharacterReferences = ( attributeValue ) =>
  attributeValue
    .replace( /&#x([0-9a-f]+);?/gi, ( match, digits ) =>
      decodeNumericCharacterReference( match, digits, 16 )
    )
    .replace( /&#([0-9]+);?/g, ( match, digits ) =>
      decodeNumericCharacterReference( match, digits, 10 )
    )
    .replace( /&(Tab|NewLine|lowbar|UnderBar|sol|plus);/g, ( match ) =>
      NAMED_ATTRIBUTE_CHARACTER_REFERENCES.get( match )
    );

// rel値をHTML標準の空白区切りトークンとして判定する処理。
const hasDecodedNoopenerToken = ( relValue ) =>
  relValue
    .split( /[\t\n\f\r ]+/ )
    .filter( Boolean )
    .some( ( token ) => toAsciiLowerCase( token ) === 'noopener' );

// ソース属性値の文字参照を復号し、noopenerトークンの有無を判定する処理。
const hasNoopenerToken = ( relValue ) =>
  hasDecodedNoopenerToken( decodeAttributeCharacterReferences( relValue ) );

// 対象リンクの既存属性を壊さずnoopenerだけを補う開始タグ変換。
const addNoopenerToStartTag = ( startTag, isDomVerified = false ) => {
  const attributes = parseStartTagAttributes( startTag );
  const targetAttribute = attributes.find(
    ( attribute ) => attribute.name === 'target'
  );

  if (
    ! targetAttribute ||
    targetAttribute.valueStart === null ||
    ( ! isDomVerified &&
      toAsciiLowerCase(
        decodeAttributeCharacterReferences(
          startTag.slice( targetAttribute.valueStart, targetAttribute.valueEnd )
        )
      ) !== '_blank' )
  ) {
    return startTag;
  }

  const relAttribute = attributes.find(
    ( attribute ) => attribute.name === 'rel'
  );

  if ( ! relAttribute ) {
    const quote = targetAttribute.quote || '"';
    const noopenerAttribute = ` rel=${ quote }noopener${ quote }`;

    return (
      startTag.slice( 0, targetAttribute.attributeEnd ) +
      noopenerAttribute +
      startTag.slice( targetAttribute.attributeEnd )
    );
  }

  if ( relAttribute.valueStart === null ) {
    return (
      startTag.slice( 0, relAttribute.nameEnd ) +
      '="noopener"' +
      startTag.slice( relAttribute.nameEnd )
    );
  }

  const relValue = startTag.slice(
    relAttribute.valueStart,
    relAttribute.valueEnd
  );

  if ( ! isDomVerified && hasNoopenerToken( relValue ) ) {
    return startTag;
  }

  let replacement = 'noopener';

  if ( relValue ) {
    replacement = relAttribute.quote
      ? `${ relValue } noopener`
      : `${ relValue }&#32;noopener`;
  }

  return (
    startTag.slice( 0, relAttribute.valueStart ) +
    replacement +
    startTag.slice( relAttribute.valueEnd )
  );
};

// 各小なり記号の直後だけを固定長で調べ、対象要素らしい字面を漏れなく拾う処理。
const getNoopenerCandidateTagName = ( html, startIndex ) => {
  for ( const tagName of NOOPENER_ELEMENT_NAMES ) {
    const nameStart = startIndex + 1;
    const nameEnd = nameStart + tagName.length;

    if (
      toAsciiLowerCase( html.slice( nameStart, nameEnd ) ) === tagName &&
      TAG_NAME_BOUNDARY.test( html[ nameEnd ] )
    ) {
      return tagName;
    }
  }

  return null;
};

// コメント・属性値・RAW TEXT内も含め、全候補のタグ名直後を点プローブする位置の列挙処理。
const collectNoopenerCandidates = ( html ) => {
  const candidates = [];
  let cursor = 0;

  while ( cursor < html.length ) {
    const startIndex = html.indexOf( '<', cursor );

    if ( startIndex < 0 ) {
      break;
    }

    const tagName = getNoopenerCandidateTagName( html, startIndex );

    if ( tagName ) {
      candidates.push( {
        startIndex,
        tagName,
        nameEnd: 1 + tagName.length,
      } );
    }

    // 前の偽タグに後続候補を飲み込ませないため、常に次の文字から再検索する処理。
    cursor = startIndex + 1;
  }

  return candidates;
};

// 入力に存在しない短い値を線形走査で作り、元の同名属性と候補IDを区別する処理。
const createProbeValuePrefix = ( html ) => {
  let hash = 0x811c9dc5;

  for ( let index = 0; index < html.length; index++ ) {
    hash = ( hash * 33 + html.charCodeAt( index ) ) % 0x100000000;
  }

  // unquoted属性値で安全かつ他位置に現れない終端記号で、prefix自身の重なり一致を防ぐ処理。
  const basePrefix = `cocoon-noopener-${ html.length.toString(
    36
  ) }-${ hash.toString( 36 ) }~`;
  let maximumTrailingXCount = -1;
  let searchIndex = 0;

  while ( searchIndex < html.length ) {
    const prefixIndex = html.indexOf( basePrefix, searchIndex );

    if ( prefixIndex < 0 ) {
      break;
    }

    let suffixIndex = prefixIndex + basePrefix.length;

    while ( html[ suffixIndex ] === 'x' ) {
      suffixIndex++;
    }

    maximumTrailingXCount = Math.max(
      maximumTrailingXCount,
      suffixIndex - prefixIndex - basePrefix.length
    );

    // 調査済みの接尾辞を飛ばし、同じx列を重複走査しないための更新処理。
    searchIndex = suffixIndex;
  }

  return basePrefix + 'x'.repeat( maximumTrailingXCount + 1 );
};

// 引用符なしの一時属性を各タグ名直後へ点挿入し、周囲の字句状態を壊さない処理。
const buildProbeHtml = ( html, candidates, valuePrefix ) => {
  let probeHtml = '';
  let cursor = 0;

  candidates.forEach( ( candidate, candidateId ) => {
    const insertionIndex = candidate.startIndex + candidate.nameEnd;
    const probeAttribute = ` ${ PROBE_ATTRIBUTE_NAME }=${ valuePrefix }-${ candidateId } `;

    probeHtml += html.slice( cursor, insertionIndex );
    probeHtml += probeAttribute;
    cursor = insertionIndex;
  } );

  return probeHtml + html.slice( cursor );
};

// browsing contextのない別文書で解析し、scriptやcustom elementを実行しないDOMを作る処理。
const createProbeContainer = ( probeHtml ) => {
  if (
    typeof document === 'undefined' ||
    ! document.implementation?.createHTMLDocument
  ) {
    return null;
  }

  try {
    const probeDocument = document.implementation.createHTMLDocument( '' );
    const container = probeDocument.createElement( 'div' );

    try {
      container.innerHTML = probeHtml;
    } catch {
      // Trusted TypesでinnerHTMLが拒否された場合にDOM Rangeの不活性解析を試す処理。
      try {
        container.textContent = '';

        const range = probeDocument.createRange();

        range.selectNodeContents( container );
        container.appendChild( range.createContextualFragment( probeHtml ) );
      } catch {
        return null;
      }
    }

    return container;
  } catch {
    return null;
  }
};

// template.contentを含む全解析木から、実要素として残った候補IDだけを回収する処理。
const collectActualCandidateIds = (
  root,
  candidates,
  valuePrefix,
  actualCandidateIds
) => {
  const selector = `[${ PROBE_ATTRIBUTE_NAME }]`;

  root.querySelectorAll( selector ).forEach( ( element ) => {
    const probeValue = element.getAttribute( PROBE_ATTRIBUTE_NAME );
    const idPrefix = `${ valuePrefix }-`;

    if ( ! probeValue?.startsWith( idPrefix ) ) {
      return;
    }

    const candidateId = Number.parseInt(
      probeValue.slice( idPrefix.length ),
      10
    );
    const candidate = candidates[ candidateId ];
    const targetValue = element.getAttribute( 'target' );
    const relValue = element.getAttribute( 'rel' ) || '';
    if (
      ! candidate ||
      probeValue !== `${ idPrefix }${ candidateId }` ||
      element.localName !== candidate.tagName ||
      toAsciiLowerCase( targetValue || '' ) !== '_blank' ||
      hasDecodedNoopenerToken( relValue ) ||
      ( candidate.tagName !== 'a' &&
        element.namespaceURI !== HTML_NAMESPACE_URI )
    ) {
      return;
    }

    actualCandidateIds.add( candidateId );
  } );

  // querySelectorAllはtemplate.contentを横断しないため内容を再帰的に確認する処理。
  root.querySelectorAll( 'template' ).forEach( ( template ) => {
    // SVG名前空間の同名要素など、DocumentFragmentを持たないtemplateは再帰対象外にする処理。
    if ( ! template.content?.querySelectorAll ) {
      return;
    }

    collectActualCandidateIds(
      template.content,
      candidates,
      valuePrefix,
      actualCandidateIds
    );
  } );
};

// 補助DOM内のnoscript部分木だけを起点にし、祖先探索なしで候補を回収する処理。
const collectNoscriptSubtreeCandidateIds = (
  root,
  candidates,
  valuePrefix,
  actualCandidateIds
) => {
  const pendingElements = [];

  // 大量の子要素でも関数引数上限へ触れないよう1件ずつ走査待ちへ積む処理。
  const pushElementChildren = ( parent ) => {
    for ( let index = 0; index < parent.children.length; index++ ) {
      pendingElements.push( parent.children[ index ] );
    }
  };

  pushElementChildren( root );

  while ( pendingElements.length > 0 ) {
    const element = pendingElements.pop();

    if (
      element.localName === 'noscript' &&
      element.namespaceURI === HTML_NAMESPACE_URI
    ) {
      // 最外のnoscript部分木を一度だけ回収し、入れ子部分木の重複走査を避ける処理。
      collectActualCandidateIds(
        element,
        candidates,
        valuePrefix,
        actualCandidateIds
      );
      continue;
    }

    pushElementChildren( element );

    // template.contentは通常のchildrenに含まれないため別の走査対象として積む処理。
    if ( element.content?.children ) {
      pushElementChildren( element.content );
    }
  }
};

// jsdomのnoscript断片解析差を補い、無効スクリプト時に実要素となる候補だけを回収する処理。
const collectNoscriptCandidateIds = (
  probeHtml,
  candidates,
  valuePrefix,
  actualCandidateIds
) => {
  const DomParser = document.defaultView?.DOMParser;

  if ( ! DomParser || ! /<noscript[\t\n\f\r />]/i.test( probeHtml ) ) {
    return;
  }

  try {
    // div断片と同じinsertion modeを保ちつつ、noscriptだけをscripting無効として再解析する処理。
    const probeDocument = new DomParser().parseFromString(
      `<!doctype html><body><div>${ probeHtml }</div>`,
      'text/html'
    );

    collectNoscriptSubtreeCandidateIds(
      probeDocument,
      candidates,
      valuePrefix,
      actualCandidateIds
    );
  } catch {
    // Trusted Typesなどで補助解析を利用できない場合は主解析の結果だけを採用する処理。
  }
};

// DOMで変換が必要と確定した点だけ、元ソース上の開始タグ範囲へ解決する処理。
const resolveCandidateTransforms = ( html, candidates, actualCandidateIds ) => {
  const transforms = [];

  candidates.forEach( ( candidate, candidateId ) => {
    if ( ! actualCandidateIds.has( candidateId ) ) {
      return;
    }

    const endIndex = findStartTagEnd( html, candidate.startIndex );

    if ( endIndex < 0 ) {
      return;
    }

    const startTag = html.slice( candidate.startIndex, endIndex + 1 );
    const transformedTag = addNoopenerToStartTag( startTag, true );

    if ( transformedTag !== startTag ) {
      transforms.push( {
        startIndex: candidate.startIndex,
        endIndex,
        transformedTag,
      } );
    }
  } );

  return transforms;
};

// 解決済み開始タグだけを元の位置で置換し、その他の全バイトをそのまま連結する処理。
const applyCandidateTransforms = ( html, transforms ) => {
  let result = '';
  let cursor = 0;

  transforms.forEach( ( transform ) => {
    result += html.slice( cursor, transform.startIndex );
    result += transform.transformedTag;
    cursor = transform.endIndex + 1;
  } );

  return result + html.slice( cursor );
};

// 解析用コピーだけをDOMへ渡し、元HTMLを再構成せず実リンクの開始タグだけを変換する処理。
export const addNoopenerToBlankTargets = ( html ) => {
  if ( typeof html !== 'string' || html.length === 0 ) {
    return html;
  }

  const candidates = collectNoopenerCandidates( html );

  if ( candidates.length === 0 ) {
    return html;
  }

  const valuePrefix = createProbeValuePrefix( html );
  const probeHtml = buildProbeHtml( html, candidates, valuePrefix );
  const container = createProbeContainer( probeHtml );

  // DOMを利用できない環境では誤変換によるデータ変更を避け、入力をそのまま保持する処理。
  if ( ! container ) {
    return html;
  }

  const actualCandidateIds = new Set();

  collectActualCandidateIds(
    container,
    candidates,
    valuePrefix,
    actualCandidateIds
  );
  collectNoscriptCandidateIds(
    probeHtml,
    candidates,
    valuePrefix,
    actualCandidateIds
  );

  const transforms = resolveCandidateTransforms(
    html,
    candidates,
    actualCandidateIds
  );

  return transforms.length > 0
    ? applyCandidateTransforms( html, transforms )
    : html;
};
