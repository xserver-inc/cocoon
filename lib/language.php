<?php //Wordpressマルチ言語化の設定
global $locale;
//言語の最初の文字がenだったら全てen.moを呼び出す
if (strpos($locale,'en') !== false) {
  $locale = 'en';
}
//Simplicityの多言語化
load_theme_textdomain( THEME_NAME, get_template_directory() . '/languages' );