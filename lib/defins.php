<?php //定数をまとめて定義

//テーマ名
define('THEME_NAME', 'cocoon');
//テーマ名（最初の一文字だけ大文字）
define('THEME_NAME_CAMEL', ucfirst(THEME_NAME));
//ウィジェット名プレフィックス
define('WIDGET_NAME_PREFIX', '['.substr(THEME_NAME_CAMEL, 0, 1).'] '); //ex.[C]
//トップレベルオリジナル設定名
define('SETTING_NAME_TOP', THEME_NAME_CAMEL.__( ' 設定', THEME_NAME ));

//ウィジェットのエントリータイプ
define('ET_DEFAULT',        'defalt');
define('ET_LARGE_THUMB',    'large_thumb');
define('ET_LARGE_THUMB_ON', 'large_thumb_on');
//ウィジェットモードデフォルト
define('WM_DEFAULT', 'all');

//アドセンスID名
define('DATA_AD_CLIENT', 'data-ad-client');
define('DATA_AD_SLOT',   'data-ad-slot');
//レスポンシブ広告の表示フォーマット
define('DATA_AD_FORMAT_AUTO', 'auto'); //おまかせ
define('DATA_AD_FORMAT_RECTANGLE', 'rectangle'); //正方形に近い長方形
define('DATA_AD_FORMAT_HORIZONTAL', 'horizontal'); //横長
define('DATA_AD_FORMAT_VERTICAL', 'vertical'); //縦長
//Googleで定義されていないフォーマットはDATA_無し
define('AD_FORMAT_SINGLE_RECTANGLE', 'single-rectangle'); //シングルレクタングル
define('AD_FORMAT_DABBLE_RECTANGLE', 'dabble-rectangle'); //ダブルレクタングル

//管理設定画面の隠しフィールド名
define('HIDDEN_FIELD_NAME', THEME_NAME.'_submit_hidden');
define('SELECT_INDEX_NAME', 'select_index');

//入力ボックスの横幅
define('DEFAULT_INPUT_COLS', 60);
//入力ボックスの縦幅
define('DEFAULT_INPUT_ROWS', 10);

//SNSシェアオプション
define('SS_TOP', 'ss-top');
define('SS_BOTTOM', 'ss-bottom');

//URLの正規表現
define('URL_REG', '/(https?|ftp)(:\/\/[-_.!~*\'()a-zA-Z0-9;\/?:\@&=+\$,%#]+)/');

//Font AwesomeのCDN
define('FONT_AWESOME_CDN_URL', 'https://max'.'cdn.boot'.'strapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css');


require_once ABSPATH.'wp-admin/includes/file.php';//WP_Filesystemの使用
require_once 'settings.php';   //Wordpressの設定
require_once 'utils.php';      //ユーティリティー関数
require_once 'html-forms.php'; //HTMLフォーム生成関数
require_once 'ad.php';         //広告関係の設定
require_once 'sns.php';        //SNS関係の設定
require_once 'sns-share.php';  //SNSシェア関数
require_once 'sns-follow.php'; //SNSフォロー関数
require_once 'open-graph.php'; //OGP取得ライブラリ
require_once 'punycode.php'; //ピュニコードライブラリ
require_once 'original-pages/header-funcs.php';     //ヘッダー設定関数
require_once 'original-pages/ads-funcs.php';        //広告設定関数
require_once 'original-pages/title-funcs.php';      //タイトル設定関数
require_once 'original-pages/seo-funcs.php';        //SEO設定関数
require_once 'original-pages/analytics-funcs.php';  //アクセス解析設定関数
require_once 'original-pages/sns-share-funcs.php';  //SNSシェア設定関数
require_once 'original-pages/sns-follow-funcs.php'; //SNSフォロー設定関数
require_once 'original-pages/code-funcs.php';       //コード設定関数
require_once 'original-pages/image-funcs.php';      //画像設定関数
require_once 'original-pages/ogp-funcs.php';        //OGP設定関数
require_once 'original-pages/blogcard-in-funcs.php';  //内部ブログカード設定関数
require_once 'original-pages/blogcard-out-funcs.php'; //外部ブログカード設定関数
require_once 'custom-fields/seo-field.php'; //SEOのページ設定
require_once 'custom-fields/ad-field.php';  //広告のページ設定
require_once 'seo.php';      //SEO関数
require_once 'ogp.php';      //OGP関数
require_once 'blogcard-in.php';  //内部ブログカード関数
require_once 'blogcard-out.php'; //外部ブログカード関数
require_once 'scripts.php'; //スクリプト関係の関数
require_once 'image.php';   //画像関係の設定
require_once 'widget-areas.php'; //ウィジェットエリアの指定
require_once 'widget.php'; //ウィジェット操作関数
require_once 'original-menu.php'; //オリジナルメニューによる設定項目
require_once 'additional-classes.php'; //スタイリング用の追加クラス関数
require_once 'auto-post-thumbnail.php'; //アイキャッチ自動追加関数


//新着記事ウィジェット
require_once 'widgets/new-entries.php';
//最近のコメントウィジェット
require_once 'widgets/recent-comments.php';