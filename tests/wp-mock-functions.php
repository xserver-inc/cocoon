<?php
/**
 * WordPress関数のスタブ定義
 *
 * Brain\Monkey が setUp() で初期化される前に必要な
 * WordPress関数のグローバルスタブをここで定義します。
 * これにより、テーマファイルの require 時に
 * "undefined function" エラーが発生しなくなります。
 */

// WordPress コア定数（関数のデフォルト引数で使われるため関数定義より先に宣言が必要）
if (!defined('OBJECT'))       define('OBJECT',       'OBJECT');
if (!defined('ARRAY_A'))      define('ARRAY_A',      'ARRAY_A');
if (!defined('ARRAY_N'))      define('ARRAY_N',      'ARRAY_N');

// WordPress のコア関数スタブ（テーマファイル読み込み時に必要）
// これらはBrain\Monkeyのセットアップ前に必要な最小限のスタブです。
// テスト内での動作カスタマイズはBrain\Monkey::functions()->when() を使用してください。

if (!function_exists('site_url')) {
    function site_url($path = '') {
        return 'http://example.com' . $path;
    }
}

if (!function_exists('home_url')) {
    function home_url($path = '') {
        return 'http://example.com' . $path;
    }
}

if (!function_exists('wp_parse_url')) {
    function wp_parse_url($url, $component = -1) {
        return parse_url($url, $component);
    }
}

if (!function_exists('get_option')) {
    function get_option($option, $default = false) {
        return $default;
    }
}

if (!function_exists('add_filter')) {
    function add_filter($tag, $function_to_add, $priority = 10, $accepted_args = 1) {
        return true;
    }
}

if (!function_exists('add_action')) {
    function add_action($tag, $function_to_add, $priority = 10, $accepted_args = 1) {
        return true;
    }
}

if (!function_exists('apply_filters')) {
    // テスト用: グローバル変数 $test_mock_apply_filters_callbacks で戻り値を制御可能
    function apply_filters($tag, $value, ...$args) {
        global $test_mock_apply_filters_callbacks;
        if (!empty($test_mock_apply_filters_callbacks[$tag]) && is_callable($test_mock_apply_filters_callbacks[$tag])) {
            return ($test_mock_apply_filters_callbacks[$tag])($value, ...$args);
        }
        return $value;
    }
}

if (!function_exists('do_action')) {
    function do_action($tag, ...$args) {
        // no-op
    }
}



if (!function_exists('__')) {
    function __($text, $domain = 'default') {
        return $text;
    }
}

if (!function_exists('_e')) {
    function _e($text, $domain = 'default') {
        echo $text;
    }
}

if (!function_exists('esc_html')) {
    function esc_html($text) {
        return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('esc_attr')) {
    function esc_attr($text) {
        return htmlspecialchars((string)$text, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('esc_url')) {
    function esc_url($url) {
        return filter_var($url, FILTER_SANITIZE_URL);
    }
}

if (!function_exists('esc_url_raw')) {
    function esc_url_raw($url, $protocols = array('http', 'https', 'ftp', 'ftps', 'mailto', 'news', 'irc', 'irc6', 'ircs', 'gopher', 'nntp', 'feed', 'telnet')) {
        $url = trim($url);
        if (!$url) return '';
        // スキームチェック: 許可されたプロトコルのみ通す
        if (preg_match('/^([a-z][a-z0-9+.\-]*):/', $url, $m)) {
            if (!in_array(strtolower($m[1]), $protocols, true)) {
                return '';
            }
        }
        return filter_var($url, FILTER_SANITIZE_URL);
    }
}

// wp_kses_post は各テストで必要に応じてモックするため削除

if (!function_exists('is_ssl')) {
    function is_ssl() {
        return false;
    }
}

if (!function_exists('is_admin')) {
    function is_admin() {
        return false;
    }
}

// lib/admin.php 読み込み時に必要（admin_bar_menu 登録をスキップするため）
if (!function_exists('is_admin_tool_menu_visible')) {
    function is_admin_tool_menu_visible() {
        return false;
    }
}

// lib/admin.php 読み込み時に必要（register_tag_check_list 登録をスキップするため）
if (!function_exists('is_editor_tag_check_list_enable')) {
    function is_editor_tag_check_list_enable() {
        return false;
    }
}

if (!function_exists('get_locale')) {
    function get_locale() {
        return 'ja';
    }
}

if (!function_exists('comments_open')) {
    function comments_open($post_id = null) {
        return true;
    }
}

if (!function_exists('is_single')) {
    function is_single($post = '') {
        return false;
    }
}

if (!function_exists('is_category')) {
    function is_category($category = '') {
        return false;
    }
}

if (!function_exists('wp_date')) {
    function wp_date($format, $timestamp = null, $timezone = null) {
        if ($timestamp === null) {
            return date($format);
        }
        return date($format, $timestamp);
    }
}



if (!function_exists('is_wp_error')) {
    function is_wp_error($thing) {
        return false;
    }
}

if (!function_exists('get_the_ID')) {
    function get_the_ID() {
        global $test_mock_get_the_id;
        return isset($test_mock_get_the_id) ? $test_mock_get_the_id : 1;
    }
}



if (!function_exists('user_trailingslashit')) {
    function user_trailingslashit($string, $type_of_url = '') {
        return rtrim($string, '/') . '/';
    }
}

if (!function_exists('wp_redirect')) {
    function wp_redirect($location, $status = 302) {
        return true;
    }
}

if (!function_exists('strip_tags')) {
    // strip_tags is a PHP native function, no need to mock
}

if (!function_exists('is_singular')) {
    function is_singular($post_types = '') {
        global $test_mock_is_singular;
        return isset($test_mock_is_singular) ? (bool)$test_mock_is_singular : false;
    }
}

if (!function_exists('has_blocks')) {
    function has_blocks($post = null) {
        return false;
    }
}

if (!function_exists('get_queried_object')) {
    function get_queried_object() {
        return null;
    }
}

if (!function_exists('is_front_page')) {
    function is_front_page() {
        return false;
    }
}

if (!function_exists('get_theme_mod')) {
    // テスト用: グローバル変数 $test_theme_mods で戻り値を制御可能
    function get_theme_mod($name, $default = false) {
        global $test_theme_mods;
        if (is_array($test_theme_mods) && array_key_exists($name, $test_theme_mods)) {
            return $test_theme_mods[$name];
        }
        return $default;
    }
}

if (!function_exists('shortcode_exists')) {
    function shortcode_exists($tag) {
        return false;
    }
}

if (!function_exists('add_shortcode')) {
    function add_shortcode($tag, $callback) {
        return true;
    }
}

if (!function_exists('is_site_icon_font_font_awesome_5')) {
    function is_site_icon_font_font_awesome_5() {
        return false;
    }
}

if (!function_exists('is_site_icon_font_font_awesome_4')) {
    function is_site_icon_font_font_awesome_4() {
        return true;
    }
}

// current_user_can は各テストで Brain\Monkey を使ってモックするためネイティブ定義を削除

if (!function_exists('wp_remote_get')) {
    // テスト用: グローバル変数 $test_mock_wp_remote_get_response で戻り値を制御可能
    // $test_mock_wp_remote_get_args にリクエスト引数をキャプチャ、 $test_mock_wp_remote_get_url にURLをキャプチャ
    function wp_remote_get($url, $args = []) {
        global $test_mock_wp_remote_get_response;
        global $test_mock_wp_remote_get_args;
        global $test_mock_wp_remote_get_url;
        $test_mock_wp_remote_get_url = $url;
        $test_mock_wp_remote_get_args = $args;
        if (isset($test_mock_wp_remote_get_response)) {
            if (is_callable($test_mock_wp_remote_get_response)) {
                return call_user_func($test_mock_wp_remote_get_response, $url, $args);
            }
            return $test_mock_wp_remote_get_response;
        }
        return [];
    }
}

if (!function_exists('wp_remote_post')) {
    // テスト用: グローバル変数 $test_mock_wp_remote_post_response で戻り値を制御可能
    // $test_mock_wp_remote_post_args にリクエスト引数をキャプチャ
    function wp_remote_post($url, $args = []) {
        global $test_mock_wp_remote_post_response;
        global $test_mock_wp_remote_post_args;
        $test_mock_wp_remote_post_args = $args;
        if (isset($test_mock_wp_remote_post_response)) {
            return $test_mock_wp_remote_post_response;
        }
        return [];
    }
}

if (!function_exists('wp_remote_retrieve_body')) {
    // テスト用: レスポンス配列から body を取得する（wp_remote_post のモックと連携）
    function wp_remote_retrieve_body($response) {
        if (is_array($response) && isset($response['body'])) {
            return $response['body'];
        }
        return '';
    }
}

if (!function_exists('current_time')) {
    function current_time($type, $gmt = 0) {
        if ($type === 'timestamp') {
            return time();
        }
        return date($type);
    }
}

if (!function_exists('get_bloginfo')) {
    function get_bloginfo($show = '', $filter = 'raw') {
        switch ($show) {
            case 'name': return 'Test Site';
            case 'url': return 'http://example.com';
            // テスト環境では WP 6.7 相当として振る舞う（バージョンガード系関数の動作を保証）
            case 'version': return '6.7';
            default: return '';
        }
    }
}

if (!function_exists('shortcode_atts')) {
    function shortcode_atts($pairs, $atts, $shortcode = '') {
        $atts = (array)$atts;
        $out = array();
        foreach ($pairs as $name => $default) {
            if (array_key_exists($name, $atts)) {
                $out[$name] = $atts[$name];
            } else {
                $out[$name] = $default;
            }
        }
        return $out;
    }
}

if (!function_exists('is_plugin_fourm_page')) {
    function is_plugin_fourm_page() {
        return false;
    }
}

if (!function_exists('is_admin_php_page')) {
    function is_admin_php_page() {
        return false;
    }
}

// term_metadata_exists と get_term_meta は各テストで必要に応じてモックするため削除

if (!function_exists('get_query_var')) {
    function get_query_var($var, $default = '') {
        return $default;
    }
}

if (!function_exists('is_page')) {
    function is_page($page = '') {
        return false;
    }
}

if (!function_exists('is_home')) {
    function is_home() {
        return false;
    }
}

if (!function_exists('is_archive')) {
    function is_archive() {
        return false;
    }
}

if (!function_exists('wp_get_nav_menu_items')) {
    function wp_get_nav_menu_items($menu, $args = array()) {
        return array();
    }
}

if (!function_exists('register_widget')) {
    function register_widget($widget_class) {
        return true;
    }
}

if (!function_exists('is_toc_shortcode_includes')) {
    function is_toc_shortcode_includes($content) {
        return false;
    }
}

// blogcard-in.php 依存
if (!function_exists('is_internal_blogcard_enable')) {
    function is_internal_blogcard_enable() {
        global $test_mock_is_internal_blogcard_enable;
        if (isset($test_mock_is_internal_blogcard_enable)) {
            return $test_mock_is_internal_blogcard_enable;
        }
        return false;
    }
}



if (!function_exists('get_post_status')) {
    function get_post_status($id = null) {
        return 'publish';
    }
}

if (!function_exists('get_permalink')) {
    // テスト用: $test_mock_get_permalink_map[$post_id] に URL をセットすると
    // その ID の get_permalink() がそのURLを返す
    function get_permalink($post = 0) {
        global $test_mock_get_permalink_map;
        if (is_array($test_mock_get_permalink_map) && isset($test_mock_get_permalink_map[$post])) {
            return $test_mock_get_permalink_map[$post];
        }
        return 'http://example.com/';
    }
}

if (!function_exists('get_the_title')) {
    function get_the_title($post = 0) {
        return 'Test Title';
    }
}

if (!function_exists('has_post_thumbnail')) {
    function has_post_thumbnail($post = null) {
        return false;
    }
}



if (!function_exists('get_post')) {
    // テスト用: $test_mock_get_post にオブジェクトをセットすると get_post() がそれを返す
    function get_post($post = null, $output = OBJECT, $filter = 'raw') {
        global $test_mock_get_post;
        return isset($test_mock_get_post) ? $test_mock_get_post : null;
    }
}

// toc.php 依存
if (!function_exists('is_toc_before_ads')) {
    function is_toc_before_ads() {
        return false;
    }
}

if (!function_exists('is_toc_heading_inner_html_tag_enable')) {
    function is_toc_heading_inner_html_tag_enable() {
        return false;
    }
}

if (!function_exists('is_multi_page_toc_visible')) {
    function is_multi_page_toc_visible() {
        return false;
    }
}

if (!function_exists('is_toc_visible')) {
    function is_toc_visible() {
        return true;
    }
}

if (!function_exists('is_tag')) {
    function is_tag($tag = '') {
        return false;
    }
}

if (!function_exists('get_toc_display_count')) {
    function get_toc_display_count() {
        return 2;
    }
}

if (!function_exists('is_active_widget')) {
    function is_active_widget($callback = false, $widget_id = false, $id_base = false, $skip_inactive = true) {
        return false;
    }
}

if (!function_exists('has_block')) {
    function has_block($block_name, $post = null) {
        return false;
    }
}

if (!function_exists('get_toc_title')) {
    function get_toc_title() {
        return '目次';
    }
}

if (!function_exists('is_toc_number_visible')) {
    function is_toc_number_visible() {
        return true;
    }
}

if (!function_exists('is_toc_open')) {
    function is_toc_open() {
        return true;
    }
}

if (!function_exists('get_toc_depth')) {
    function get_toc_depth() {
        return 0;
    }
}

if (!function_exists('is_toc_center_enable')) {
    function is_toc_center_enable() {
        return false;
    }
}

if (!function_exists('get_toc_display_type')) {
    function get_toc_display_type() {
        return 'default';
    }
}

if (!function_exists('is_classicpress')) {
    function is_classicpress() {
        return false;
    }
}

if (!function_exists('do_blocks')) {
    function do_blocks($content) {
        return $content;
    }
}

if (!function_exists('get_shortcode_removed_content')) {
    function get_shortcode_removed_content($content) {
        return $content;
    }
}

if (!function_exists('is_toc_heading_id_prefix_enable')) {
    function is_toc_heading_id_prefix_enable() {
        return true;
    }
}

// shortcodes.php 依存
if (!function_exists('generate_author_box_tag')) {
    function generate_author_box_tag($id = null, $label = null, $is_image_circle = 0) {}
}

if (!function_exists('generate_item_ranking_tag')) {
    function generate_item_ranking_tag($id) {}
}

if (!function_exists('get_affiliate_tag')) {
    function get_affiliate_tag($id) {
        return null;
    }
}

if (!function_exists('get_function_text')) {
    function get_function_text($id) {
        return null;
    }
}

if (!function_exists('is_user_logged_in')) {
    function is_user_logged_in() {
        global $test_mock_is_user_logged_in;
        if (isset($test_mock_is_user_logged_in)) {
            return $test_mock_is_user_logged_in;
        }
        return false;
    }
}

if (!function_exists('do_shortcode')) {
    function do_shortcode($content) {
        return $content;
    }
}

if (!function_exists('shortcode_unautop')) {
    function shortcode_unautop($content) {
        return $content;
    }
}

if (!function_exists('_n')) {
    function _n($single, $plural, $number, $domain = 'default') {
        return ($number == 1) ? sprintf($single, $number) : sprintf($plural, $number);
    }
}

if (!function_exists('url_to_internal_blogcard_tag')) {
    function url_to_internal_blogcard_tag($url) {
        global $test_mock_url_to_internal_blogcard_tag_return;
        if (isset($test_mock_url_to_internal_blogcard_tag_return)) {
            return $test_mock_url_to_internal_blogcard_tag_return;
        }
        return null;
    }
}

if (!function_exists('is_ad_shortcode_enable')) {
    function is_ad_shortcode_enable() {
        return false;
    }
}

if (!function_exists('is_external_blogcard_enable')) {
    function is_external_blogcard_enable() {
        global $test_mock_is_external_blogcard_enable;
        if (isset($test_mock_is_external_blogcard_enable)) {
            return $test_mock_is_external_blogcard_enable;
        }
        return false;
    }
}

if (!function_exists('wp_rand')) {
    function wp_rand($min = 0, $max = 0) {
        return rand($min, $max);
    }
}

if (!function_exists('is_internal_blogcard_url')) {
    function is_internal_blogcard_url($url) {
        global $test_mock_is_internal_blogcard_url;
        if (isset($test_mock_is_internal_blogcard_url)) {
            return $test_mock_is_internal_blogcard_url;
        }
        return false; // デフォルト
    }
}

if (!function_exists('url_to_external_blog_card_tag')) {
    function url_to_external_blog_card_tag($url) {
        global $test_mock_url_to_external_blog_card_tag_return;
        if (isset($test_mock_url_to_external_blog_card_tag_return)) {
            return $test_mock_url_to_external_blog_card_tag_return;
        }
        return null;
    }
}

if (!function_exists('wpautop')) {
    function wpautop($text, $br = true) {
        return "<p>$text</p>";
    }
}

if (!function_exists('set_query_var')) {
    function set_query_var($key, $value) {}
}

if (!function_exists('get_the_time')) {
    function get_the_time($format = '') {
        return '2025/01/01';
    }
}

if (!function_exists('get_the_content')) {
    function get_the_content($more_link_text = null, $strip_teaser = false) {
        return '';
    }
}

if (!function_exists('get_queried_object_id')) {
    function get_queried_object_id() {
        return 0;
    }
}

if (!function_exists('wp_get_sidebars_widgets')) {
    function wp_get_sidebars_widgets() {
        return array();
    }
}

if (!function_exists('is_search')) {
    function is_search() {
        return false;
    }
}

if (!function_exists('is_404')) {
    function is_404() {
        return false;
    }
}

if (!function_exists('is_attachment')) {
    function is_attachment() {
        return false;
    }
}

// === SSL関連 ===
if (!function_exists('is_easy_ssl_enable')) {
    function is_easy_ssl_enable() {
        return false;
    }
}

// === 広告関連 ===
if (!function_exists('get_ad_code')) {
    function get_ad_code() {
        return '';
    }
}

if (!function_exists('get_ad_exclude_post_ids')) {
    function get_ad_exclude_post_ids() {
        return '';
    }
}

if (!function_exists('get_ad_exclude_category_ids')) {
    function get_ad_exclude_category_ids() {
        return [];
    }
}

if (!function_exists('is_all_ads_visible')) {
    function is_all_ads_visible() {
        return true;
    }
}

if (!function_exists('is_the_page_ads_visible')) {
    function is_the_page_ads_visible() {
        return true;
    }
}

if (!function_exists('is_all_adsenses_visible')) {
    function is_all_adsenses_visible() {
        return true;
    }
}

if (!function_exists('is_amp')) {
    function is_amp() {
        return false;
    }
}

if (!function_exists('is_mobile_adsense_width_wide')) {
    function is_mobile_adsense_width_wide() {
        return false;
    }
}

if (!function_exists('is_ad_pos_content_middle_visible')) {
    function is_ad_pos_content_middle_visible() {
        return false;
    }
}

if (!function_exists('in_category')) {
    function in_category($ids) {
        return false;
    }
}

if (!function_exists('is_category')) {
    function is_category($ids = '') {
        return false;
    }
}

if (!function_exists('is_single')) {
    function is_single($post = '') {
        return false;
    }
}

if (!function_exists('is_singular')) {
    function is_singular($post_types = '') {
        return false;
    }
}

// === CSSクラス関連 ===
if (!function_exists('get_site_font_family')) {
    function get_site_font_family() {
        return 'Noto_Sans_JP';
    }
}

if (!function_exists('get_site_font_size')) {
    function get_site_font_size() {
        return '16px';
    }
}

if (!function_exists('get_site_font_weight')) {
    function get_site_font_weight() {
        return '400';
    }
}

if (!function_exists('is_front_top_page')) {
    function is_front_top_page() {
        return false;
    }
}

if (!function_exists('is_header_layout_type_top')) {
    function is_header_layout_type_top() {
        return false;
    }
}

if (!function_exists('is_singular_page_type_column1')) {
    function is_singular_page_type_column1() {
        return false;
    }
}

if (!function_exists('is_singular_page_type_content_only')) {
    function is_singular_page_type_content_only() {
        return false;
    }
}

if (!function_exists('is_singular_page_type_narrow')) {
    function is_singular_page_type_narrow() {
        return false;
    }
}

if (!function_exists('is_singular_page_type_wide')) {
    function is_singular_page_type_wide() {
        return false;
    }
}

if (!function_exists('is_singular_page_type_full_wide')) {
    function is_singular_page_type_full_wide() {
        return false;
    }
}

if (!function_exists('get_the_tags')) {
    function get_the_tags($post_id = 0) {
        return false;
    }
}

// === リンク関連 ===
if (!function_exists('includes_string')) {
    function includes_string($haystack, $needle) {
        return strpos((string)$haystack, (string)$needle) !== false;
    }
}

if (!function_exists('includes_target_blalk')) {
    function includes_target_blalk($tag) {
        return strpos($tag, 'target="_blank"') !== false;
    }
}

if (!function_exists('is_comment_external_blogcard_enable')) {
    function is_comment_external_blogcard_enable() {
        return false;
    }
}

if (!function_exists('get_image_sized_url')) { function get_image_sized_url($url, $w = 0, $h = 0) { return $url; } }
if (!function_exists('url_to_local')) { function url_to_local($url) { return false; } }

// lib/admin.php の get_admin_bar_menu_array が使用
if (!function_exists('admin_url')) {
    function admin_url($path = '', $scheme = 'admin') {
        return 'http://example.com/wp-admin/' . $path;
    }
}

// WordPress コア関数: URL の末尾スラッシュ操作
if (!function_exists('untrailingslashit')) {
    function untrailingslashit($value) {
        return rtrim($value, '/\\');
    }
}
if (!function_exists('trailingslashit')) {
    function trailingslashit($value) {
        return untrailingslashit($value) . '/';
    }
}

// WordPress コア関数: クエリ引数の追加（簡易実装）
if (!function_exists('add_query_arg')) {
    function add_query_arg($args, $url = '') {
        // テスト用: 空配列の場合はURLをそのまま返す
        if (is_array($args) && empty($args)) {
            return $url;
        }
        return $url . '?' . http_build_query($args);
    }
}

// WP_HTML_Tag_Processor のテスト用モッククラス（WP 6.2+ 相当の簡易実装）
// add_editor_no_link_click_class 等のテストで使用する。
if (!class_exists('WP_HTML_Tag_Processor')) {
    class WP_HTML_Tag_Processor {
        private $html;
        private $tags = [];
        private $cursor = -1;
        private $attr_changes = [];
        private $class_additions = [];

        public function __construct( $html ) {
            $this->html = $html;
            $this->parse_tags();
        }

        // 開始タグの位置・名前・属性文字列をパースする簡易実装。
        private function parse_tags() {
            $len = strlen( $this->html );
            $i = 0;
            while ( $i < $len ) {
                // コメントをスキップ。
                if ( substr( $this->html, $i, 4 ) === '<!--' ) {
                    $end = strpos( $this->html, '-->', $i + 4 );
                    $i = $end === false ? $len : $end + 3;
                    continue;
                }
                // <script> ブロックをスキップ。
                if ( preg_match( '/\G<script\b[^>]*>/i', $this->html, $m, 0, $i ) ) {
                    $close = stripos( $this->html, '</script', $i + strlen( $m[0] ) );
                    $i = $close === false ? $len : $close + 9;
                    continue;
                }
                // <style> ブロックをスキップ。
                if ( preg_match( '/\G<style\b[^>]*>/i', $this->html, $m, 0, $i ) ) {
                    $close = stripos( $this->html, '</style', $i + strlen( $m[0] ) );
                    $i = $close === false ? $len : $close + 8;
                    continue;
                }
                // 開始タグを検出。
                if ( preg_match( '/\G<([a-zA-Z][a-zA-Z0-9:-]*)((?:\s+(?:[^>"\'=\s]+(?:\s*=\s*(?:"[^"]*"|\'[^\']*\'|[^\s>]*))?))*)(\s*\/?)>/s', $this->html, $m, 0, $i ) ) {
                    $this->tags[] = [
                        'offset' => $i,
                        'length' => strlen( $m[0] ),
                        'name'   => strtolower( $m[1] ),
                        'attrs'  => $m[2],
                    ];
                    $i += strlen( $m[0] );
                    continue;
                }
                $i++;
            }
        }

        public function next_tag( $query = null ) {
            $target = is_string( $query ) ? strtolower( $query ) : null;
            while ( ++$this->cursor < count( $this->tags ) ) {
                if ( $target === null || $this->tags[ $this->cursor ]['name'] === $target ) {
                    return true;
                }
            }
            return false;
        }

        public function get_tag() {
            if ( $this->cursor < 0 || $this->cursor >= count( $this->tags ) ) {
                return null;
            }
            return strtoupper( $this->tags[ $this->cursor ]['name'] );
        }

        public function get_attribute( $name ) {
            if ( $this->cursor < 0 || $this->cursor >= count( $this->tags ) ) {
                return null;
            }
            $idx = $this->cursor;
            // 変更済みの属性を優先する。
            if ( isset( $this->attr_changes[ $idx ][ $name ] ) ) {
                return $this->attr_changes[ $idx ][ $name ];
            }
            $attrs = $this->tags[ $idx ]['attrs'];
            // 引用符付き属性値。
            if ( preg_match( '/(?:^|\s)' . preg_quote( $name, '/' ) . '\s*=\s*(["\'])(.*?)\1/is', $attrs, $m ) ) {
                return $m[2];
            }
            // 引用符なし属性値。
            if ( preg_match( '/(?:^|\s)' . preg_quote( $name, '/' ) . '\s*=\s*([^\s>"\']+)/i', $attrs, $m ) ) {
                return $m[1];
            }
            // 値なし（boolean）属性。
            if ( preg_match( '/(?:^|\s)' . preg_quote( $name, '/' ) . '(?:\s|$)/i', $attrs ) ) {
                return true;
            }
            return null;
        }

        public function set_attribute( $name, $value ) {
            if ( $this->cursor < 0 || $this->cursor >= count( $this->tags ) ) {
                return;
            }
            $this->attr_changes[ $this->cursor ][ $name ] = $value;
        }

        public function add_class( $class ) {
            if ( $this->cursor < 0 || $this->cursor >= count( $this->tags ) ) {
                return;
            }
            $this->class_additions[ $this->cursor ][] = $class;
        }

        public function get_updated_html() {
            $result = $this->html;
            // オフセットがずれないよう逆順で適用する。
            for ( $i = count( $this->tags ) - 1; $i >= 0; $i-- ) {
                $tag = $this->tags[ $i ];
                $name = $tag['name'];
                $attrs = $tag['attrs'];
                $suffix = '';

                // 属性の追加・上書き。
                if ( isset( $this->attr_changes[ $i ] ) ) {
                    foreach ( $this->attr_changes[ $i ] as $attr_name => $attr_value ) {
                        $pattern = '/(\s)' . preg_quote( $attr_name, '/' ) . '\s*=\s*(?:"[^"]*"|\'[^\']*\'|[^\s>]*)/i';
                        if ( preg_match( $pattern, $attrs ) ) {
                            $attrs = preg_replace( $pattern, '$1' . $attr_name . '="' . $attr_value . '"', $attrs, 1 );
                        } else {
                            $suffix .= ' ' . $attr_name . '="' . $attr_value . '"';
                        }
                    }
                }

                // クラスの追加。
                if ( isset( $this->class_additions[ $i ] ) ) {
                    $new_classes = $this->class_additions[ $i ];
                    if ( preg_match( '/(\s)class\s*=\s*(["\'])(.*?)\2/is', $attrs, $m ) ) {
                        $existing = trim( $m[3] );
                        $all = $existing ? $existing . ' ' . implode( ' ', $new_classes ) : implode( ' ', $new_classes );
                        $attrs = preg_replace(
                            '/(\s)class\s*=\s*(["\'])(.*?)\2/is',
                            '$1class=$2' . $all . '$2',
                            $attrs,
                            1
                        );
                    } else {
                        $suffix .= ' class="' . implode( ' ', $new_classes ) . '"';
                    }
                }

                $new_tag = '<' . $name . $attrs . $suffix . '>';
                $result = substr_replace( $result, $new_tag, $tag['offset'], $tag['length'] );
            }
            return $result;
        }
    }
}
