<?php //コード設定に必要な定数や関数
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

//ソースコードをハイライト表示する
define('OP_CODE_HIGHLIGHT_ENABLE', 'code_highlight_enable');
if ( !function_exists( 'is_code_highlight_enable' ) ):
function is_code_highlight_enable(){
  return get_theme_option(OP_CODE_HIGHLIGHT_ENABLE);
}
endif;

//行番号を表示する
define('OP_CODE_ROW_NUMBER_ENABLE', 'code_row_number_enable');
if ( !function_exists( 'is_code_row_number_enable' ) ):
function is_code_row_number_enable(){
  return get_theme_option(OP_CODE_ROW_NUMBER_ENABLE);
}
endif;

//ソースコードのライブラリ
define('OP_CODE_HIGHLIGHT_PACKAGE', 'code_highlight_package');
if ( !function_exists( 'get_code_highlight_package' ) ):
function get_code_highlight_package(){
  return get_theme_option(OP_CODE_HIGHLIGHT_PACKAGE, 'light');
}
endif;
if ( !function_exists( 'is_code_highlight_package_light' ) ):
function is_code_highlight_package_light(){
  return get_code_highlight_package() == 'light';
}
endif;

//ソースコードのハイライトタイプ
define('OP_CODE_HIGHLIGHT_STYLE', 'code_highlight_style');
if ( !function_exists( 'get_code_highlight_style' ) ):
function get_code_highlight_style(){
  return get_theme_option(OP_CODE_HIGHLIGHT_STYLE, 'monokai');
}
endif;

//ソースコードをハイライト表示するCSSセレクタ
define('OP_CODE_HIGHLIGHT_CSS_SELECTOR', 'code_highlight_css_selector');
if ( !function_exists( 'get_code_highlight_css_selector' ) ):
function get_code_highlight_css_selector(){
  return get_theme_option(OP_CODE_HIGHLIGHT_CSS_SELECTOR, CODE_HIGHLIGHT_CSS_SELECTOR);
}
endif;

global $_HIGHLIGHT_STYLES;
$_HIGHLIGHT_STYLES = array(
    'a11y-dark' => 'a11y-dark',
    'a11y-light' => 'a11y-light',
    'agate' => 'agate',
    'an-old-hope' => 'an-old-hope',
    'androidstudio' => 'androidstudio',
    'arduino-light' => 'arduino-light',
    'arta' => 'arta',
    'ascetic' => 'ascetic',
    'atelier-cave-dark' => 'atelier-cave-dark',
    'atelier-cave-light' => 'atelier-cave-light',
    'atelier-dune-dark' => 'atelier-dune-dark',
    'atelier-dune-light' => 'atelier-dune-light',
    'atelier-estuary-dark' => 'atelier-estuary-dark',
    'atelier-estuary-light' => 'atelier-estuary-light',
    'atelier-forest-dark' => 'atelier-forest-dark',
    'atelier-forest-light' => 'atelier-forest-light',
    'atelier-heath-dark' => 'atelier-heath-dark',
    'atelier-heath-light' => 'atelier-heath-light',
    'atelier-lakeside-dark' => 'atelier-lakeside-dark',
    'atelier-lakeside-light' => 'atelier-lakeside-light',
    'atelier-plateau-dark' => 'atelier-plateau-dark',
    'atelier-plateau-light' => 'atelier-plateau-light',
    'atelier-savanna-dark' => 'atelier-savanna-dark',
    'atelier-savanna-light' => 'atelier-savanna-light',
    'atelier-seaside-dark' => 'atelier-seaside-dark',
    'atelier-seaside-light' => 'atelier-seaside-light',
    'atelier-sulphurpool-dark' => 'atelier-sulphurpool-dark',
    'atelier-sulphurpool-light' => 'atelier-sulphurpool-light',
    'atom-one-dark' => 'atom-one-dark',
    'atom-one-dark-reasonable' => 'atom-one-dark-reasonable',
    'atom-one-light' => 'atom-one-light',
    'brown-paper' => 'brown-paper',
    'codepen-embed' => 'codepen-embed',
    'color-brewer' => 'color-brewer',
    'darcula' => 'darcula',
    'dark' => 'dark',
    'darkula' => 'darkula',
    'default' => 'default',
    'docco' => 'docco',
    'dracula' => 'dracula',
    'far' => 'far',
    'foundation' => 'foundation',
    'github' => 'github',
    'github-gist' => 'github-gist',
    'gml' => 'gml',
    'googlecode' => 'googlecode',
    'grayscale' => 'grayscale',
    'gruvbox-dark' => 'gruvbox-dark',
    'gruvbox-light' => 'gruvbox-light',
    'hopscotch' => 'hopscotch',
    'hybrid' => 'hybrid',
    'idea' => 'idea',
    'ir-black' => 'ir-black',
    'isbl-editor-dark' => 'isbl-editor-dark',
    'isbl-editor-light' => 'isbl-editor-light',
    'kimbie.dark' => 'kimbie.dark',
    'kimbie.light' => 'kimbie.light',
    'lightfair' => 'lightfair',
    'magula' => 'magula',
    'mono-blue' => 'mono-blue',
    'monokai' => 'monokai',
    'monokai-sublime' => 'monokai-sublime',
    'nord' => 'nord',
    'obsidian' => 'obsidian',
    'ocean' => 'ocean',
    'paraiso-dark' => 'paraiso-dark',
    'paraiso-light' => 'paraiso-light',
    'pojoaque' => 'pojoaque',
    'purebasic' => 'purebasic',
    'qtcreator_dark' => 'qtcreator_dark',
    'qtcreator_light' => 'qtcreator_light',
    'railscasts' => 'railscasts',
    'rainbow' => 'rainbow',
    'school-book' => 'school-book',
    'shades-of-purple' => 'shades-of-purple',
    'solarized-dark' => 'solarized-dark',
    'solarized-light' => 'solarized-light',
    'sunburst' => 'sunburst',
    'tomorrow' => 'tomorrow',
    'tomorrow-night' => 'tomorrow-night',
    'tomorrow-night-blue' => 'tomorrow-night-blue',
    'tomorrow-night-bright' => 'tomorrow-night-bright',
    'tomorrow-night-eighties' => 'tomorrow-night-eighties',
    'vs' => 'vs',
    'vs2015' => 'vs2015',
    'xcode' => 'xcode',
    'xt256' => 'xt256',
    'zenburn' => 'zenburn',
  );
// define('HIGHLIGHT_STYLES', HIGHLIGHT_STYLES);

//数式を表示するか
define('OP_FORMULA_ENABLE', 'formula_enable');
if ( !function_exists( 'is_formula_enable' ) ):
function is_formula_enable(){
  return get_theme_option(OP_FORMULA_ENABLE);
}
endif;

//[math]ショートコードが存在するか
if ( !function_exists( 'is_math_shortcode_exist' ) ):
function is_math_shortcode_exist(){
  return DEBUG_MODE && includes_string(get_the_content(), MATH_SHORTCODE);
}
endif;
