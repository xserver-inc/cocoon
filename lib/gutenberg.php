<?php //ブロックエディター（Gutenberg）関連
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

//カラーパレット色の取得
if ( !function_exists( 'get_cocoon_editor_color_palette_colors' ) ):
function get_cocoon_editor_color_palette_colors(){
  $colors = array(
    array(
        'name' => __( 'キーカラー', THEME_NAME ),
        'slug' => 'key-color',
        'color' => get_editor_key_color(),
    ),
    array(
        'name' => __( '赤色', THEME_NAME ),
        'slug' => 'red',
        'color' => '#e60033',
    ),
    array(
        'name' => __( 'ピンク', THEME_NAME ),
        'slug' => 'pink',
        'color' => '#e95295',
    ),
    array(
        'name' => __( '紫色', THEME_NAME ),
        'slug' => 'purple',
        'color' => '#884898',
    ),
    array(
        'name' => __( '深紫色', THEME_NAME ),
        'slug' => 'deep',
        'color' => '#55295b',
    ),
    array(
        'name' => __( '紺色', THEME_NAME ),
        'slug' => 'indigo',
        'color' => '#1e50a2',
    ),
    array(
        'name' => __( '青色', THEME_NAME ),
        'slug' => 'blue',
        'color' => '#0095d9',
    ),
    array(
        'name' => __( '天色', THEME_NAME ),
        'slug' => 'light-blue',
        'color' => '#2ca9e1',
    ),
    array(
        'name' => __( '浅葱色', THEME_NAME ),
        'slug' => 'cyan',
        'color' => '#00a3af',
    ),
    array(
        'name' => __( '深緑色', THEME_NAME ),
        'slug' => 'teal',
        'color' => '#007b43',
    ),
    array(
        'name' => __( '緑色', THEME_NAME ),
        'slug' => 'green',
        'color' => '#3eb370',
    ),
    array(
        'name' => __( '黄緑色', THEME_NAME ),
        'slug' => 'light-green',
        'color' => '#8bc34a',
    ),
    array(
        'name' => __( 'ライム', THEME_NAME ),
        'slug' => 'lime',
        'color' => '#c3d825',
    ),
    array(
        'name' => __( '黄色', THEME_NAME ),
        'slug' => 'yellow',
        'color' => '#ffd900',
    ),
    array(
        'name' => __( 'アンバー', THEME_NAME ),
        'slug' => 'amber',
        'color' => '#ffc107',
    ),
    array(
        'name' => __( 'オレンジ', THEME_NAME ),
        'slug' => 'orange',
        'color' => '#f39800',
    ),
    array(
        'name' => __( 'ディープオレンジ', THEME_NAME ),
        'slug' => 'deep-orange',
        'color' => '#ea5506',
    ),
    array(
        'name' => __( '茶色', THEME_NAME ),
        'slug' => 'brown',
        'color' => '#954e2a',
    ),
    array(
        'name' => __( '灰色', THEME_NAME ),
        'slug' => 'grey',
        'color' => '#949495',
    ),
    array(
        'name' => __( '黒', THEME_NAME ),
        'slug' => 'black',
        'color' => '#333333',
    ),
    array(
        'name' => __( '白', THEME_NAME ),
        'slug' => 'white',
        'color' => '#ffffff',
    ),
  );
  //カラーパレットフック
  $colors = apply_filters('block_editor_color_palette_colors', $colors);
  return $colors;
}
endif;

//ブロックエディターカラーパレット用のCSS
if ( !function_exists( 'get_block_editor_color_palette_css' ) ):
function get_block_editor_color_palette_css(){
  $color_sets = get_cocoon_editor_color_palette_colors();
  $default_colors = array(get_editor_key_color(), '#e60033', '#e95295', '#884898', '#55295b', '#1e50a2', '#0095d9', '#2ca9e1', '#00a3af', '#007b43', '#3eb370', '#8bc34a', '#c3d825', '#ffd900', '#ffc107', '#f39800', '#ea5506', '#954e2a', '#949495', '#333333', '#ffffff');
  $css = '';
  foreach ($color_sets as $color_set) {
    $color = $color_set['color'];
    //デフォルトで定義されていない色があった場合
    if (!in_array($color, $default_colors)) {
     $name = 'color--'.str_replace('#', '', $color);
      $css .= get_block_editor_color_style(null, $name, $color);
    }
  }
  return $css;
}
endif;
