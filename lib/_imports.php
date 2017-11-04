<?php //ファイル読み込み用

require_once ABSPATH.'wp-admin/includes/file.php';//WP_Filesystemの使用
require_once 'language.php';   //マルチ言語設定
require_once 'settings.php';   //Wordpressの設定
require_once 'utils.php';      //ユーティリティー関数
require_once 'html-forms.php'; //HTMLフォーム生成関数
require_once 'html-tooltips.php'; //HTMLツールチップ生成関数
require_once 'ad.php';         //広告関係の設定
require_once 'sns.php';        //SNS関係の設定
require_once 'sns-share.php';  //SNSシェア関数
require_once 'sns-follow.php'; //SNSフォロー関数
require_once 'open-graph.php'; //OGP取得ライブラリ
require_once 'punycode.php';   //ピュニコードライブラリ
require_once 'medias.php';      //メディアライブラリ
require_once 'php-html-css-js-minifier.php'; //HTML・CSS・JavaScript縮小化ライブラリ
require_once 'page-settings/all-funcs.php';        //全体設定関数
require_once 'page-settings/header-funcs.php';     //ヘッダー設定関数
require_once 'page-settings/navi-funcs.php';       //グローバルナビ設定関数
require_once 'page-settings/ads-funcs.php';        //広告設定関数
require_once 'page-settings/title-funcs.php';      //タイトル設定関数
require_once 'page-settings/seo-funcs.php';        //SEO設定関数
require_once 'page-settings/analytics-funcs.php';  //アクセス解析設定関数
require_once 'page-settings/index-funcs.php';      //インデックス設定関数
require_once 'page-settings/single-funcs.php';     //投稿設定関数
require_once 'page-settings/page-funcs.php';       //固定ページ設定関数
require_once 'page-settings/content-funcs.php';    //本文設定関数
require_once 'page-settings/toc-funcs.php';        //目次設定関数
require_once 'page-settings/sns-share-funcs.php';  //SNSシェア設定関数
require_once 'page-settings/sns-follow-funcs.php'; //SNSフォロー設定関数
require_once 'page-settings/image-funcs.php';      //画像設定関数
require_once 'page-settings/ogp-funcs.php';        //OGP設定関数
require_once 'page-settings/blogcard-in-funcs.php';  //内部ブログカード設定関数
require_once 'page-settings/blogcard-out-funcs.php'; //外部ブログカード設定関数
require_once 'page-settings/code-funcs.php';       //コード設定関数
require_once 'page-settings/comment-funcs.php';    //コメント設定関数
require_once 'page-settings/appeal-funcs.php';     //アピールエリア設定関数
require_once 'page-settings/carousel-funcs.php';   //カルーセル設定関数
require_once 'page-settings/footer-funcs.php';     //フッター設定関数
require_once 'page-settings/buttons-funcs.php';    //ボタン設定関数
require_once 'page-settings/admin-funcs.php';      //管理画面設定関数
require_once 'page-settings/reset-funcs.php';      //リセット設定関数
require_once 'custom-fields/seo-field.php'; //SEOのページ設定
require_once 'custom-fields/ad-field.php';  //広告のページ設定
require_once 'custom-fields/page-field.php';//投稿・固定ページのページ設定
require_once 'custom-fields/update-field.php'; //アップデートのページ設定
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
//フォローボタンウィジェット
require_once 'widgets/sns-follow-buttons.php';


//require_once 'admin.php'; //管理者機能