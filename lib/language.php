<?php //Wordpressマルチ言語化の設定
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

global $locale;
//言語の最初の文字がenだったら全てen.moを呼び出す
if (strpos($locale,'en') !== false) {
  $locale = 'en';
}

//親テーマの翻訳ディレクトリ
$language_dir = get_template_directory() . '/languages';
load_theme_textdomain( THEME_NAME, $language_dir );

//以降は正式版公開後に
// //子テーマの翻訳ディレクトリ
// $child_language_dir = get_stylesheet_directory() . '/languages';
// //子テーマの翻訳ファイル
// $child_language_file = $child_language_dir.'/en.mo';

// //Simplicityの多言語化
// if (is_child_theme()) {
//   //子テーマでは子テーマ内の翻訳ファイルを使用
//   if (file_exists($child_language_file)) {
//     load_theme_textdomain( THEME_NAME, $child_language_dir );
//   } else {
//     load_theme_textdomain( THEME_NAME, $language_dir );
//   }
// } else {
//   //親テーマの翻訳ファイル
//   load_theme_textdomain( THEME_NAME, $language_dir );
// }

