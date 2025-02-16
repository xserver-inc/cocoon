<?php
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

//クラシックエディタースタイルを適用
add_editor_style();

//ファイルのディレクトリパスを取得する（最後の/付き）
if ( !function_exists( 'abspath' ) ):
function abspath($file){return dirname($file).'/';}
endif;

require_once abspath(__FILE__).'lib/_defins.php'; //定数を定義

/**
 * ダウンロード配信サーバーの参照先を取得する関数
 *
 * @param int $new_sv_weight 新サーバーを参照するウェイト（1～100）
 * @return string $url アップデートサーバーのURL
 */
function fetch_updater_url( $new_sv_weight ) {
  $uri = get_template_directory_uri();

  // サイトURLをベースに符号化（数値化）
  $crc = abs( crc32( $uri ) ) ;

  // 符号化した値をシード値として用いて0～100の乱数を生成
  srand( $crc );

  $percent = rand( 1, 100 );

  srand();

  // 指定したウェイトよりも小さい数であれば新サーバー、それ以外は既存を見に行く
  if ( $percent <= $new_sv_weight ) {
    $url = 'https://download.wp-cocoon.com/v1/update.php?action=get_metadata&slug=cocoon-master';
  } else {
    $url = 'https://raw.githubusercontent.com/xserver-inc/cocoon/master/update-info.json';
  }

  return $url;
}

//アップデートチェックの初期化
use YahnisElsts\PluginUpdateChecker\v5\PucFactory;
add_action( 'init', function() {
    require_once abspath(__FILE__).'lib/plugin-update-checker/plugin-update-checker.php';
    $myUpdateChecker = PucFactory::buildUpdateChecker(
        fetch_updater_url(100), //JSONファイルのURL
        __FILE__,
        'cocoon-master'
    );
});


//本文部分の冒頭を綺麗に抜粋する
if ( !function_exists( 'get_content_excerpt' ) ):
function get_content_excerpt($content, $length = 120){
  $content = apply_filters( 'content_excerpt_before', $content);
  $content = cancel_blog_card_deactivation($content, false);
  $content = preg_replace('/<!--more-->.+/is', '', $content); //moreタグ以降削除
  $content = strip_tags($content);//タグの除去
  $content = strip_shortcodes($content);//ショートコード削除
  $content = str_replace('&nbsp;', '', $content);//特殊文字の削除（今回はスペースのみ）
  $content = preg_replace('/\[.+?\]/i', '', $content); //ショートコードを取り除く
  $content = preg_replace(URL_REG, '', $content); //URLを取り除く
  $content = $content ?? '';
  $content = str_replace(array("\r\n", "\r", "\n"), '', $content); //改行部分を取り除く
  $content = preg_replace('/\s+/', ' ', $content); // 連続するスペースを1つにまとめる
  $content = preg_replace('/\A[\x00\s]++|[\x00\s]++\z/u', '', $content); // 先頭と末尾から空白文字を取り除く
  $content = html_entity_decode($content); //HTML エンティティを対応する文字に変換する

  //$lengthが整数じゃなかった場合の処理
  if (!is_numeric($length)) {
    $length = 120;
  }

  $over    = intval(mb_strlen($content)) > $length;
  $content = mb_substr($content, 0, $length);//文字列を指定した長さで切り取る
  if ( $over && $more = get_entry_card_excerpt_more() ) {
    $content = $content.$more;
  }
  $content = esc_html($content);

  $content = apply_filters( 'content_excerpt_after', $content);

  return $content;
}
endif;

//images/no-image.pngを使用するimgタグに出力するサイズ関係の属性
if ( !function_exists( 'get_noimage_sizes_attr' ) ):
function get_noimage_sizes_attr($image = null){
  if (!$image) {
    $image = get_no_image_160x90_url();
  }
  $w = THUMB160WIDTH;
  $h = THUMB160HEIGHT;
  $sizes = ' srcset="'.$image.' '.$w.'w" width="'.$w.'" height="'.$h.'" sizes="(max-width: '.$w.'px) '.$w.'vw, '.$h.'px"';
  return $sizes;
}
endif;

//投稿ナビのサムネイルタグを取得する
if ( !function_exists( 'get_post_navi_thumbnail_tag' ) ):
function get_post_navi_thumbnail_tag($id, $width = THUMB120WIDTH, $height = THUMB120HEIGHT){
  $thumbnail_size = 'thumb'.strval($width);
  $thumbnail_size = apply_filters('get_post_navi_thumbnail_size', $thumbnail_size);
  $thumb = get_the_post_thumbnail( $id, $thumbnail_size, array('alt' => '') );
  if ( !$thumb ) {
    $image = get_template_directory_uri().'/images/no-image-%s.png';

    //表示タイプ＝デフォルト
    if ($width == THUMB120WIDTH) {
      $w = THUMB120WIDTH;
      $h = THUMB120HEIGHT;
      $image = get_no_image_160x90_url($id);
    } else {//表示タイプ＝スクエア
      $image = get_no_image_150x150_url($id);
      $w = THUMB150WIDTH;
      $h = THUMB150HEIGHT;
    }
    $thumb = get_original_image_tag($image, $w, $h, 'no-image post-navi-no-image');
  }
  return $thumb;
}
endif;

// アーカイブタイトルの取得
if ( !function_exists( 'get_archive_chapter_title' ) ) :
function get_archive_chapter_title(){
  $chapter_title = '';

  if( is_category() ) { // カテゴリーページの場合
    $icon_font = '<span class="fa fa-folder-open" aria-hidden="true"></span>';
    $category = get_queried_object();
    if ( $category ) {
      $chapter_title .= $icon_font . esc_html($category->name);
    } else {
      $chapter_title .= single_cat_title($icon_font, false);
    }
  } elseif( is_tag() || is_tax()) { // タグ・タクソノミページの場合
    $icon_font = '<span class="fa fa-tags" aria-hidden="true"></span>';

    // 現在のタームのタクソノミーを取得
    $tag = get_queried_object();

    if ( is_tax() ) {
      $taxonomy = $tag->taxonomy; // 現在のタクソノミーを取得

      // 階層型タクソノミーの場合はフォルダアイコン、階層型でない場合はタグアイコン
      if ( is_taxonomy_hierarchical($taxonomy) ) {
        $icon_font = '<span class="fa fa-folder-open" aria-hidden="true"></span>';
      } else {
        $icon_font = '<span class="fa fa-tags" aria-hidden="true"></span>';
      }
    }

    if ( $tag ) {
      $chapter_title .= $icon_font . esc_html($tag->name);
    } else {
      $chapter_title .= single_tag_title($icon_font, false);
    }
  } elseif( is_search() ) { // 検索結果ページ
    $search_query = trim(strip_tags(get_search_query()));
    if (empty($search_query)) {
      $search_query = __( 'キーワード指定なし', 'text-domain' ); // THEME_NAME を適切なテキストドメインに変更
    }
    $chapter_title .= '<span class="fa fa-search" aria-hidden="true"></span>"' . esc_html($search_query) . '"';
  } elseif (is_day()) { // 日別アーカイブ
    $chapter_title .= '<span class="fa fa-calendar" aria-hidden="true"></span>' . get_the_time('Y-m-d');
  } elseif (is_month()) { // 月別アーカイブ
    $chapter_title .= '<span class="fa fa-calendar" aria-hidden="true"></span>' . get_the_time('Y-m');
  } elseif (is_year()) { // 年別アーカイブ
    $chapter_title .= '<span class="fa fa-calendar" aria-hidden="true"></span>' . get_the_time('Y');
  } elseif (is_author()) { // 著者ページ
    $chapter_title .= '<span class="fa fa-user" aria-hidden="true"></span>' . esc_html(get_the_author());
  } elseif (is_paged()) { // 2ページ目以降
    $chapter_title .= 'Archives';
  } else { // その他
    $chapter_title .= 'Archives';
  }

  return apply_filters('get_archive_chapter_title', $chapter_title);
}
endif;



//アーカイブ見出しの取得
if ( !function_exists( 'get_archive_chapter_text' ) ):
function get_archive_chapter_text(){
  $chapter_text = null;

  //アーカイブタイトルの取得
  $chapter_text .= get_archive_chapter_title();

  //返り値として返す
  return $chapter_text;
}
endif;

//'wp-color-picker'の呼び出し順操作（最初の方に読み込む）
add_action('admin_enqueue_scripts', 'admin_enqueue_scripts_custom');
if ( !function_exists( 'admin_enqueue_scripts_custom' ) ):
function admin_enqueue_scripts_custom($hook) {
  wp_enqueue_script('colorpicker-script', get_template_directory_uri() . '/js/color-picker.js', array( 'wp-color-picker' ), false, true);
}
endif;

//投稿管理画面のカテゴリーリストの階層を保つ
add_filter('wp_terms_checklist_args', 'solecolor_wp_terms_checklist_args', 10, 2);
if ( !function_exists( 'solecolor_wp_terms_checklist_args' ) ):
function solecolor_wp_terms_checklist_args( $args, $post_id ){
 if ( isset($args['checked_ontop']) && ($args['checked_ontop'] !== false )){
    $args['checked_ontop'] = false;
 }
 return $args;
}
endif;

//リダイレクト
add_action( 'wp','wp_singular_page_redirect', 0 );
if ( !function_exists( 'wp_singular_page_redirect' ) ):
function wp_singular_page_redirect() {
  //リダイレクト
  if (is_singular() && $redirect_url = get_singular_redirect_url()) {
    //URL形式にマッチする場合
    if (preg_match(URL_REG, $redirect_url)) {
      redirect_to_url($redirect_url);
    }
  }
}
endif;

//マルチページページャーの現在のページにcurrentクラスを追加
add_filter('wp_link_pages_link', 'wp_link_pages_link_custom');
if ( !function_exists( 'wp_link_pages_link_custom' ) ):
function wp_link_pages_link_custom($link){
  //リンク内にAタグが含まれていない場合は現在のページ
  if (!includes_string($link, '</a>')) {
    $link = str_replace('class="page-numbers"', 'class="page-numbers current"', $link);
  }
  return $link;
}
endif;

//メインクエリの出力変更
add_action( 'pre_get_posts', 'custom_main_query_pre_get_posts' );
if ( !function_exists( 'custom_main_query_pre_get_posts' ) ):
function custom_main_query_pre_get_posts( $query ) {
  if (is_admin()) return;

  //メインループ内
  if ($query->is_main_query()) {

    //順番変更
  if (!is_index_sort_orderby_date() && !is_search()) {
    //投稿日順じゃないときは設定値を挿入する
    $query->set( 'orderby', get_index_sort_orderby() );
  }

    //カテゴリーの除外
    $exclude_category_ids = get_archive_exclude_category_ids();
    if (!is_singular() && $exclude_category_ids && is_array($exclude_category_ids)) {
      $query->set( 'category__not_in', $exclude_category_ids );
    }

    //除外投稿
    $exclude_post_ids = get_archive_exclude_post_ids();
    if (!is_singular() && $exclude_post_ids && is_array($exclude_post_ids)) {
      $query->set( 'post__not_in', $exclude_post_ids );
    }

  }

  //フィード
  if ($query->is_feed) {
    $exclude_post_ids = get_rss_exclude_post_ids();
    if ($exclude_post_ids && is_array($exclude_post_ids)) {
      $query->set( 'post__not_in', $exclude_post_ids );
    }
  }
}
endif;

//強制付与されるnoreferrer削除
add_filter( 'wp_targeted_link_rel', 'wp_targeted_link_rel_custom', 10, 2 );
if ( !function_exists( 'wp_targeted_link_rel_custom' ) ):
function wp_targeted_link_rel_custom( $rel_value, $link_html ){
  $rel_value = str_replace('noopener noreferrer', '', $rel_value);
  return $rel_value;
}
endif;

//SmartNewsフィード追加
add_action('init', 'smartnews_feed_init');
if ( !function_exists( 'smartnews_feed_init' ) ):
function smartnews_feed_init(){
  add_feed('smartnews', 'smartnews_feed');
}
endif;

//domain.com/?feed=smartnewsで表示
if ( !function_exists( 'smartnews_feed' ) ):
function smartnews_feed() {
  cocoon_template_part('/tmp/smartnews');
}
endif;

//SmartNewsのHTTP header for Content-type
add_filter( 'feed_content_type', 'smartnews_feed_content_type', 10, 2 );
if ( !function_exists( 'smartnews_feed_content_type' ) ):
function smartnews_feed_content_type( $content_type, $type ) {
  if ( 'smartnews' === $type ) {
    return feed_content_type( 'rss2' );
  }
  return $content_type;
}
endif;

//サイトマップにnoindex設定を反映させる
add_filter('wp_sitemaps_posts_query_args', 'wp_sitemaps_posts_query_args_noindex_custom');
if ( !function_exists( 'wp_sitemaps_posts_query_args_noindex_custom' ) ):
function wp_sitemaps_posts_query_args_noindex_custom($args){
  $args['post__not_in'] = get_noindex_post_ids();
  return $args;
}
endif;

//サイトマップにカテゴリー・タグのnoindex設定を反映させる
add_filter('wp_sitemaps_taxonomies_query_args', 'wp_sitemaps_taxonomies_query_args_noindex_custom');
if ( !function_exists( 'wp_sitemaps_taxonomies_query_args_noindex_custom' ) ):
function wp_sitemaps_taxonomies_query_args_noindex_custom($args){
  //カテゴリーの除外
  $category_ids = get_noindex_category_ids();
  if (($args['taxonomy'] == 'category') && $category_ids) {
    $args['exclude'] = $category_ids;
  }

  //タグの除外
  $tag_ids = get_noindex_tag_ids();
  if (($args['taxonomy'] == 'post_tag') && $tag_ids) {
    $args['exclude'] = $tag_ids;
  }
  return $args;
}
endif;

//サイトマップにカテゴリー・タグのnoindex設定を反映させる
add_filter('wp_sitemaps_taxonomies', 'wp_sitemaps_taxonomies_custum');
if ( !function_exists( 'wp_sitemaps_taxonomies_custum' ) ):
function wp_sitemaps_taxonomies_custum( $taxonomies ) {
  //サイトマップにカテゴリーを出力しない
  if (is_category_page_noindex()) {
    unset( $taxonomies['category'] );
  }

  //サイトマップにタグを出力しない
  if (is_tag_page_noindex()) {
    unset( $taxonomies['post_tag'] );
  }

  return $taxonomies;
}
endif;

//サイトマップにその他のアーカイブのnoindex設定を反映する
add_filter('wp_sitemaps_add_provider', 'wp_sitemaps_add_provider_custom', 10, 2);
if ( !function_exists( 'wp_sitemaps_add_provider_custom' ) ):
function wp_sitemaps_add_provider_custom( $provider, $name ) {
  if ( is_other_archive_page_noindex() && 'users' === $name ) {
      return false;
  }

  return $provider;
}
endif;

//ウィジェットの「最近の投稿」から「アーカイブ除外ページ」を削除
add_filter('widget_posts_args', 'remove_no_archive_pages_from_widget_recent_entries');
if ( !function_exists( 'remove_no_archive_pages_from_widget_recent_entries' ) ):
function remove_no_archive_pages_from_widget_recent_entries($args){
  $archive_exclude_post_ids = get_archive_exclude_post_ids();
  if ($archive_exclude_post_ids) {
    $args['post__not_in'] = $archive_exclude_post_ids;
  }
  return $args;
}
endif;

//wpForoで添付画像をイメージリンクにする
add_filter('wpforo_body_text_filter', function ($text){
  $text = preg_replace('#(<div id="wpfa-\d+?" class="wpforo-attached-file"><a class="wpforo-default-attachment" .*?href="(.+?('.IMAGE_RECOGNITION_EXTENSIONS_REG.'))".*?>).+?(</a></div>)#i', '$1<i class="fas fa-paperclip paperclip"></i><img alt="" src="$2" />$4', $text);
  return $text;
});

//ウィジェットブロックエディターの停止
add_action( 'after_setup_theme', 'remove_widgets_block_editor' );
if ( !function_exists( 'remove_widgets_block_editor' ) ):
function remove_widgets_block_editor() {
  remove_theme_support( 'widgets-block-editor' );
}
endif;

//FAQブロック
//参考：SILKスキンのコード（ろこさん作成）
//URL：https://dateqa.com/cocoon/
//参考にしたコード：https://github.com/yhira/cocoon/blob/20cdc9efc15c9074a8ce445af926fe70d6dec3f7/skins/silk/functions.php#L1034
add_filter('render_block_cocoon-blocks/faq', 'cocoon_blocks_faq', 10, 2);

function cocoon_blocks_faq($content, $block) {
  add_filter('cocoon_faq_entity', function ($faq) use ($content, $block) {
    return cocoon_add_faq($content, $block, $faq, 'question');
  });

  return $content;
}

function cocoon_add_faq($content, $block, $faq, $question) {
  $name = array_key_exists($question, $block['attrs']) ? strip_tags($block['attrs'][$question]) : '';
  $answer = '';
  $innerBlocks = $block['innerBlocks'];
  foreach ($innerBlocks as $innerBlock) {

    //WordPress 6.1以上の場合
    if (is_wp_6_1_or_over()) {
      //リストブロックかどうか
      if ($innerBlock['blockName'] === 'core/list') {
        $lis = isset($innerBlock['innerBlocks']) ? $innerBlock['innerBlocks'] : '';
        if ($lis) {
          //<ul> or <ol>
          $answer .= $innerBlock['innerContent'][0];
          //liを取得する
          foreach ($lis as $li) {
            $answer .= trim($li['innerHTML']);
          }
          //</ul> or </ol>
          $answer .= $innerBlock['innerContent'][count($innerBlock['innerContent']) - 1];
        }
      } else {
        //リストブロック以外
        $answer .= trim($innerBlock['innerHTML']);
      }
    } else {
      //ONE PIECE 6.1未満の場合
      $answer .= trim($innerBlock['innerHTML']);
    }
  }
  $text = strip_tags(str_replace(["\n", "\r"], '', $answer), '<h1><h2><h3><h4><h5><h6><br><ol><ul><li><a><p><div><b><strong><i><em>');

  $faq[count($faq)] = [
    '@type'          => 'Question',
    'name'           => $name,
    'acceptedAnswer' => [
      '@type' => 'Answer',
      'text'  => $text
    ]
  ];

  return $faq;
}

if (apply_filters('cocoon_json_ld_faq_visible', true)) {
  add_action('wp_footer', 'cocoon_footer_faq_script');
}

function cocoon_footer_faq_script() {
  $faq = apply_filters('cocoon_faq_entity', []);

  if (!empty($faq)) {
    $entity = [];

    foreach ($faq as $key => $value) {
      $entity[] = $value;
    }

    echo '<!-- '.THEME_NAME_CAMEL.' FAQ JSON-LD -->'.PHP_EOL;
    echo '<script type="application/ld+json">'.json_encode([
      '@context'   => 'https://schema.org',
      '@type'      => 'FAQPage',
      'mainEntity' => $entity
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT).'</script>';
  }
}

//oembedプロバイダーからread.amazonを削除
add_filter('oembed_providers', function ($providers){
  foreach ($providers as $key => $value) {
    if (includes_string($value[0], 'read.amazon.')) {
      unset($providers[$key]);
    }
  }
  return $providers;
});

//WordPress6.3から画像ブロックの表示比率が崩れる不具合の対応コード追加（WordPress側で対応されたら解除する）
//参考：https://github.com/WordPress/gutenberg/issues/53555#issuecomment-1675107104
add_filter( 'render_block_core/image', __NAMESPACE__ . '\fix_img_v63', 10, 2 );
function fix_img_v63( $block_content, $block ) {
  $attrs = $block['attrs'] ?? [];
  $w = $attrs['width'] ?? '';
  $h = $attrs['height'] ?? '';
  if ( $w && $h ) {
    $size_style    = "width:{$w}px;height:{$h}px";
    $ratio         = "{$w}/{$h}";
    $block_content = str_replace( $size_style, "aspect-ratio:{$ratio}", $block_content );
  }
  return $block_content;
}


//ダッシュボードにブロックパターンメニューを追加
add_action('admin_menu', 'add_reuse_block_menu_page');
if ( !function_exists( 'add_reuse_block_menu_page' ) ):
function add_reuse_block_menu_page() {
  if (is_admin() && !is_wp_6_5_or_over() && !is_classicpress()) {
    add_menu_page(
      __( 'パターン一覧', THEME_NAME ),
      __( 'パターン一覧', THEME_NAME ),
      'manage_options',
      'edit.php?post_type=wp_block',
      '',
      'dashicons-image-rotate',
      26
    );
  }
}
endif;

//sizes="auto"対策
add_filter ( 'wp_img_tag_add_auto_sizes' ,  '__return_false' );


// タクソノミ対応カテゴリー・タグリンク
add_filter('cocoon_part__tmp/categories-tags', function($content) {
  $post_type = get_post_type();

  // カスタム投稿の場合
  if ($post_type !== 'post') {
    // 投稿タイプに関連付けられたタクソノミーを取得
    $taxonomies = get_object_taxonomies($post_type);

    // ターム取得
    $args = array(
      'order'   => 'ASC',
      'orderby' => 'name',
    );
    $terms = wp_get_post_terms(get_the_ID(), $taxonomies, $args);

    if ($terms && !is_wp_error($terms)) {
      $categories_html = '';  // 階層型タクソノミーのHTML
      $tags_html = '';        // 非階層型タクソノミーのHTML

      foreach ($terms as $term) {
        // タクソノミーが階層型（カテゴリー）かどうか
        if (is_taxonomy_hierarchical($term->taxonomy)) {
          $categories_html .= '<a class="cat-link cat-link-' . $term->term_id . '" href="' . esc_url(get_term_link($term)) . '">
            <span class="fa fa-folder cat-icon tax-icon" aria-hidden="true"></span>' . esc_html($term->name) . '</a>';
        } else {
          $tags_html .= '<a class="tag-link tag-link-' . $term->term_id . '" href="' . esc_url(get_term_link($term)) . '">
            <span class="fa fa-tag tag-icon tax-icon" aria-hidden="true"></span>' . esc_html($term->name) . '</a>';
        }
      }

      // カテゴリーがある場合は出力
      if (!empty($categories_html)) {
        $categories_html = '<div class="entry-categories">' . $categories_html . '</div>';
      }

      // タグがある場合は出力
      if (!empty($tags_html)) {
        $tags_html = '<div class="entry-tags">' . $tags_html . '</div>';
      }

      // 最終的なHTMLを構築
      $content = '<div class="entry-categories-tags ' . esc_attr(get_additional_categories_tags_area_classes() ?: '') . '">'
                . $categories_html . $tags_html . '</div>';
    }
  }

  return $content;
});
