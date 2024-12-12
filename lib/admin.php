<?php //管理画面関係の関数
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

//管理画面に読み込むリソースの設定
add_action('admin_print_styles', 'admin_print_styles_custom');
if ( !function_exists( 'admin_print_styles_custom' ) ):
function admin_print_styles_custom() {
  //管理者以外にJetpackメニューを表示しない
  if (!is_user_administrator()) {
    echo '<style>#toplevel_page_jetpack{display:none;}</style>';
  }
  if (is_admin_post_page() && !is_admin_editor_counter_visible()) {
    echo '<style>.editor-post-title::after{display:none !important;}</style>';
  }

  //JetPackの統計ページでない時
  //これをしないとなぜか以下のような不具合が出る
  //https://wp-cocoon.com/community/postid/76360/
  if (is_admin_jetpack_stats_page()) return;

  if (!is_screen_editor_page()) {
    //管理用スタイル
    wp_enqueue_style( 'admin-style', get_template_directory_uri().'/css/admin.css' );
  } else {
    //ブロックエディターページやクラシックエディターページ全体に適用されるCSS
    wp_enqueue_style( 'editor-page-style', get_template_directory_uri().'/css/editor-page.css' );
  }

  //Font Awesome
  wp_enqueue_style_font_awesome();

  //IcoMoon
  wp_enqueue_style_icomoon();

  //カラーピッカー
  wp_enqueue_style( 'wp-color-picker' );

  //メディアアップローダの javascript API
  wp_enqueue_media();

  ///////////////////////////////////
  //Swiper
  ///////////////////////////////////
  wp_enqueue_swiper();

  ///////////////////////////////////////
  // 管理画面でのJavaScript読み込み
  ///////////////////////////////////////
  //管理画面用での独自JavaScriptの読み込み
  wp_enqueue_script( 'admin-javascript', get_template_directory_uri() . '/js/admin-javascript.js', array(), false, true );

  //投稿ページの場合
  if (is_admin_post_page()) {
    //投稿時に記事公開確認ダイアログ処理
    wp_enqueue_confirmation_before_publish();
  }

  if (is_admin_php_page()) {
    //IcoMoonの呼び出し
    wp_enqueue_style_icomoon();

    //ソースコードハイライトリソースの読み込み
    wp_enqueue_highlight_js();
    //画像リンク拡大効果がLightboxのとき
    wp_enqueue_lightbox();
    //画像リンク拡大効果がLityのとき
    wp_enqueue_lity();
    //画像リンク拡大効果がbaguetteboxのとき
    wp_enqueue_baguettebox();
    //画像リンク拡大効果がspotlightのとき
    wp_enqueue_spotlight();
    //カルーセル用
    wp_enqueue_slick();
    //タイルカード
    wp_enqueue_jquery_masonry();

    //Google Fonts
    wp_enqueue_google_fonts();
  }
}
endif;

//メディアを挿入の初期表示を「この投稿へのアップロード」にする
// add_action( 'admin_footer-post-new.php', 'customize_initial_view_of_media_uploader' );
// add_action( 'admin_footer-post.php', 'customize_initial_view_of_media_uploader' );
if ( !function_exists( 'customize_initial_view_of_media_uploader' ) ):
function customize_initial_view_of_media_uploader() {
echo "<script type='text/javascript'>
 //<![CDATA[
jQuery(function($) {
    $('#wpcontent').ajaxSuccess(function() {
        $('select.attachment-filters [value=\"uploaded\"]').attr( 'selected', true ).parent().trigger('change');
    });
});
  //]]>
</script>";
}
endif;


//投稿記事一覧にアイキャッチ画像を表示
//カラムの挿入
add_filter( 'manage_posts_columns', 'customize_admin_manage_posts_columns' );
add_filter( 'manage_pages_columns', 'customize_admin_manage_posts_columns' );
if ( !function_exists( 'customize_admin_manage_posts_columns' ) ):
function customize_admin_manage_posts_columns($columns) {
  //作成者表示
  if (!is_admin_list_author_visible()) {
    unset($columns['author']);
  }

  //カテゴリー表示
  if (!is_admin_list_categories_visible()) {
    unset($columns['categories']);
  }

  //タグ表示
  if (!is_admin_list_tags_visible()) {
    unset($columns['tags']);
  }

  //コメント表示
  if (!is_admin_list_comments_visible()) {
    unset($columns['comments']);
  }

  //日付表示
  if (!is_admin_list_date_visible()) {
    unset($columns['date']);
  }

  //投稿ID表示
  if (is_admin_list_post_id_visible()) {
    $columns['post-id'] = __( 'ID', THEME_NAME );
  }

  //文字数表示
  if (is_admin_list_word_count_visible()) {
    $columns['word-count'] = __( '文字数', THEME_NAME );
  }

  //文字数表示
  if (is_admin_list_pv_visible()) {
    $columns['pv'] = __( 'PV', THEME_NAME );
  }

  //アイキャッチ表示
  if (is_admin_list_eyecatch_visible()) {
    $columns['thumbnail'] = __( 'アイキャッチ', THEME_NAME );
  }

  //メモ表示
  if (is_admin_list_memo_visible()) {
    $columns['memo'] = __( 'メモ', THEME_NAME );
  }

  //unset($columns['comments']);

  return $columns;
}
endif;


//管理画面の記事一覧テーブルにサムネイルの挿入
add_action( 'manage_posts_custom_column', 'customize_admin_add_column', 10, 2 );
add_action( 'manage_pages_custom_column', 'customize_admin_add_column', 10, 2 );
if ( !function_exists( 'customize_admin_add_column' ) ):
function customize_admin_add_column($column_name, $post_id) {
  //投稿ID
  if ( is_admin_list_post_id_visible() && ('post-id' === $column_name) ) {
    $thum = $post_id;
  }

  //文字数表示
  if ( is_admin_list_word_count_visible() && ('word-count' === $column_name) ) {
    //テーマで設定されているサムネイルを利用する場合
    $post = get_post($post_id);
    //_v($post);
    $title_count = mb_strlen(strip_tags($post->post_title));
    $content_count = mb_strlen(strip_tags($post->post_content));
    $digit = max(array(strlen($title_count), strlen($content_count)));
    //var_dump($digit);
    $thum =
    '<div class="word-count-wrap">'.
      '<div class="word-count-title">'.
        '<span class="word-count-title-label">'.
          __( '　題：', THEME_NAME ).
        '</span>'.
        '<span class="word-count-title-count">'.
          sprintf( '%s', $title_count ).
        '</span>'.
      '</div>'.
      '<div class="word-count-coutent">'.
        '<span class="word-count-coutent-label">'.
          __( '本文：', THEME_NAME ).
        '</span>'.
        '<span class="word-count-coutent-count">'.
          sprintf( '%s', $content_count ).
        '</span>'.
      '</div>'.
    '</div>';
  }

  //PV表示
  if ( is_admin_list_pv_visible() && ('pv' === $column_name) ) {
    //テーマで設定されているサムネイルを利用する場合
    $post = get_post($post_id);
    //_v($post);
    $title_count = mb_strlen(strip_tags($post->post_title));
    $content_count = mb_strlen(strip_tags($post->post_content));
    $digit = max(array(strlen($title_count), strlen($content_count)));
    //var_dump($digit);
    $thum =
    '<div class="pv-wrap">'.
      '<div class="pv-title">'.
        '<span class="pv-title-label">'.
          __( '日：', THEME_NAME ).
        '</span>'.
        '<span class="pv-title-count">'.
          get_todays_pv().
        '</span>'.
      '</div>'.
      '<div class="pv-title">'.
        '<span class="pv-title-label">'.
          __( '週：', THEME_NAME ).
        '</span>'.
        '<span class="pv-title-count">'.
          get_last_7days_pv().
        '</span>'.
      '</div>'.
      '<div class="pv-title">'.
        '<span class="pv-title-label">'.
          __( '月：', THEME_NAME ).
        '</span>'.
        '<span class="pv-title-count">'.
          get_last_30days_pv().
        '</span>'.
      '</div>'.
      '<div class="pv-title">'.
        '<span class="pv-title-label">'.
          __( '全：', THEME_NAME ).
        '</span>'.
        '<span class="pv-title-count">'.
          get_all_pv().
        '</span>'.
      '</div>'.
    '</div>';
  }

  //アイキャッチ表示
  if ( is_admin_list_eyecatch_visible() && ('thumbnail' === $column_name) ) {
    //テーマで設定されているサムネイルを利用する場合
    $thum = get_the_post_thumbnail($post_id, THUMB150, array( 'style' => 'width:75px;height:auto;' ));
  }

  //メモ表示
  if ( is_admin_list_memo_visible() && ('memo' === $column_name) ) {
    //テーマで設定されているサムネイルを利用する場合
    $thum = htmlspecialchars(get_the_page_memo($post_id));
  }

  if ( isset($thum) && $thum ) {
    echo $thum;
  }
}
endif;

//投稿・固定ページ管理画面の記事一覧テーブルにIDソートを可能にする
add_filter( 'manage_edit-post_sortable_columns', 'sort_post_columns' );//投稿
add_filter( 'manage_edit-page_sortable_columns', 'sort_post_columns' );//固定ページ
if ( !function_exists( 'sort_post_columns' ) ):
function sort_post_columns($columns) {
  $columns['post-id'] = 'ID';
  return $columns;
}
endif;

//カテゴリー・タグ管理画面にIDカラムを設置する
add_filter('manage_edit-category_columns', 'add_taxonomy_columns');
add_filter('manage_edit-post_tag_columns', 'add_taxonomy_columns');
if ( !function_exists( 'add_taxonomy_columns' ) ):
function add_taxonomy_columns($columns){
  $index = 4; // 追加位置

  return array_merge(
    array_slice($columns, 0, $index),
    array('id' => 'ID'),
    array_slice($columns, $index)
  );
}
endif;

//カテゴリー・タグ管理画面にIDを表示
add_action('manage_category_custom_column', 'add_taxonomy_custom_fields', 10, 3);
add_action('manage_post_tag_custom_column', 'add_taxonomy_custom_fields', 10, 3);
if ( !function_exists( 'add_taxonomy_custom_fields' ) ):
function add_taxonomy_custom_fields($content, $column_name, $term_id){
  if ( 'id' === $column_name ) {
    $content = $term_id;
  }
  return $content;
}
endif;

//カテゴリー・タグ管理画面にIDソートを可能にする
add_filter( 'manage_edit-category_sortable_columns', 'sort_term_columns' );
add_filter( 'manage_edit-post_tag_sortable_columns', 'sort_term_columns' );
if ( !function_exists( 'sort_term_columns' ) ):
function sort_term_columns($columns) {
  $columns['id'] = 'ID';
  return $columns;
}
endif;




//パターン管理画面にIDカラムを設置する
add_filter('manage_edit-wp_block_columns', 'add_wp_block_columns');
if ( !function_exists( 'add_wp_block_columns' ) ):
function add_wp_block_columns($columns){
  $index = 5; // 追加位置

  return array_merge(
    array_slice($columns, 0, $index),
    array('shortcode' => __( 'ショートコード', THEME_NAME )),
    array_slice($columns, $index)
  );
}
endif;

//ショートコード管理画面にIDを表示
add_action('manage_wp_block_posts_custom_column', 'add_wp_block_custom_fields', 10, 2);
if ( !function_exists( 'add_wp_block_custom_fields' ) ):
function add_wp_block_custom_fields($column_name, $term_id){
  if ( 'shortcode' === $column_name ) {
    $thum = '<input type="text" size="16" value="[pattern id=&quot;'.esc_attr($term_id).'&quot;]">';
  }

  if ( isset($thum) && $thum ) {
    echo $thum;
  }
}
endif;

//管理ツールバーにメニュー追加
if (is_admin_tool_menu_visible() && is_user_administrator()) {
  add_action('admin_bar_menu', 'customize_admin_bar_menu', 9999);
}
if ( !function_exists( 'customize_admin_bar_menu' ) ):
function customize_admin_bar_menu($wp_admin_bar){
  //バーにメニューを追加
  $title = sprintf(
    '<span class="ab-label">%s</span>',
    __( '管理メニュー', THEME_NAME )//親メニューラベル
  );
  $wp_admin_bar->add_menu(array(
    'id'  => 'dashboard_menu',
    'meta'  => array(),
    'title' => $title
  ));
  //サブメニューを追加
  $wp_admin_bar->add_menu(array(
    'parent' => 'dashboard_menu', // 親メニューID
    'id'   => 'dashboard_menu-dashboard', // 子メニューID
    'meta'   => array(),
    'title'  => __( 'ダッシュボード', THEME_NAME ), // ラベル
    'href'   => admin_url() // ページURL
  ));
  $wp_admin_bar->add_menu(array(
    'parent' => 'dashboard_menu', // 親メニューID
    'id'   => 'dashboard_menu-singles', // 子メニューID
    'meta'   => array(),
    'title'  => __( '投稿一覧', THEME_NAME ), // ラベル
    'href'   => admin_url('edit.php') // ページURL
  ));
  $wp_admin_bar->add_menu(array(
    'parent' => 'dashboard_menu', // 親メニューID
    'id'   => 'dashboard_menu-pages', // 子メニューID
    'meta'   => array(),
    'title'  => __( '固定ページ一覧', THEME_NAME ), // ラベル
    'href'   => admin_url('edit.php?post_type=page') // ページURL
  ));
  $wp_admin_bar->add_menu(array(
    'parent' => 'dashboard_menu', // 親メニューID
    'id'   => 'dashboard_menu-medias', // 子メニューID
    'meta'   => array(),
    'title'  => __( 'メディア一覧', THEME_NAME ), // ラベル
    'href'   => admin_url('upload.php') // ページURL
  ));
  $wp_admin_bar->add_menu(array(
    'parent' => 'dashboard_menu', // 親メニューID
    'id'   => 'dashboard_menu-themes', // 子メニューID
    'meta'   => array(),
    'title'  => __( 'テーマ', THEME_NAME ), // ラベル
    'href'   => admin_url('themes.php') // ページURL
  ));
  if (!is_classicpress()) {
    $wp_admin_bar->add_menu(array(
      'parent' => 'dashboard_menu', // 親メニューID
      'id'   => 'dashboard_menu-patterns', // 子メニューID
      'meta'   => array(),
      'title'  => __( 'パターン一覧', THEME_NAME ), // ラベル
      'href'   => admin_url('edit.php?post_type=wp_block') // ページURL
    ));
  }
  $wp_admin_bar->add_menu(array(
    'parent' => 'dashboard_menu', // 親メニューID
    'id'   => 'dashboard_menu-customize', // 子メニューID
    'meta'   => array(),
    'title'  => __( 'カスタマイズ', THEME_NAME ), // ラベル
    'href'   => admin_url('customize.php?return=' . esc_url(admin_url('themes.php'))) // ページURL
  ));
  $wp_admin_bar->add_menu(array(
    'parent' => 'dashboard_menu', // 親メニューID
    'id'   => 'dashboard_menu-widget', // 子メニューID
    'meta'   => array(),
    'title'  => __( 'ウィジェット', THEME_NAME ), // ラベル
    'href'   => admin_url('widgets.php') // ページURL
  ));
  $wp_admin_bar->add_menu(array(
    'parent' => 'dashboard_menu', // 親メニューID
    'id'   => 'dashboard_menu-nav-menus', // 子メニューID
    'meta'   => array(),
    'title'  => __( 'メニュー', THEME_NAME ), // ラベル
    'href'   => admin_url('nav-menus.php') // ページURL
  ));
  $wp_admin_bar->add_menu(array(
    'parent' => 'dashboard_menu', // 親メニューID
    'id'   => 'dashboard_menu-theme-editor', // 子メニューID
    'meta'   => array(),
    'title'  => __( 'テーマの編集', THEME_NAME ), // ラベル
    'href'   => admin_url('theme-editor.php') // ページURL
  ));
  $wp_admin_bar->add_menu(array(
    'parent' => 'dashboard_menu', // 親メニューID
    'id'   => 'dashboard_menu-plugins', // 子メニューID
    'meta'   => array(),
    'title'  => __( 'プラグイン一覧', THEME_NAME ), // ラベル
    'href'   => admin_url('plugins.php') // ページURL
  ));
  if (current_user_can('manage_options')) {//管理者権限がある場合
    $wp_admin_bar->add_menu(array(
      'parent' => 'dashboard_menu', // 親メニューID
      'id'   => 'dashboard_menu-theme-settings', // 子メニューID
      'meta'   => array(),
      'title'  => __( 'Cocoon 設定', THEME_NAME ), // ラベル
      'href'   => admin_url('admin.php?page=theme-settings') // ページURL
    ));
    $wp_admin_bar->add_menu(array(
      'parent' => 'dashboard_menu', // 親メニューID
      'id'   => 'dashboard_menu-speech-balloon', // 子メニューID
      'meta'   => array(),
      'title'  => __( '吹き出し', THEME_NAME ), // ラベル
      'href'   => admin_url('admin.php?page=speech-balloon') // ページURL
    ));
    $wp_admin_bar->add_menu(array(
      'parent' => 'dashboard_menu', // 親メニューID
      'id'   => 'dashboard_menu-theme-func-text', // 子メニューID
      'meta'   => array(),
      'title'  => __( 'テンプレート', THEME_NAME ), // ラベル
      'href'   => admin_url('admin.php?page=theme-func-text') // ページURL
    ));
    $wp_admin_bar->add_menu(array(
      'parent' => 'dashboard_menu', // 親メニューID
      'id'   => 'dashboard_menu-theme-affiliate-tag', // 子メニューID
      'meta'   => array(),
      'title'  => __( 'アフィリエイトタグ', THEME_NAME ), // ラベル
      'href'   => admin_url('admin.php?page=theme-affiliate-tag') // ページURL
    ));
    $wp_admin_bar->add_menu(array(
      'parent' => 'dashboard_menu', // 親メニューID
      'id'   => 'dashboard_menu-theme-ranking', // 子メニューID
      'meta'   => array(),
      'title'  => __( 'ランキング', THEME_NAME ), // ラベル
      'href'   => admin_url('admin.php?page=theme-ranking') // ページURL
    ));
    $wp_admin_bar->add_menu(array(
      'parent' => 'dashboard_menu', // 親メニューID
      'id'   => 'dashboard_menu-theme-access', // 子メニューID
      'meta'   => array(),
      'title'  => __( 'アクセス集計', THEME_NAME ), // ラベル
      'href'   => admin_url('admin.php?page=theme-access') // ページURL
    ));
    $wp_admin_bar->add_menu(array(
      'parent' => 'dashboard_menu', // 親メニューID
      'id'   => 'dashboard_menu-theme-speed-up', // 子メニューID
      'meta'   => array(),
      'title'  => __( '高速化', THEME_NAME ), // ラベル
      'href'   => admin_url('admin.php?page=theme-speed-up') // ページURL
    ));
    $wp_admin_bar->add_menu(array(
      'parent' => 'dashboard_menu', // 親メニューID
      'id'   => 'dashboard_menu-theme-backup', // 子メニューID
      'meta'   => array(),
      'title'  => __( 'バックアップ', THEME_NAME ), // ラベル
      'href'   => admin_url('admin.php?page=theme-backup') // ページURL
    ));
    $wp_admin_bar->add_menu(array(
      'parent' => 'dashboard_menu', // 親メニューID
      'id'   => 'dashboard_menu-theme-cache', // 子メニューID
      'meta'   => array(),
      'title'  => __( 'キャッシュ削除', THEME_NAME ), // ラベル
      'href'   => admin_url('admin.php?page=theme-cache') // ページURL
    ));
  }
}
endif;


// //記事公開前に確認アラートを出す
// if (is_confirmation_before_publish()) {
//   add_action('admin_print_scripts', 'publish_confirm_admin_print_scripts');
// }
// if ( !function_exists( 'publish_confirm_admin_print_scripts' ) ):
// function publish_confirm_admin_print_scripts() {
//   $post_text = __( '公開', THEME_NAME );
//   $confirm_text = __( '記事を公開してもよろしいですか？', THEME_NAME );
//   echo <<< EOM
// <script type="text/javascript">
// //console.log("id");
// window.onload = function() {
//   var id = document.getElementById('publish');
//   if (id.value.indexOf("$post_text", 0) != -1) {
//     id.onclick = publish_confirm;
//   }
// }
// function publish_confirm() {
//   if (window.confirm("$confirm_text")) {
//     return true;
//   } else {
//     var elements = document.getElementsByTagName('span');
//     for (var i = 0; i < elements.length; i++) {
//       var element = elements[i];
//       if (element.className.indexOf("spinner", 0) != -1) {
//         element.classList.remove('spinner');
//       }
//     }
//     document.getElementById('publish').classList.remove('button-primary-disabled');
//     document.getElementById('save-post').classList.remove('button-disabled');

//       return false;
//   }
// }
// </script>
// EOM;
// }
// endif;

//投稿一覧リストの上にタグフィルターと管理者フィルターを追加する
add_action('restrict_manage_posts', 'custmuize_restrict_manage_posts');
if ( !function_exists( 'custmuize_restrict_manage_posts' ) ):
function custmuize_restrict_manage_posts(){
  global $post_type, $tag;
  if (($post_type == 'post') || ($post_type == 'page')) {
    if ( is_object_in_taxonomy( $post_type, 'post_tag' ) ) {
      $dropdown_options = array(
        'show_option_all' => get_taxonomy( 'post_tag' )->labels->all_items,
        'hide_empty' => 0,
        'hierarchical' => 1,
        'show_count' => 0,
        'orderby' => 'name',
        'selected' => $tag,
        'name' => 'tag',
        'taxonomy' => 'post_tag',
        'value_field' => 'slug'
      );
      wp_dropdown_categories( $dropdown_options );
      }

      wp_dropdown_users(
      array(
        'show_option_all' => 'すべてのユーザー',
        'name' => 'author'
      )
    );
  }

}
endif;

//投稿一覧で「全てのタグ」選択時は$_GET['tag']をセットしない
add_action('load-edit.php', 'custmuize_load_edit_php');
if ( !function_exists( 'custmuize_load_edit_php' ) ):
function custmuize_load_edit_php(){
  if (isset($_GET['tag']) && '0' === $_GET['tag']) {
  unset ($_GET['tag']);
  }
}
endif;

//テーマ変更時にテーマカスタマイザーCSSファイルの書き出し
add_action( 'after_switch_theme', function() {
  put_theme_css_cache_file();
});

//投稿管理画面のヘッダーカスタマイズ
add_action( 'admin_head-post-new.php', 'add_head_post_custum' );
add_action( 'admin_head-post.php', 'add_head_post_custum' );
add_action( 'admin_head-widgets.php', 'add_head_post_custum' );
if ( !function_exists( 'add_head_post_custum' ) ):
function add_head_post_custum() {
  //カスタムCSSスタイルが反映されていない場合は反映させる
  define('OP_LAST_OUTPUT_EDITOR_CSS_DAY', 'last_output_editor_css_day');
  $day = get_theme_mod(OP_LAST_OUTPUT_EDITOR_CSS_DAY);
  $now = date('Y-m-d H:i:s');
  if (!$day || strtotime($day) < strtotime('-7 day') ) {
    set_theme_mod(OP_LAST_OUTPUT_EDITOR_CSS_DAY, $now);
    // エディター用のカスタマイズCSS出力
    put_theme_css_cache_file();
    // var_dump(strtotime($day));
    // var_dump(strtotime('-7 day'));
    // var_dump($day);
    // var_dump(date('Y-m-d H:i:s', strtotime('-7 day')));
    // var_dump($now);
  }
?>
<style>
div.editor-block-list__block,
div.editor-block-list__block p{
  <?php //フォントサイズ
  if(get_site_font_size()): ?>
    font-size: <?php echo get_site_font_size(); ?>;
  <?php endif; ?>

  <?php //フォントファミリー
  if(get_site_font_family()): ?>
    font-family: <?php echo get_site_font_family(); ?>;
  <?php endif; ?>

  <?php //文字の太さ
  if(get_site_font_weight()): ?>
    font-weight: <?php echo get_site_font_weight(); ?>;
  <?php endif; ?>
}
</style>
<script type="text/javascript">
jQuery(function($) {
  function zenToHan(text) {
  return text.replace(/[Ａ-Ｚａ-ｚ０-９]/g, function(s) {
      return String.fromCharCode(s.charCodeAt(0) - 65248);
    });
  }

  function catFilter( header, list ) {
  var form  = $('<form>').attr({'class':'filterform', 'action':'#'}).css({'position':'absolute', 'top':'38px'}),
    input = $('<input>').attr({'class':'filterinput', 'type':'text', 'placeholder':'<?php _e( 'カテゴリー検索', THEME_NAME ) ?>' });
  $(form).append(input).appendTo(header);
  $(header).css({'padding-top':'42px'});
  $(input).change(function() {
    var filter = $(this).val();
    filter = zenToHan(filter).toLowerCase();
    if( filter ) {
    //ラベルテキストから検索文字の見つからなかった場合は非表示
    $(list).find('label').filter(
      function (index) {
      var labelText = zenToHan($(this).text()).toLowerCase();
      return labelText.indexOf(filter) == -1;
      }
    ).hide();
    //ラベルテキストから検索文字の見つかった場合は表示
    $(list).find('label').filter(
      function (index) {
      var labelText = zenToHan($(this).text()).toLowerCase();
      return labelText.indexOf(filter) != -1;
      }
    ).show();
    } else {
    $(list).find('label').show();
    }
    return false;
  })
  .keyup(function() {
    $(this).change();
  });
  }

  $(function() {
  catFilter( $('#category-all'), $('#categorychecklist') );
  });
});
</script>
<?php
}
endif;

//デフォルトの抜粋入力欄をビジュアルエディターにする
// add_action( 'add_meta_boxes', array ( 'VisualEditorExcerpt', 'switch_boxes' ) );
if ( !class_exists( 'VisualEditorExcerpt' ) ):
class VisualEditorExcerpt{
  public static function switch_boxes()
  {
    if ( isset($GLOBALS['post']) && ! post_type_supports( $GLOBALS['post']->post_type, 'excerpt' ) )    {
      return;
    }
    $current_screen = get_current_screen();
    if ( ( method_exists( $current_screen, 'is_block_editor' ) && $current_screen->is_block_editor() ) || ( function_exists( 'is_gutenberg_page' ) && is_gutenberg_page() ) ) {
    } else {
      remove_meta_box(
        'postexcerpt' // ID
      ,   ''      // スクリーン、空だと全ての投稿タイプをサポート
      ,   'normal'    // コンテキスト
      );

      add_meta_box(
        'postexcerpt2'   // Reusing just 'postexcerpt' doesn't work.
      ,   __( 'Excerpt' )  // タイトル
      ,   array ( __CLASS__, 'show' ) // 表示関数
      ,   null          // スクリーン
      ,   'normal'      // コンテキスト
      ,   'core'        // 優先度
      );
    }
  }


  //メタボックスの出力
  public static function show( $post )  {
  ?>
    <label class="screen-reader-text" for="excerpt"><?php
    _e( 'Excerpt' )
    ?></label>
    <?php
    //デフォルト名の'excerpt'を使用
    wp_editor(
      self::unescape( $post->post_excerpt ),
      'excerpt',
      array (
      'textarea_rows' => 15
    ,   'media_buttons' => false
    ,   'teeny'     => true
    ,   'tinymce'     => true
      )
    );
  }

  //エスケープ解除
  public static function unescape( $str )  {
    return str_replace(
      array ( '&lt;', '&gt;', '&quot;', '&amp;', '&nbsp;', '&amp;nbsp;' )
    ,   array ( '<',  '>',  '"',    '&',   ' ', ' ' )
    ,   $str
    );
  }
}
endif;



//テーマの編集機能で編集できるファイルを追加する wp4.4以降
add_filter('wp_theme_editor_filetypes', 'wp_theme_editor_filetypes_custom');
if ( !function_exists( 'wp_theme_editor_filetypes_custom' ) ):
function wp_theme_editor_filetypes_custom($default_types){
  $default_types[] = 'js';
  $default_types[] = 'scss';
  return $default_types;
}
endif;


//ビジュアルエディターのクラス名に任意のclassを追加する
//add_filter( 'teeny_mce_before_init', 'abc' );
add_filter( 'tiny_mce_before_init', 'tiny_mce_before_init_custom' );
if ( !function_exists( 'tiny_mce_before_init_custom' ) ):
function tiny_mce_before_init_custom( $mceInit ) {
  //var_dump($mceInit );

  //旧ビジュアルエディター
  if (isset($mceInit['body_class'])) {
    //if (!is_plugin_fourm_page()) {
      $fa_class = ' '.get_site_icon_font_class();
      $mceInit['body_class'] .= ' body main article'.$fa_class.get_editor_page_type_class();
    //}

    if (is_admin()) {
      $mceInit['body_class'] .= ' admin-page';
    } else {
      $mceInit['body_class'] .= ' public-page';
    }


    if (get_site_font_family_class()) {
      $mceInit['body_class'] .= ' '.get_site_font_family_class();
    }
    if (get_site_font_size_class()) {
      $mceInit['body_class'] .= ' '.get_site_font_size_class();
    }
    if (get_site_font_weight_class()) {
      $mceInit['body_class'] .= ' '.get_site_font_weight_class();
    }
  }


  return $mceInit;
}
endif;

//無害化したプレビューのテンプレートファイル呼び出し
if ( !function_exists( 'get_sanitize_preview_template_part' ) ):
function get_sanitize_preview_template_part($slug, $name = null){
  restore_global_skin_theme_options();
  // global $_THEME_OPTIONS;
  // _v($_THEME_OPTIONS);
  ob_start();
  cocoon_template_part($slug, $name);
  $tag = ob_get_clean();
  $tag = preg_replace('{<form.+?</form>}is', '', $tag);
  $tag = change_fa($tag);
  echo $tag;
  clear_global_skin_theme_options();
}
endif;

// テーマが指定するプレビュー画面かどうか
// if ( !function_exists( 'is_cocoon_settings_preview' ) ):
// function is_cocoon_settings_preview(){
//   return isset($_GET['preview']) && $_GET['preview'] == 'theme-settings';
// }
// endif;

//グーテンベルグとクラシックエディターのタグをチェックリストボックス形式にする
//参考：https://nldot.info/how-to-change-the-tags-to-checkbox-in-gutenberg/
if (is_editor_tag_check_list_enable()) {
  add_action( 'init', 'register_tag_check_list', 1 );
}
if ( !function_exists( 'register_tag_check_list' ) ):
function register_tag_check_list() {
  $tag_slug_args = get_taxonomy('post_tag'); // returns an object
  $tag_slug_args->hierarchical = true;
  $tag_slug_args->meta_box_cb = 'post_categories_meta_box';

  register_taxonomy( 'post_tag', 'post',(array) $tag_slug_args);
}
endif;

//管理画面のbodyタグのクラスに挿入
add_filter('admin_body_class', 'add_custom_admin_body_class');
function add_custom_admin_body_class($classes) {
  $classes .= ' classicpress';
  return $classes;
}
