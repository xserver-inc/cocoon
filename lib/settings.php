<?php //Wordpressのセッティング

// アイキャッチ画像を有効化
add_theme_support('post-thumbnails');

//サムネイルサイズ
add_image_size('thumb100', 100, 100, true);
add_image_size('thumb150', 150, 150, true);
add_image_size('thumb80',   80,  45, true);
add_image_size('thumb160', 160,  90, true);
add_image_size('thumb320', 320, 180, true);
add_image_size('thumb640', 640, 360, true);
add_image_size('thumb960', 960, 540, true);
add_image_size('thumb320_raw', 320, 0, false);

//コンテンツの幅の指定
if ( ! isset( $content_width ) ) $content_width = 800;

//カテゴリー説明文でHTMLタグを使う
remove_filter( 'pre_term_description', 'wp_filter_kses' );

//ビジュアルエディターとテーマ表示のスタイルを合わせる
add_editor_style();

// RSS2 の feed リンクを出力
add_theme_support( 'automatic-feed-links' );

// カスタムメニューを有効化
add_theme_support( 'menus' );

// カスタムメニューの「場所」を設定
//register_nav_menu( 'header-navi', 'ヘッダーナビゲーション' );
register_nav_menus(
  array(
    'navi-header' => 'ヘッダーナビ',
    'navi-footer' => 'フッターナビ（サブメニュー不可）',
  )
);

//固定ページに抜粋を追加
add_post_type_support( 'page', 'excerpt' );

//カスタムヘッダー
$custom_header_defaults = array(
 'random-default' => false,
 'width' => 1200,//サイドバーの幅によって変更
 'height' => 100,
 'flex-height' => false,
 'flex-width' => false,
 'default-text-color' => '',
 'header-text' => false,
 'uploads' => true,
);
//カスタムヘッダー
add_theme_support( 'custom-header', $custom_header_defaults );

//テキストウィジェットでショートコードを使用する
add_filter('widget_text', 'do_shortcode');
add_filter('widget_text_pc_text', 'do_shortcode');
add_filter('widget_text_mobile_text', 'do_shortcode');
add_filter('widget_mobile_ad_text', 'do_shortcode');
add_filter('widget_classic_text', 'do_shortcode');
add_filter('widget_pc_ad_text', 'do_shortcode');
add_filter('widget_pc_double_ad1_text', 'do_shortcode');
add_filter('widget_pc_double_ad2_text', 'do_shortcode');

//generator を削除
remove_action('wp_head', 'wp_generator');
//EditURI を削除
remove_action('wp_head', 'rsd_link');
//wlwmanifest を削除
remove_action('wp_head', 'wlwmanifest_link');

//カスタム背景
$custom_background_defaults = array(
        'default-color' => 'ffffff',
);
add_theme_support( 'custom-background', $custom_background_defaults );

//ヘッダーに以下のようなタグが挿入されるWP4.4からの機能を解除
//<link rel='https://api.w.org/' href='http:/xxxx/wordpress/wp-json/' />
remove_action( 'wp_head', 'rest_output_link_wp_head' );

//Wordpress3.5で廃止されたリンクマネージャを表示する
add_filter('pre_option_link_manager_enabled','__return_true');