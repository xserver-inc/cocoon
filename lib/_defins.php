<?php //定数をまとめて定義

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

//開発関係の場合デバッグ値を有効にする
define('DEBAG_VALU', $_SERVER["HTTP_HOST"] == THEME_NAME.'.dev' ? 1 : 0);

//デバッグモード
define('DEBUG_MODE', DEBAG_VALU);


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

///////////////////////////////////////
// キャッシュ
///////////////////////////////////////
//シェアカウントキャッシュのプレフィックス
define('TRANSIENT_SHARE_PREFIX', THEME_NAME.'_share_count_');
//フォローカウントキャッシュのプレフィックス
define('TRANSIENT_FOLLOW_PREFIX', THEME_NAME.'_follow_count_');
//人気記事ウィジェットのプレフィックス
define('TRANSIENT_POPULAR_PREFIX', THEME_NAME.'_popular_entries');

//最初のH2見出し用の優先度
define('BEFORE_1ST_H2_WIDGET_PRIORITY_STANDARD', 10002);
define('BEFORE_1ST_H2_AD_PRIORITY_STANDARD', 10001);
define('BEFORE_1ST_H2_TOC_PRIORITY_STANDARD', 10003);
define('BEFORE_1ST_H2_TOC_PRIORITY_HIGH', 10000);



//URLの正規表現
define('URL_REG_STR', '(https?|ftp)(:\/\/[-_.!~*\'()a-zA-Z0-9;\/?:\@&=+\$,%#]+)');
define('URL_REG', '/'.URL_REG_STR.'/');

//Font AwesomeのCDN
define('FONT_AWESOME_CDN_URL', 'https://max'.'cdn.boot'.'strapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css');

//AMPのトップへ戻る用のコード
define('AMP_GO_TO_TOP_ON_CODE', ' on="tap:header.scrollTo(\'duration\'=375, \'easing\'=\'cubic-bezier(.4,0,.2,1)\')"');


//インポートファイルの読み込み
require_once '_imports.php';