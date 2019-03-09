<?php //Wordpressマルチ言語化の設定
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

global $locale;
//_v($locale);
//言語の最初の文字がenだったら全てen.moを呼び出す（英語）
if (preg_match('/^en_/', $locale)) {
  $locale = 'en';
}
//言語の最初の文字がkoだったら全てko.moを呼び出す（韓国語）
if (preg_match('/^ko_/', $locale)) {
  $locale = 'ko';
}

//親テーマの翻訳ディレクトリ
$language_dir = get_template_directory() . '/languages';
//load_theme_textdomain( THEME_NAME, $language_dir );

//子テーマの翻訳ディレクトリ
$child_language_dir = get_stylesheet_directory() . '/languages';
//子テーマの翻訳ファイル
$child_language_file = $child_language_dir.'/'.$locale.'.mo';
// _v(file_exists($child_language_dir));
// _v(file_exists($child_language_file));
// _v(($child_language_file));
//子テーマを利用していて子テーマにファイルがある場合
if (is_child_theme() && file_exists($child_language_file)) {
  load_theme_textdomain( THEME_NAME, $child_language_dir );
} else {
  load_theme_textdomain( THEME_NAME, $language_dir );
}
