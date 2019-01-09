<?php //コード設定に必要な定数や関数
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

//ソースコードをハイライト表示するか
define('OP_CODE_HIGHLIGHT_ENABLE', 'code_highlight_enable');
if ( !function_exists( 'is_code_highlight_enable' ) ):
function is_code_highlight_enable(){
  return get_theme_option(OP_CODE_HIGHLIGHT_ENABLE);
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
  return get_theme_option(OP_CODE_HIGHLIGHT_CSS_SELECTOR, '.entry-content pre');
}
endif;

global $_HIGHLIGHT_STYLES;
$_HIGHLIGHT_STYLES = array(
    'agate' => 'agate',
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
    'brown-paper' => 'brown-paper',
    'codepen-embed' => 'codepen-embed',
    'color-brewer' => 'color-brewer',
    'dark' => 'dark',
    'darkula' => 'darkula',
    'default' => 'default',
    'docco' => 'docco',
    'dracula' => 'dracula',
    'far' => 'far',
    'foundation' => 'foundation',
    'github-gist' => 'github-gist',
    'github' => 'github',
    'googlecode' => 'googlecode',
    'grayscale' => 'grayscale',
    'gruvbox-dark' => 'gruvbox-dark',
    'gruvbox-light' => 'gruvbox-light',
    'hopscotch' => 'hopscotch',
    'hybrid' => 'hybrid',
    'idea' => 'idea',
    'ir-black' => 'ir-black',
    'kimbie.dark' => 'kimbie.dark',
    'kimbie.light' => 'kimbie.light',
    'magula' => 'magula',
    'mono-blue' => 'mono-blue',
    'monokai-sublime' => 'monokai-sublime',
    'monokai' => 'monokai',
    'obsidian' => 'obsidian',
    'paraiso-dark' => 'paraiso-dark',
    'paraiso-light' => 'paraiso-light',
    'pojoaque' => 'pojoaque',
    'purebasic' => 'purebasic',
    'qtcreator_dark' => 'qtcreator_dark',
    'qtcreator_light' => 'qtcreator_light',
    'railscasts' => 'railscasts',
    'rainbow' => 'rainbow',
    'school-book' => 'school-book',
    'solarized-dark' => 'solarized-dark',
    'solarized-light' => 'solarized-light',
    'sunburst' => 'sunburst',
    'tomorrow-night-blue' => 'tomorrow-night-blue',
    'tomorrow-night-bright' => 'tomorrow-night-bright',
    'tomorrow-night-eighties' => 'tomorrow-night-eighties',
    'tomorrow-night' => 'tomorrow-night',
    'tomorrow' => 'tomorrow',
    'vs' => 'vs',
    'xcode' => 'xcode',
    'xt256' => 'xt256',
    'zenburn' => 'zenburn',
    'agate' => 'agate',
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
    'brown-paper' => 'brown-paper',
    'codepen-embed' => 'codepen-embed',
    'color-brewer' => 'color-brewer',
    'dark' => 'dark',
    'darkula' => 'darkula',
    'default' => 'default',
    'docco' => 'docco',
    'dracula' => 'dracula',
    'far' => 'far',
    'foundation' => 'foundation',
    'github-gist' => 'github-gist',
    'github' => 'github',
    'googlecode' => 'googlecode',
    'grayscale' => 'grayscale',
    'gruvbox-dark' => 'gruvbox-dark',
    'gruvbox-light' => 'gruvbox-light',
    'hopscotch' => 'hopscotch',
    'hybrid' => 'hybrid',
    'idea' => 'idea',
    'ir-black' => 'ir-black',
    'kimbie.dark' => 'kimbie.dark',
    'kimbie.light' => 'kimbie.light',
    'magula' => 'magula',
    'mono-blue' => 'mono-blue',
    'monokai-sublime' => 'monokai-sublime',
    'monokai' => 'monokai',
    'obsidian' => 'obsidian',
    'paraiso-dark' => 'paraiso-dark',
    'paraiso-light' => 'paraiso-light',
    'pojoaque' => 'pojoaque',
    'purebasic' => 'purebasic',
    'qtcreator_dark' => 'qtcreator_dark',
    'qtcreator_light' => 'qtcreator_light',
    'railscasts' => 'railscasts',
    'rainbow' => 'rainbow',
    'school-book' => 'school-book',
    'solarized-dark' => 'solarized-dark',
    'solarized-light' => 'solarized-light',
    'sunburst' => 'sunburst',
    'tomorrow-night-blue' => 'tomorrow-night-blue',
    'tomorrow-night-bright' => 'tomorrow-night-bright',
    'tomorrow-night-eighties' => 'tomorrow-night-eighties',
    'tomorrow-night' => 'tomorrow-night',
    'tomorrow' => 'tomorrow',
    'vs' => 'vs',
    'xcode' => 'xcode',
    'zenburn' => 'zenburn',
    'xt256' => 'xt256',
  );
// define('HIGHLIGHT_STYLES', HIGHLIGHT_STYLES);
