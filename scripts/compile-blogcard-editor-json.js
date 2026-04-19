#!/usr/bin/env node
/**
 * cocoon-{locale}-cocoon-blogcard-editor.json を生成するスクリプト
 * blogcard-editor.js で使用される wp.i18n.__() の翻訳 JSON ファイルを作成する
 * 使い方: node scripts/compile-blogcard-editor-json.js
 */
const fs = require( 'fs' );
const path = require( 'path' );

const LANG_DIR = path.join( __dirname, '..', 'languages' );

// blogcard-editor.js で使用される翻訳文字列
const translations = {
  en_US: {
    'ブログカード（埋め込み）': 'Blog Card (Embed)',
    'URLが設定されていません': 'URL is not set',
  },
  de_DE: {
    'ブログカード（埋め込み）': 'Blogkarte (Eingebettet)',
    'URLが設定されていません': 'URL ist nicht festgelegt',
  },
  es_ES: {
    'ブログカード（埋め込み）': 'Tarjeta de blog (insertada)',
    'URLが設定されていません': 'La URL no está configurada',
  },
  fr_FR: {
    'ブログカード（埋め込み）': 'Carte de blog (intégrée)',
    'URLが設定されていません': "L'URL n'est pas configurée",
  },
  ko_KR: {
    'ブログカード（埋め込み）': '블로그 카드（임베드）',
    'URLが設定されていません': 'URL이 설정되지 않았습니다',
  },
  pt_PT: {
    'ブログカード（埋め込み）': 'Cartão de blog (incorporado)',
    'URLが設定されていません': 'O URL não está configurado',
  },
  zh_CN: {
    'ブログカード（埋め込み）': '博客卡片（嵌入）',
    'URLが設定されていません': '未设置URL',
  },
  zh_TW: {
    'ブログカード（埋め込み）': '部落格卡片（嵌入）',
    'URLが設定されていません': '未設定URL',
  },
};

// 言語ごとの plural_forms と lang 設定
const localeConfig = {
  en_US: { plural_forms: 'nplurals=2; plural=(n != 1);', lang: 'en_US' },
  de_DE: { plural_forms: 'nplurals=2; plural=(n != 1);', lang: 'de_DE' },
  es_ES: { plural_forms: 'nplurals=2; plural=(n != 1);', lang: 'es_ES' },
  fr_FR: { plural_forms: 'nplurals=2; plural=(n != 1);', lang: 'fr_FR' },
  ko_KR: { plural_forms: 'nplurals=1; plural=0;', lang: 'ko_KR' },
  pt_PT: { plural_forms: 'nplurals=2; plural=(n != 1);', lang: 'pt_PT' },
  zh_CN: { plural_forms: 'nplurals=1; plural=0;', lang: 'zh_CN' },
  zh_TW: { plural_forms: 'nplurals=1; plural=0;', lang: 'zh_TW' },
};

let count = 0;

for ( const [ locale, strings ] of Object.entries( translations ) ) {
  const config = localeConfig[ locale ];
  const messages = {
    // JED 1.x 形式のヘッダー
    '': {
      domain: 'messages',
      plural_forms: config.plural_forms,
      lang: config.lang,
    },
  };

  // 各翻訳文字列を追加（JED1.x 形式: { "msgid": ["msgstr"] }）
  for ( const [ msgid, msgstr ] of Object.entries( strings ) ) {
    messages[ msgid ] = [ msgstr ];
  }

  const json = {
    domain: 'messages',
    locale_data: { messages },
  };

  const outPath = path.join(
    LANG_DIR,
    `cocoon-${ locale }-cocoon-blogcard-editor.json`
  );
  fs.writeFileSync( outPath, JSON.stringify( json ), 'utf8' );
  console.log( `✅ ${ locale }: cocoon-${ locale }-cocoon-blogcard-editor.json` );
  count++;
}

console.log( `\n${ count } ファイルを生成しました。` );
