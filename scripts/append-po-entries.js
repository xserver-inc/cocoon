#!/usr/bin/env node
/**
 * .po ファイルの末尾に新しい msgid エントリを追加するスクリプト
 * 使い方: node scripts/append-po-entries.js
 */
const fs = require( 'fs' );
const path = require( 'path' );

const LANG_DIR = path.join( __dirname, '..', 'languages' );

// 追加する新規 msgid エントリ（ソースファイルのコメント付き）
const newEntries = [
  {
    // js/blogcard-editor.js で wp.i18n.__() を使用するために追加
    comment: '#: js/blogcard-editor.js',
    msgid: 'ブログカード（埋め込み）',
  },
  {
    comment: '#: js/blogcard-editor.js',
    msgid: 'URLが設定されていません',
  },
];

// 言語ごとの翻訳
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

let count = 0;

for ( const [ locale, langMap ] of Object.entries( translations ) ) {
  const filePath = path.join( LANG_DIR, `${ locale }.po` );
  if ( ! fs.existsSync( filePath ) ) {
    console.warn( `⚠️  ${ locale }.po が見つかりません` );
    continue;
  }

  const content = fs.readFileSync( filePath, 'utf8' );
  const isCRLF = content.includes( '\r\n' );
  const NL = isCRLF ? '\r\n' : '\n';

  let appended = 0;
  let newContent = content;

  for ( const entry of newEntries ) {
    const msgid = entry.msgid;
    const translation = langMap[ msgid ] || '';

    // 既に存在する場合はスキップ
    const escapedMsgid = msgid.replace( /\\/g, '\\\\' ).replace( /"/g, '\\"' );
    if ( content.includes( `msgid "${ escapedMsgid }"` ) ) {
      continue;
    }

    // 末尾に追加するエントリを作成
    const escapedTranslation = translation
      .replace( /\\/g, '\\\\' )
      .replace( /"/g, '\\"' )
      .replace( /\n/g, '\\n' );

    const block =
      NL +
      entry.comment + NL +
      `msgid "${ escapedMsgid }"` + NL +
      `msgstr "${ escapedTranslation }"` + NL;

    newContent += block;
    appended++;
  }

  if ( appended > 0 ) {
    fs.writeFileSync( filePath, newContent, 'utf8' );
    console.log( `✅ ${ locale }: ${ appended } 件追加` );
    count += appended;
  } else {
    console.log( `⏭  ${ locale }: 追加なし（既存）` );
  }
}

console.log( `\n合計 ${ count } 件のエントリを .po ファイルに追加しました。` );
