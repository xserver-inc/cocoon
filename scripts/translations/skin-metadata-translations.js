/**
 * バンドルスキンの名前・説明文に対する翻訳辞書
 * メイド・イン・ヘブンは変更禁止対象のため、元データにも含めない。
 */
const fs = require( 'fs' );
const path = require( 'path' );

const catalogPath = path.join(
  __dirname,
  '..',
  '..',
  'lib',
  'page-settings',
  'skin-translations.php'
);
const catalogSource = fs.readFileSync( catalogPath, 'utf8' );

//PHPの静的カタログを翻訳辞書の原文一覧として共有する
const keys = [
  ...catalogSource.matchAll( /^\s*'([^']+)'\s*=>\s*__/gmu ),
].map( ( match ) => match[ 1 ] );
const NAME_COUNT = 66;
const nameKeys = keys.slice( 0, NAME_COUNT );
const descriptionKeys = keys.slice( NAME_COUNT );

//固有名詞として扱えるよう、全ロケール共通の英字スキン名を定義する
const translatedNames = [
  'Bizarre-food (Green Soba)',
  'Bizarre-food (Pink Wine)',
  'Bizarre-food (Black Burger)',
  'Bizarre-food (Blue Curry)',
  'Bizarre-food (White Ramen)',
  'COLORS (Yellow)',
  'COLORS (Green)',
  'COLORS (Pink)',
  'COLORS (Black)',
  'COLORS (Blue)',
  'COLORS (Red)',
  'Fuwari -Omeshi-cha-',
  'Fuwari -Miru-cha-',
  'Fuwari -Ebi-cha-',
  'Fuwari -Kachi-iro-',
  'Momoon (Aqua)',
  'Momoon (Orange)',
  'Momoon (Green)',
  'Momoon (Purple)',
  'Momoon (Pink)',
  'Natural (Green)',
  'Natural (Blue)',
  'SILK',
  'Simple-Darkmode Always (Always Dark Mode)',
  'Everyday Coffee',
  'Everyday Rose',
  'Everyday Deep Sea',
  'Outing Pink',
  'Outing Blue',
  'Outing Lemon',
  'Handwritten Note (Green Orange)',
  'Handwritten Note (Dark Sky)',
  'Handwritten Note (Pink Lemon)',
  'Handwritten Note (Blue Coral)',
  'Handwritten Note (White Banana)',
  'Soft Fade-In Add-on',
  'Gentle Lightning',
  'Miru Light',
  'Slowly Changing Colors',
  'Alice (Cheshire Cat)',
  'Alice (Vitamin)',
  'Alice (Unicorn)',
  'Alice (Rose)',
  'Alice (Original)',
  'Alice (White Rabbit)',
  'Innocence',
  'Colorful Line',
  'Samurai Blue (Kachi-iro)',
  'Samurai Blue (Original)',
  'Skin Option Change Sample (CSV)',
  'Skin Option Change Sample (JSON)',
  'Skin Option Change Sample (PHP)',
  'Skin Template',
  'Dark Enji',
  'Dark Kamonoha',
  'Dark Ruri',
  'Under Testing',
  'Dot Rainy Blue',
  'Dot Wine Red',
  'Mix Green',
  'Mix Blue',
  'Mix Red',
  'Modern Black',
  'Monochrome',
  'Embossed',
  'Travel Camera',
];

//変更禁止スキンと共有される名前は、既存カタログのロケール別表記を維持する
const translatedNameOverrides = {
  de_DE: { 'モノクロ': 'Monochrom' },
  en_US: { 'モノクロ': 'Monochrome' },
  es_ES: { 'モノクロ': 'Monocromático' },
  fr_FR: { 'モノクロ': 'Monochrome' },
  ko_KR: { 'モノクロ': '모노크롬' },
  pt_PT: { 'モノクロ': 'Monocromático' },
  zh_CN: { 'モノクロ': '单色' },
  zh_TW: { 'モノクロ': '黑白' },
};

const exactSources = {
  cocoonGradient:
    'Cocoonらしさを薄めて、所々に青系とピンク系を基調としたグラデーションを取り入れたスキン。',
  alwaysDark:
    'Hirositeさんが作成されたSimple‐Darkmodeスキンを元に、ブラウザやOSの設定に関わらず常にダークモードになるよう作成されたスキンです。',
  pcFooter: 'PCでもフッターモバイルボタンが使える',
  functionsDemo:
    'functions.phpファイルの変更により、テーマのデザインを変更するデモ。詳細は、スキンフォルダー内のfunctions.phpファイルを参照してください。サンプルですので、実用性はありません。',
  tomato: 'marine がお届けする Cocoon のスキン m-tomato です。',
  sora: 'marineがお届けするテーマ「sora」',
  csvDemo:
    'option.csvファイルの変更により、テーマのデザインを変更するデモ。詳細は、スキンフォルダー内のoption.csvファイルを参照してください。サンプルですので、実用性はありません。',
  jsonDemo:
    'option.jsonファイルの変更により、テーマのデザインを変更するデモ。詳細は、スキンフォルダー内のoption.jsonファイルを参照してください。サンプルですので、実用性はありません。',
  lightning: '「勝手にライトニング！」で使用しているオリジナルスキン',
  adultCute: '「大人可愛さ」と「知的さ」を兼ね備えた居心地の良いスキン。',
  miruLight:
    'ふわっとした居心地のよいシンプルなデザイン。「みるめも」というサイトのデザインを「らいと」に表現したスキンです。',
  handwritten: 'アナログな手書き風をイメージした少し抜けのあるスキンです。',
  blockEditor: 'キーカラー設定やダークスキン対応のブロックエディター向けCocoonスキン。',
  grayVisual:
    'グレイベースのシンプルデザイン フロントページにメインビジュアルを画面高さいっぱいに表示するスキンです',
  samuraiSoccer: 'サムライブルーを基調としたサッカー向けのコンセプトスキン',
  samuraiKachi: 'サムライブルー（勝色モデル）を基調としたスキン',
  oneColumn:
    'シンプルな1カラムスキン。文章メインのサイトなどに。1カラムスキンなのでサイドバーなどは利用できません。',
  template: 'スキン自作の際にひな型となるスキンです。スタイルは何も入っていません。',
  testing: 'テスト中のスキン。',
  pastelCute: 'パステルカラーの可愛らしいスキンです。',
  pastelColorful: 'パステルカラー調のカラフルなスキン',
  monochrome: 'モノクロを基調としています。',
  affiliate: '一番の特徴といえばアフィリエイトボタンをフッターに固定表示できるところですかね。。',
  outing:
    '女性が春色のスプリングコートを羽織っておでかけするイメージで作りました。ファッション・女性向けコンテンツにいかがでしょうか。',
  fade: '好きなスキンにふわっと動き（フェイドイン）を追加します。',
  travel: '旅ブログ専用スキンです。旅行写真が美しく見えるように工夫しています。',
  seasons: '春夏秋冬のような季節をモチーフにして優しさを感じられるデザインを目指して作成されたスキン。',
  wordpressLike: '某無料WordPressテーマっぽいスキンです。',
  everyday:
    '毎日使っても飽きないデザインを目指しました。スキンカラーや背景画像を変えることでレイアウトは同じでも部屋のカーテンを変えるように雰囲気をチェンジできます。',
  ruri:
    '瑠璃色（るりいろ）のダークスキン。濃い紫みの鮮やかな青色をキーカラーにした暗色のスキン。',
  neumorphism:
    '立体感のあるニューモフィズムデザインにするためのスキンです。※全体の色と角の丸みは外観⇒カスタマイズ⇒Neumorphismで変更できます。',
  slowBackground: '背景の色がゆっくり変化していきます（4つの色を選べます）。',
  enji:
    '臙脂色（えんじいろ）のダークスキン。黒みをおびた深く艶やかな紅色をキーカラーにした暗色のスキン。',
  simpleDark:
    '装飾は控えめかつダークモードに対応したシンプルスキン。OSやブラウザーがダークモード設定の場合はダークモードで表示されます。',
  kamonoha:
    '鴨の羽色（かものはいろ）のダークスキン。真鴨 の頭の羽色に由来する少し暗い青緑色をキーカラーにした暗色のスキン。',
};

const exactTranslations = {
  en_US: [
    'A skin that softens the typical Cocoon look and adds blue- and pink-based gradients in selected areas.',
    'Based on the Simple-Darkmode skin by Hirosite, this skin stays in dark mode regardless of browser or OS settings.',
    'Enables the footer mobile buttons on desktop computers as well.',
    'A demonstration that changes the theme design through functions.php. See functions.php in the skin folder for details. This is a sample and is not intended for practical use.',
    'm-tomato, a Cocoon skin by marine.',
    'The “sora” theme by marine.',
    'A demonstration that changes the theme design through option.csv. See option.csv in the skin folder for details. This is a sample and is not intended for practical use.',
    'A demonstration that changes the theme design through option.json. See option.json in the skin folder for details. This is a sample and is not intended for practical use.',
    'The original skin used by “Katteni Lightning!”.',
    'A comfortable skin combining mature cuteness with an intelligent look.',
    'A soft, comfortable, and simple design that gives the Mirumemo website a lighter expression.',
    'A relaxed skin inspired by an analog handwritten style.',
    'A Cocoon skin for the block editor with key-color settings and dark-skin support.',
    'A simple gray-based design whose front-page main visual fills the full screen height.',
    'A soccer-themed concept skin based on Samurai Blue.',
    'A skin based on Samurai Blue in the traditional Kachi-iro model.',
    'A simple one-column skin for text-focused sites. Sidebars and similar areas are unavailable.',
    'A blank starter skin for creating your own skin.',
    'A skin currently under testing.',
    'A cute pastel-colored skin.',
    'A colorful pastel-style skin.',
    'A monochrome-based skin.',
    'Its main feature is the ability to keep an affiliate button fixed in the footer.',
    'Designed around the image of a woman going out in a spring-colored coat. Ideal for fashion and content aimed at women.',
    'Adds a gentle fade-in motion to your favorite skin.',
    'A skin designed for travel blogs, with styling that makes travel photos look their best.',
    'A gentle design inspired by the four seasons.',
    'A skin styled after a certain free WordPress theme.',
    'Designed to remain enjoyable every day. Change the skin color or background image to refresh the atmosphere like changing the curtains in a room.',
    'A dark skin based on vivid, purplish Ruri blue.',
    'A skin with a dimensional neumorphic design. Change the overall color and corner radius under Appearance → Customize → Neumorphism.',
    'The background colors change slowly; four colors can be selected.',
    'A dark skin based on deep, glossy Enji crimson.',
    'A minimally decorated simple skin with dark-mode support. It follows the OS or browser dark-mode setting.',
    'A dark skin based on Kamonoha, a subdued blue-green inspired by a mallard’s head feathers.',
  ],
  de_DE: [
    'Ein Skin, der den typischen Cocoon-Look zurücknimmt und stellenweise Farbverläufe in Blau- und Rosatönen verwendet.',
    'Dieser Skin basiert auf dem Simple-Darkmode-Skin von Hirosite und bleibt unabhängig von Browser- oder Betriebssystemeinstellungen immer im Dunkelmodus.',
    'Ermöglicht die mobilen Fußzeilen-Schaltflächen auch auf dem PC.',
    'Eine Demonstration, die das Theme-Design über functions.php ändert. Einzelheiten stehen in der functions.php im Skin-Ordner. Das Beispiel ist nicht für den praktischen Einsatz gedacht.',
    'm-tomato, ein Cocoon-Skin von marine.',
    'Das Theme „sora“ von marine.',
    'Eine Demonstration, die das Theme-Design über option.csv ändert. Einzelheiten stehen in der option.csv im Skin-Ordner. Das Beispiel ist nicht für den praktischen Einsatz gedacht.',
    'Eine Demonstration, die das Theme-Design über option.json ändert. Einzelheiten stehen in der option.json im Skin-Ordner. Das Beispiel ist nicht für den praktischen Einsatz gedacht.',
    'Der Original-Skin von „Katteni Lightning!“.',
    'Ein angenehmer Skin, der erwachsene Niedlichkeit mit einem intelligenten Erscheinungsbild verbindet.',
    'Ein weiches, angenehmes und schlichtes Design, das die Website Mirumemo leichter interpretiert.',
    'Ein lockerer Skin im Stil analoger Handschrift.',
    'Ein Cocoon-Skin für den Block-Editor mit Schlüsselfarben und Unterstützung für dunkle Skins.',
    'Ein schlichtes graues Design, dessen Hauptmotiv auf der Startseite die gesamte Bildschirmhöhe ausfüllt.',
    'Ein Fußball-Konzept-Skin auf Grundlage von Samurai Blue.',
    'Ein Skin auf Grundlage von Samurai Blue im traditionellen Kachi-iro-Modell.',
    'Ein einfacher einspaltiger Skin für textorientierte Websites. Seitenleisten und ähnliche Bereiche sind nicht verfügbar.',
    'Ein leerer Ausgangs-Skin zum Erstellen eigener Skins.',
    'Ein Skin, der derzeit getestet wird.',
    'Ein hübscher Skin in Pastellfarben.',
    'Ein farbenfroher Skin im Pastellstil.',
    'Ein monochromer Skin.',
    'Das Hauptmerkmal ist eine feststehende Affiliate-Schaltfläche in der Fußzeile.',
    'Entworfen nach dem Bild einer Frau im frühlingsfarbenen Mantel. Ideal für Mode- und Fraueninhalte.',
    'Fügt dem gewünschten Skin eine sanfte Einblendbewegung hinzu.',
    'Ein Skin für Reiseblogs, der Reisefotos besonders schön zur Geltung bringt.',
    'Ein sanftes Design, inspiriert von den vier Jahreszeiten.',
    'Ein Skin im Stil eines bestimmten kostenlosen WordPress-Themes.',
    'Für ein Design, das täglich Freude macht. Skin-Farbe und Hintergrundbild verändern die Atmosphäre wie neue Vorhänge im Zimmer.',
    'Ein dunkler Skin auf Basis des leuchtenden, violettstichigen Ruri-Blaus.',
    'Ein Skin mit räumlichem Neumorphismus-Design. Gesamtfarbe und Eckenradius lassen sich unter Design → Customizer → Neumorphism ändern.',
    'Die Hintergrundfarben wechseln langsam; vier Farben können gewählt werden.',
    'Ein dunkler Skin auf Basis des tiefen, glänzenden Enji-Karmesins.',
    'Ein schlicht dekorierter Skin mit Dunkelmodus-Unterstützung. Er folgt der Dunkelmodus-Einstellung von Betriebssystem oder Browser.',
    'Ein dunkler Skin auf Basis von Kamonoha, einem gedämpften Blaugrün nach den Kopffedern der Stockente.',
  ],
  es_ES: [
    'Un skin que suaviza el aspecto típico de Cocoon e incorpora degradados azules y rosas en algunas zonas.',
    'Basado en el skin Simple-Darkmode de Hirosite, permanece siempre en modo oscuro con independencia del navegador o del sistema operativo.',
    'Permite usar también en el PC los botones móviles del pie de página.',
    'Demostración que cambia el diseño mediante functions.php. Consulta ese archivo en la carpeta del skin. Es solo una muestra sin uso práctico.',
    'm-tomato, un skin de Cocoon creado por marine.',
    'El tema «sora» creado por marine.',
    'Demostración que cambia el diseño mediante option.csv. Consulta ese archivo en la carpeta del skin. Es solo una muestra sin uso práctico.',
    'Demostración que cambia el diseño mediante option.json. Consulta ese archivo en la carpeta del skin. Es solo una muestra sin uso práctico.',
    'El skin original utilizado en «Katteni Lightning!».',
    'Un skin acogedor que combina una estética adulta y adorable con un aire inteligente.',
    'Un diseño suave, acogedor y sencillo que expresa de forma ligera el diseño del sitio Mirumemo.',
    'Un skin desenfadado inspirado en la escritura manual analógica.',
    'Un skin de Cocoon para el editor de bloques con colores clave y compatibilidad con skins oscuros.',
    'Diseño sencillo en gris cuyo visual principal ocupa toda la altura de la pantalla en la portada.',
    'Un skin conceptual de fútbol basado en Samurai Blue.',
    'Un skin basado en Samurai Blue, modelo tradicional Kachi-iro.',
    'Skin sencillo de una columna para sitios centrados en texto. No permite usar barras laterales.',
    'Un skin inicial vacío para crear skins propios.',
    'Un skin actualmente en pruebas.',
    'Un bonito skin de colores pastel.',
    'Un skin colorido de estilo pastel.',
    'Un skin monocromático.',
    'Su característica principal es poder fijar un botón de afiliado en el pie de página.',
    'Diseñado con la imagen de una mujer que sale con un abrigo de color primaveral. Ideal para moda y contenido dirigido a mujeres.',
    'Añade un suave movimiento de aparición al skin que prefieras.',
    'Skin para blogs de viajes, diseñado para realzar las fotografías de viaje.',
    'Un diseño amable inspirado en las cuatro estaciones.',
    'Un skin con el estilo de cierto tema gratuito de WordPress.',
    'Diseñado para no cansar con el uso diario. Cambiar el color o el fondo renueva el ambiente como cambiar las cortinas de una habitación.',
    'Skin oscuro basado en el vivo azul Ruri con matiz violáceo.',
    'Skin con diseño neumórfico tridimensional. El color y el radio de las esquinas se cambian en Apariencia → Personalizar → Neumorphism.',
    'Los colores del fondo cambian lentamente; se pueden elegir cuatro colores.',
    'Skin oscuro basado en el carmesí Enji, profundo y brillante.',
    'Skin sencillo, sobrio y compatible con el modo oscuro. Sigue la configuración del sistema operativo o del navegador.',
    'Skin oscuro basado en Kamonoha, un verde azulado apagado inspirado en las plumas de la cabeza del ánade real.',
  ],
  fr_FR: [
    'Un skin qui atténue l’aspect typique de Cocoon et ajoute par endroits des dégradés bleus et roses.',
    'Basé sur le skin Simple-Darkmode de Hirosite, ce skin reste toujours en mode sombre, quels que soient le navigateur et le système.',
    'Permet également d’utiliser sur PC les boutons mobiles du pied de page.',
    'Démonstration modifiant le design via functions.php. Consultez ce fichier dans le dossier du skin. Cet exemple n’est pas destiné à un usage réel.',
    'm-tomato, un skin Cocoon proposé par marine.',
    'Le thème « sora » proposé par marine.',
    'Démonstration modifiant le design via option.csv. Consultez ce fichier dans le dossier du skin. Cet exemple n’est pas destiné à un usage réel.',
    'Démonstration modifiant le design via option.json. Consultez ce fichier dans le dossier du skin. Cet exemple n’est pas destiné à un usage réel.',
    'Le skin original utilisé par « Katteni Lightning! ».',
    'Un skin accueillant qui associe charme adulte et apparence intelligente.',
    'Un design doux, accueillant et simple qui réinterprète avec légèreté le site Mirumemo.',
    'Un skin décontracté inspiré d’une écriture manuscrite analogique.',
    'Un skin Cocoon pour l’éditeur de blocs avec couleurs principales et prise en charge des skins sombres.',
    'Un design gris simple dont le visuel principal occupe toute la hauteur de l’écran sur la page d’accueil.',
    'Un skin conceptuel consacré au football et basé sur Samurai Blue.',
    'Un skin basé sur Samurai Blue, modèle traditionnel Kachi-iro.',
    'Skin simple à une colonne pour les sites axés sur le texte. Les barres latérales ne sont pas disponibles.',
    'Un skin de départ vide pour créer vos propres skins.',
    'Un skin actuellement en cours de test.',
    'Un joli skin aux couleurs pastel.',
    'Un skin coloré de style pastel.',
    'Un skin monochrome.',
    'Sa principale caractéristique est de pouvoir fixer un bouton d’affiliation dans le pied de page.',
    'Conçu autour de l’image d’une femme sortant avec un manteau aux couleurs printanières. Idéal pour la mode et les contenus destinés aux femmes.',
    'Ajoute un léger mouvement d’apparition au skin de votre choix.',
    'Un skin pour les blogs de voyage, conçu pour mettre les photos de voyage en valeur.',
    'Un design doux inspiré des quatre saisons.',
    'Un skin dans le style d’un certain thème WordPress gratuit.',
    'Conçu pour rester agréable chaque jour. Modifier la couleur ou l’arrière-plan renouvelle l’ambiance comme si l’on changeait les rideaux d’une pièce.',
    'Un skin sombre basé sur le bleu Ruri vif aux nuances violettes.',
    'Un skin au design neumorphique en relief. La couleur et l’arrondi se règlent dans Apparence → Personnaliser → Neumorphism.',
    'Les couleurs d’arrière-plan changent lentement ; quatre couleurs peuvent être choisies.',
    'Un skin sombre basé sur le cramoisi Enji profond et brillant.',
    'Un skin simple, discret et compatible avec le mode sombre. Il suit le réglage du système ou du navigateur.',
    'Un skin sombre basé sur Kamonoha, un bleu-vert discret inspiré des plumes de la tête du colvert.',
  ],
  ko_KR: [
    'Cocoon 특유의 느낌을 줄이고 곳곳에 파란색과 분홍색 계열의 그라데이션을 적용한 스킨입니다.',
    'Hirosite가 만든 Simple-Darkmode 스킨을 바탕으로 브라우저나 OS 설정과 관계없이 항상 다크 모드로 표시되는 스킨입니다.',
    'PC에서도 푸터 모바일 버튼을 사용할 수 있습니다.',
    'functions.php를 변경해 테마 디자인을 바꾸는 데모입니다. 자세한 내용은 스킨 폴더의 functions.php를 확인하세요. 샘플이므로 실용 목적은 아닙니다.',
    'marine이 제공하는 Cocoon 스킨 m-tomato입니다.',
    'marine이 제공하는 테마 “sora”입니다.',
    'option.csv를 변경해 테마 디자인을 바꾸는 데모입니다. 자세한 내용은 스킨 폴더의 option.csv를 확인하세요. 샘플이므로 실용 목적은 아닙니다.',
    'option.json을 변경해 테마 디자인을 바꾸는 데모입니다. 자세한 내용은 스킨 폴더의 option.json을 확인하세요. 샘플이므로 실용 목적은 아닙니다.',
    '“Katteni Lightning!”에서 사용하는 오리지널 스킨입니다.',
    '성숙한 귀여움과 지적인 분위기를 함께 갖춘 편안한 스킨입니다.',
    '부드럽고 편안한 심플 디자인으로 Mirumemo 사이트의 디자인을 가볍게 표현한 스킨입니다.',
    '아날로그 손글씨 느낌을 살린 여유로운 스킨입니다.',
    '키 컬러 설정과 다크 스킨을 지원하는 블록 편집기용 Cocoon 스킨입니다.',
    '회색 기반의 심플한 디자인으로 첫 페이지의 메인 비주얼을 화면 높이 가득 표시합니다.',
    'Samurai Blue를 바탕으로 한 축구 콘셉트 스킨입니다.',
    '전통색 Kachi-iro 모델의 Samurai Blue를 바탕으로 한 스킨입니다.',
    '글 중심 사이트에 적합한 심플한 1열 스킨입니다. 사이드바 등은 사용할 수 없습니다.',
    '스킨을 직접 만들 때 사용하는 빈 템플릿 스킨입니다.',
    '현재 테스트 중인 스킨입니다.',
    '파스텔 색상의 귀여운 스킨입니다.',
    '파스텔 색조의 컬러풀한 스킨입니다.',
    '모노크롬을 기반으로 한 스킨입니다.',
    '제휴 버튼을 푸터에 고정 표시할 수 있는 것이 가장 큰 특징입니다.',
    '봄빛 코트를 입고 외출하는 여성을 이미지로 만들었습니다. 패션 및 여성 대상 콘텐츠에 잘 어울립니다.',
    '원하는 스킨에 부드러운 페이드인 동작을 추가합니다.',
    '여행 사진이 아름답게 보이도록 만든 여행 블로그 전용 스킨입니다.',
    '사계절을 모티프로 부드러운 느낌을 목표로 만든 스킨입니다.',
    '특정 무료 WordPress 테마와 비슷한 분위기의 스킨입니다.',
    '매일 사용해도 질리지 않는 디자인입니다. 스킨 색상이나 배경 이미지를 바꾸면 방의 커튼을 바꾸듯 분위기를 바꿀 수 있습니다.',
    '보랏빛이 도는 선명한 Ruri 청색을 핵심 색상으로 한 다크 스킨입니다.',
    '입체적인 뉴모피즘 디자인의 스킨입니다. 전체 색상과 모서리 둥글기는 외모 → 사용자 정의 → Neumorphism에서 변경할 수 있습니다.',
    '배경색이 천천히 바뀌며 네 가지 색상을 선택할 수 있습니다.',
    '검은 기가 도는 깊고 윤기 있는 Enji 진홍색을 핵심 색상으로 한 다크 스킨입니다.',
    '장식을 절제하고 다크 모드를 지원하는 심플 스킨입니다. OS 또는 브라우저 설정에 따라 다크 모드로 표시됩니다.',
    '청둥오리 머리 깃털에서 유래한 어두운 청록색 Kamonoha를 핵심 색상으로 한 다크 스킨입니다.',
  ],
  pt_PT: [
    'Um skin que suaviza o visual típico do Cocoon e incorpora gradientes azuis e cor-de-rosa em algumas áreas.',
    'Baseado no skin Simple-Darkmode de Hirosite, permanece sempre em modo escuro independentemente do navegador ou do sistema operativo.',
    'Permite utilizar no PC os botões móveis do rodapé.',
    'Demonstração que altera o design através de functions.php. Consulte o ficheiro na pasta do skin. É apenas um exemplo sem utilidade prática.',
    'm-tomato, um skin Cocoon criado por marine.',
    'O tema “sora” criado por marine.',
    'Demonstração que altera o design através de option.csv. Consulte o ficheiro na pasta do skin. É apenas um exemplo sem utilidade prática.',
    'Demonstração que altera o design através de option.json. Consulte o ficheiro na pasta do skin. É apenas um exemplo sem utilidade prática.',
    'O skin original utilizado em “Katteni Lightning!”.',
    'Um skin acolhedor que combina charme adulto com um aspeto inteligente.',
    'Um design suave, confortável e simples que interpreta de forma leve o site Mirumemo.',
    'Um skin descontraído inspirado na escrita manual analógica.',
    'Um skin Cocoon para o editor de blocos com cores principais e suporte para skins escuros.',
    'Um design cinzento simples cujo visual principal ocupa toda a altura do ecrã na página inicial.',
    'Um skin conceptual de futebol baseado em Samurai Blue.',
    'Um skin baseado em Samurai Blue, modelo tradicional Kachi-iro.',
    'Skin simples de uma coluna para sites centrados em texto. Barras laterais e áreas semelhantes não estão disponíveis.',
    'Um skin inicial vazio para criar os seus próprios skins.',
    'Um skin atualmente em testes.',
    'Um bonito skin em tons pastel.',
    'Um skin colorido em estilo pastel.',
    'Um skin monocromático.',
    'A principal característica é poder fixar um botão de afiliado no rodapé.',
    'Concebido com a imagem de uma mulher a sair com um casaco de cor primaveril. Ideal para moda e conteúdos dirigidos a mulheres.',
    'Adiciona um movimento suave de aparecimento ao skin escolhido.',
    'Um skin para blogues de viagens, concebido para valorizar fotografias de viagem.',
    'Um design suave inspirado nas quatro estações.',
    'Um skin com o estilo de um determinado tema WordPress gratuito.',
    'Concebido para continuar agradável todos os dias. Alterar a cor ou o fundo renova o ambiente como trocar as cortinas de uma divisão.',
    'Um skin escuro baseado no azul Ruri vivo com tonalidade violeta.',
    'Um skin com design neumórfico tridimensional. A cor e o raio dos cantos são alterados em Aparência → Personalizar → Neumorphism.',
    'As cores do fundo mudam lentamente; podem ser escolhidas quatro cores.',
    'Um skin escuro baseado no carmesim Enji profundo e brilhante.',
    'Um skin simples, discreto e compatível com o modo escuro. Segue a definição do sistema operativo ou do navegador.',
    'Um skin escuro baseado em Kamonoha, um azul-esverdeado discreto inspirado nas penas da cabeça do pato-real.',
  ],
  zh_CN: [
    '弱化 Cocoon 的典型风格，并在部分区域加入以蓝色和粉色为主的渐变效果。',
    '以 Hirosite 制作的 Simple-Darkmode 皮肤为基础，无论浏览器或操作系统如何设置，都始终使用深色模式。',
    '在电脑端也可使用页脚移动按钮。',
    '通过修改 functions.php 改变主题设计的演示。详情请参阅皮肤文件夹中的 functions.php。本内容仅为示例，不具备实用性。',
    'marine 推出的 Cocoon 皮肤 m-tomato。',
    'marine 推出的主题“sora”。',
    '通过修改 option.csv 改变主题设计的演示。详情请参阅皮肤文件夹中的 option.csv。本内容仅为示例，不具备实用性。',
    '通过修改 option.json 改变主题设计的演示。详情请参阅皮肤文件夹中的 option.json。本内容仅为示例，不具备实用性。',
    '“Katteni Lightning!”使用的原创皮肤。',
    '兼具成熟可爱与知性气质的舒适皮肤。',
    '柔和、舒适且简洁的设计，以轻盈的方式呈现 Mirumemo 网站的风格。',
    '以模拟手写风格为灵感、略带随性感的皮肤。',
    '面向区块编辑器的 Cocoon 皮肤，支持关键色设置和深色皮肤。',
    '简洁的灰色系设计，首页主视觉占满整个屏幕高度。',
    '以 Samurai Blue 为基调的足球概念皮肤。',
    '以传统胜色款 Samurai Blue 为基调的皮肤。',
    '适合文字类网站的简洁单栏皮肤。由于是单栏布局，无法使用侧边栏等区域。',
    '用于自行制作皮肤的空白模板皮肤。',
    '目前正在测试的皮肤。',
    '可爱的柔和色皮肤。',
    '色彩丰富的柔和色调皮肤。',
    '以黑白色调为主的皮肤。',
    '最大的特点是可以在页脚固定显示联盟营销按钮。',
    '以穿着春色外套出门的女性为设计意象，适合时尚及女性向内容。',
    '为喜欢的皮肤添加柔和的淡入动画。',
    '专为旅行博客设计，并对旅行照片的显示效果进行了优化。',
    '以四季为主题、追求柔和感的设计。',
    '风格类似某款免费 WordPress 主题的皮肤。',
    '以每天使用也不会厌倦为目标。更换皮肤颜色或背景图片，即使布局不变，也能像更换房间窗帘一样改变氛围。',
    '以略带紫色的鲜艳琉璃蓝为关键色的深色皮肤。',
    '具有立体新拟态设计的皮肤。可在“外观 → 自定义 → Neumorphism”中修改整体颜色和圆角。',
    '背景颜色会缓慢变化，可选择四种颜色。',
    '以深沉而富有光泽的胭脂红为关键色的深色皮肤。',
    '装饰简洁并支持深色模式的皮肤。操作系统或浏览器设为深色模式时会自动使用深色显示。',
    '以源自绿头鸭头部羽毛的暗蓝绿色“鸭羽色”为关键色的深色皮肤。',
  ],
  zh_TW: [
    '淡化 Cocoon 的典型風格，並在部分區域加入以藍色和粉紅色為主的漸層效果。',
    '以 Hirosite 製作的 Simple-Darkmode 外觀為基礎，無論瀏覽器或作業系統如何設定，都會始終使用深色模式。',
    '在電腦版也能使用頁尾行動版按鈕。',
    '透過修改 functions.php 改變佈景主題設計的示範。詳情請參閱外觀資料夾中的 functions.php。本內容僅供示範，沒有實用性。',
    'marine 推出的 Cocoon 外觀 m-tomato。',
    'marine 推出的佈景主題「sora」。',
    '透過修改 option.csv 改變佈景主題設計的示範。詳情請參閱外觀資料夾中的 option.csv。本內容僅供示範，沒有實用性。',
    '透過修改 option.json 改變佈景主題設計的示範。詳情請參閱外觀資料夾中的 option.json。本內容僅供示範，沒有實用性。',
    '「Katteni Lightning!」所使用的原創外觀。',
    '兼具成熟可愛與知性氣質的舒適外觀。',
    '柔和、舒適且簡潔的設計，以輕盈的方式呈現 Mirumemo 網站的風格。',
    '以類比手寫風格為靈感、略帶隨性感的外觀。',
    '供區塊編輯器使用的 Cocoon 外觀，支援主色設定和深色外觀。',
    '簡潔的灰色系設計，首頁主視覺會填滿整個畫面高度。',
    '以 Samurai Blue 為基調的足球概念外觀。',
    '以傳統勝色款 Samurai Blue 為基調的外觀。',
    '適合文字型網站的簡潔單欄外觀。由於是單欄版面配置，無法使用側邊欄等區域。',
    '用來自行製作外觀的空白範本。',
    '目前正在測試的外觀。',
    '可愛的粉彩外觀。',
    '色彩豐富的粉彩風格外觀。',
    '以黑白色調為主的外觀。',
    '最大的特色是可以在頁尾固定顯示聯盟行銷按鈕。',
    '以穿著春色外套出門的女性為設計意象，適合時尚及女性取向內容。',
    '為喜愛的外觀加入柔和的淡入動畫。',
    '專為旅遊部落格設計，並針對旅遊照片的顯示效果進行最佳化。',
    '以四季為主題、追求柔和感的設計。',
    '風格類似某款免費 WordPress 佈景主題的外觀。',
    '以每天使用也不會厭倦為目標。更換外觀色彩或背景圖片，即使版面不變，也能像更換房間窗簾一樣改變氣氛。',
    '以略帶紫色的鮮豔琉璃藍為主色的深色外觀。',
    '具有立體新擬態設計的外觀。可在「外觀 → 自訂 → Neumorphism」中修改整體色彩和圓角。',
    '背景色彩會緩慢變化，可選擇四種色彩。',
    '以深沉而富有光澤的臙脂紅為主色的深色外觀。',
    '裝飾簡潔並支援深色模式的外觀。作業系統或瀏覽器設為深色模式時會自動使用深色顯示。',
    '以源自綠頭鴨頭部羽毛的暗藍綠色「鴨羽色」為主色的深色外觀。',
  ],
};

const patternData = {
  en_US: {
    colors: { 'オレンジ': 'orange', 'グリーン': 'green', 'パープル': 'purple', 'ピンク': 'pink', '水色': 'light blue', 'シアン': 'cyan', 'ライム': 'lime', '明るい緑色': 'bright green', '濃いオレンジ色': 'dark orange', '濃紫色': 'dark purple', '琥珀色': 'amber', '紫色': 'purple', '緑色': 'green', '群青色': 'ultramarine', '茶色': 'brown', '赤色': 'red', '青色': 'blue', '鴨の羽色': 'Kamonoha blue-green', '黄色': 'yellow', '紺': 'navy blue', '緑': 'green', '赤': 'red', '青': 'blue', '黒': 'black' },
    subjects: { 'ジューシーな白桃': 'a juicy white peach', 'ブドウ': 'grapes', 'マンゴー': 'a mango', 'ライム': 'a lime', '土壌': 'soil', '夕焼け': 'a sunset', '月の表面': 'the surface of the moon', '空・海・葉': 'the sky, sea, and leaves', '青空': 'a blue sky' },
    templates: [ 'An easy-to-use skin with gentle colors based on {0}.', 'A Cocoon design skin based on {0}.', 'A simple blog skin ({0}).', 'A simple, easy-to-use skin based on {0}.', 'A simple skin based on {0}.', 'A skin inspired by {0}.', 'A softly colored skin based on the traditional Japanese color “{0}”.', 'A simple skin based on the traditional Japanese color {0} ({1}).', 'An Alice color variation: a cute pastel skin based on {0}.', 'A skin with a dark {0} base and a dotted background.' ],
  },
  de_DE: {
    colors: { 'オレンジ': 'Orange', 'グリーン': 'Grün', 'パープル': 'Violett', 'ピンク': 'Rosa', '水色': 'Hellblau', 'シアン': 'Cyan', 'ライム': 'Limette', '明るい緑色': 'Hellgrün', '濃いオレンジ色': 'Dunkelorange', '濃紫色': 'Dunkelviolett', '琥珀色': 'Bernstein', '紫色': 'Violett', '緑色': 'Grün', '群青色': 'Ultramarin', '茶色': 'Braun', '赤色': 'Rot', '青色': 'Blau', '鴨の羽色': 'Kamonoha-Blaugrün', '黄色': 'Gelb', '紺': 'Marineblau', '緑': 'Grün', '赤': 'Rot', '青': 'Blau', '黒': 'Schwarz' },
    subjects: { 'ジューシーな白桃': 'einem saftigen weißen Pfirsich', 'ブドウ': 'Weintrauben', 'マンゴー': 'einer Mango', 'ライム': 'einer Limette', '土壌': 'Erde', '夕焼け': 'einem Sonnenuntergang', '月の表面': 'der Mondoberfläche', '空・海・葉': 'Himmel, Meer und Blättern', '青空': 'einem blauen Himmel' },
    templates: [ 'Ein benutzerfreundlicher Skin mit sanften Farben auf Basis von {0}.', 'Ein Cocoon-Design-Skin auf Grundlage von {0}.', 'Ein einfacher Blog-Skin ({0}).', 'Ein einfacher, benutzerfreundlicher Skin auf Grundlage von {0}.', 'Ein einfacher Skin auf Grundlage von {0}.', 'Ein Skin, inspiriert von {0}.', 'Ein sanft gefärbter Skin auf Basis der traditionellen japanischen Farbe „{0}“.', 'Ein einfacher Skin auf Basis der traditionellen japanischen Farbe {0} ({1}).', 'Eine Alice-Farbvariante: ein hübscher Pastell-Skin auf Grundlage von {0}.', 'Ein Skin mit dunkler {0}-Basis und gepunktetem Hintergrund.' ],
  },
  es_ES: {
    colors: { 'オレンジ': 'naranja', 'グリーン': 'verde', 'パープル': 'morado', 'ピンク': 'rosa', '水色': 'azul claro', 'シアン': 'cian', 'ライム': 'lima', '明るい緑色': 'verde claro', '濃いオレンジ色': 'naranja oscuro', '濃紫色': 'morado oscuro', '琥珀色': 'ámbar', '紫色': 'morado', '緑色': 'verde', '群青色': 'ultramar', '茶色': 'marrón', '赤色': 'rojo', '青色': 'azul', '鴨の羽色': 'verde azulado Kamonoha', '黄色': 'amarillo', '紺': 'azul marino', '緑': 'verde', '赤': 'rojo', '青': 'azul', '黒': 'negro' },
    subjects: { 'ジューシーな白桃': 'un melocotón blanco y jugoso', 'ブドウ': 'las uvas', 'マンゴー': 'un mango', 'ライム': 'una lima', '土壌': 'la tierra', '夕焼け': 'una puesta de sol', '月の表面': 'la superficie lunar', '空・海・葉': 'el cielo, el mar y las hojas', '青空': 'un cielo azul' },
    templates: [ 'Skin fácil de usar y de colores suaves basado en {0}.', 'Skin de diseño para Cocoon basado en {0}.', 'Skin de blog sencillo ({0}).', 'Skin sencillo y fácil de usar basado en {0}.', 'Skin sencillo basado en {0}.', 'Skin inspirado en {0}.', 'Skin de color suave basado en el color japonés tradicional «{0}».', 'Skin sencillo basado en el color japonés tradicional {0} ({1}).', 'Una variante de color de Alice: bonito skin pastel basado en {0}.', 'Skin con base {0} oscura y fondo de puntos.' ],
  },
  fr_FR: {
    colors: { 'オレンジ': 'orange', 'グリーン': 'vert', 'パープル': 'violet', 'ピンク': 'rose', '水色': 'bleu clair', 'シアン': 'cyan', 'ライム': 'citron vert', '明るい緑色': 'vert clair', '濃いオレンジ色': 'orange foncé', '濃紫色': 'violet foncé', '琥珀色': 'ambre', '紫色': 'violet', '緑色': 'vert', '群青色': 'outremer', '茶色': 'marron', '赤色': 'rouge', '青色': 'bleu', '鴨の羽色': 'bleu-vert Kamonoha', '黄色': 'jaune', '紺': 'bleu marine', '緑': 'vert', '赤': 'rouge', '青': 'bleu', '黒': 'noir' },
    subjects: { 'ジューシーな白桃': 'une pêche blanche juteuse', 'ブドウ': 'le raisin', 'マンゴー': 'une mangue', 'ライム': 'un citron vert', '土壌': 'la terre', '夕焼け': 'un coucher de soleil', '月の表面': 'la surface de la Lune', '空・海・葉': 'le ciel, la mer et les feuilles', '青空': 'un ciel bleu' },
    templates: [ 'Skin facile à utiliser aux couleurs douces basé sur {0}.', 'Skin de design pour Cocoon basé sur {0}.', 'Skin de blog simple ({0}).', 'Skin simple et facile à utiliser basé sur {0}.', 'Skin simple basé sur {0}.', 'Skin inspiré de {0}.', 'Skin aux teintes douces basé sur la couleur japonaise traditionnelle « {0} ».', 'Skin simple basé sur la couleur japonaise traditionnelle {0} ({1}).', 'Une variante de couleur d’Alice : joli skin pastel basé sur {0}.', 'Skin à base {0} foncée avec un arrière-plan à pois.' ],
  },
  ko_KR: {
    colors: { 'オレンジ': '주황색', 'グリーン': '초록색', 'パープル': '보라색', 'ピンク': '분홍색', '水色': '하늘색', 'シアン': '청록색', 'ライム': '라임색', '明るい緑色': '밝은 초록색', '濃いオレンジ色': '진한 주황색', '濃紫色': '진한 보라색', '琥珀色': '호박색', '紫色': '보라색', '緑色': '초록색', '群青色': '군청색', '茶色': '갈색', '赤色': '빨간색', '青色': '파란색', '鴨の羽色': 'Kamonoha 청록색', '黄色': '노란색', '紺': '남색', '緑': '초록색', '赤': '빨간색', '青': '파란색', '黒': '검은색' },
    subjects: { 'ジューシーな白桃': '과즙이 풍부한 백도', 'ブドウ': '포도', 'マンゴー': '망고', 'ライム': '라임', '土壌': '흙', '夕焼け': '노을', '月の表面': '달 표면', '空・海・葉': '하늘·바다·나뭇잎', '青空': '푸른 하늘' },
    templates: [ '{0}을 바탕으로 부드러운 색조를 사용한 편리한 스킨입니다.', '{0}을 바탕으로 한 Cocoon 디자인 스킨입니다.', '심플한 블로그 스킨({0})입니다.', '{0}을 바탕으로 한 심플하고 사용하기 쉬운 스킨입니다.', '{0}을 바탕으로 한 심플 스킨입니다.', '{0}에서 영감을 받은 스킨입니다.', '일본 전통색 “{0}”을 바탕으로 은은하게 물든 스킨입니다.', '일본 전통색 {0}({1})을 바탕으로 한 심플 스킨입니다.', 'Alice 색상 변형으로, {0}을 바탕으로 한 귀여운 파스텔 스킨입니다.', '어두운 {0} 바탕에 도트 무늬 배경을 적용한 스킨입니다.' ],
  },
  pt_PT: {
    colors: { 'オレンジ': 'laranja', 'グリーン': 'verde', 'パープル': 'roxo', 'ピンク': 'cor-de-rosa', '水色': 'azul-claro', 'シアン': 'ciano', 'ライム': 'lima', '明るい緑色': 'verde-claro', '濃いオレンジ色': 'laranja-escuro', '濃紫色': 'roxo-escuro', '琥珀色': 'âmbar', '紫色': 'roxo', '緑色': 'verde', '群青色': 'ultramarino', '茶色': 'castanho', '赤色': 'vermelho', '青色': 'azul', '鴨の羽色': 'azul-esverdeado Kamonoha', '黄色': 'amarelo', '紺': 'azul-marinho', '緑': 'verde', '赤': 'vermelho', '青': 'azul', '黒': 'preto' },
    subjects: { 'ジューシーな白桃': 'um pêssego branco sumarento', 'ブドウ': 'uvas', 'マンゴー': 'uma manga', 'ライム': 'uma lima', '土壌': 'terra', '夕焼け': 'um pôr do sol', '月の表面': 'a superfície da Lua', '空・海・葉': 'o céu, o mar e as folhas', '青空': 'um céu azul' },
    templates: [ 'Skin fácil de utilizar, com cores suaves, baseado em {0}.', 'Skin de design para Cocoon baseado em {0}.', 'Skin simples para blogue ({0}).', 'Skin simples e fácil de utilizar baseado em {0}.', 'Skin simples baseado em {0}.', 'Skin inspirado em {0}.', 'Skin suavemente colorido baseado na cor japonesa tradicional “{0}”.', 'Skin simples baseado na cor japonesa tradicional {0} ({1}).', 'Uma variação de cor de Alice: bonito skin pastel baseado em {0}.', 'Skin com base {0} escura e fundo pontilhado.' ],
  },
  zh_CN: {
    colors: { 'オレンジ': '橙色', 'グリーン': '绿色', 'パープル': '紫色', 'ピンク': '粉色', '水色': '浅蓝色', 'シアン': '青色', 'ライム': '青柠色', '明るい緑色': '亮绿色', '濃いオレンジ色': '深橙色', '濃紫色': '深紫色', '琥珀色': '琥珀色', '紫色': '紫色', '緑色': '绿色', '群青色': '群青色', '茶色': '棕色', '赤色': '红色', '青色': '蓝色', '鴨の羽色': '鸭羽青绿色', '黄色': '黄色', '紺': '藏青色', '緑': '绿色', '赤': '红色', '青': '蓝色', '黒': '黑色' },
    subjects: { 'ジューシーな白桃': '多汁的白桃', 'ブドウ': '葡萄', 'マンゴー': '芒果', 'ライム': '青柠', '土壌': '土壤', '夕焼け': '晚霞', '月の表面': '月球表面', '空・海・葉': '天空、海洋与树叶', '青空': '蓝天' },
    templates: [ '以 {0} 为基础、色调柔和且易于使用的皮肤。', '以 {0} 为基调的 Cocoon 设计皮肤。', '简洁的博客皮肤（{0}）。', '以 {0} 为基础、简洁且易于使用的皮肤。', '以 {0} 为基调的简洁皮肤。', '灵感来自{0}的皮肤。', '以日本传统色“{0}”为基础、带有柔和色彩的皮肤。', '以日本传统色 {0}（{1}）为基础的简洁皮肤。', 'Alice 的配色变体，以{0}为基调的可爱柔和色皮肤。', '以暗{0}为基调并使用圆点背景的皮肤。' ],
  },
  zh_TW: {
    colors: { 'オレンジ': '橙色', 'グリーン': '綠色', 'パープル': '紫色', 'ピンク': '粉紅色', '水色': '淺藍色', 'シアン': '青色', 'ライム': '萊姆色', '明るい緑色': '亮綠色', '濃いオレンジ色': '深橙色', '濃紫色': '深紫色', '琥珀色': '琥珀色', '紫色': '紫色', '緑色': '綠色', '群青色': '群青色', '茶色': '棕色', '赤色': '紅色', '青色': '藍色', '鴨の羽色': '鴨羽藍綠色', '黄色': '黃色', '紺': '藏青色', '緑': '綠色', '赤': '紅色', '青': '藍色', '黒': '黑色' },
    subjects: { 'ジューシーな白桃': '多汁的白桃', 'ブドウ': '葡萄', 'マンゴー': '芒果', 'ライム': '萊姆', '土壌': '土壤', '夕焼け': '晚霞', '月の表面': '月球表面', '空・海・葉': '天空、海洋與樹葉', '青空': '藍天' },
    templates: [ '以 {0} 為基礎、色調柔和且易於使用的外觀。', '以 {0} 為基調的 Cocoon 設計外觀。', '簡潔的部落格外觀（{0}）。', '以 {0} 為基礎、簡潔且易於使用的外觀。', '以 {0} 為基調的簡潔外觀。', '靈感來自{0}的外觀。', '以日本傳統色「{0}」為基礎、帶有柔和色彩的外觀。', '以日本傳統色 {0}（{1}）為基礎的簡潔外觀。', 'Alice 的配色變化，以{0}為基調的可愛粉彩外觀。', '以暗{0}為基調並使用圓點背景的外觀。' ],
  },
};

//日本独自の色名や複合色を、説明文に残らない表記へ変換する
const specialTerms = {
  en_US: {
    'ユニコーンカラー': 'unicorn colors', '白×ブルー': 'white and blue',
    '御召茶': 'Omeshi-cha', '海松茶': 'Miru-cha', '海老茶': 'Ebi-cha', '褐色': 'Kachi-iro',
    '山吹色': 'Yamabuki yellow', '常磐色': 'Tokiwa green', '緋色': 'Hi scarlet', '藍鉄': 'Aitetsu blue', '躑躅色': 'Tsutsuji pink', '青色': 'Ao blue',
  },
  de_DE: {
    'ユニコーンカラー': 'Einhornfarben', '白×ブルー': 'Weiß und Blau',
    '御召茶': 'Omeshi-cha', '海松茶': 'Miru-cha', '海老茶': 'Ebi-cha', '褐色': 'Kachi-iro',
    '山吹色': 'Yamabuki-Gelb', '常磐色': 'Tokiwa-Grün', '緋色': 'Hi-Scharlach', '藍鉄': 'Aitetsu-Blau', '躑躅色': 'Tsutsuji-Rosa', '青色': 'Ao-Blau',
  },
  es_ES: {
    'ユニコーンカラー': 'colores de unicornio', '白×ブルー': 'blanco y azul',
    '御召茶': 'Omeshi-cha', '海松茶': 'Miru-cha', '海老茶': 'Ebi-cha', '褐色': 'Kachi-iro',
    '山吹色': 'amarillo Yamabuki', '常磐色': 'verde Tokiwa', '緋色': 'escarlata Hi', '藍鉄': 'azul Aitetsu', '躑躅色': 'rosa Tsutsuji', '青色': 'azul Ao',
  },
  fr_FR: {
    'ユニコーンカラー': 'couleurs licorne', '白×ブルー': 'blanc et bleu',
    '御召茶': 'Omeshi-cha', '海松茶': 'Miru-cha', '海老茶': 'Ebi-cha', '褐色': 'Kachi-iro',
    '山吹色': 'jaune Yamabuki', '常磐色': 'vert Tokiwa', '緋色': 'écarlate Hi', '藍鉄': 'bleu Aitetsu', '躑躅色': 'rose Tsutsuji', '青色': 'bleu Ao',
  },
  ko_KR: {
    'ユニコーンカラー': '유니콘 색상', '白×ブルー': '흰색과 파란색',
    '御召茶': '오메시차', '海松茶': '미루차', '海老茶': '에비차', '褐色': '카치이로',
    '山吹色': '야마부키색', '常磐色': '도키와색', '緋色': '히이로', '藍鉄': '아이테쓰색', '躑躅色': '진달래색', '青色': '청색',
  },
  pt_PT: {
    'ユニコーンカラー': 'cores de unicórnio', '白×ブルー': 'branco e azul',
    '御召茶': 'Omeshi-cha', '海松茶': 'Miru-cha', '海老茶': 'Ebi-cha', '褐色': 'Kachi-iro',
    '山吹色': 'amarelo Yamabuki', '常磐色': 'verde Tokiwa', '緋色': 'escarlate Hi', '藍鉄': 'azul Aitetsu', '躑躅色': 'rosa Tsutsuji', '青色': 'azul Ao',
  },
  zh_CN: {
    'ユニコーンカラー': '独角兽配色', '白×ブルー': '白色与蓝色',
    '御召茶': '御召茶', '海松茶': '海松茶', '海老茶': '海老茶', '褐色': '褐色',
    '山吹色': '山吹黄', '常磐色': '常磐绿', '緋色': '绯红色', '藍鉄': '蓝铁色', '躑躅色': '杜鹃粉', '青色': '蓝色',
  },
  zh_TW: {
    'ユニコーンカラー': '獨角獸配色', '白×ブルー': '白色與藍色',
    '御召茶': '御召茶', '海松茶': '海松茶', '海老茶': '海老茶', '褐色': '褐色',
    '山吹色': '山吹黃', '常磐色': '常磐綠', '緋色': '緋紅色', '藍鉄': '藍鐵色', '躑躅色': '杜鵑粉', '青色': '藍色',
  },
};

//テンプレート内の連番プレースホルダーを置換する
const format = ( template, ...values ) =>
  values.reduce(
    ( result, value, index ) => result.split( `{${ index }}` ).join( value ),
    template
  );

//定型的な説明文をロケール別のテンプレートで翻訳する
const translatePattern = ( locale, source ) => {
  const data = patternData[ locale ];
  let match;

  if ( ( match = source.match( /^(#[0-9a-f]+)をベースにした優しい色合いで使いやすいスキン$/i ) ) ) {
    return format( data.templates[ 0 ], match[ 1 ] );
  }
  if ( ( match = source.match( /^(.+)を基調とした「cocoon」用のデザインスキン$/u ) ) ) {
    return format( data.templates[ 1 ], data.colors[ match[ 1 ] ] );
  }
  if ( ( match = source.match( /^シンプルなブログスキン（(.+)）$/u ) ) ) {
    return format( data.templates[ 2 ], data.colors[ match[ 1 ] ] || data.colors[ match[ 1 ].replace( /色$/u, '' ) ] );
  }
  if ( ( match = source.match( /^(.+)をベースにしたシンプルで使いやすいスキン$/u ) ) ) {
    return format( data.templates[ 3 ], data.colors[ match[ 1 ] ] );
  }
  if ( ( match = source.match( /^(.+)を基調としたシンプルなスキン$/u ) ) ) {
    return format( data.templates[ 4 ], data.colors[ match[ 1 ] ] );
  }
  if ( ( match = source.match( /^(.+)をベースにしたシンプルなスキン$/u ) ) ) {
    return format( data.templates[ 4 ], data.colors[ match[ 1 ] ] );
  }
  if ( ( match = source.match( /^(.+)をイメージしたスキンです。$/u ) ) ) {
    return format( data.templates[ 5 ], data.subjects[ match[ 1 ] ] );
  }
  if ( ( match = source.match( /^和色「(.+)」をベースにしたふわりと色づいたスキン$/u ) ) ) {
    const color = match[ 1 ].replace( /\(.+\)/u, '' );
    return format( data.templates[ 6 ], specialTerms[ locale ][ color ] );
  }
  if ( ( match = source.match( /^日本の伝統色・(.+)（(#[0-9a-f]+)）をベースとしたシンプルスキン。$/iu ) ) ) {
    return format( data.templates[ 7 ], specialTerms[ locale ][ match[ 1 ] ], match[ 2 ] );
  }
  if ( ( match = source.match( /^アリスの色違い。(.+)を基調とした、パステルカラーの可愛らしいスキンです。$/u ) ) ) {
    return format( data.templates[ 8 ], data.colors[ match[ 1 ] ] || specialTerms[ locale ][ match[ 1 ] ] );
  }
  if ( ( match = source.match( /^暗めの(.+)をベースにドット柄を背景にしたスキン$/u ) ) ) {
    return format( data.templates[ 9 ], data.colors[ match[ 1 ] ] );
  }

  return null;
};

//定型外の説明文をロケール別配列から取得する
const translateExact = ( locale, source ) => {
  const index = Object.values( exactSources ).indexOf( source );
  return index === -1 ? null : exactTranslations[ locale ][ index ];
};

//全スキンメタデータの翻訳辞書を組み立てる
const getTranslations = ( locale ) => {
  if ( !patternData[ locale ] || !exactTranslations[ locale ] ) {
    throw new Error( `未対応のロケールです: ${ locale }` );
  }

  const translatedDescriptions = descriptionKeys.map( ( source ) => {
    const translated = translatePattern( locale, source ) || translateExact( locale, source );

    if ( !translated ) {
      throw new Error( `${ locale }のスキン説明文に翻訳がありません: ${ source }` );
    }
    return translated;
  } );

  //全ロケール共通名へ、共有キーだけロケール固有の既存表記を上書きする
  const localizedNames = nameKeys.map( ( source, index ) =>
    translatedNameOverrides[ locale ][ source ] || translatedNames[ index ]
  );
  const values = [ ...localizedNames, ...translatedDescriptions ];
  if ( values.length !== keys.length || nameKeys.length !== translatedNames.length ) {
    throw new Error( `${ locale }のスキンメタデータ翻訳件数が一致しません。` );
  }

  return Object.fromEntries( keys.map( ( key, index ) => [ key, values[ index ] ] ) );
};

getTranslations.keys = keys;
module.exports = getTranslations;
