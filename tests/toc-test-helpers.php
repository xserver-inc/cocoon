<?php
/**
 * TocTest用のスタブ関数・定数定義
 *
 * toc.php が依存するWordPress関数と定数のうち、
 * wp-mock-functions.php でカバーされていないものを定義します。
 * 名前空間付きテストファイルから使うため、
 * グローバル名前空間で定義する別ファイルとして分離しています。
 */

// WordPressのOBJECT定数（get_postのデフォルト引数で使用）
if (!defined('OBJECT')) {
    define('OBJECT', 'OBJECT');
}

// toc.php が依存する定数
if (!defined('BEFORE_1ST_H2_TOC_PRIORITY_HIGH')) {
    define('BEFORE_1ST_H2_TOC_PRIORITY_HIGH', 10000);
}
if (!defined('BEFORE_1ST_H2_TOC_PRIORITY_STANDARD')) {
    define('BEFORE_1ST_H2_TOC_PRIORITY_STANDARD', 10003);
}
if (!defined('H2_REG')) {
    define('H2_REG', '/<h2.*?>/i');
}

// toc.php に依存するスタブ関数
if (!function_exists('is_toc_toggle_switch_enable')) {
    function is_toc_toggle_switch_enable() { return false; }
}
if (!function_exists('is_toc_content_visible')) {
    function is_toc_content_visible() { return true; }
}
if (!function_exists('get_additional_toc_classes')) {
    function get_additional_toc_classes() { return ''; }
}
if (!function_exists('is_single_toc_visible')) {
    function is_single_toc_visible() { return true; }
}
if (!function_exists('is_page_toc_visible')) {
    function is_page_toc_visible() { return true; }
}
if (!function_exists('is_category_toc_visible')) {
    function is_category_toc_visible() { return true; }
}
if (!function_exists('is_tag_toc_visible')) {
    function is_tag_toc_visible() { return true; }
}
if (!function_exists('is_the_page_toc_visible')) {
    function is_the_page_toc_visible() { return true; }
}
if (!function_exists('get_the_category_content')) {
    function get_the_category_content($id = null, $expanded = false) { return ''; }
}
if (!function_exists('get_the_tag_content')) {
    function get_the_tag_content($id = null, $expanded = false) { return ''; }
}
if (!function_exists('get_h2_included_in_body')) {
    // 最初のH2タグを返す
    function get_h2_included_in_body($content) {
        if (preg_match('/<h2.*?>.*?<\/h2>/is', $content, $m)) {
            return $m[0];
        }
        return '';
    }
}
// get_toc_tag() → additional-classes.php が呼ぶ関数のスタブ
if (!function_exists('get_toc_number_type')) {
    function get_toc_number_type() { return 'ol'; }
}
if (!function_exists('get_toc_list_type_class')) {
    function get_toc_list_type_class() { return ' toc-list-ol'; }
}
