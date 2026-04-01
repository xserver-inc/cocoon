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
    wp_enqueue_style( 'admin-style', get_cocoon_template_directory_uri().'/css/admin.css' );
  } else {
    //ブロックエディターページやクラシックエディターページ全体に適用されるCSS
    wp_enqueue_style( 'editor-page-style', get_cocoon_template_directory_uri().'/css/editor-page.css' );
  }

  //Font Awesome
  wp_enqueue_style_font_awesome();

  //IcoMoon
  wp_enqueue_style_icomoon();

  //カラーピッカー
  wp_enqueue_style( 'wp-color-picker' );

  //メディアアップローダの javascript API
  wp_enqueue_media();

  // ///////////////////////////////////
  // //Swiper
  // ///////////////////////////////////
  // wp_enqueue_swiper();

  ///////////////////////////////////////
  // 管理画面でのJavaScript読み込み
  ///////////////////////////////////////
  //管理画面用での独自JavaScriptの読み込み
  wp_enqueue_script( 'admin-javascript', get_cocoon_template_directory_uri() . '/js/admin-javascript.js', array(), false, true );

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
  // 現在の画面オブジェクトを取得
  $current_screen = get_current_screen();
  $is_block_screen = ($current_screen && $current_screen->id === 'edit-wp_block');

  // WordPress標準カラムを削除
  $remove_columns = [
    'author'     => !is_admin_list_author_visible(),
    'categories' => !is_admin_list_categories_visible(),
    'tags'       => !is_admin_list_tags_visible(),
    'comments'   => !is_admin_list_comments_visible(),
    'date'       => !is_admin_list_date_visible(),
  ];

  foreach ($remove_columns as $key => $condition) {
    if ($condition && isset($columns[$key])) {
      unset($columns[$key]);
    }
  }

  // 独自カラムを追加
  $add_columns = [
    'post-id'    => ['label' => __( 'ID', THEME_NAME ),           'condition' => is_admin_list_post_id_visible()],
    'word-count' => ['label' => __( '文字数', THEME_NAME ),       'condition' => is_admin_list_word_count_visible()],
    'pv'         => ['label' => __( 'PV', THEME_NAME ),           'condition' => is_admin_list_pv_visible() && !$is_block_screen],
    'thumbnail'  => ['label' => __( 'アイキャッチ', THEME_NAME ), 'condition' => is_admin_list_eyecatch_visible() && !$is_block_screen],
    'memo'       => ['label' => __( 'メモ', THEME_NAME ),         'condition' => is_admin_list_memo_visible() && !$is_block_screen],
  ];

  foreach ($add_columns as $key => $data) {
    if (!empty($data['condition'])) {
      $columns[$key] = $data['label'];
    }
  }

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
    $title_count = mb_strlen(strip_tags($post->post_title));
    $content_count = get_post_content_word_count($post->post_content);
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

/**
 * パターン管理画面のカテゴリーカラムをソート可能にする
 */
add_filter( 'manage_edit-wp_block_sortable_columns', 'customize_wp_block_sortable_columns' );
if ( !function_exists( 'customize_wp_block_sortable_columns' ) ):
function customize_wp_block_sortable_columns( $columns ) {
  $columns['taxonomy-wp_pattern_category'] = 'pattern_category';
  return $columns;
}
endif;

/**
 * パターン管理画面のカテゴリーソート処理
 */
add_action( 'pre_get_posts', 'customize_wp_block_category_sort' );
if ( !function_exists( 'customize_wp_block_category_sort' ) ):
function customize_wp_block_category_sort( $query ) {
  if ( !is_admin() || !$query->is_main_query() ) {
    return;
  }

  // 対象のポストタイプとソートキーを確認
  if ( $query->get( 'post_type' ) === 'wp_block' && $query->get( 'orderby' ) === 'pattern_category' ) {
    add_filter( 'posts_clauses', 'customize_wp_block_category_sort_clauses', 10, 2 );
  }
}
endif;

/**
 * カテゴリー名でソートするためのSQLクエリを構築する
 */
if ( !function_exists( 'customize_wp_block_category_sort_clauses' ) ):
function customize_wp_block_category_sort_clauses( $clauses, $query ) {
  global $wpdb;
  // ソート順を取得
  $order = strtoupper( $query->get( 'order' ) ) === 'ASC' ? 'ASC' : 'DESC';
  // カテゴリーテーブルをジョイン
  $clauses['join'] .= " LEFT JOIN {$wpdb->term_relationships} AS tr ON ({$wpdb->posts}.ID = tr.object_id) LEFT JOIN {$wpdb->term_taxonomy} AS tt ON (tr.term_taxonomy_id = tt.term_taxonomy_id AND tt.taxonomy = 'wp_pattern_category') LEFT JOIN {$wpdb->terms} AS t ON (tt.term_id = t.term_id) ";
  // ソート順序を指定
  $clauses['orderby'] = "t.name {$order}";
  // グループ化して重複を避ける
  $clauses['groupby'] = "{$wpdb->posts}.ID";
  return $clauses;
}
endif;


//管理ツールバーにメニュー追加
if (is_admin_tool_menu_visible() && is_user_administrator()) {
  add_action('admin_bar_menu', 'customize_admin_bar_menu', 9999);
}
if ( !function_exists( 'customize_admin_bar_menu' ) ):
function customize_admin_bar_menu($wp_admin_bar){
  // 管理メニューを取得
  $menus = get_admin_bar_menu_array($wp_admin_bar);

  // 管理メニューを追加
  foreach ($menus as $menu) {
    if ($menu) {
      $wp_admin_bar->add_menu($menu);
    }
  }
}
endif;

// 管理メニュー配列
if ( !function_exists( 'get_admin_bar_menu_array' ) ):
function get_admin_bar_menu_array($wp_admin_bar = null) {
  $title = '<span class="ab-label">' . __('管理メニュー', THEME_NAME) . '</span>';
  $menus = [
    // 親メニュー
    ['id' => 'dashboard_menu', 'meta' => [] ,'title' => $title],

    // 子メニュー
    ['parent' => 'dashboard_menu', 'id' => 'dashboard_menu-dashboard'   , 'meta' => [], 'title' => __('ダッシュボード', THEME_NAME)     , 'href' => admin_url()],
    ['parent' => 'dashboard_menu', 'id' => 'dashboard_menu-singles'     , 'meta' => [], 'title' => __('投稿一覧', THEME_NAME)             , 'href' => admin_url('edit.php')],
    ['parent' => 'dashboard_menu', 'id' => 'dashboard_menu-pages'       , 'meta' => [], 'title' => __('固定ページ一覧', THEME_NAME)       , 'href' => admin_url('edit.php?post_type=page')],
    ['parent' => 'dashboard_menu', 'id' => 'dashboard_menu-medias'      , 'meta' => [], 'title' => __('メディア一覧', THEME_NAME)         , 'href' => admin_url('upload.php')],
    ['parent' => 'dashboard_menu', 'id' => 'dashboard_menu-themes'      , 'meta' => [], 'title' => __('テーマ', THEME_NAME)               , 'href' => admin_url('themes.php')],
  ];

  // パターン一覧
  if (!is_classicpress()) {
    $menus[] = ['parent' => 'dashboard_menu', 'id' => 'dashboard_menu-patterns', 'meta' => [], 'title' => __('パターン一覧', THEME_NAME), 'href' => admin_url('edit.php?post_type=wp_block')];
  }

  // 残りの子メニュー
  $menus = array_merge($menus, [
    ['parent' => 'dashboard_menu', 'id' => 'dashboard_menu-customize'   , 'meta' => [], 'title' => __('カスタマイズ', THEME_NAME)         , 'href' => admin_url('customize.php?return=' . esc_url(admin_url('themes.php')))],
    ['parent' => 'dashboard_menu', 'id' => 'dashboard_menu-widget'      , 'meta' => [], 'title' => __('ウィジェット', THEME_NAME)         , 'href' => admin_url('widgets.php')],
    ['parent' => 'dashboard_menu', 'id' => 'dashboard_menu-nav-menus'   , 'meta' => [], 'title' => __('メニュー', THEME_NAME)             , 'href' => admin_url('nav-menus.php')],
    ['parent' => 'dashboard_menu', 'id' => 'dashboard_menu-theme-editor', 'meta' => [], 'title' => __('テーマの編集', THEME_NAME)         , 'href' => admin_url('theme-editor.php')],
    ['parent' => 'dashboard_menu', 'id' => 'dashboard_menu-plugins'     , 'meta' => [], 'title' => __('プラグイン一覧', THEME_NAME)       , 'href' => admin_url('plugins.php')],
  ]);

  // 管理者権限があれば追加
  if (current_user_can('manage_options')) {
    $menus = array_merge($menus, [
      ['parent' => 'dashboard_menu', 'id' => 'dashboard_menu-theme-settings'    , 'meta' => [], 'title' => __('Cocoon 設定', THEME_NAME)        , 'href' => admin_url('admin.php?page=theme-settings')],
      ['parent' => 'dashboard_menu', 'id' => 'dashboard_menu-speech-balloon'    , 'meta' => [], 'title' => __('吹き出し', THEME_NAME)           , 'href' => admin_url('admin.php?page=speech-balloon')],
      ['parent' => 'dashboard_menu', 'id' => 'dashboard_menu-theme-func-text'   , 'meta' => [], 'title' => __('テンプレート', THEME_NAME)       , 'href' => admin_url('admin.php?page=theme-func-text')],
      ['parent' => 'dashboard_menu', 'id' => 'dashboard_menu-theme-affiliate-tag', 'meta' => [], 'title' => __('アフィリエイトタグ', THEME_NAME) , 'href' => admin_url('admin.php?page=theme-affiliate-tag')],
      ['parent' => 'dashboard_menu', 'id' => 'dashboard_menu-theme-ranking'     , 'meta' => [], 'title' => __('ランキング', THEME_NAME)         , 'href' => admin_url('admin.php?page=theme-ranking')],
      ['parent' => 'dashboard_menu', 'id' => 'dashboard_menu-theme-access'      , 'meta' => [], 'title' => __('アクセス集計', THEME_NAME)       , 'href' => admin_url('admin.php?page=theme-access')],
      ['parent' => 'dashboard_menu', 'id' => 'dashboard_menu-theme-speed-up'    , 'meta' => [], 'title' => __('高速化', THEME_NAME)             , 'href' => admin_url('admin.php?page=theme-speed-up')],
      ['parent' => 'dashboard_menu', 'id' => 'dashboard_menu-theme-backup'      , 'meta' => [], 'title' => __('バックアップ', THEME_NAME)       , 'href' => admin_url('admin.php?page=theme-backup')],
      ['parent' => 'dashboard_menu', 'id' => 'dashboard_menu-theme-cache'       , 'meta' => [], 'title' => __('キャッシュ削除', THEME_NAME)     , 'href' => admin_url('admin.php?page=theme-cache')],
    ]);
  }

  return apply_filters('cocoon_admin_bar_menus', $menus, $wp_admin_bar);
}
endif;


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

// ブロックエディター用フォント設定（iframe 内に注入）
// WP 6.3+ では enqueue_block_assets が iframe 内で実行されるため、
// admin_head で div.editor-block-list__block を対象にしていた旧方式を置き換える
add_action( 'enqueue_block_assets', 'cocoon_enqueue_editor_font_styles' );
if ( !function_exists( 'cocoon_enqueue_editor_font_styles' ) ):
function cocoon_enqueue_editor_font_styles() {
  // 管理画面以外では実行しない
  if ( !is_admin() ) return;

  // フォントファミリーのキー文字列から CSS の font-family 値への変換マップ
  // _font.scss のクラス定義と対応させる
  $font_family_map = array(
    'meiryo'             => 'Meiryo, "Hiragino Kaku Gothic ProN", "Hiragino Sans", sans-serif',
    'yu_gothic'          => 'YuGothic, "Yu Gothic", Meiryo, "Hiragino Kaku Gothic ProN", "Hiragino Sans", sans-serif',
    'ms_pgothic'         => '"MS PGothic", "Hiragino Kaku Gothic ProN", "Hiragino Sans", Meiryo, sans-serif',
    'noto_sans_jp'       => '"Noto Sans JP", sans-serif',
    'noto_serif_jp'      => '"Noto Serif JP", sans-serif',
    'mplus_1p'           => '"M PLUS 1p", sans-serif',
    'rounded_mplus_1c'   => '"M PLUS Rounded 1c", sans-serif',
    'kosugi'             => '"Kosugi", sans-serif',
    'kosugi_maru'        => '"Kosugi Maru", sans-serif',
    'sawarabi_gothic'    => '"Sawarabi Gothic", sans-serif',
    'sawarabi_mincho'    => '"Sawarabi Mincho", sans-serif',
    'noto_sans_korean'   => '"Noto Sans KR", sans-serif',
    'pretendard'         => '"Pretendard Variable", Pretendard, -apple-system, BlinkMacSystemFont, system-ui, Roboto, "Helvetica Neue", "Segoe UI", "Apple SD Gothic Neo", "Noto Sans KR", "Malgun Gothic", sans-serif',
    'microsoft_jhenghei' => '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Microsoft JhengHei", sans-serif',
    'noto_sans_tc'       => '"Noto Sans TC", sans-serif',
    // hiragino はブラウザデフォルトと同等なのでマップに含めない（空の扱い）
  );

  // フォント設定値を配列に集める
  $rules = array();
  if ( get_site_font_size() ) {
    $rules[] = 'font-size: ' . esc_attr( get_site_font_size() ) . ';';
  }
  // フォントファミリーはキー値を CSS の font-family 値に変換して出力する
  $font_family_key = get_site_font_family();
  if ( $font_family_key && isset( $font_family_map[ $font_family_key ] ) ) {
    $rules[] = 'font-family: ' . $font_family_map[ $font_family_key ] . ';';
  }
  if ( get_site_font_weight() ) {
    $rules[] = 'font-weight: ' . esc_attr( get_site_font_weight() ) . ';';
  }

  // フォント設定がなければ何もしない
  if ( empty( $rules ) ) return;

  // .editor-styles-wrapper はブロックエディター iframe 内のコンテンツ領域のラッパー
  $css = '.editor-styles-wrapper, .editor-styles-wrapper p { '
       . implode( ' ', $rules )
       . ' }';

  // ダミーハンドルにインライン CSS を付加して iframe 内の <style> として出力
  wp_register_style( 'cocoon-editor-font-inline', false, array(), false );
  wp_enqueue_style( 'cocoon-editor-font-inline' );
  wp_add_inline_style( 'cocoon-editor-font-inline', $css );
}
endif;

// ブロックエディター内の文字数カウンター非表示（iframe 内に注入）
// admin_print_styles での外側注入は WP 7.0 の iframe 内に届かないため、
// enqueue_block_assets で補完する
add_action( 'enqueue_block_assets', 'cocoon_enqueue_editor_counter_style' );
if ( !function_exists( 'cocoon_enqueue_editor_counter_style' ) ):
function cocoon_enqueue_editor_counter_style() {
  // 管理画面以外では実行しない
  if ( !is_admin() ) return;
  // 投稿編集画面以外では実行しない
  if ( !is_admin_post_page() ) return;
  // カウンター表示設定がオンなら何もしない
  if ( is_admin_editor_counter_visible() ) return;

  // 文字数カウンターの疑似要素を非表示にする CSS
  wp_register_style( 'cocoon-editor-counter-hide', false, array(), false );
  wp_enqueue_style( 'cocoon-editor-counter-hide' );
  wp_add_inline_style( 'cocoon-editor-counter-hide',
    '.editor-post-title::after { display: none !important; }'
  );
}
endif;
