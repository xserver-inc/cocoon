<?php //ファイル読み込み用
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

require_once ABSPATH.'wp-admin/includes/file.php';//WP_Filesystemの使用
//abspath(__FILE__)
require_once abspath(__FILE__).'utils.php';      //ユーティリティー関数
require_once abspath(__FILE__).'page-settings/skin-funcs.php';       //スキン設定関数
//スキンのセット
if (get_skin_url() && !isset($_POST[HIDDEN_FIELD_NAME])) {
  cocoon_skin_settings();  //スキン設定
}

// require_once abspath(__FILE__).'language.php';   //マルチ言語設定
require_once abspath(__FILE__).'utils.php';      //ユーティリティー関数
require_once abspath(__FILE__).'html-forms.php'; //HTMLフォーム生成関数
require_once abspath(__FILE__).'html-tooltips.php'; //HTMLツールチップ生成関数
require_once abspath(__FILE__).'gutenberg.php';   //ブロックエディター関係の関数
require_once abspath(__FILE__).'ad.php';         //広告関係の設定
require_once abspath(__FILE__).'sns.php';        //SNS関係の設定
require_once abspath(__FILE__).'sns-share.php';  //SNSシェア関数
require_once abspath(__FILE__).'sns-follow.php'; //SNSフォロー関数
require_once abspath(__FILE__).'open-graph.php'; //OGP取得ライブラリ
require_once abspath(__FILE__).'punycode.php';   //ピュニコードライブラリ
require_once abspath(__FILE__).'medias.php';     //メディアライブラリ
require_once abspath(__FILE__).'links.php';      //本文リンクライブラリ
require_once abspath(__FILE__).'content-category.php';   //カテゴリー関係の関数
require_once abspath(__FILE__).'content-tag.php';   //タグ関係の関数
require_once abspath(__FILE__).'entry-card.php'; //エントリーカード関数
require_once abspath(__FILE__).'amp.php';        //AMP関係の関数
require_once abspath(__FILE__).'content.php';    //本文関係の関数
require_once abspath(__FILE__).'comments.php';   //コメント関係の関数
require_once abspath(__FILE__).'related-entries.php';   //関連記事関係の関数
require_once abspath(__FILE__).'walkers.php';    //Walker_Nav_Menuまとめ
require_once abspath(__FILE__).'plugins.php';    //プラグイン関係の関数
require_once abspath(__FILE__).'eyecatch.php';   //タイトルからアイキャッチ生成関数
//CSS・JavaScript縮小化ライブラリ
if (!class_exists('MatthiasMullie\Minify\Minify')) {
  $path = get_template_directory() . '/plugins/minify';
  require_once $path . '/minify-master/src/Minify.php';
  require_once $path . '/minify-master/src/CSS.php';
  require_once $path . '/minify-master/src/JS.php';
  require_once $path . '/minify-master/src/Exception.php';
  require_once $path . '/minify-master/src/Exceptions/BasicException.php';
  require_once $path . '/minify-master/src/Exceptions/FileImportException.php';
  require_once $path . '/minify-master/src/Exceptions/IOException.php';
  require_once $path . '/path-converter-master/src/ConverterInterface.php';
  require_once $path . '/path-converter-master/src/Converter.php';
}
require_once abspath(__FILE__).'php-html-css-js-minifier-new.php';   //HTML・CSS・JavaScript縮小化ライブラリ
require_once abspath(__FILE__).'page-settings/all-funcs.php';        //全体設定関数
require_once abspath(__FILE__).'page-settings/header-funcs.php';     //ヘッダー設定関数
require_once abspath(__FILE__).'page-settings/navi-funcs.php';       //グローバルナビ設定関数
require_once abspath(__FILE__).'page-settings/ads-funcs.php';        //広告設定関数
require_once abspath(__FILE__).'page-settings/title-funcs.php';      //タイトル設定関数
require_once abspath(__FILE__).'page-settings/seo-funcs.php';        //SEO設定関数
require_once abspath(__FILE__).'page-settings/analytics-funcs.php';  //アクセス解析設定関数
require_once abspath(__FILE__).'page-settings/column-funcs.php';     //カラム設定関数
require_once abspath(__FILE__).'page-settings/index-funcs.php';      //インデックス設定関数
require_once abspath(__FILE__).'page-settings/single-funcs.php';     //投稿設定関数
require_once abspath(__FILE__).'page-settings/page-funcs.php';       //固定ページ設定関数
require_once abspath(__FILE__).'page-settings/content-funcs.php';    //本文設定関数
require_once abspath(__FILE__).'page-settings/toc-funcs.php';        //目次設定関数
require_once abspath(__FILE__).'page-settings/sns-share-funcs.php';  //SNSシェア設定関数
require_once abspath(__FILE__).'page-settings/sns-follow-funcs.php'; //SNSフォロー設定関数
require_once abspath(__FILE__).'page-settings/image-funcs.php';      //画像設定関数
require_once abspath(__FILE__).'page-settings/ogp-funcs.php';        //OGP設定関数
require_once abspath(__FILE__).'page-settings/blogcard-in-funcs.php';  //内部ブログカード設定関数
require_once abspath(__FILE__).'page-settings/blogcard-out-funcs.php'; //外部ブログカード設定関数
require_once abspath(__FILE__).'page-settings/code-funcs.php';       //コード設定関数
require_once abspath(__FILE__).'page-settings/comment-funcs.php';    //コメント設定関数
require_once abspath(__FILE__).'page-settings/notice-funcs.php';     //通知エリア設定関数
require_once abspath(__FILE__).'page-settings/appeal-funcs.php';     //アピールエリア設定関数
require_once abspath(__FILE__).'page-settings/recommended-funcs.php';//おすすめカード設定関数
require_once abspath(__FILE__).'page-settings/carousel-funcs.php';   //カルーセル設定関数
require_once abspath(__FILE__).'page-settings/footer-funcs.php';     //フッター設定関数
require_once abspath(__FILE__).'page-settings/buttons-funcs.php';    //ボタン設定関数
require_once abspath(__FILE__).'page-settings/mobile-buttons-funcs.php'; //モバイルボタン設定関数
require_once abspath(__FILE__).'page-settings/404-funcs.php';        //404ページ設定関数
require_once abspath(__FILE__).'page-settings/amp-funcs.php';        //AMP設定関数
require_once abspath(__FILE__).'page-settings/pwa-funcs.php';        //PWA設定関数
require_once abspath(__FILE__).'page-settings/admin-funcs.php';      //管理画面設定関数
require_once abspath(__FILE__).'page-settings/editor-funcs.php';     //エディター設定関数
require_once abspath(__FILE__).'page-settings/widget-funcs.php';     //ウィジェット設定関数
require_once abspath(__FILE__).'page-settings/widget-area-funcs.php';//ウィジェットエリア設定関数
require_once abspath(__FILE__).'page-settings/apis-funcs.php';       //API設定関数
require_once abspath(__FILE__).'page-settings/others-funcs.php';     //その他設定関数
require_once abspath(__FILE__).'page-settings/reset-funcs.php';      //リセット設定関数
require_once abspath(__FILE__).'page-settings/about-funcs.php';      //テーマ情報設定関数
// require_once abspath(__FILE__).'page-settings/donation-funcs.php';      //寄付設定関数
require_once abspath(__FILE__).'db.php';  //データベース操作関数
require_once abspath(__FILE__).'page-func-text/func-text-func.php';  //使いまわしテキスト関数
require_once abspath(__FILE__).'page-backup/backup-func.php';  //バックアップ関数
require_once abspath(__FILE__).'page-affiliate-tag/affiliate-tag-func.php';  //アフィリエイトタグ関数
require_once abspath(__FILE__).'page-speech-balloon/speech-balloon-func.php';  //吹き出し関数
require_once abspath(__FILE__).'page-speed-up/speed-up-func.php';  //高速化設定関数
require_once abspath(__FILE__).'page-access/access-func.php';  //アクセス数統計
require_once abspath(__FILE__).'page-item-ranking/item-ranking-func.php';  //ランキング
require_once abspath(__FILE__).'custom-fields/seo-field.php'; //SEOのページ設定
require_once abspath(__FILE__).'custom-fields/ad-field.php';  //広告のページ設定
require_once abspath(__FILE__).'custom-fields/page-field.php';//投稿・固定ページのページ設定
require_once abspath(__FILE__).'custom-fields/update-field.php'; //アップデートのページ設定
require_once abspath(__FILE__).'custom-fields/review-field.php'; //レビュー設定
require_once abspath(__FILE__).'custom-fields/redirect-field.php'; //リダイレクト設定
require_once abspath(__FILE__).'custom-fields/amp-field.php'; //AMPのページ設定
require_once abspath(__FILE__).'custom-fields/custom-css-field.php'; //カスタムCSS設定
require_once abspath(__FILE__).'custom-fields/custom-js-field.php';  //カスタJS設定
require_once abspath(__FILE__).'custom-fields/memo-field.php';  //メモ
require_once abspath(__FILE__).'custom-fields/sns-image-field.php';  //SNS画像
require_once abspath(__FILE__).'custom-fields/other-field.php';  //その他
require_once abspath(__FILE__).'seo.php';      //SEO関数
require_once abspath(__FILE__).'ogp.php';      //OGP関数
require_once abspath(__FILE__).'blogcard-in.php';  //内部ブログカード関数
require_once abspath(__FILE__).'blogcard-out.php'; //外部ブログカード関数
require_once abspath(__FILE__).'scripts.php'; //スクリプト関係の関数
require_once abspath(__FILE__).'image.php';   //画像関係の設定
require_once abspath(__FILE__).'toc.php';     //目次関数
require_once abspath(__FILE__).'cache.php';   //キャッシュ処理
require_once abspath(__FILE__).'widget-areas.php'; //ウィジェットエリアの指定
require_once abspath(__FILE__).'widget.php'; //ウィジェット操作関数
require_once abspath(__FILE__).'original-menu.php'; //オリジナルメニューによる設定項目
require_once abspath(__FILE__).'additional-classes.php'; //スタイリング用の追加クラス関数
require_once abspath(__FILE__).'auto-post-thumbnail.php'; //アイキャッチ自動追加関数
require_once abspath(__FILE__).'ssl.php'; //SSL関係の処理
require_once abspath(__FILE__).'shortcodes.php'; //ショートコード関係の処理
require_once abspath(__FILE__).'shortcodes-product-func.php'; //商品リンク関係の処理
require_once abspath(__FILE__).'shortcodes-amazon.php'; //Amazon商品リンク関係の処理
require_once abspath(__FILE__).'shortcodes-rakuten.php'; //楽天商品リンク関係の処理
require_once abspath(__FILE__).'html5.php'; //HTML5チェック関係
//フルパスを指定しないとうまくいかないファイル
require_once abspath(__FILE__).'profile.php'; //プロフィール関係の処理
if (apply_filters('cocoon_youtube_speed_up_enable', false)) {
  require_once abspath(__FILE__).'youtube.php'; //YouTube関係の処理
}
require_once abspath(__FILE__).'font-awesome.php'; //Font Awesome
require_once abspath(__FILE__).'admin.php'; //管理者機能
if ( function_exists( 'register_block_style' ) && is_block_editor_style_block_option_visible() && is_gutenberg_editor_enable() ){
  // require_once abspath(__FILE__).'block-editor-styles-paragraph.php'; //ブロックエディタースタイル（段落）
  // require_once abspath(__FILE__).'block-editor-styles-list.php'; //ブロックエディタースタイル（リスト）
  require_once abspath(__FILE__).'block-editor-styles-group.php'; //ブロックエディタースタイル（グループ）
  require_once abspath(__FILE__).'block-editor-styles-image.php'; //ブロックエディタースタイル（画像）
  require_once abspath(__FILE__).'block-editor-styles-faq.php'; //ブロックエディタースタイル（FAQ）
}

//Cocoon Blocks
if ( !function_exists( 'cocoon_blocks_cgb_block_assets' ) && is_gutenberg_editor_enable() ):
  require_once get_template_directory().'/blocks/plugin.php';
endif;


//TinyMCE
if (is_admin()) {;
  require_once abspath(__FILE__).'admin-tinymce-qtag.php'; //管理者用編集ボタン機能
  require_once abspath(__FILE__).'tinymce/insert-html.php'; //HTML追加ボタン
  require_once abspath(__FILE__).'tinymce/speech-balloons.php'; //吹き出し追加
  require_once abspath(__FILE__).'tinymce/function-texts.php'; //テンプレート追加
  require_once abspath(__FILE__).'tinymce/affiliate-tags.php'; //アフィリエイトタグ追加
  require_once abspath(__FILE__).'tinymce/item-rankings.php'; //ランキングタグ追加
  require_once abspath(__FILE__).'tinymce/html-tags.php'; //拡張タグ追加
  require_once abspath(__FILE__).'tinymce/shortcodes.php'; //ショートコード追加
  require_once abspath(__FILE__).'admin-tools.php'; //外部ツールを利用したもの
  require_once abspath(__FILE__).'admin-forms.php'; //管理画面で使用するフォームパーツ
  // if (is_dashboard_message_visible()) {
  //   require_once abspath(__FILE__).'dashboard-message.php'; //ダッシュボードに表示するメッセージ
  // }
}

require_once abspath(__FILE__).'settings.php';   //WordPressの設定


//新着記事ウィジェット
require_once abspath(__FILE__).'widgets/new-entries.php';
//関連記事ウィジェット
require_once abspath(__FILE__).'widgets/related-entries.php';
//人気記事ウィジェット
require_once abspath(__FILE__).'widgets/popular-entries.php';
//最近のコメントウィジェット
require_once abspath(__FILE__).'widgets/recent-comments.php';
//フォローボタンウィジェット
require_once abspath(__FILE__).'widgets/sns-follow-buttons.php';
//PC用テキストウィジェット
require_once abspath(__FILE__).'widgets/pc-text.php';
//PC用広告ウィジェット
require_once abspath(__FILE__).'widgets/pc-ad.php';
//PC用ダブルレクタングル広告ウィジェット
//require_once abspath(__FILE__).'widgets/pc-double-ads.php';
//モバイル用テキストウィジェット
require_once abspath(__FILE__).'widgets/mobile-text.php';
//モバイル用広告ウィジェット
require_once abspath(__FILE__).'widgets/mobile-ad.php';
//プロフィールウィジェット
require_once abspath(__FILE__).'widgets/author-box.php';
//Facebookボックス
require_once abspath(__FILE__).'widgets/fb-like-box.php';
//Facebookバルーン
require_once abspath(__FILE__).'widgets/fb-like-balloon.php';
//CTAボックス
require_once abspath(__FILE__).'widgets/cta-box.php';
//ランキング
require_once abspath(__FILE__).'widgets/item-ranking.php';
//広告ウィジェット
require_once abspath(__FILE__).'widgets/ad.php';
//目次ウィジェット
require_once abspath(__FILE__).'widgets/toc.php';
//ウィジェットの表示制御
require_once abspath(__FILE__).'widgets/display-widgets.php';
//メニューを取得
$nav_menus = wp_get_nav_menus();
if (!empty($nav_menus)) {
  //ナビカードウィジェット
  require_once abspath(__FILE__).'widgets/navi-entries.php';
  //おすすめカードウィジェット
  require_once abspath(__FILE__).'widgets/recommended-cards.php';
  //ボックスメニューウィジェット
  require_once abspath(__FILE__).'widgets/box-menus.php';
}
// //インフォリストウィジェット
require_once abspath(__FILE__).'widgets/info-list.php';
