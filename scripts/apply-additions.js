// 残り未翻訳エントリを各言語に追加適用するスクリプト
const fs = require( 'fs' );
const path = require( 'path' );
const gp = require( '../node_modules/gettext-parser' );

const langDir = path.join( __dirname, '..', 'languages' );

// 言語ごとの追加翻訳辞書
const additions = {
  de_DE: {
    'Microsoft JhengHei': 'Microsoft JhengHei',
    '（繁體中文）': '（Traditionelles Chinesisch）',
    'Noto Sans TC': 'Noto Sans TC',
    '（繁體中文WEBフォント）': '（Webschrift Traditionelles Chinesisch）',
  },
  en_US: {
    // ログイン中のユーザー表示（HTMLタグを含む書式文字列）
    'Logged in as %1$s. <a href="%2$s">Edit your profile</a>. <a href="%3$s">Log out?</a>':
      'Logged in as %1$s. <a href="%2$s">Edit your profile</a>. <a href="%3$s">Log out?</a>',
    'Microsoft JhengHei': 'Microsoft JhengHei',
    '（繁體中文）': '（Traditional Chinese）',
    'Noto Sans TC': 'Noto Sans TC',
    '（繁體中文WEBフォント）': '（Traditional Chinese Web Font）',
  },
  es_ES: {
    'Microsoft JhengHei': 'Microsoft JhengHei',
    '（繁體中文）': '（Chino tradicional）',
    'Noto Sans TC': 'Noto Sans TC',
    '（繁體中文WEBフォント）': '（Fuente web chino tradicional）',
  },
  fr_FR: {
    総計: 'Total',
    'Microsoft JhengHei': 'Microsoft JhengHei',
    '（繁體中文）': '（Chinois traditionnel）',
    'Noto Sans TC': 'Noto Sans TC',
    '（繁體中文WEBフォント）': '（Police web chinois traditionnel）',
  },
  ko_KR: {
    'Microsoft JhengHei': 'Microsoft JhengHei',
    '（繁體中文）': '（번체 중국어）',
    'Noto Sans TC': 'Noto Sans TC',
    '（繁體中文WEBフォント）': '（번체 중국어 웹폰트）',
  },
  pt_PT: {
    'Microsoft JhengHei': 'Microsoft JhengHei',
    '（繁體中文）': '（Chinês tradicional）',
    'Noto Sans TC': 'Noto Sans TC',
    '（繁體中文WEBフォント）': '（Tipo de letra web chinês tradicional）',
  },
  zh_CN: {
    canonicalタグを追加する: '添加canonical标签',
    'Microsoft JhengHei': 'Microsoft JhengHei',
    '（繁體中文）': '（繁体中文）',
    'Noto Sans TC': 'Noto Sans TC',
    '（繁體中文WEBフォント）': '（繁体中文网络字体）',
  },
};

let totalApplied = 0;
for ( const [ locale, dict ] of Object.entries( additions ) ) {
  const poPath = path.join( langDir, `${ locale }.po` );
  if ( ! fs.existsSync( poPath ) ) continue;

  // .po ファイルをパースして未翻訳エントリに翻訳を適用する
  const parsed = gp.po.parse( fs.readFileSync( poPath ) );
  let applied = 0;

  for ( const [ msgid, msgstr ] of Object.entries( dict ) ) {
    const entry = parsed.translations[ '' ][ msgid ];
    if ( entry && ( ! entry.msgstr || entry.msgstr[ 0 ] === '' ) ) {
      entry.msgstr = [ msgstr ];
      applied++;
    }
  }

  // 更新した内容を書き戻す
  fs.writeFileSync( poPath, gp.po.compile( parsed ) );
  console.log( `✅ ${ locale }: ${ applied }件 追加適用` );
  totalApplied += applied;
}
console.log( `\n合計追加: ${ totalApplied }件` );
