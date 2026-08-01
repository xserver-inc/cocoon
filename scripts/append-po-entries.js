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
    // lib/utils.php のブログカードラベルCSS変数で使用するため追加
    comment: '#: lib/utils.php',
    msgid: 'ラベルなし',
  },
];

// 言語ごとの翻訳
const translations = {
  en_US: {
    'ラベルなし': 'No label',
  },
  de_DE: {
    'ラベルなし': 'Kein Label',
  },
  es_ES: {
    'ラベルなし': 'Sin etiqueta',
  },
  fr_FR: {
    'ラベルなし': 'Aucune étiquette',
  },
  ko_KR: {
    'ラベルなし': '라벨 없음',
  },
  pt_PT: {
    'ラベルなし': 'Sem etiqueta',
  },
  zh_CN: {
    'ラベルなし': '无标签',
  },
  zh_TW: {
    'ラベルなし': '無標籤',
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
