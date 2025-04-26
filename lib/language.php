<?php //WordPressマルチ言語化の設定
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

//「Function _load_textdomain_just_in_time was called incorrectly. Translation loading for the health-check domain was triggered too early. This is usually an indicator for some code in the plugin or theme running too early. Translations should be loaded at the init action or later. (This message was added in version 6.7.0.)」警告対策
add_action( 'after_setup_theme', function (){
  global $locale;

  //親テーマの翻訳ディレクトリ
  $language_dir = get_template_directory() . '/languages';

  //子テーマの翻訳ディレクトリ
  $child_language_dir = get_stylesheet_directory() . '/languages';
  //子テーマの翻訳ファイル
  $child_language_file = $child_language_dir.'/'.$locale.'.mo';

  //子テーマを利用していて子テーマにファイルがある場合
  if (is_child_theme() && file_exists($child_language_file)) {
    load_theme_textdomain( THEME_NAME, $child_language_dir );
  } else {
    load_theme_textdomain( THEME_NAME, $language_dir );
  }
} );

