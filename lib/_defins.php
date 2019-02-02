<?php //定数をまとめて定義
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

//テーマ名
define('THEME_NAME', 'cocoon');
//テーマ名（最初の一文字だけ大文字）
define('THEME_NAME_CAMEL', ucfirst(THEME_NAME));
//テーマ名（大文字）
define('THEME_NAME_UPPER', strtoupper(THEME_NAME));
//親テーマフォルダ
define('THEME_PARENT_DIR', THEME_NAME.'-master');
//子テーマフォルダ
define('THEME_CHILD_DIR', THEME_NAME.'-child');
//テーマ設定ページ用のURLクエリ
define('THEME_SETTINGS_PAFE', 'theme-settings');

//開発関係の場合デバッグ値を有効にする
define('DEBAG_VALU', $_SERVER["HTTP_HOST"] == THEME_NAME.'.local' ? 1 : 0);

//デバッグモード
define('DEBUG_MODE', DEBAG_VALU);
define('DEBUG_CACHE_ENABLE', 1);
define('DEBUG_ADMIN_DEMO_ENABLE', 1/*!DEBUG_MODE*/);


define('THEME_JS', THEME_NAME.'-js');
define('THEME_CHILD_JS', THEME_NAME.'-child-js');
define('THEME_SKIN_JS', THEME_NAME.'-skin-js');
//ウィジェット名プレフィックス
define('WIDGET_NAME_PREFIX', '['.substr(THEME_NAME_CAMEL, 0, 1).'] '); //ex.[C]
//トップレベルオリジナル設定名
define('SETTING_NAME_TOP', THEME_NAME_CAMEL.__( ' 設定', THEME_NAME ));

//ウィジェットのエントリータイプ
define('ET_DEFAULT',        'default');
define('ET_LARGE_THUMB',    'large_thumb');
define('ET_LARGE_THUMB_ON', 'large_thumb_on');
//ウィジェットモードデフォルト
define('WM_DEFAULT', 'all');
//人気ウィジェット集計期間デフォルト
define('PCD_DEFAULT', 30);
//新着・人気ウィジェットのデフォルト表示数字
define('EC_DEFAULT', 5);
//目次のインデックス番号
global $_TOC_INDEX;
$_TOC_INDEX = 1;
//目次利用フラグ
global $_TOC_WIDGET_OR_SHORTCODE_USE;
$_TOC_WIDGET_OR_SHORTCODE_USE = false;

//アドセンスID名
define('DATA_AD_CLIENT', 'data-ad-client');
define('DATA_AD_SLOT',   'data-ad-slot');
//レスポンシブ広告の表示フォーマット
define('DATA_AD_FORMAT_NONE', 'none'); //おまかせ
define('DATA_AD_FORMAT_AUTO', 'auto'); //おまかせ
define('DATA_AD_FORMAT_RECTANGLE', 'rectangle'); //正方形に近い長方形
define('DATA_AD_FORMAT_HORIZONTAL', 'horizontal'); //横長
define('DATA_AD_FORMAT_VERTICAL', 'vertical'); //縦長
//Googleで定義されていないフォーマットはDATA_無し
define('AD_FORMAT_SINGLE_RECTANGLE', 'single-rectangle'); //シングルレクタングル
define('AD_FORMAT_DABBLE_RECTANGLE', 'dabble-rectangle'); //ダブルレクタングル
//広告ユニット以外
define('DATA_AD_FORMAT_FLUID', 'fluid'); //記事中広告
define('DATA_AD_FORMAT_AUTORELAXED', 'autorelaxed'); //関連記事
//リンクユニット
define('DATA_AD_FORMAT_LINK', 'link');

//メインカラム用の広告フォーマット集
global $_MAIN_DATA_AD_FORMATS;
$_MAIN_DATA_AD_FORMATS = array(
  DATA_AD_FORMAT_AUTO => __( 'オート（AdSenseにおまかせ）', THEME_NAME ),
  DATA_AD_FORMAT_HORIZONTAL => __( 'バナー', THEME_NAME ),
  DATA_AD_FORMAT_RECTANGLE => __( 'レスポンシブレクタングル', THEME_NAME ),
  AD_FORMAT_SINGLE_RECTANGLE => __( 'シングルレクタングル', THEME_NAME ),
  AD_FORMAT_DABBLE_RECTANGLE => __( 'ダブルレクタングル', THEME_NAME ),
  DATA_AD_FORMAT_FLUID => __( '記事内広告', THEME_NAME ),
  DATA_AD_FORMAT_LINK => __( 'リンクユニット', THEME_NAME ),
);
// define('MAIN_DATA_AD_FORMATS', $_MAIN_DATA_AD_FORMATS);

//アドセンス共通スクリプトコード
define('ADSENSE_SCRIPT_CODE', '<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>');
//AdSenseの存在フラグ
global $_IS_ADSENSE_EXIST;
$_IS_ADSENSE_EXIST = false; //最初はAdSenseの存在がない
// //アドセンススクリプトコードが読み込まれているか
// global $_IS_ADSENSE_SCRIPT_EMPTY;
// $_IS_ADSENSE_SCRIPT_EMPTY = true;
// _v('def');
// _v($_IS_ADSENSE_SCRIPT_EMPTY);

//サイドバー用の広告フォーマット集
global $_SIDEBAR_DATA_AD_FORMATS;
$_SIDEBAR_DATA_AD_FORMATS = array(
  DATA_AD_FORMAT_AUTO => __( 'オート（AdSenseにおまかせ）', THEME_NAME ),
  DATA_AD_FORMAT_HORIZONTAL => __( 'バナー', THEME_NAME ),
  DATA_AD_FORMAT_RECTANGLE => __( 'レクタングル', THEME_NAME ),
  DATA_AD_FORMAT_VERTICAL => __( 'ラージスカイスクレイパー', THEME_NAME ),
  DATA_AD_FORMAT_LINK => __( 'リンクユニット', THEME_NAME ),
);
// define('SIDEBAR_DATA_AD_FORMATS', $_SIDEBAR_DATA_AD_FORMATS);

//PCウィジェット用の広告フォーマット集
global $_PC_WIDGET_DATA_AD_FORMATS;
$_PC_WIDGET_DATA_AD_FORMATS = array(
  'none' => __( '広告コードをそのまま表示', THEME_NAME ),
  DATA_AD_FORMAT_AUTO => __( 'オート（AdSenseにおまかせ）', THEME_NAME ),
  DATA_AD_FORMAT_HORIZONTAL => __( 'バナー', THEME_NAME ),
  DATA_AD_FORMAT_RECTANGLE => __( 'レスポンシブレクタングル', THEME_NAME ),
  AD_FORMAT_SINGLE_RECTANGLE => __( 'シングルレクタングル', THEME_NAME ),
  AD_FORMAT_DABBLE_RECTANGLE => __( 'ダブルレクタングル', THEME_NAME ),
  DATA_AD_FORMAT_VERTICAL => __( 'ラージスカイスクレイパー', THEME_NAME ),
  DATA_AD_FORMAT_FLUID => __( '記事内広告', THEME_NAME ),
  DATA_AD_FORMAT_LINK => __( 'リンクユニット', THEME_NAME ),
);
// define('PC_WIDGET_DATA_AD_FORMATS', $_PC_WIDGET_DATA_AD_FORMATS);

//モバイル用の広告フォーマット集
global $_MOBILE_WIDGET_DATA_AD_FORMATS;
$_MOBILE_WIDGET_DATA_AD_FORMATS = array(
  'none' => __( '広告コードをそのまま表示', THEME_NAME ),
  DATA_AD_FORMAT_AUTO => __( 'オート（AdSenseにおまかせ）', THEME_NAME ),
  DATA_AD_FORMAT_HORIZONTAL => __( 'バナー', THEME_NAME ),
  DATA_AD_FORMAT_RECTANGLE => __( 'レスポンシブレクタングル', THEME_NAME ),
  AD_FORMAT_SINGLE_RECTANGLE => __( 'シングルレクタングル', THEME_NAME ),
  DATA_AD_FORMAT_LINK => __( 'リンクユニット', THEME_NAME ),
);
// define('MOBILE_WIDGET_DATA_AD_FORMATS', $_MOBILE_WIDGET_DATA_AD_FORMATS);

//設定向けのグローバル変数
global $_THEME_OPTIONS;
if (is_null($_THEME_OPTIONS)) {
  $_THEME_OPTIONS = array();
}


//管理設定画面の隠しフィールド名
define('HIDDEN_FIELD_NAME', THEME_NAME.'_submit_hidden');
define('HIDDEN_DELETE_FIELD_NAME', THEME_NAME.'_delete_hidden');
define('SELECT_INDEX_NAME', 'select_index');

//入力ボックスの横幅
define('DEFAULT_INPUT_COLS', 60);
//入力ボックスの縦幅
define('DEFAULT_INPUT_ROWS', 10);

//SNSシェアオプション
define('SS_TOP', 'ss-top');
define('SS_BOTTOM', 'ss-bottom');

//NO IMAGE画像URL
define('NO_IMAGE_320', get_template_directory_uri().'/images/no-image-320.png');
define('NO_IMAGE_160', get_template_directory_uri().'/images/no-image-160.png');
define('NO_IMAGE_120', get_template_directory_uri().'/images/no-image-120.png');
define('NO_IMAGE_150', get_template_directory_uri().'/images/no-image-150.png');
define('NO_IMAGE_LARGE', get_template_directory_uri().'/images/no-image-large.png');

///////////////////////////////////////
// キャッシュ
///////////////////////////////////////
//シェアカウントキャッシュのプレフィックス
define('TRANSIENT_SHARE_PREFIX', THEME_NAME.'_share_count_');
//フォローカウントキャッシュのプレフィックス
define('TRANSIENT_FOLLOW_PREFIX', THEME_NAME.'_follow_count_');
//人気記事ウィジェットのプレフィックス
define('TRANSIENT_POPULAR_PREFIX', THEME_NAME.'_popular_entries');
//ブログカードのプレフィックス
define('TRANSIENT_BLOGCARD_PREFIX', THEME_NAME.'_bcc_');
//AMPのプレフィックス
define('TRANSIENT_AMP_PREFIX', THEME_NAME.'_amp_');
//Amazon APIのプレフィックス
define('TRANSIENT_AMAZON_API_PREFIX', THEME_NAME.'_amazon_api_asin_');
//Amazon APIのバックアッププレフィックス
define('TRANSIENT_BACKUP_AMAZON_API_PREFIX', THEME_NAME.'_backup_amazon_api_asin_');
//楽天APIのプレフィックス
define('TRANSIENT_RAKUTEN_API_PREFIX', THEME_NAME.'_rakuten_api_id_');
//楽天APIのバックアッププレフィックス
define('TRANSIENT_BACKUP_RAKUTEN_API_PREFIX', THEME_NAME.'_backup_rakuten_api_id_');

//最初のH2見出し用の優先度
define('BEFORE_1ST_H2_WIDGET_PRIORITY_STANDARD', 10002);
define('BEFORE_1ST_H2_AD_PRIORITY_STANDARD', 10001);
define('BEFORE_1ST_H2_TOC_PRIORITY_STANDARD', 10003);
define('BEFORE_1ST_H2_TOC_PRIORITY_HIGH', 10000);



//URLの正規表現
define('URL_REG_STR', '(https?|ftp)(:\/\/[-_.!~*\'()a-zA-Z0-9;\/?:\@&=+\$,%#]+)');
define('URL_REG', '/'.URL_REG_STR.'/');

//Font Awesome4.7のCDN
define('FONT_AWESOME4_CDN_URL', 'https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css');
//Font Awesome5のCDN
define('FONT_AWESOME5_CDN_URL', 'https://use.fontawesome.com/releases/v5.6.3/css/all.css');
//IcoMoonフォント
define('FONT_AICOMOON_URL', get_template_directory_uri() . '/webfonts/icomoon/style.css');

//AMPのトップへ戻る用のコード
define('AMP_GO_TO_TOP_ON_CODE', ' on="tap:header.scrollTo(\'duration\'=375)"');

//リンククリック時の削除確認JavaScript
define('ONCLICK_DELETE_CONFIRM', ' onclick="if(!confirm(\''.__( '本当に削除してもいいですか？', THEME_NAME ).'\'))return false"');

//インポートファイルの読み込み

require_once abspath(__FILE__).'_imports.php';
