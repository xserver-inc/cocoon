#!/usr/bin/env node
/**
 * 重箱チェックで追加した静的翻訳をPOT・POへ同期するスクリプト
 */
const fs = require( 'fs' );
const path = require( 'path' );
const gettextParser = require( '../node_modules/gettext-parser' );
const additions = require( './translations/translation-additions-202608' );
const skinMetadata = require( './translations/skin-metadata-translations' );
const translationCorrections = require( './translations/translation-corrections' );

const LANGUAGES_DIR = path.join( __dirname, '..', 'languages' );
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

//指定したカタログへ存在しないエントリを追加する
const ensureEntry = ( parsed, msgid, reference, context = '' ) => {
  if ( !parsed.translations[ context ] ) {
    parsed.translations[ context ] = {};
  }
  if ( !parsed.translations[ context ][ msgid ] ) {
    parsed.translations[ context ][ msgid ] = {
      msgid,
      msgstr: [ '' ],
      comments: { reference },
    };
  }
  return parsed.translations[ context ][ msgid ];
};

const potPath = path.join( LANGUAGES_DIR, 'cocoon.pot' );
const pot = gettextParser.po.parse( fs.readFileSync( potPath ) );

for ( const msgid of additions.keys ) {
  ensureEntry( pot, msgid, 'scripts/translations/translation-additions-202608.js' );
}
for ( const msgid of skinMetadata.keys ) {
  ensureEntry( pot, msgid, 'lib/page-settings/skin-translations.php' );
}
for ( const correction of translationCorrections( 'en_US' ) ) {
  const entry = ensureEntry(
    pot,
    correction.msgid,
    'scripts/translations/translation-corrections.js',
    correction.context
  );
  if ( correction.msgidPlural ) {
    entry.msgid_plural = correction.msgidPlural;
    entry.msgstr = [ '', '' ];
  }
}
fs.writeFileSync( potPath, gettextParser.po.compile( pot ) );

for ( const locale of LOCALES ) {
  const poPath = path.join( LANGUAGES_DIR, `${ locale }.po` );
  const parsed = gettextParser.po.parse( fs.readFileSync( poPath ) );
  const dictionary = {
    ...skinMetadata( locale ),
    ...additions( locale ),
  };

  //今回管理する辞書を既存・新規のどちらのエントリにも反映する
  for ( const [ msgid, msgstr ] of Object.entries( dictionary ) ) {
    let found = false;
    for ( const entries of Object.values( parsed.translations ) ) {
      if ( entries[ msgid ] ) {
        entries[ msgid ].msgstr = [ msgstr ];
        found = true;
      }
    }
    if ( !found ) {
      const entry = ensureEntry(
        parsed,
        msgid,
        skinMetadata.keys.includes( msgid )
          ? 'lib/page-settings/skin-translations.php'
          : 'scripts/translations/translation-additions-202608.js'
      );
      entry.msgstr = [ msgstr ];
    }
  }

  //複数形とコンテキスト付きエントリーは、配列を保ったまま全形式へ同期する
  for ( const correction of translationCorrections( locale ) ) {
    const entry = ensureEntry(
      parsed,
      correction.msgid,
      'scripts/translations/translation-corrections.js',
      correction.context
    );
    if ( correction.msgidPlural ) {
      entry.msgid_plural = correction.msgidPlural;
    }
    entry.msgstr = correction.msgstr.slice();
  }

  fs.writeFileSync( poPath, gettextParser.po.compile( parsed ) );
  process.stdout.write( `✅ ${ locale }: ${ Object.keys( dictionary ).length }件\n` );
}
