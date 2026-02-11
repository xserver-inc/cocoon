<?php
/**
 * WordPress関数のスタブ定義
 *
 * Brain\Monkey が setUp() で初期化される前に必要な
 * WordPress関数のグローバルスタブをここで定義します。
 * これにより、テーマファイルの require 時に
 * "undefined function" エラーが発生しなくなります。
 */

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
    function apply_filters($tag, $value, ...$args) {
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
        return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('esc_url')) {
    function esc_url($url) {
        return filter_var($url, FILTER_SANITIZE_URL);
    }
}

if (!function_exists('wp_kses_post')) {
    function wp_kses_post($data) {
        return $data;
    }
}

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

if (!function_exists('date_i18n')) {
    function date_i18n($format, $timestamp = false, $gmt = false) {
        if ($timestamp === false) {
            return date($format);
        }
        return date($format, $timestamp);
    }
}

if (!function_exists('get_transient')) {
    function get_transient($transient) {
        return false;
    }
}

if (!function_exists('set_transient')) {
    function set_transient($transient, $value, $expiration = 0) {
        return true;
    }
}

if (!function_exists('is_wp_error')) {
    function is_wp_error($thing) {
        return false;
    }
}

if (!function_exists('get_the_ID')) {
    function get_the_ID() {
        return 1;
    }
}

if (!function_exists('get_post_type')) {
    function get_post_type($post = null) {
        return 'post';
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
        return false;
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
    function get_theme_mod($name, $default = false) {
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

if (!function_exists('current_user_can')) {
    function current_user_can($capability) {
        return false;
    }
}

if (!function_exists('wp_remote_get')) {
    function wp_remote_get($url, $args = []) {
        return [];
    }
}

if (!function_exists('wp_remote_post')) {
    function wp_remote_post($url, $args = []) {
        return [];
    }
}

if (!function_exists('wp_remote_retrieve_body')) {
    function wp_remote_retrieve_body($response) {
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

if (!function_exists('term_metadata_exists')) {
    function term_metadata_exists($term_id, $meta_key) {
        return false;
    }
}

if (!function_exists('get_term_meta')) {
    function get_term_meta($term_id, $key = '', $single = false) {
        return '';
    }
}

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
        return false;
    }
}

if (!function_exists('url_to_postid')) {
    function url_to_postid($url) {
        return 0;
    }
}

if (!function_exists('get_post_status')) {
    function get_post_status($id = null) {
        return 'publish';
    }
}

if (!function_exists('get_permalink')) {
    function get_permalink($post = 0) {
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

if (!function_exists('get_post_thumbnail_id')) {
    function get_post_thumbnail_id($post = null) {
        return 0;
    }
}

if (!function_exists('get_post')) {
    function get_post($post = null, $output = OBJECT, $filter = 'raw') {
        return null;
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
        return false;
    }
}

if (!function_exists('wp_rand')) {
    function wp_rand($min = 0, $max = 0) {
        return rand($min, $max);
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
