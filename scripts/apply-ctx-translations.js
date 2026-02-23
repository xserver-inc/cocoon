// msgctxt付き5件の未翻訳エントリを各言語辞書に追加適用するスクリプト
const fs = require( 'fs' );
const path = require( 'path' );
const gp = require( '../node_modules/gettext-parser' );

const LANGUAGES_DIR = path.join( __dirname, '..', 'languages' );

// 各言語の翻訳（msgidをキーとして共通で辞書から引く）
const ctxTranslations = {
  de_DE: {
    accordion: 'Akkordeon',
    Amazon商品リンク: 'Amazon-Produktlink',
    楽天商品リンク: 'Rakuten-Produktlink',
    'Amazon商品をエディタ内で検索・選択し、静的な商品リンクを挿入します。':
      'Suchen und wählen Sie Amazon-Produkte im Editor aus und fügen Sie einen statischen Produktlink ein.',
    '楽天商品をエディタ内で検索・選択し、静的な商品リンクを挿入します。':
      'Suchen und wählen Sie Rakuten-Produkte im Editor aus und fügen Sie einen statischen Produktlink ein.',
  },
  en_US: {
    accordion: 'accordion',
    Amazon商品リンク: 'Amazon Product Link',
    楽天商品リンク: 'Rakuten Product Link',
    'Amazon商品をエディタ内で検索・選択し、静的な商品リンクを挿入します。':
      'Search and select Amazon products in the editor, then insert a static product link.',
    '楽天商品をエディタ内で検索・選択し、静的な商品リンクを挿入します。':
      'Search and select Rakuten products in the editor, then insert a static product link.',
  },
  es_ES: {
    accordion: 'acordeón',
    Amazon商品リンク: 'Enlace de producto Amazon',
    楽天商品リンク: 'Enlace de producto Rakuten',
    'Amazon商品をエディタ内で検索・選択し、静的な商品リンクを挿入します。':
      'Busque y seleccione productos de Amazon en el editor e inserte un enlace de producto estático.',
    '楽天商品をエディタ内で検索・選択し、静的な商品リンクを挿入します。':
      'Busque y seleccione productos de Rakuten en el editor e inserte un enlace de producto estático.',
  },
  fr_FR: {
    accordion: 'accordéon',
    Amazon商品リンク: 'Lien produit Amazon',
    楽天商品リンク: 'Lien produit Rakuten',
    'Amazon商品をエディタ内で検索・選択し、静的な商品リンクを挿入します。':
      "Recherchez et sélectionnez des produits Amazon dans l'éditeur et insérez un lien produit statique.",
    '楽天商品をエディタ内で検索・選択し、静的な商品リンクを挿入します。':
      "Recherchez et sélectionnez des produits Rakuten dans l'éditeur et insérez un lien produit statique.",
  },
  ko_KR: {
    accordion: '아코디언',
    Amazon商品リンク: 'Amazon 상품 링크',
    楽天商品リンク: 'Rakuten 상품 링크',
    'Amazon商品をエディタ内で検索・選択し、静的な商品リンクを挿入します。':
      '에디터에서 Amazon 상품을 검색·선택하고 정적 상품 링크를 삽입합니다.',
    '楽天商品をエディタ内で検索・選択し、静的な商品リンクを挿入します。':
      '에디터에서 Rakuten 상품을 검색·선택하고 정적 상품 링크를 삽입합니다.',
  },
  pt_PT: {
    accordion: 'acordeão',
    Amazon商品リンク: 'Link de produto Amazon',
    楽天商品リンク: 'Link de produto Rakuten',
    'Amazon商品をエディタ内で検索・選択し、静的な商品リンクを挿入します。':
      'Pesquise e selecione produtos da Amazon no editor e insira um link de produto estático.',
    '楽天商品をエディタ内で検索・選択し、静的な商品リンクを挿入します。':
      'Pesquise e selecione produtos da Rakuten no editor e insira um link de produto estático.',
  },
  zh_CN: {
    accordion: '手风琴',
    Amazon商品リンク: 'Amazon商品链接',
    楽天商品リンク: '乐天商品链接',
    'Amazon商品をエディタ内で検索・選択し、静的な商品リンクを挿入します。':
      '在编辑器中搜索并选择Amazon商品，插入静态商品链接。',
    '楽天商品をエディタ内で検索・選択し、静的な商品リンクを挿入します。':
      '在编辑器中搜索并选择乐天商品，插入静态商品链接。',
  },
  zh_TW: {
    accordion: '手風琴',
    Amazon商品リンク: 'Amazon商品連結',
    楽天商品リンク: '樂天商品連結',
    'Amazon商品をエディタ内で検索・選択し、静的な商品リンクを挿入します。':
      '在編輯器中搜尋並選擇Amazon商品，插入靜態商品連結。',
    '楽天商品をエディタ内で検索・選択し、静的な商品リンクを挿入します。':
      '在編輯器中搜尋並選擇樂天商品，插入靜態商品連結。',
  },
};

let totalApplied = 0;

for ( const [ locale, dict ] of Object.entries( ctxTranslations ) ) {
  const poPath = path.join( LANGUAGES_DIR, `${ locale }.po` );
  if ( ! fs.existsSync( poPath ) ) continue;

  const parsed = gp.po.parse( fs.readFileSync( poPath ) );
  let applied = 0;

  // すべてのコンテキストを走査して未翻訳エントリに辞書を適用する
  for ( const [ ctx, entries ] of Object.entries( parsed.translations ) ) {
    for ( const [ msgid, entry ] of Object.entries( entries ) ) {
      if ( msgid === '' ) continue;
      if ( ! entry.msgstr || entry.msgstr[ 0 ] === '' ) {
        if ( dict[ msgid ] ) {
          entry.msgstr = [ dict[ msgid ] ];
          applied++;
        }
      }
    }
  }

  fs.writeFileSync( poPath, gp.po.compile( parsed ) );
  console.log( `✅ ${ locale }: ${ applied }件 適用` );
  totalApplied += applied;
}
console.log( `\n合計: ${ totalApplied }件 適用完了` );
