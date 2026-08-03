#!/usr/bin/env node
/**
 * Cocoon翻訳資産の整合性テスト
 */

const assert = require( 'assert' );
const { execFileSync } = require( 'child_process' );
const fs = require( 'fs' );
const path = require( 'path' );
const vm = require( 'vm' );
const gettextParser = require( '../node_modules/gettext-parser' );
const additions = require( './translations/translation-additions-202608' );
const skinMetadata = require( './translations/skin-metadata-translations' );
const translationCorrections = require( './translations/translation-corrections' );

const THEME_ROOT = path.join( __dirname, '..' );
const LOCALES = [
  'de_DE',
  'en_US',
  'es_ES',
  'fr_FR',
  'ko_KR',
  'pt_PT',
  'zh_CN',
  'zh_TW',
];
const JAPANESE_PATTERN = /[ぁ-んァ-ヶ一-龠々ー～]/u;
const JAPANESE_KANA_PATTERN = /[ぁ-んァ-ヶ々ー]/u;

//中国語では共通漢字を許容し、それ以外の言語では日本語の漢字も未翻訳として検出する
const hasUnexpectedJapanese = ( locale, value ) =>
  ( locale.startsWith( 'zh_' ) ? JAPANESE_KANA_PATTERN : JAPANESE_PATTERN )
    .test( value );

//POヘッダーから、そのロケールで必要な複数形の数を取得する
const getPluralFormsCount = ( parsed ) => {
  const pluralForms = parsed.headers[ 'plural-forms' ] || '';
  const match = pluralForms.match( /nplurals\s*=\s*(\d+)/u );

  return match ? Number( match[ 1 ] ) : 1;
};

//WordPress 6.5以降で使用するl10n.phpをPHP経由で安全に読み込む
const readL10nMessages = ( l10nPath ) => {
  const phpCode =
    '$catalog = include $argv[1]; echo json_encode($catalog["messages"], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);';

  return JSON.parse(
    execFileSync( 'php', [ '-r', phpCode, l10nPath ], { encoding: 'utf8' } )
  );
};

// printf形式の置換指定子を比較する抽出処理
const extractPlaceholders = ( value ) =>
  ( value.match( /%(?:\d+\$)?[bcdeEfFgGosuxXdi]/g ) || [] ).sort();

// 翻訳後にも保持すべきHTMLタグを比較する抽出処理
const extractHtmlTags = ( value ) => {
  const tags = [];
  const pattern = /<\/?([a-z][a-z0-9]*)\b/gi;
  let match;

  while ( ( match = pattern.exec( value ) ) !== null ) {
    tags.push( match[ 1 ].toLowerCase() );
  }

  return tags.sort();
};

// ファイル内容をテーマルート基準で取得する処理
const readThemeFile = ( relativePath ) =>
  fs.readFileSync( path.join( THEME_ROOT, relativePath ), 'utf8' );

// 指定ディレクトリ以下の対象ファイルを再帰取得する処理
const findFiles = ( directory, fileName ) => {
  const results = [];

  for ( const entry of fs.readdirSync( directory, { withFileTypes: true } ) ) {
    const entryPath = path.join( directory, entry.name );

    if ( entry.isDirectory() ) {
      results.push( ...findFiles( entryPath, fileName ) );
    } else if ( entry.name === fileName ) {
      results.push( entryPath );
    }
  }

  return results;
};

// block.jsonの初期値と使用例に含まれる日本語の抽出処理
const findJapaneseMetadataValues = ( value, propertyPath = [] ) => {
  if ( typeof value === 'string' ) {
    const isDefault = propertyPath[ propertyPath.length - 1 ] === 'default';
    const isExample = propertyPath.includes( 'example' );

    return JAPANESE_PATTERN.test( value ) && ( isDefault || isExample )
      ? [ value ]
      : [];
  }

  if ( Array.isArray( value ) ) {
    return value.flatMap( ( item, index ) =>
      findJapaneseMetadataValues( item, [ ...propertyPath, index ] )
    );
  }

  if ( value && typeof value === 'object' ) {
    return Object.entries( value ).flatMap( ( [ key, item ] ) =>
      findJapaneseMetadataValues( item, [ ...propertyPath, key ] )
    );
  }

  return [];
};

for ( const locale of LOCALES ) {
  const dictionary = additions( locale );
  const skinDictionary = skinMetadata( locale );
  const corrections = translationCorrections( locale );

  assert.strictEqual(
    Object.keys( dictionary ).length,
    additions.keys.length,
    `${ locale }の追加翻訳件数が原文件数と一致しません。`
  );

  for ( const key of additions.keys ) {
    const value = dictionary[ key ];

    assert.ok( value, `${ locale }の翻訳が空です: ${ key }` );
    assert.deepStrictEqual(
      extractPlaceholders( value ),
      extractPlaceholders( key ),
      `${ locale }のプレースホルダーが一致しません: ${ key }`
    );
    assert.deepStrictEqual(
      extractHtmlTags( value ),
      extractHtmlTags( key ),
      `${ locale }のHTMLタグが一致しません: ${ key }`
    );
  }

  assert.strictEqual(
    Object.keys( skinDictionary ).length,
    skinMetadata.keys.length,
    `${ locale }のスキンメタデータ翻訳件数が一致しません。`
  );

  for ( const key of skinMetadata.keys ) {
    const value = skinDictionary[ key ];
    assert.ok( value, `${ locale }のスキンメタデータ翻訳が空です: ${ key }` );
    assert.ok(
      !JAPANESE_KANA_PATTERN.test( value ) && !value.includes( 'undefined' ),
      `${ locale }のスキンメタデータ翻訳に日本語または未定義値が残っています: ${ key }`
    );
  }

  const poPath = path.join( THEME_ROOT, 'languages', `${ locale }.po` );
  const parsed = gettextParser.po.parse( fs.readFileSync( poPath ) );
  const pluralFormsCount = getPluralFormsCount( parsed );
  const madeOnlyCatalogKeys = new Set();
  const untranslated = [];
  const incompletePlurals = [];
  const placeholderMismatches = [];
  const htmlTagMismatches = [];

  //変更禁止スキンだけを参照するカタログ項目は、翻訳内容の監査対象から除外する
  for ( const [ context, entries ] of Object.entries( parsed.translations ) ) {
    for ( const [ msgid, entry ] of Object.entries( entries ) ) {
      const references = ( entry.comments && entry.comments.reference || '' )
        .split( /\s+/u )
        .filter( Boolean );
      if (
        msgid &&
        references.length > 0 &&
        references.every( ( reference ) =>
          reference.replace( /\\/gu, '/' ).startsWith( 'skins/skin-made-in-heaven/' )
        )
      ) {
        madeOnlyCatalogKeys.add( context ? `${ context }\u0004${ msgid }` : msgid );
      }
    }
  }

  for ( const [ context, entries ] of Object.entries( parsed.translations ) ) {
    for ( const [ msgid, entry ] of Object.entries( entries ) ) {
      if ( !msgid ) {
        continue;
      }

      const catalogKey = context ? `${ context }\u0004${ msgid }` : msgid;
      if ( madeOnlyCatalogKeys.has( catalogKey ) ) {
        continue;
      }

      const translatedForms = entry.msgstr || [];
      if (
        entry.msgid_plural &&
        ( translatedForms.length !== pluralFormsCount ||
          translatedForms.some( ( value ) => !value ) )
      ) {
        incompletePlurals.push( `${ context }\u0004${ msgid }` );
      }

      if (
        JAPANESE_PATTERN.test( msgid ) &&
        ( !translatedForms[ 0 ] || hasUnexpectedJapanese( locale, translatedForms[ 0 ] ) )
      ) {
        untranslated.push( `${ context }\u0004${ msgid }` );
      }

      for ( const [ formIndex, msgstr ] of translatedForms.entries() ) {
        if ( !msgstr ) {
          continue;
        }

        const source = formIndex === 0 ? msgid : entry.msgid_plural || msgid;

        if ( hasUnexpectedJapanese( locale, msgstr ) ) {
          untranslated.push( `${ context }\u0004${ msgid }[${ formIndex }]` );
        }
        if (
          JSON.stringify( extractPlaceholders( msgstr ) ) !==
          JSON.stringify( extractPlaceholders( source ) )
        ) {
          placeholderMismatches.push( `${ context }\u0004${ msgid }[${ formIndex }]` );
        }
        if (
          JSON.stringify( extractHtmlTags( msgstr ) ) !==
          JSON.stringify( extractHtmlTags( source ) )
        ) {
          htmlTagMismatches.push( `${ context }\u0004${ msgid }[${ formIndex }]` );
        }
      }
    }
  }

  assert.deepStrictEqual(
    untranslated,
    [],
    `${ locale }のPOファイルに未翻訳があります。`
  );
  assert.deepStrictEqual(
    incompletePlurals,
    [],
    `${ locale }のPOファイルに空または不足した複数形があります。`
  );
  assert.deepStrictEqual(
    placeholderMismatches,
    [],
    `${ locale }のPOファイルにプレースホルダーの不一致があります。`
  );
  assert.deepStrictEqual(
    htmlTagMismatches,
    [],
    `${ locale }のPOファイルにHTMLタグの不一致があります。`
  );

  //補助辞書で管理する翻訳がPOにも同じ値で反映されていることを確認する
  const defaultEntries = parsed.translations[ '' ];
  for ( const [ key, value ] of Object.entries( {
    ...dictionary,
    ...skinDictionary,
  } ) ) {
    assert.strictEqual(
      defaultEntries[ key ] && defaultEntries[ key ].msgstr[ 0 ],
      value,
      `${ locale }のPOへ管理対象の翻訳が反映されていません: ${ key }`
    );
  }

  //補正辞書はコンテキストと全複数形を含めてPOへ反映されていることを確認する
  for ( const correction of corrections ) {
    const context = correction.context || '';
    const entry = parsed.translations[ context ] &&
      parsed.translations[ context ][ correction.msgid ];

    assert.ok( entry, `${ locale }のPOに補正対象がありません: ${ correction.msgid }` );
    assert.strictEqual(
      entry.msgid_plural || undefined,
      correction.msgidPlural,
      `${ locale }のPOで複数形原文が一致しません: ${ correction.msgid }`
    );
    assert.deepStrictEqual(
      entry.msgstr,
      correction.msgstr,
      `${ locale }のPOで補正翻訳が一致しません: ${ correction.msgid }`
    );
  }

  const managedDictionary = { ...dictionary, ...skinDictionary };
  const moPath = path.join( THEME_ROOT, 'languages', `${ locale }.mo` );
  const mo = gettextParser.mo.parse( fs.readFileSync( moPath ) );
  const moEntries = mo.translations[ '' ];
  const l10nPath = path.join( THEME_ROOT, 'languages', `${ locale }.l10n.php` );
  const l10nMessages = readL10nMessages( l10nPath );
  const jsonPath = path.join(
    THEME_ROOT,
    'languages',
    `cocoon-${ locale }-cocoon-blocks-js.json`
  );
  const jedMessages = JSON.parse( fs.readFileSync( jsonPath, 'utf8' ) )
    .locale_data.messages;

  const moKanaResidues = [];
  for ( const [ context, entries ] of Object.entries( mo.translations ) ) {
    for ( const [ msgid, entry ] of Object.entries( entries ) ) {
      if (
        msgid &&
        !madeOnlyCatalogKeys.has( context ? `${ context }\u0004${ msgid }` : msgid ) &&
        entry.msgstr &&
        entry.msgstr.some( ( value ) => hasUnexpectedJapanese( locale, value ) )
      ) {
        moKanaResidues.push( msgid );
      }
    }
  }
  const jedKanaResidues = Object.entries( jedMessages )
    .filter( ( [ msgid, values ] ) =>
      msgid &&
      !madeOnlyCatalogKeys.has( msgid ) &&
      Array.isArray( values ) &&
      values.some( ( value ) =>
        typeof value === 'string' && hasUnexpectedJapanese( locale, value )
      )
    )
    .map( ( [ msgid ] ) => msgid );

  assert.deepStrictEqual(
    moKanaResidues,
    [],
    `${ locale }のMOに日本語の仮名が残っています。`
  );
  assert.deepStrictEqual(
    jedKanaResidues,
    [],
    `${ locale }のJED JSONに日本語の仮名が残っています。`
  );

  //PHP用MOとJavaScript用JED JSONにも同じ翻訳が収録されていることを確認する
  for ( const [ key, value ] of Object.entries( managedDictionary ) ) {
    assert.strictEqual(
      moEntries[ key ] && moEntries[ key ].msgstr[ 0 ],
      value,
      `${ locale }のMOへ管理対象の翻訳が反映されていません: ${ key }`
    );
    assert.strictEqual(
      jedMessages[ key ] && jedMessages[ key ][ 0 ],
      value,
      `${ locale }のJED JSONへ管理対象の翻訳が反映されていません: ${ key }`
    );
  }

  //PO・MO・l10n.php・JED JSONで補正対象の全形式が一致することを確認する
  for ( const correction of corrections ) {
    const context = correction.context || '';
    const catalogKey = context
      ? `${ context }\u0004${ correction.msgid }`
      : correction.msgid;
    const moEntry = mo.translations[ context ] &&
      mo.translations[ context ][ correction.msgid ];

    assert.deepStrictEqual(
      moEntry && moEntry.msgstr,
      correction.msgstr,
      `${ locale }のMOで補正翻訳が一致しません: ${ catalogKey }`
    );
    assert.strictEqual(
      l10nMessages[ catalogKey ],
      correction.msgstr.join( '\u0000' ),
      `${ locale }のl10n.phpで補正翻訳が一致しません: ${ catalogKey }`
    );
    assert.deepStrictEqual(
      jedMessages[ catalogKey ],
      correction.msgstr,
      `${ locale }のJED JSONで補正翻訳が一致しません: ${ catalogKey }`
    );
  }
}

const loginMetadata = JSON.parse(
  readThemeFile( 'blocks/src/block/login-user-only/block.json' )
);
const loginEditor = readThemeFile(
  'blocks/src/block/login-user-only/index.js'
);
const loginRenderer = readThemeFile(
  'blocks/src/block/login-user-only/index.php'
);
const blocks = readThemeFile( 'blocks/src/blocks.js' );

assert.strictEqual(
  loginMetadata.attributes.msg.default,
  '',
  'ログインユーザー限定ブロックの保存属性に日本語の既定値があります。'
);
assert.match(
  loginEditor,
  /title:\s*__\(\s*'ログインユーザー限定',\s*THEME_NAME\s*\)/,
  'ログインユーザー限定ブロックのタイトルが翻訳されていません。'
);

const skinCatalog = readThemeFile(
  'lib/page-settings/skin-translations.php'
);
const skinFunctions = readThemeFile( 'lib/page-settings/skin-funcs.php' );
const themeUtils = readThemeFile( 'lib/utils.php' );
const themeScripts = readThemeFile( 'lib/scripts.php' );
const themeAdmin = readThemeFile( 'lib/admin.php' );
const themeSettings = readThemeFile( 'lib/settings.php' );
const themeAmp = readThemeFile( 'lib/amp.php' );
const adminScss = readThemeFile( 'scss/admin.scss' );
const adminCss = readThemeFile( 'css/admin.css' );
const rakuColorChanging = readThemeFile(
  'skins/raku-color-changing/functions.php'
);

assert.match(
  skinFunctions,
  /translate_skin_metadata\(trim\(strip_tags\(\$matches\[2\]\)\)\)/,
  'スキン名が実行時翻訳になっていません。'
);
assert.match(
  skinFunctions,
  /\$description\s*=\s*translate_skin_metadata\(trim\(\$m\[1\]\)\)/,
  'スキン説明文が実行時翻訳になっていません。'
);
assert.ok(
  !skinCatalog.includes( 'Made in Heaven' ) &&
    !skinMetadata.keys.some( ( key ) => /メイド[・･]イン[・･]ヘブン/u.test( key ) ),
  '変更禁止スキンがスキン翻訳カタログへ含まれています。'
);

//style.cssの日本語メタデータが、禁止スキンを除いてすべてカタログ登録済みか確認する
const skinStyles = findFiles( path.join( THEME_ROOT, 'skins' ), 'style.css' )
  .filter( ( file ) => !file.includes( `${ path.sep }skin-made-in-heaven${ path.sep }` ) );
const discoveredSkinMetadata = new Set();
for ( const stylePath of skinStyles ) {
  const style = fs.readFileSync( stylePath, 'utf8' );
  for ( const pattern of [
    /^\s*(?:Skin )?Name:\s*(.+)$/imu,
    /^\s*Description:\s*(.+)$/imu,
  ] ) {
    const match = style.match( pattern );
    if ( match && JAPANESE_PATTERN.test( match[ 1 ] ) ) {
      discoveredSkinMetadata.add( match[ 1 ].trim() );
    }
  }
}
assert.deepStrictEqual(
  [ ...discoveredSkinMetadata ].sort(),
  [ ...skinMetadata.keys ].sort(),
  'スキン名または説明文の翻訳カタログに抜けがあります。'
);

assert.match(
  themeUtils,
  /function\s+get_skin_text_css_variables\(\)/,
  'スキン疑似要素用の翻訳CSS変数がありません。'
);
assert.match(
  themeScripts,
  /get_skin_text_css_variables\(\)/,
  'スキン疑似要素用の翻訳CSS変数が出力されていません。'
);
assert.match(
  themeAdmin,
  /wp_add_inline_style\(\s*\$blogcard_label_style_handle,\s*\$skin_text_css\s*\)/,
  '管理画面へスキン疑似要素用の翻訳CSS変数が出力されていません。'
);
assert.match(
  themeSettings,
  /get_skin_text_css_variables\(\)/,
  'ブロックエディターへスキン疑似要素用の翻訳CSS変数が出力されていません。'
);
assert.match(
  themeAmp,
  /get_skin_text_css_variables\(\)/,
  'AMPへスキン疑似要素用の翻訳CSS変数が出力されていません。'
);
assert.match(
  adminScss,
  /content:\s*var\(--cocoon-skin-control-text,/,
  '管理画面SCSSのスキン制御ラベルが翻訳CSS変数を使用していません。'
);
assert.match(
  adminCss,
  /content:\s*var\(--cocoon-skin-control-text,/,
  '配布用管理画面CSSのスキン制御ラベルが翻訳CSS変数を使用していません。'
);

//CSSのcontentに日本語を使う場合は、翻訳CSS変数のフォールバックに限定する
for ( const stylePath of skinStyles ) {
  const style = fs.readFileSync( stylePath, 'utf8' );
  for ( const line of style.split( /\r?\n/u ) ) {
    const declaration = line.replace( /\/\*.*\*\//gu, '' );
    if ( /content\s*:/u.test( declaration ) && JAPANESE_PATTERN.test( declaration ) ) {
      assert.match(
        declaration,
        /var\(--cocoon-skin-/u,
        `CSSのcontentに未翻訳の日本語があります: ${ path.relative( THEME_ROOT, stylePath ) }`
      );
    }
  }
}

//テーマ本体のCSSでも、日本語contentは翻訳CSS変数のフォールバックだけに限定する
for ( const stylePath of [
  path.join( THEME_ROOT, 'scss', 'admin.scss' ),
  path.join( THEME_ROOT, 'css', 'admin.css' ),
] ) {
  const style = fs.readFileSync( stylePath, 'utf8' );
  for ( const line of style.split( /\r?\n/u ) ) {
    const declaration = line.replace( /\/\*.*\*\//gu, '' );
    if ( /content\s*:/u.test( declaration ) && JAPANESE_PATTERN.test( declaration ) ) {
      assert.match(
        declaration,
        /var\(--cocoon-/u,
        `テーマCSSのcontentに未翻訳の日本語があります: ${ path.relative( THEME_ROOT, stylePath ) }`
      );
    }
  }
}

//Cocoon固有文言の翻訳呼び出しが、必ずテーマのテキストドメインを使うことを確認する
const themeDomainRequirements = {
  'lib/comments.php': [ '名前:</span>' ],
  'lib/original-menu.php': [ 'このページにアクセスする管理者権限がありません。' ],
  'lib/page-settings/about-forms.php': [
    '利用中のプラグイン：',
    '停止中のプラグイン：',
  ],
  'lib/page-settings/donation-forms.php': [ '有効' ],
  'lib/widgets/new-entries.php': [
    '全ての新着記事（全ページで表示）',
    'カテゴリー別新着記事（投稿・カテゴリーで表示）',
  ],
  'lib/widgets/classic-text.php': [
    'テキストエディターのみの旧タイプのテキストウィジェット',
    '[S] クラシックテキスト',
    'タイトル：',
    '内容：',
    '自動的に段落を追加する',
  ],
  'lib/html-forms.php': [ 'NO USER' ],
};

for ( const [ relativePath, messages ] of Object.entries( themeDomainRequirements ) ) {
  const sourceLines = readThemeFile( relativePath ).split( /\r?\n/u );

  for ( const message of messages ) {
    const matchingLines = sourceLines.filter( ( line ) => line.includes( message ) );
    assert.ok(
      matchingLines.length > 0,
      `テキストドメイン検査対象が見つかりません: ${ relativePath } / ${ message }`
    );
    assert.ok(
      matchingLines.every( ( line ) => line.includes( 'THEME_NAME' ) ),
      `Cocoon固有文言にTHEME_NAMEがありません: ${ relativePath } / ${ message }`
    );
  }
}

assert.doesNotMatch(
  readThemeFile( 'lib/widgets/classic-text.php' ),
  /simplicity2/u,
  'クラシックテキストウィジェットに旧テキストドメインが残っています。'
);

for ( const pattern of [
  /_n\('%s min', '%s mins', \$mins, THEME_NAME\)/u,
  /_n\('%s hour', '%s hours', \$hours, THEME_NAME\)/u,
  /_n\('%s day', '%s days', \$days, THEME_NAME\)/u,
  /_n\('%s週間', '%s週間', \$weeks, THEME_NAME\)/u,
  /_n\('%sヶ月', '%sヶ月', \$months, THEME_NAME\)/u,
  /_n\('%s年', '%s年', \$years, THEME_NAME\)/u,
  /__\('%1\$s%2\$s', THEME_NAME\)/u,
] ) {
  assert.match(
    themeUtils,
    pattern,
    '経過時間の単数形・複数形処理またはテキストドメインが不足しています。'
  );
}

assert.doesNotMatch(
  themeUtils,
  /__\('%s年(?:%sヶ月)?', THEME_NAME\)/u,
  '年・月の経過時間が単数形専用の翻訳関数を使用しています。'
);

assert.match(
  themeUtils,
  /translate_skin_option_value\(\$name,\s*\$value\)/,
  'JSON・CSVスキン設定の翻訳処理がありません。'
);
assert.match(
  themeUtils,
  /add_action\(\s*'after_setup_theme',\s*'translate_loaded_skin_options',\s*20\s*\)/,
  'テーマ翻訳読込後にJSON・CSVスキン設定を再翻訳していません。'
);
assert.match(
  rakuColorChanging,
  /const\s+APPLY\s*=\s*'apply'/,
  'ゆっくり色が変化スキンの保存値が固定値になっていません。'
);
assert.doesNotMatch(
  rakuColorChanging,
  /value="<\?=\$val\?>"/,
  'ゆっくり色が変化スキンが翻訳ラベルを設定値として保存しています。'
);

//ブックマークレットを疑似ブラウザーで実行し、中国語ロケールの分岐を確認する
const bookmarklet = readThemeFile( 'js/rakuten-bookmarklet.js' );
const runUnsupportedBookmarklet = ( language ) => {
  let alertMessage = '';
  vm.runInNewContext(
    bookmarklet.replace( /^javascript:\s*/u, '' ),
    {
      navigator: { language },
      location: { href: 'https://example.com/', host: 'example.com' },
      alert: ( message ) => {
        alertMessage = message;
      },
      document: {},
    }
  );
  return alertMessage;
};

for ( const locale of [ 'zh-TW', 'zh-Hant', 'zh-HK', 'zh-MO', 'zh_HK' ] ) {
  assert.match(
    runUnsupportedBookmarklet( locale ),
    /錯誤/u,
    `${ locale }が繁体字中国語として判定されていません。`
  );
}
for ( const locale of [ 'zh-CN', 'zh-Hans' ] ) {
  assert.match(
    runUnsupportedBookmarklet( locale ),
    /错误/u,
    `${ locale }が簡体字中国語として判定されていません。`
  );
}
assert.match(
  loginEditor,
  /description:\s*__\(/,
  'ログインユーザー限定ブロックの説明が翻訳されていません。'
);
assert.match(
  loginRenderer,
  /__\(\s*'こちらのコンテンツはログインユーザーのみに表示されます。',\s*THEME_NAME\s*\)/,
  '未入力時のログイン案内が実行時翻訳になっていません。'
);
assert.match(
  blocks,
  /'cocoon-blocks\/login-user-only'[\s\S]*?msg:\s*__\(/,
  'ログイン案内のブロック属性初期値が翻訳されていません。'
);
assert.match(
  blocks,
  /export const localizeBlockMetadata/,
  'ブロック属性初期値の翻訳処理がありません。'
);

const blockMetadataFiles = findFiles(
  path.join( THEME_ROOT, 'blocks', 'src' ),
  'block.json'
);

for ( const metadataPath of blockMetadataFiles ) {
  const metadata = JSON.parse( fs.readFileSync( metadataPath, 'utf8' ) );

  for ( const value of findJapaneseMetadataValues( metadata ) ) {
    assert.ok(
      blocks.includes( `'${ value.replace( /'/g, "\\'" ) }'` ),
      `block.jsonの日本語初期値または使用例が翻訳されていません: ${ value }`
    );
  }
}

const analytics = readThemeFile(
  'lib/page-access/analytics/assets/analytics.js'
);
const toolbar = readThemeFile( 'js/gutenberg-toolbar.js' );
const silkEditor = readThemeFile( 'skins/silk/gutenberg.js' );

for ( const rawText of [
  '該当する記事が見つかりませんでした。',
  '読み込み中...',
  'エラーが発生しました',
  '通信エラーが発生しました',
  '一致する項目がありません',
] ) {
  assert.ok(
    ! analytics.includes( rawText ),
    `アクセス集計JavaScriptに未翻訳文字列があります: ${ rawText }`
  );
}

assert.match(
  toolbar,
  /var\s+__\s*=\s*wp\.i18n\.__;/,
  'Gutenbergツールバーが翻訳APIを使用していません。'
);
assert.match(
  silkEditor,
  /wp\.i18n\.__/,
  'SILKエディター拡張が翻訳APIを使用していません。'
);

//make-potの除外設定と揃えた走査対象
const POT_EXCLUDE_SEGMENTS = [
  'node_modules',
  'vendor',
  'tests',
  'scripts',
  'plugins',
  'fonts',
  'icomoon',
  'webfonts',
  'tmp',
  'tmp-user',
];

// 除外ディレクトリを避けてPHPファイルを再帰取得する処理
const collectPhpFiles = ( directory ) => {
  const results = [];

  for ( const entry of fs.readdirSync( directory, { withFileTypes: true } ) ) {
    if ( entry.name.startsWith( '.' ) ) {
      continue;
    }

    if ( entry.isDirectory() ) {
      if (
        POT_EXCLUDE_SEGMENTS.includes( entry.name ) ||
        entry.name === 'skin-made-in-heaven'
      ) {
        continue;
      }

      results.push( ...collectPhpFiles( path.join( directory, entry.name ) ) );
    } else if ( entry.name.endsWith( '.php' ) ) {
      results.push( path.join( directory, entry.name ) );
    }
  }

  return results;
};

const potCatalog = gettextParser.po.parse(
  fs.readFileSync( path.join( THEME_ROOT, 'languages', 'cocoon.pot' ) )
);
const potMsgids = new Set();

for ( const context of Object.values( potCatalog.translations ) ) {
  for ( const msgid of Object.keys( context ) ) {
    if ( msgid ) {
      potMsgids.add( msgid );
    }
  }
}

//文字列の直後がカンマの呼び出しだけを対象にし、連結式の誤検出を避ける
const TRANSLATION_CALL_PATTERN =
  /\b(?:__|_e|_n|_x|esc_html__|esc_html_e|esc_html_x|esc_attr__|esc_attr_e|esc_attr_x)\(\s*'((?:[^'\\]|\\.)*)'\s*,/g;

for ( const phpPath of collectPhpFiles( THEME_ROOT ) ) {
  const source = fs.readFileSync( phpPath, 'utf8' );
  let call;

  TRANSLATION_CALL_PATTERN.lastIndex = 0;

  while ( ( call = TRANSLATION_CALL_PATTERN.exec( source ) ) !== null ) {
    const lineStart = source.lastIndexOf( '\n', call.index ) + 1;
    const linePrefix = source.slice( lineStart, call.index ).trimStart();

    //コメント行の呼び出しは抽出対象外
    if (
      linePrefix.startsWith( '//' ) ||
      linePrefix.startsWith( '#' ) ||
      linePrefix.startsWith( '*' ) ||
      linePrefix.startsWith( '/*' )
    ) {
      continue;
    }

    //PHPのシングルクォート記法のエスケープを解除する
    const msgid = call[ 1 ].replace( /\\(['\\])/g, '$1' );

    //空文字はPOTのヘッダーと同一キーになるため対象外
    if ( ! msgid ) {
      continue;
    }

    assert.ok(
      potMsgids.has( msgid ),
      `POTに未登録の翻訳文字列があります（npm run make-pot が必要）: ${ path.relative(
        THEME_ROOT,
        phpPath
      ) } / ${ msgid }`
    );
  }
}

//package.jsonで保証するNode.js 14でも利用できないAPIを翻訳ツールへ持ち込まない
for ( const relativePath of [
  'scripts/test-i18n.js',
  'scripts/translations/skin-metadata-translations.js',
] ) {
  const source = readThemeFile( relativePath );
  assert.doesNotMatch(
    source,
    /\.at\s*\(|\.replaceAll\s*\(/u,
    `Node.js 14で利用できないAPIがあります: ${ relativePath }`
  );
}

//翻訳生成の一部が失敗した場合に、compile-allを成功扱いにしないことを確認する
for ( const relativePath of [
  'scripts/compile-po.js',
  'scripts/compile-blocks-json.js',
  'scripts/add-blocks-translations.js',
] ) {
  assert.match(
    readThemeFile( relativePath ),
    /process\.exitCode\s*=\s*1/u,
    `翻訳生成失敗時の終了コードが設定されていません: ${ relativePath }`
  );
}

process.stdout.write( '✅ i18n整合性テスト: 8言語すべて合格\n' );
