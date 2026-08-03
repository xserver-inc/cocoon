#!/usr/bin/env node
/**
 * 重箱チェックで見つかった既存翻訳の誤りを再適用するスクリプト
 */
const fs = require( 'fs' );
const path = require( 'path' );
const gettextParser = require( '../node_modules/gettext-parser' );

const LANGUAGES_DIR = path.join( __dirname, '..', 'languages' );

const colorDescription1 =
  'サイトロゴテキストや各テキストリンクのホバー時の色、<br>フロントページのNewPostの色、<br>カテゴリタイトル下線、<br>サイドバー・ウィジェット見出し下線の色、<br>フッター内アクセント線の色を変更。';
const colorDescription2 =
  'グローバルナビの3階層目の背景色、<br>フロントページのView Moreボタン、次のページへボタン、タグクラウドのホバー時背景色。';
const colorDescription3 =
  'フロントページカテゴリー２つ目の背景色、<br>目次・記事下のSNSボタン背景のストライプの青系、<br>一覧ページのページネーションボタン背景色。';
const screenResolution =
  '<a href="http://whatismyscreenresolution.net/multi-screen-test" target="_blank" rel="noopener">WhatIsMyScreenResolution</a>チェック用リンクの表示。';
const followMessage =
  'この記事が気に入ったら最新ニュース情報を、<br><span class="bold-red">フォロー</span>してチェックしよう！';
const irasutoyaMessage =
  'このアイコンは「いらすとや」さんの許可の下、当テーマのCDNサーバで配信中のデモです。アクセスが増えると、表示されなくなる可能性もあるので、自前で画像を用意するか、「<a href="https://www.irasutoya.com/" target="_blank" rel="noopener">いらすとや</a>」さんの豊富なイラストの中から好みのアイコンを探すなどして、自サーバーにアップして利用することをおすすめします。アクセス集中によりCDN上の画像が表示されなくなっても保証はできませんのでご了承ください。';

const corrections = {
  de_DE: {
    [ colorDescription1 ]:
      'Ändern Sie die Farbe des Site-Logo-Textes und der Textlinks beim Hover,<br>die Farbe von NewPost auf der Startseite,<br>die Unterstreichung der Kategorietitel,<br>die Unterstreichung der Widget-Überschriften in der Seitenleiste und<br>die Akzentfarbe im Fußbereich.',
    [ colorDescription2 ]:
      'Die Hintergrundfarbe der dritten Ebene der globalen Navigation,<br>die Farbe der Schaltfläche "Mehr sehen" auf der Startseite, die Schaltfläche "Zur nächsten Seite" und die Hintergrundfarbe beim Hover im Tag-Cloud.',
    [ colorDescription3 ]:
      'Hintergrundfarbe der zweiten Kategorie auf der Startseite,<br>blaue Streifen für den Hintergrund der SNS-Schaltflächen unter dem Inhaltsverzeichnis und dem Artikel,<br>Hintergrundfarbe der Paginierungsschaltflächen auf der Listen-Seite.',
  },
  es_ES: {
    [ colorDescription1 ]:
      'Se cambia el color del texto del logotipo del sitio y de cada enlace de texto al pasar el cursor,<br>el color del New Post en la página de inicio,<br>el subrayado del título de la categoría,<br>el color del subrayado del encabezado del widget de la barra lateral y<br>el color de la línea de acento en el pie de página.',
    [ colorDescription2 ]:
      'El color de fondo del tercer nivel de la navegación global,<br>el botón Ver Más en la página principal, el botón de la siguiente página y el color de fondo al pasar el ratón sobre la nube de etiquetas.',
    [ colorDescription3 ]:
      'Color de fondo para la segunda categoría de la página principal,<br>rayas azules para los fondos de los botones de redes sociales debajo de la tabla de contenido y artículos,<br>y el color de fondo del botón de paginación en la página de lista.',
  },
  fr_FR: {
    [ colorDescription1 ]:
      "Modifiez la couleur du texte du logo du site,<br>la couleur des liens lors du survol,<br>la couleur des nouveaux articles sur la page d'accueil,<br>la couleur des sous-titres des catégories et des widgets dans la barre latérale,<br>et la couleur des lignes d'accent dans le pied de page.",
    [ colorDescription2 ]:
      "Couleur de fond du troisième niveau de la navigation globale,<br>bouton Voir More sur la page d'accueil, bouton Next page, couleur de fond du nuage de tags lors du survol.",
    [ colorDescription3 ]:
      "Couleur de fond de la deuxième catégorie sur la page d'accueil,<br>rayures bleues pour l'arrière-plan des boutons SNS sous le sommaire et les articles,<br>couleur de fond des boutons de pagination sur la page de liste.",
  },
  ko_KR: {
    '%s週間': '%s주',
    'スキンNAGIの設定': '스킨 NAGI 설정',
    [ irasutoyaMessage ]:
      '이 아이콘은 “Irasutoya”의 허가를 받아 테마 CDN 서버에서 배포하는 데모입니다. 접속이 증가하면 표시되지 않을 수 있으므로 직접 이미지를 준비하거나 “<a href="https://www.irasutoya.com/" target="_blank" rel="noopener">Irasutoya</a>”의 다양한 일러스트에서 원하는 아이콘을 찾아 자신의 서버에 업로드해 사용하는 것을 권장합니다. 접속 집중으로 CDN 이미지가 표시되지 않더라도 보장할 수 없으니 양해해 주세요.',
  },
  pt_PT: {
    [ colorDescription1 ]:
      'Alterar a cor do texto do logotipo do site,<br>a cor ao passar o mouse sobre os links de texto,<br>a cor de "Nova postagem" na página inicial,<br>a linha abaixo dos títulos da categoria e do widget da barra lateral e<br>a cor da linha de destaque no rodapé.',
    [ colorDescription2 ]:
      'Cor de fundo da terceira camada do menu de navegação global,<br>botão "Ver Mais" na página inicial, botão "Próximo" e a cor de fundo ao passar o mouse sobre as tags no "Tag Cloud".',
    [ colorDescription3 ]:
      'Cor de fundo da segunda categoria na página inicial,<br>fundo das listras azuis dos botões de redes sociais abaixo do artigo e<br>a cor de fundo dos botões de paginação na página de lista.',
  },
  zh_CN: {
    [ screenResolution ]:
      '<a href="http://whatismyscreenresolution.net/multi-screen-test" target="_blank" rel="noopener">WhatIsMyScreenResolution</a> 显示检查用链接。',
    [ irasutoyaMessage ]:
      '此图标是在“Irasutoya”的许可下，通过本主题的 CDN 服务器分发的演示。访问量增加时可能无法显示，因此建议自行准备图片，或从“<a href="https://www.irasutoya.com/" target="_blank" rel="noopener">Irasutoya</a>”丰富的插图中选择图标并上传到自己的服务器使用。请注意，访问集中时无法保证 CDN 上的图片正常显示。',
  },
  zh_TW: {
    [ followMessage ]:
      '如果您喜歡這篇文章，請按<br><span class="bold-red">讚</span>，以查看最新資訊！',
    'もしも必須': 'Moshimo 必填',
    'もしもアフィリエイト': 'Moshimo 聯盟行銷',
    'リンクをもしもアフィリエイトを経由にする': '透過 Moshimo 聯盟行銷建立連結',
    'もしもアフィリエイト経由でAmazonリンクを掲載し報酬を得ます。【重要】2019年1月23日の<a href="https://affiliate.amazon.co.jp/help/topic/t52/ref=amb_link_zYXX0aRKMACI_Qkj9rR6Nw_1?pf_rd_p=c08a6c9b-94fe-481e-ad8b-b2c640121b1f" target="_blank" rel="noopener">PA-APIの仕様変更</a>により、APIが生成するリンクから売上が発生しないとAPIが利用できなくなりました。ですので、<span class="red">もしもアフィリエイト経由の場合は、30日でAPIが利用できなくなる可能性があります</span>。AmazonのAPIを利用したい場合は、この機能は有効にしないことをおすすめします。PA-APIの制限がクリアできない場合は、楽天商品リンクをご利用ください。':
      '透過 Moshimo 聯盟行銷刊登 Amazon 連結並獲得報酬。【重要】由於 2019 年 1 月 23 日的 <a href="https://affiliate.amazon.co.jp/help/topic/t52/ref=amb_link_zYXX0aRKMACI_Qkj9rR6Nw_1?pf_rd_p=c08a6c9b-94fe-481e-ad8b-b2c640121b1f" target="_blank" rel="noopener">PA-API 規格變更</a>，若 API 產生的連結沒有帶來銷售，API 將無法繼續使用。因此，<span class="red">透過 Moshimo 聯盟行銷時，API 可能會在 30 天後無法使用</span>。如要使用 Amazon API，建議不要啟用此功能。若無法符合 PA-API 限制，請使用樂天商品連結。',
    'もしもアフィリエイトのAmazon IDを入力してください。': '請輸入 Moshimo 聯盟行銷的 Amazon ID。',
    'もしもアフィリエイトの楽天IDを入力してください。': '請輸入 Moshimo 聯盟行銷的樂天 ID。',
    'もしもアフィリエイトのYahoo!ショッピングIDを入力してください。': '請輸入 Moshimo 聯盟行銷的 Yahoo! 購物 ID。',
    '「ふりがな（ルビ）」ボタンを表示する': '顯示「注音」按鈕',
    'Cocoonの「ルビ」ボタン表示を切り替えます。プラグインのルビ機能を利用していてエラーが出る場合は無効にしてください。':
      '切換 Cocoon 的「注音」按鈕顯示。如果使用外掛的注音功能時發生錯誤，請停用此功能。',
    'Cocoonの「書式のクリア」表示を切り替えます。プラグインのルビ機能を利用していてエラーが出る場合は無効にしてください。':
      '切換 Cocoon 的「清除格式」顯示。如果使用外掛的注音功能時發生錯誤，請停用此功能。',
    'はてなブックマーク': 'Hatena 書籤',
    [ irasutoyaMessage ]:
      '此圖示是在「Irasutoya」的許可下，由本佈景主題的 CDN 伺服器分發的示範。流量增加時可能無法顯示，因此建議自行準備圖片，或從「<a href="https://www.irasutoya.com/" target="_blank" rel="noopener">Irasutoya</a>」豐富的插圖中選擇圖示並上傳到自己的伺服器使用。請注意，流量集中時無法保證 CDN 上的圖片正常顯示。',
    '※固定ページや投稿のコンテンツは除く。「設定なし」にするとCocoon設定 > 全体設定 > サイトフォント の設定を継承します。':
      '※不包括頁面及文章內容。設定為「未設定」時，將沿用 Cocoon 設定 > 全域設定 > 網站字型的設定。',
    'Cocoon設定>インデックス>カテゴリーごとを選択した場合に表示されます。':
      '選取 Cocoon 設定 > 索引 > 依分類顯示時顯示。',
    'Cocoon設定>インデックス>タブ一覧又はカテゴリーごとを選択した場合に表示されます。':
      '選取 Cocoon 設定 > 索引 > 分頁清單或依分類顯示時顯示。',
  },
};

for ( const [ locale, dictionary ] of Object.entries( corrections ) ) {
  const poPath = path.join( LANGUAGES_DIR, `${ locale }.po` );
  const parsed = gettextParser.po.parse( fs.readFileSync( poPath ) );

  // msgctxt付きのエントリも含め、対象原文の翻訳を上書きする
  for ( const entries of Object.values( parsed.translations ) ) {
    for ( const [ msgid, entry ] of Object.entries( entries ) ) {
      if ( Object.prototype.hasOwnProperty.call( dictionary, msgid ) ) {
        entry.msgstr = [ dictionary[ msgid ] ];
      }
    }
  }

  fs.writeFileSync( poPath, gettextParser.po.compile( parsed ) );
  process.stdout.write( `✅ ${ locale }\n` );
}
