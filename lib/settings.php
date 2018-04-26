<?php //Wordpressのセッティング

//ERR_BLOCKED_BY_XSS_AUDITOR エラー対策
if (is_admin_php_page()) {
  header('X-XSS-Protection: 0');
}

// アイキャッチ画像を有効化
add_theme_support('post-thumbnails');

//サムネイルサイズ
add_image_size('thumb100', 100, 100, true);//Facebookバルーン
add_image_size('thumb150', 150, 150, true);//ページ送りナビ
add_image_size('thumb120', 120,  67, true);//新着・人気記事ウィジェット・ページ送りナビ・関連記事ミニカード
add_image_size('thumb160', 160,  90, true);//関連記事デフォルト・関連記事4列
add_image_size('thumb320', 320, 180, true);//関連記事3列
//本文レスポンシブ表示用
add_image_size('thumb320_raw', 320, 0, false);
// add_image_size('thumb360_raw', 360, 0, false);
// add_image_size('thumb375_raw', 375, 0, false);
add_image_size('thumb414_raw', 414, 0, false);
add_image_size('thumb600_raw', 600, 0, false);
// add_image_size('thumb768_raw', 768, 0, false);
//Retinaディスプレイ用
//add_image_size('thumb640', 640, 360, true);

// remove_filter( 'the_content', 'wpautop' );
// remove_filter( 'the_content', 'wptexturize' );
// add_filter( 'the_content', 'wpautop' , 7);
// add_filter( 'the_content', 'wptexturize' , 7);

//縦型カード2列用の可変サムネイル
add_image_size(get_vartical_card_2_thumbnail_size(), get_vartical_card_2_width(), get_vartical_card_2_height(), true);
//縦型カード3列用の可変サムネイル
add_image_size(get_vartical_card_3_thumbnail_size(), get_vartical_card_3_width(), get_vartical_card_3_height(), true);

//コンテンツの幅の指定
if ( ! isset( $content_width ) ) $content_width = get_site_wrap_width();

//カテゴリー説明文でHTMLタグを使う
remove_filter( 'pre_term_description', 'wp_filter_kses' );

///////////////////////////////////////
// ビジュアルエディターのCSSの読み込み順を変更する
///////////////////////////////////////
add_filter( 'editor_stylesheets', 'visual_editor_stylesheets_custom');
if ( !function_exists( 'visual_editor_stylesheets_custom' ) ):
function visual_editor_stylesheets_custom($stylesheets) {
  //ビジュアルエディタースタイルが有効な時
  if (is_visual_editor_style_enable()) {
    $style_url = get_template_directory_uri().'/style.css';
    $cache_file_url = get_theme_css_cache_file_url();
    $editor_style_url = get_template_directory_uri().'/editor-style.css';
    array_push($stylesheets,
      FONT_AWESOME_CDN_URL,
      add_file_ver_to_css_js($style_url),
      add_file_ver_to_css_js($cache_file_url), //テーマ設定で変更したスタイル
      add_file_ver_to_css_js($editor_style_url)
    );
    //スキンが設定されている場合
    if (get_skin_url()) {
      array_push($stylesheets,
        add_file_ver_to_css_js(get_skin_url())
      );
    }
    //子テーマがある場合、子テーマ内のスタイルも読み込む
    if (is_child_theme()) {
      array_push($stylesheets,
        add_file_ver_to_css_js(get_stylesheet_directory_uri().'/style.css'),
        add_file_ver_to_css_js(get_stylesheet_directory_uri().'/editor-style.css')
      );
    }
  }

  //_v($stylesheets);
  return $stylesheets;
}
endif;
// add_editor_style( 'style.css' );


// RSS2 の feed リンクを出力
add_theme_support( 'automatic-feed-links' );

// カスタムメニューを有効化
add_theme_support( 'menus' );

// カスタムメニューの「場所」を設定
//register_nav_menu( 'header-navi', 'ヘッダーナビゲーション' );
register_nav_menus(
  array(
    'navi-header' => __( 'ヘッダーナビ', THEME_NAME ),
    'navi-mobile' => __( 'モバイルヘッダーナビ（サブ不可）', THEME_NAME ),
    'navi-footer' => __( 'フッターナビ（サブ不可）', THEME_NAME ),
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

//テキストウィジェットでショートコード利用する
add_filter('widget_text', 'do_shortcode');
add_filter('widget_text_pc_text', 'do_shortcode');
add_filter('widget_text_mobile_text', 'do_shortcode');
add_filter('widget_mobile_ad_text', 'do_shortcode');
add_filter('widget_classic_text', 'do_shortcode');
add_filter('widget_pc_ad_text', 'do_shortcode');
add_filter('widget_pc_double_ad1_text', 'do_shortcode');
add_filter('widget_pc_double_ad2_text', 'do_shortcode');
//ランキングアイテム内でショートコード利用する
add_filter('ranking_item_name', 'do_shortcode');
add_filter('ranking_item_image_tag', 'do_shortcode');
add_filter('ranking_item_description', 'do_shortcode');
add_filter('ranking_item_link_tag', 'do_shortcode');
//カテゴリ
add_filter('the_category_content', 'do_shortcode');

//generator を削除
remove_action('wp_head', 'wp_generator');
//EditURI を削除
remove_action('wp_head', 'rsd_link');
//wlwmanifest を削除
remove_action('wp_head', 'wlwmanifest_link');
// Embed WPのブログカード。他サイトのアイキャッチ画像や抜粋自動埋め込み
remove_action('wp_head', 'rest_output_link_wp_head');
remove_action('wp_head', 'wp_oembed_add_discovery_links');
remove_action('wp_head', 'wp_oembed_add_host_js');
// 管理画面絵文字削除
add_action('init', 'remove_action_emoji');
if ( !function_exists( 'remove_action_emoji' ) ):
function remove_action_emoji(){
  remove_action('wp_head', 'print_emoji_detection_script', 7);
  remove_action('admin_print_scripts', 'print_emoji_detection_script');
  remove_action('wp_print_styles', 'print_emoji_styles');
  remove_action('admin_print_styles', 'print_emoji_styles');
  remove_filter('the_content_feed', 'wp_staticize_emoji');
  remove_filter('comment_text_rss', 'wp_staticize_emoji');
  remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
}
endif;


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

//はてな oEmbed対応
wp_oembed_add_provider('https://*', 'https://hatenablog.com/oembed');
//oembed無効
add_filter( 'embed_oembed_discover', '__return_false' );
//Embeds
remove_action( 'parse_query', 'wp_oembed_parse_query' );
remove_action( 'wp_head', 'wp_oembed_remove_discovery_links' );
remove_action( 'wp_head', 'wp_oembed_remove_host_js' );
//本文中のURLが内部リンクの場合にWordpressがoembedをしてしまうのを解除(WP4.5.3向けの対策)
remove_filter( 'pre_oembed_result', 'wp_filter_pre_oembed_result' );


//不要なテーマカスタマイザー項目を削除
add_action( 'customize_register', 'customize_register_custom' );
if ( !function_exists( 'customize_register_custom' ) ):
function customize_register_custom( $wp_customize ) {
  //サイト基本情報
  //$wp_customize->remove_section('title_tagline');
  //色
  $wp_customize->remove_section('colors');
  // //背景画像がある場合
  // $wp_customize->remove_section('background_image');
  //ヘッダーメディア
  $wp_customize->remove_section('header_image');
  //メニュー
  //$wp_customize->remove_panel('nav_menus');
  //↑こちのコードでは消えなかった
  //remove_action( 'customize_register', array( $wp_customize->nav_menus, 'customize_register' ), 11 );
  // //ウィジェット
  // $wp_customize->remove_panel('widgets');
  // //固定フロントページ
  // $wp_customize->remove_section('static_front_page');
  // //追加CSS
  // $wp_customize->remove_section('custom_css');
}
endif;

// //Wordpressデフォルトが出力するサイトアイコンを表示しない
// remove_action ('wp_head', 'wp_site_icon');
// add_filter('site_icon_meta_tags', 'filter_site_icon_meta_tags');
// if ( !function_exists( 'filter_site_icon_meta_tags' ) ):
// function filter_site_icon_meta_tags($meta_tags) {
//  //array_splice($meta_tags, 2);
//   return array();
// }
// endif;

//Live Writerでテーマ取得する際は、RSSを出力しない
if (is_user_agent_live_writer()) {
  //RSSフィードの停止
  remove_action('do_feed_rdf', 'do_feed_rdf');
  remove_action('do_feed_rss', 'do_feed_rss');
  remove_action('do_feed_rss2', 'do_feed_rss2');
  remove_action('do_feed_atom', 'do_feed_atom');
}

//show_admin_bar( false );
//ログインユーザー以外はBuddyPressの管理バーを除去する
if (!is_user_logged_in()) {
  remove_action('init', 'bp_core_load_admin_bar', 9);
}


//CSS、JSファイルに編集時間をバージョンとして付加する（ファイル編集後のブラウザキャッシュ対策）
add_filter( 'style_loader_src', 'add_file_ver_to_css_js', 9999 );
add_filter( 'script_loader_src', 'add_file_ver_to_css_js', 9999 );
if ( !function_exists( 'add_file_ver_to_css_js' ) ):
function add_file_ver_to_css_js( $src ) {
  //サーバー内のファイルの場合
  if (includes_site_url($src)) {
    //Wordpressのバージョンを除去する場合
    // if ( strpos( $src, 'ver=' ) )
    //   $src = remove_query_arg( 'ver', $src );
    //クエリーを削除したファイルURLを取得
    $removed_src = preg_replace('{\?.*}i', '', $src);
    //URLをパスに変換
    $stylesheet_file = url_to_local( $removed_src );
    if (file_exists($stylesheet_file)) {
      //ファイルの編集時間バージョンを追加
      $src = add_query_arg( 'fver', date('Ymdhis', filemtime($stylesheet_file)), $src );
    }

  }
  return $src;
}
endif;

//gistのembed対応
wp_embed_register_handler( 'gist', '/https?:\/\/gist\.github\.com\/([a-z0-9]+)\/([a-z0-9]+)(#file=.*)?/i', 'wp_embed_register_handler_for_gist' );
if ( !function_exists( 'wp_embed_register_handler_for_gist' ) ):
function wp_embed_register_handler_for_gist( $matches, $attr, $url, $rawattr ) {
  $embed = sprintf(
    '<script src="https://gist.github.com/%1$s/%2$s.js"></script>',
    esc_attr( $matches[1] ),
    esc_attr( $matches[2] )
    );
  return apply_filters( 'embed_gist', $embed, $matches, $attr, $url, $rawattr );
}
endif;