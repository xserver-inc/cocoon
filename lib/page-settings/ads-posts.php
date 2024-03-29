<?php //広告設定をデータベースに保存
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

//広告表示設定
update_theme_option(OP_ALL_ADS_VISIBLE);
//アドセンス広告表示設定
update_theme_option(OP_ALL_ADSENSES_VISIBLE);
//広告コード
update_theme_option(OP_AD_CODE);
//広告ラベル
update_theme_option(OP_AD_LABEL_CAPTION);


//アドセンス表示方式
update_theme_option(OP_ADSENSE_DISPLAY_METHOD);
//モバイル広告の幅を広くする
update_theme_option(OP_MOBILE_ADSENSE_WIDTH_WIDE);

//インデックストップの広告表示
update_theme_option(OP_AD_POS_INDEX_TOP_VISIBLE);
//インデックストップの広告フォーマット
update_theme_option(OP_AD_POS_INDEX_TOP_FORMAT);
//インデックストップの広告ラベル表示
update_theme_option(OP_AD_POS_INDEX_TOP_LABEL_VISIBLE);

//インデックスミドルの広告表示
update_theme_option(OP_AD_POS_INDEX_MIDDLE_VISIBLE);
//インデックスミドルの広告フォーマット
update_theme_option(OP_AD_POS_INDEX_MIDDLE_FORMAT);
//インデックスミドルの広告ラベル表示
update_theme_option(OP_AD_POS_INDEX_MIDDLE_LABEL_VISIBLE);

//インデックスボトムの広告表示
update_theme_option(OP_AD_POS_INDEX_BOTTOM_VISIBLE);
//インデックスボトムの広告フォーマット
update_theme_option(OP_AD_POS_INDEX_BOTTOM_FORMAT);
//インデックスボトムの広告ラベル表示
update_theme_option(OP_AD_POS_INDEX_BOTTOM_LABEL_VISIBLE);

//サイドバートップの広告表示
update_theme_option(OP_AD_POS_SIDEBAR_TOP_VISIBLE);
//サイドバートップの広告フォーマット
update_theme_option(OP_AD_POS_SIDEBAR_TOP_FORMAT);
//サイドバートップの広告ラベル表示
update_theme_option(OP_AD_POS_SIDEBAR_TOP_LABEL_VISIBLE);

//サイドバーボトムの広告表示
update_theme_option(OP_AD_POS_SIDEBAR_BOTTOM_VISIBLE);
//サイドバーボトムの広告フォーマット
update_theme_option(OP_AD_POS_SIDEBAR_BOTTOM_FORMAT);
//サイドバーボトムの広告ラベル表示
update_theme_option(OP_AD_POS_SIDEBAR_BOTTOM_LABEL_VISIBLE);

//投稿・固定ページタイトル上の広告表示
update_theme_option(OP_AD_POS_ABOVE_TITLE_VISIBLE);
//投稿・固定ページタイトル上の広告フォーマット
update_theme_option(OP_AD_POS_ABOVE_TITLE_FORMAT);
//投稿・固定ページタイトル上の広告ラベル表示
update_theme_option(OP_AD_POS_ABOVE_TITLE_LABEL_VISIBLE);

//投稿・固定ページタイトル下の広告表示
update_theme_option(OP_AD_POS_BELOW_TITLE_VISIBLE);
//投稿・固定ページタイトル下の広告フォーマット
update_theme_option(OP_AD_POS_BELOW_TITLE_FORMAT);
//投稿・固定ページタイトル下の広告ラベル表示
update_theme_option(OP_AD_POS_BELOW_TITLE_LABEL_VISIBLE);

//投稿・固定ページ本文上の広告表示
update_theme_option(OP_AD_POS_CONTENT_TOP_VISIBLE);
//投稿・固定ページ本文上の広告フォーマット
update_theme_option(OP_AD_POS_CONTENT_TOP_FORMAT);
//投稿・固定ページ本文上の広告ラベル表示
update_theme_option(OP_AD_POS_CONTENT_TOP_LABEL_VISIBLE);

//投稿・固定ページ本文中の広告表示
update_theme_option(OP_AD_POS_CONTENT_MIDDLE_VISIBLE);
//投稿・固定ページ本文中の広告フォーマット
update_theme_option(OP_AD_POS_CONTENT_MIDDLE_FORMAT);
//投稿・固定ページ本文中の広告ラベル表示
update_theme_option(OP_AD_POS_CONTENT_MIDDLE_LABEL_VISIBLE);
//投稿・固定ページ本文中の全てのH2見出し手前に広告表示
update_theme_option(OP_AD_POS_ALL_CONTENT_MIDDLE_VISIBLE);
//投稿・固定ページ本文中の広表示数
update_theme_option(OP_AD_POS_CONTENT_MIDDLE_COUNT);

//投稿・固定ページ本文下の広告表示
update_theme_option(OP_AD_POS_CONTENT_BOTTOM_VISIBLE);
//投稿・固定ページ本文下の広告フォーマット
update_theme_option(OP_AD_POS_CONTENT_BOTTOM_FORMAT);
//投稿・固定ページ本文下の広告ラベル表示
update_theme_option(OP_AD_POS_CONTENT_BOTTOM_LABEL_VISIBLE);

//投稿・固定ページSNSボタン上の広告表示
update_theme_option(OP_AD_POS_ABOVE_SNS_BUTTONS_VISIBLE);
//投稿・固定ページSNSボタン上の広告フォーマット
update_theme_option(OP_AD_POS_ABOVE_SNS_BUTTONS_FORMAT);
//投稿・固定ページSNSボタン上の広告ラベル表示
update_theme_option(OP_AD_POS_ABOVE_SNS_BUTTONS_LABEL_VISIBLE);

//投稿・固定ページSNSボタン下の広告表示
update_theme_option(OP_AD_POS_BELOW_SNS_BUTTONS_VISIBLE);
//投稿・固定ページSNSボタン下の広告フォーマット
update_theme_option(OP_AD_POS_BELOW_SNS_BUTTONS_FORMAT);
//投稿・固定ページSNSボタン下の広告ラベル表示
update_theme_option(OP_AD_POS_BELOW_SNS_BUTTONS_LABEL_VISIBLE);

//投稿関連記事下の広告表示
update_theme_option(OP_AD_POS_BELOW_RELATED_POSTS_VISIBLE);
//投稿関連記事下の広告フォーマット
update_theme_option(OP_AD_POS_BELOW_RELATED_POSTS_FORMAT);
//投稿関連記事下の広告ラベル表示
update_theme_option(OP_AD_POS_BELOW_RELATED_POSTS_LABEL_VISIBLE);

//[ad]ショートコードを有効
update_theme_option(OP_AD_SHORTCODE_ENABLE);
//[ad]ショートコード広告フォーマット
update_theme_option(OP_AD_SHORTCODE_FORMAT);
//[ad]ショートコード広告ラベル表示
update_theme_option(OP_AD_SHORTCODE_LABEL_VISIBLE);

//ads.txtを更新する
update_theme_option(OP_AD_ADS_TXT_ENABLE);
//ads.txt内容
update_theme_option(OP_AD_ADS_TXT_CONTENT);

//投稿ページにPR表記表示
update_theme_option(OP_PR_LABEL_SINGLE_VISIBLE);
//固定ページにPR表記表示
update_theme_option(OP_PR_LABEL_PAGE_VISIBLE);
//カテゴリーページにPR表記表示
update_theme_option(OP_PR_LABEL_CATEGORY_PAGE_VISIBLE);
//タグページにPR表記表示
update_theme_option(OP_PR_LABEL_TAG_PAGE_VISIBLE);
//PR表記（小）表示
update_theme_option(OP_PR_LABEL_SMALL_VISIBLE);
//PR表記（大）表示
update_theme_option(OP_PR_LABEL_LARGE_VISIBLE);
//PR表記（小）キャプション
update_theme_option(OP_PR_LABEL_SMALL_CAPTION);
//PR表記（大）キャプション
update_theme_option(OP_PR_LABEL_LARGE_CAPTION);
//PR表記除外記事ID
update_theme_option(OP_PR_LABEL_EXCLUDE_POST_IDS);
//PR表記除外カテゴリーID
update_theme_option(OP_PR_LABEL_EXCLUDE_CATEGORY_IDS);
//PR表記除外タグID
update_theme_option(OP_PR_LABEL_EXCLUDE_TAG_IDS);

//LinkSwitch有効
update_theme_option(OP_AD_LINKSWITCH_ENABLE);
//LinkSwitch ID
update_theme_option(OP_AD_LINKSWITCH_ID);

//広告除外記事ID
update_theme_option(OP_AD_EXCLUDE_POST_IDS);
//広告除外カテゴリーID
update_theme_option(OP_AD_EXCLUDE_CATEGORY_IDS);
