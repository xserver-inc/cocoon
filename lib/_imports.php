<?php //ファイル読み込み用

require_once ABSPATH.'wp-admin/includes/file.php';//WP_Filesystemの使用
require_once 'language.php';   //マルチ言語設定
require_once 'utils.php';      //ユーティリティー関数
require_once 'html-forms.php'; //HTMLフォーム生成関数
require_once 'html-tooltips.php'; //HTMLツールチップ生成関数
require_once 'ad.php';         //広告関係の設定
require_once 'sns.php';        //SNS関係の設定
require_once 'sns-share.php';  //SNSシェア関数
require_once 'sns-follow.php'; //SNSフォロー関数
require_once 'open-graph.php'; //OGP取得ライブラリ
require_once 'punycode.php';   //ピュニコードライブラリ
require_once 'medias.php';     //メディアライブラリ
require_once 'links.php';      //本文リンクライブラリ
require_once 'category.php';   //カテゴリー関係の関数
require_once 'entry-card.php'; //エントリーカード関数
require_once 'amp.php';        //AMP関係の関数
require_once 'content.php';    //本文関係の関数
require_once 'php-html-css-js-minifier.php'; //HTML・CSS・JavaScript縮小化ライブラリ
require_once 'page-settings/all-funcs.php';        //全体設定関数
require_once 'page-settings/header-funcs.php';     //ヘッダー設定関数
require_once 'page-settings/navi-funcs.php';       //グローバルナビ設定関数
require_once 'page-settings/ads-funcs.php';        //広告設定関数
require_once 'page-settings/title-funcs.php';      //タイトル設定関数
require_once 'page-settings/seo-funcs.php';        //SEO設定関数
require_once 'page-settings/analytics-funcs.php';  //アクセス解析設定関数
require_once 'page-settings/column-funcs.php';     //カラム設定関数
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
require_once 'page-settings/notice-funcs.php';     //通知エリア設定関数
require_once 'page-settings/appeal-funcs.php';     //アピールエリア設定関数
require_once 'page-settings/carousel-funcs.php';   //カルーセル設定関数
require_once 'page-settings/footer-funcs.php';     //フッター設定関数
require_once 'page-settings/buttons-funcs.php';    //ボタン設定関数
require_once 'page-settings/mobile-buttons-funcs.php'; //モバイルボタン設定関数
require_once 'page-settings/404-funcs.php';        //404ページ設定関数
require_once 'page-settings/skin-funcs.php';       //スキン設定関数
require_once 'page-settings/amp-funcs.php';        //AMP設定関数
require_once 'page-settings/admin-funcs.php';      //管理画面設定関数
require_once 'page-settings/editor-funcs.php';     //エディター設定関数
require_once 'page-settings/others-funcs.php';     //その他設定関数
require_once 'page-settings/reset-funcs.php';      //リセット設定関数
require_once 'page-settings/about-funcs.php';      //テーマ情報設定関数
require_once 'db.php';  //データベース操作関数
require_once 'page-func-text/func-text-func.php';  //使いまわしテキスト関数
require_once 'page-backup/backup-func.php';  //バックアップ関数
require_once 'page-affiliate-tag/affiliate-tag-func.php';  //アフィリエイトタグ関数
require_once 'page-speech-balloon/speech-balloon-func.php';  //吹き出し関数
require_once 'page-speed-up/speed-up-func.php';  //高速化設定関数
require_once 'page-access/access-func.php';  //アクセス数統計
require_once 'page-item-ranking/item-ranking-func.php';  //ランキング
require_once 'custom-fields/seo-field.php'; //SEOのページ設定
require_once 'custom-fields/ad-field.php';  //広告のページ設定
require_once 'custom-fields/page-field.php';//投稿・固定ページのページ設定
require_once 'custom-fields/update-field.php'; //アップデートのページ設定
require_once 'custom-fields/redirect-field.php'; //アップデートのページ設定
require_once 'custom-fields/amp-field.php'; //AMPのページ設定
require_once 'custom-fields/custom-css-field.php'; //カスタムCSS設定
require_once 'custom-fields/custom-js-field.php';  //カスタJS設定
require_once 'seo.php';      //SEO関数
require_once 'ogp.php';      //OGP関数
require_once 'blogcard-in.php';  //内部ブログカード関数
require_once 'blogcard-out.php'; //外部ブログカード関数
require_once 'scripts.php'; //スクリプト関係の関数
require_once 'image.php';   //画像関係の設定
require_once 'toc.php';     //目次関数
require_once 'cache.php';   //キャッシュ処理
require_once 'widget-areas.php'; //ウィジェットエリアの指定
require_once 'widget.php'; //ウィジェット操作関数
require_once 'original-menu.php'; //オリジナルメニューによる設定項目
require_once 'additional-classes.php'; //スタイリング用の追加クラス関数
require_once 'auto-post-thumbnail.php'; //アイキャッチ自動追加関数
require_once 'ssl.php'; //SSL関係の処理
require_once 'shortcodes.php'; //ショートコード関係の処理
require_once 'html5.php'; //HTML5チェック関係
//フルパスを指定しないとうまくいかないファイル
require_once get_template_directory().'/lib/profile.php'; //プロフィール関係の処理
require_once get_template_directory().'/lib/admin.php'; //管理者機能（functions.phpで呼ばないと動作しないので）

//TinyMCE
if (is_admin()) {;
  require_once get_template_directory().'/lib/admin-tinymce-qtag.php'; //管理者用編集ボタン機能
  require_once 'tinymce/insert-html.php'; //HTML追加ボタン
  require_once 'tinymce/speech-balloons.php'; //吹き出し追加
  require_once 'tinymce/function-texts.php'; //テンプレート追加
  require_once 'tinymce/affiliate-tags.php'; //アフィリエイトタグ追加
  require_once 'tinymce/item-rankings.php'; //ランキングタグ追加
  require_once 'tinymce/html-tags.php'; //拡張タグ追加
  require_once 'tinymce/shortcodes.php'; //ショートコード追加

}
require_once get_template_directory().'/lib/settings.php';   //Wordpressの設定


//新着記事ウィジェット
require_once 'widgets/new-entries.php';
//人気記事ウィジェット
require_once 'widgets/popular-entries.php';
//最近のコメントウィジェット
require_once 'widgets/recent-comments.php';
//フォローボタンウィジェット
require_once 'widgets/sns-follow-buttons.php';
//PC用テキストウィジェット
require_once 'widgets/pc-text.php';
//PC用広告ウィジェット
require_once 'widgets/pc-ad.php';
//PC用ダブルレクタングル広告ウィジェット
//require_once 'widgets/pc-double-ads.php';
//モバイル用テキストウィジェット
require_once 'widgets/mobile-text.php';
//モバイル用広告ウィジェット
require_once 'widgets/mobile-ad.php';
//プロフィールウィジェット
require_once 'widgets/author-box.php';
//Facebookボックス
require_once 'widgets/fb-like-box.php';
//Facebookバルーン
require_once 'widgets/fb-like-balloon.php';
//CTAボックス
require_once 'widgets/cta-box.php';
//ランキング
require_once 'widgets/item-ranking.php';
//広告ウィジェット
require_once 'widgets/ad.php';
//ウィジェットの表示制御
require_once 'widgets/display-widgets.php';
