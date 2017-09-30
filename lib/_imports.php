<?php //ファイル読み込み用

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
require_once 'php-html-css-js-minifier.php'; //HTML・CSS・JavaScript縮小化ライブラリ
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
require_once 'original-pages/admin-funcs.php';       //管理画面設定関数
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