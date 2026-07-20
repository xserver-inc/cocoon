<?php //テーマ設定画面用の軽量プレビュー
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

//プレビューURLで使うクエリ変数名（例: home_url('/?cocoon_preview=all') ）
if ( !defined( 'COCOON_PREVIEW_QUERY_KEY' ) ) {
  define( 'COCOON_PREVIEW_QUERY_KEY', 'cocoon_preview' );
}
//キャッシュ（transient）名の接頭辞
if ( !defined( 'COCOON_PREVIEW_TRANSIENT_PREFIX' ) ) {
  define( 'COCOON_PREVIEW_TRANSIENT_PREFIX', 'cocoon_preview_html_v2_' );
}

//有効なプレビュー種別の一覧を返す
if ( !function_exists( 'get_cocoon_settings_preview_types' ) ):
function get_cocoon_settings_preview_types(){
  return array( 'all', 'header', 'skin', 'column', '404', 'mobile' );
}
endif;

//指定種別が有効なプレビュー種別かどうか
if ( !function_exists( 'is_valid_cocoon_settings_preview_type' ) ):
function is_valid_cocoon_settings_preview_type($type){
  return in_array( $type, get_cocoon_settings_preview_types(), true );
}
endif;

//現在のリクエストが設定プレビューかどうか（権限チェック込み）
if ( !function_exists( 'is_cocoon_settings_preview' ) ):
function is_cocoon_settings_preview(){
  //管理画面・未指定・権限なしは対象外
  if ( is_admin() ) {
    return false;
  }
  if ( !isset( $_GET[COCOON_PREVIEW_QUERY_KEY] ) ) {
    return false;
  }
  //テーマ設定を編集できるユーザーのみプレビュー可能（情報漏えい防止）
  if ( !current_user_can( 'edit_theme_options' ) ) {
    return false;
  }
  $type = sanitize_key( wp_unslash( $_GET[COCOON_PREVIEW_QUERY_KEY] ) );
  return is_valid_cocoon_settings_preview_type( $type );
}
endif;

//現在のプレビュー種別を取得（無効なら 'all'）
if ( !function_exists( 'get_cocoon_settings_preview_type' ) ):
function get_cocoon_settings_preview_type(){
  if ( !isset( $_GET[COCOON_PREVIEW_QUERY_KEY] ) ) {
    return 'all';
  }
  $type = sanitize_key( wp_unslash( $_GET[COCOON_PREVIEW_QUERY_KEY] ) );
  return is_valid_cocoon_settings_preview_type( $type ) ? $type : 'all';
}
endif;

//プレビューURLを取得（iframeのsrcに使用）
if ( !function_exists( 'get_cocoon_settings_preview_url' ) ):
function get_cocoon_settings_preview_url($type = 'all'){
  if ( !is_valid_cocoon_settings_preview_type( $type ) ) {
    $type = 'all';
  }
  return esc_url( add_query_arg( COCOON_PREVIEW_QUERY_KEY, $type, home_url( '/' ) ) );
}
endif;

//プレビュー種別ごとのキャッシュキーを返す（404以外は本文が同一なので共通キーにまとめる）
if ( !function_exists( 'get_cocoon_settings_preview_cache_key' ) ):
function get_cocoon_settings_preview_cache_key($type){
  //404のみ本文が異なるので別キー、それ以外は共通キーで重複保存を防ぐ
  $slug = ( $type === '404' ) ? '404' : 'shared';
  return COCOON_PREVIEW_TRANSIENT_PREFIX . $slug;
}
endif;

//プレビューで実際に使われるキャッシュキー一覧を返す（無効化処理で使用）
if ( !function_exists( 'get_cocoon_settings_preview_cache_keys' ) ):
function get_cocoon_settings_preview_cache_keys(){
  return array(
    COCOON_PREVIEW_TRANSIENT_PREFIX . 'shared',
    COCOON_PREVIEW_TRANSIENT_PREFIX . '404',
  );
}
endif;

//プレビュー本体の描画（template_redirectで割り込み、キャッシュを利用して出力後にexit）
if ( !function_exists( 'cocoon_render_settings_preview' ) ):
function cocoon_render_settings_preview(){
  if ( !is_cocoon_settings_preview() ) {
    return;
  }

  $type = get_cocoon_settings_preview_type();

  //プレビューでは管理バーを非表示にして余計な描画を省く
  add_filter( 'show_admin_bar', '__return_false' );

  //404プレビューは実際に404状態にして、ヘッダー等の条件分岐も404として正しく描画する
  if ( $type === '404' ) {
    global $wp_query;
    if ( isset( $wp_query ) && is_object( $wp_query ) ) {
      $wp_query->set_404();
    }
    status_header( 404 );
  }

  //キャッシュ（transient）があればそれを返す
  $transient_key = get_cocoon_settings_preview_cache_key( $type );
  $html = get_transient( $transient_key );

  //空文字は無効とみなして作り直す（空表示の固定化を防ぐ）
  if ( !is_string( $html ) || $html === '' ) {
    $html = cocoon_build_settings_preview_html( $type );
    //安全網として1時間キャッシュ（コンテンツ・設定変更時には個別に無効化される）
    if ( is_string( $html ) && $html !== '' ) {
      set_transient( $transient_key, $html, HOUR_IN_SECONDS );
    }
  }

  //プレビューはブラウザ・プロキシでキャッシュさせない（設定変更が即反映されるように）
  nocache_headers();
  echo $html;
  exit;
}
endif;
add_action( 'template_redirect', 'cocoon_render_settings_preview', 0 );

//プレビューHTMLを生成する（実テンプレートのヘッダー・フッターを使い、実フロント種別に応じて本文を切り替える）
if ( !function_exists( 'cocoon_build_settings_preview_html' ) ):
function cocoon_build_settings_preview_html($type){
  ob_start();
  //実際のヘッダー（wp_head・css-custom.php のインラインCSSを含む）を出力
  get_header();

  cocoon_output_settings_preview_main_content( $type );

  //実際のフッター（サイドバー・ウィジェット・wp_footer を含む）を出力
  get_footer();
  $html = ob_get_clean();
  //通常フロントは code_minify() 内で Font Awesome 5 へ変換されるが、プレビューは exit で早期終了しその処理を通らない。
  //そのためここで明示的に変換し、FA5 設定時にアイコンが正しく表示されるようにする。
  return change_fa( $html );
}
endif;

//プレビュー本文を出力する（インデックス時は一覧、固定フロント時はそのページ内容）
if ( !function_exists( 'cocoon_output_settings_preview_main_content' ) ):
function cocoon_output_settings_preview_main_content($type){
  if ( $type === '404' ) {
    echo cocoon_get_settings_preview_404_content();
    return;
  }

  //「最新の投稿」ならインデックス一覧を表示（インデックスタブ設定がそのまま反映される）
  if ( get_option( 'show_on_front' ) === 'posts' ) {
    cocoon_template_part('tmp/list');
    return;
  }

  //「固定ページ」なら、現在のフロントページ本文テンプレートを表示
  if ( have_posts() ) {
    cocoon_template_part('tmp/page-contents');
    return;
  }

  //フロントページ設定が未整備のときの最終フォールバック
  echo cocoon_get_settings_preview_sample_article();
}
endif;

//404プレビュー用のコンテンツHTMLを返す
if ( !function_exists( 'cocoon_get_settings_preview_404_content' ) ):
function cocoon_get_settings_preview_404_content(){
  ob_start();
  $not_found_title     = function_exists( 'get_404_page_title' ) ? get_404_page_title() : __( 'ページが見つかりませんでした', THEME_NAME );
  $not_found_image_url = function_exists( 'get_404_image_url' ) ? get_404_image_url() : '';
  $not_found_message   = function_exists( 'get_404_page_message' ) ? get_404_page_message() : '';
  ?>
  <article class="post article">
    <?php if ( $not_found_title ): ?>
      <h1 class="entry-title"><?php echo $not_found_title; ?></h1>
    <?php endif; ?>
    <?php if ( $not_found_image_url ): ?>
      <img class="not-found" src="<?php echo esc_url( $not_found_image_url ); ?>" alt="404 Not Found" />
    <?php endif; ?>
    <?php echo do_shortcode( wpautop( $not_found_message ) ); ?>
  </article>
  <?php
  return ob_get_clean();
}
endif;

//通常プレビュー用のサンプル記事HTMLを返す（見出し・引用・リスト等で各設定の効きを確認できる）
if ( !function_exists( 'cocoon_get_settings_preview_sample_article' ) ):
function cocoon_get_settings_preview_sample_article(){
  ob_start();
  ?>
  <article id="post-0" class="post-0 post type-post status-publish hentry article">
    <div class="entry-card-wrap a-wrap border-element cf">
      <h1 class="entry-title"><?php _e( 'サンプル記事タイトル', THEME_NAME ); ?></h1>
      <div class="entry-content">
        <p><?php _e( 'これはテーマ設定の見え方を確認するためのサンプル本文です。文字サイズ・行間・配色などの設定がどのように反映されるかをプレビューできます。', THEME_NAME ); ?></p>

        <h2><?php _e( '見出し2のサンプル', THEME_NAME ); ?></h2>
        <p><?php _e( '見出し2の装飾（背景色・ボーダー・キーカラー）がここに反映されます。本文の段落スタイルもあわせて確認できます。', THEME_NAME ); ?></p>

        <h3><?php _e( '見出し3のサンプル', THEME_NAME ); ?></h3>
        <p><?php _e( '見出し3の装飾を確認するためのサンプルテキストです。', THEME_NAME ); ?></p>

        <blockquote>
          <p><?php _e( 'これは引用ブロックのサンプルです。引用の装飾を確認できます。', THEME_NAME ); ?></p>
        </blockquote>

        <ul>
          <li><?php _e( 'リスト項目のサンプル1', THEME_NAME ); ?></li>
          <li><?php _e( 'リスト項目のサンプル2', THEME_NAME ); ?></li>
          <li><?php _e( 'リスト項目のサンプル3', THEME_NAME ); ?></li>
        </ul>

        <p><?php _e( 'リンクの色を確認するための', THEME_NAME ); ?><a href="#"><?php _e( 'サンプルリンク', THEME_NAME ); ?></a><?php _e( 'です。', THEME_NAME ); ?></p>
      </div>
    </div>
  </article>
  <?php
  return ob_get_clean();
}
endif;

//プレビューキャッシュを全て削除する
if ( !function_exists( 'clear_cocoon_settings_preview_cache' ) ):
function clear_cocoon_settings_preview_cache(){
  foreach ( get_cocoon_settings_preview_cache_keys() as $key ) {
    delete_transient( $key );
  }
}
endif;
//テーマ設定の保存時
add_action( 'cocoon_settings_after_save', 'clear_cocoon_settings_preview_cache' );
//スキン変更・カスタマイザー保存時
add_action( 'switch_theme', 'clear_cocoon_settings_preview_cache' );
add_action( 'customize_save_after', 'clear_cocoon_settings_preview_cache' );
//投稿・固定ページの更新／削除時（固定フロントやインデックス一覧の内容反映のため）
add_action( 'save_post', 'clear_cocoon_settings_preview_cache' );
add_action( 'deleted_post', 'clear_cocoon_settings_preview_cache' );
//ウィジェット・メニューの変更時
add_action( 'update_option_sidebars_widgets', 'clear_cocoon_settings_preview_cache' );
add_action( 'wp_update_nav_menu', 'clear_cocoon_settings_preview_cache' );
