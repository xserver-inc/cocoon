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
    array(
        'name' => __( '淡い青', THEME_NAME ),
        'slug' => 'watery-blue',
        'color' => '#f3fafe',
    ),
    array(
        'name' => __( '淡い黄色', THEME_NAME ),
        'slug' => 'watery-yellow',
        'color' => '#fff7cc',
    ),
    array(
        'name' => __( '淡い赤', THEME_NAME ),
        'slug' => 'watery-red',
        'color' => '#fdf2f2',
    ),
    array(
        'name' => __( '淡い緑', THEME_NAME ),
        'slug' => 'watery-green',
        'color' => '#ebf8f4',
    ),
    // array(
    //     'name' => __( '桜', THEME_NAME ),
    //     'slug' => 'sakura',
    //     'color' => '#fef4f4',
    // ),
    // array(
    //     'name' => __( '象牙', THEME_NAME ),
    //     'slug' => 'ebur',
    //     'color' => '#f8f4e6',
    // ),
    // array(
    //     'name' => __( '月白', THEME_NAME ),
    //     'slug' => 'luna-white',
    //     'color' => '#eaf4fc',
    // ),
    // array(
    //     'name' => __( '白菫色', THEME_NAME ),
    //     'slug' => 'white-violet',
    //     'color' => '#eaedf7',
    // ),
    // array(
    //     'name' => __( '白花色', THEME_NAME ),
    //     'slug' => 'white-flower',
    //     'color' => '#e8ecef',
    // ),
    // array(
    //     'name' => __( '藍白', THEME_NAME ),
    //     'slug' => 'indigo-white',
    //     'color' => '#ebf6f7',
    // ),
    // array(
    //     'name' => __( '白磁', THEME_NAME ),
    //     'slug' => 'white-porcelain',
    //     'color' => '#f8fbf8',
    // ),
    // array(
    //     'name' => __( '生成り色', THEME_NAME ),
    //     'slug' => 'kinari',
    //     'color' => '#fbfaf5',
    // ),
    array(
        'name' => __( '拡張色A', THEME_NAME ),
        'slug' => 'ex-a',
        'color' => get_block_editor_extended_palette_color_a(),
    ),
    array(
        'name' => __( '拡張色B', THEME_NAME ),
        'slug' => 'ex-b',
        'color' => get_block_editor_extended_palette_color_b(),
    ),
    array(
        'name' => __( '拡張色C', THEME_NAME ),
        'slug' => 'ex-c',
        'color' => get_block_editor_extended_palette_color_c(),
    ),
    array(
        'name' => __( '拡張色D', THEME_NAME ),
        'slug' => 'ex-d',
        'color' => get_block_editor_extended_palette_color_d(),
    ),
    array(
        'name' => __( '拡張色E', THEME_NAME ),
        'slug' => 'ex-e',
        'color' => get_block_editor_extended_palette_color_e(),
    ),
    array(
        'name' => __( '拡張色F', THEME_NAME ),
        'slug' => 'ex-f',
        'color' => get_block_editor_extended_palette_color_f(),
    ),
  );
  //カラーパレットフック
  $colors = apply_filters('block_editor_color_palette_colors', $colors);
  return $colors;
}
endif;

// //ブロックエディターカラーパレット用のCSS
// if ( !function_exists( 'get_block_editor_color_palette_css' ) ):
// function get_block_editor_color_palette_css(){
//   $color_sets = get_cocoon_editor_color_palette_colors();
//   $default_colors = array(get_editor_key_color(), '#e60033', '#e95295', '#884898', '#55295b', '#1e50a2', '#0095d9', '#2ca9e1', '#00a3af', '#007b43', '#3eb370', '#8bc34a', '#c3d825', '#ffd900', '#ffc107', '#f39800', '#ea5506', '#954e2a', '#949495', '#333333', '#ffffff');
//   $css = '';
//   foreach ($color_sets as $color_set) {
//     $color = $color_set['color'];
//     //デフォルトで定義されていない色があった場合
//     if (!in_array($color, $default_colors)) {
//      $name = 'color--'.str_replace('#', '', $color);
//       $css .= get_block_editor_color_style(null, $name, $color);
//     }
//   }
//   return $css;
// }
// endif;

//ブロックエディターカラーパレット用のCSS
if ( !function_exists( 'get_block_editor_color_palette_css' ) ):
function get_block_editor_color_palette_css(){
    $colors = get_cocoon_editor_color_palette_colors();
    ob_start();
    foreach ($colors as $color) {
    $slug = $color['slug'];
    $color = $color['color']; ?>


<?php //WordPressデフォルト ?>
.main .has-<?php echo $slug; ?>-background-color {
    background-color: <?php echo $color; ?>;
}
.main .has-<?php echo $slug; ?>-color {
    color: <?php echo $color; ?>;
}
.main .has-<?php echo $slug; ?>-border-color {
    border-color: <?php echo $color; ?>;
}
<?php //囲みボタン ?>
.btn-wrap.has-<?php echo $slug; ?>-background-color > a{
    background-color: <?php echo $color; ?>;
}
.btn-wrap.has-<?php echo $slug; ?>-color > a{
    color: <?php echo $color; ?>;
}
.btn-wrap.has-<?php echo $slug; ?>-border-color > a{
    border-color: <?php echo $color; ?>;
}
<?php //タブボックス ?>
<?php if(is_admin()): ?>
.bb-tab.has-<?php echo $slug; ?>-border-color::before{
    background-color: <?php echo $color; ?>;
}
<?php endif; ?>
.bb-tab.has-<?php echo $slug; ?>-border-color .bb-label{
    background-color: <?php echo $color; ?>;
}
<?php //トグルボックス ?>
.toggle-wrap.has-<?php echo $slug; ?>-border-color .toggle-button{
    background-color: <?php echo $color; ?>;
}
.toggle-wrap.has-<?php echo $slug; ?>-border-color .toggle-button,
.toggle-wrap.has-<?php echo $slug; ?>-border-color .toggle-content{
    border-color: <?php echo $color; ?>;
}
<?php //アイコンリストボックス ?>
.iconlist-box.has-<?php echo $slug; ?>-icon-color li::before{
    color: <?php echo $color; ?>;
}
<?php //マイクロバルーン（背景色） ?>
.micro-balloon.has-<?php echo $slug; ?>-background-color {
  background-color: <?php echo $color; ?>;
  border-color: transparent;
}
.micro-balloon.has-<?php echo $slug; ?>-background-color.micro-bottom::after {
  border-bottom-color: <?php echo $color; ?>;
  border-top-color: transparent;
}
.micro-balloon.has-<?php echo $slug; ?>-background-color::before {
  border-top-color: transparent;
  border-bottom-color: transparent;
}
.micro-balloon.has-<?php echo $slug; ?>-background-color::after {
  border-top-color: <?php echo $color; ?>;
}
<?php //マイクロバルーン（ボーダー色） ?>
.micro-balloon.has-border-color.has-<?php echo $slug; ?>-border-color {
  border-color: <?php echo $color; ?>;
}
.micro-balloon.micro-top.has-<?php echo $slug; ?>-border-color::before {
  border-top-color: <?php echo $color; ?>;
}
.micro-balloon.micro-bottom.has-<?php echo $slug; ?>-border-color::before {
  border-bottom-color: <?php echo $color; ?>;
}
<?php //見出しボックス ?>
.caption-box.has-<?php echo $slug; ?>-border-color .box-label{
  background-color: <?php echo $color; ?>;
}
<?php //タブ見出しボックス ?>
.tab-caption-box.has-<?php echo $slug; ?>-border-color .box-label{
  background-color: <?php echo $color; ?>;
}
.tab-caption-box.has-<?php echo $slug; ?>-border-color .box-content{
  border-color: <?php echo $color; ?>;
}
.tab-caption-box.has-<?php echo $slug; ?>-background-color .box-content{
  background-color: <?php echo $color; ?>;
}
<?php //ラベルボックス ?>
.label-box.has-<?php echo $slug; ?>-border-color .box-content{
  border-color: <?php echo $color; ?>;
}
.label-box.has-<?php echo $slug; ?>-background-color .box-content{
  background-color: <?php echo $color; ?>;
}
<?php //吹き出しボックス ?>
.sbp-l .speech-balloon.has-<?php echo $slug; ?>-border-color::before{
  border-right-color: <?php echo $color; ?>;
}
.sbp-r .speech-balloon.has-<?php echo $slug; ?>-border-color::before{
  border-left-color: <?php echo $color; ?>;
}
.sbp-l .speech-balloon.has-<?php echo $slug; ?>-background-color::after{
  border-right-color: <?php echo $color; ?>;
}
.sbp-r .speech-balloon.has-<?php echo $slug; ?>-background-color::after{
  border-left-color: <?php echo $color; ?>;
}
.sbs-line.sbp-r .speech-balloon.has-<?php echo $slug; ?>-background-color{
  background-color: <?php echo $color; ?>;
}
.sbs-line.sbp-r .speech-balloon.has-<?php echo $slug; ?>-border-color{
  border-color: <?php echo $color; ?>;
}
.speech-wrap.sbs-think .speech-balloon.has-<?php echo $slug; ?>-border-color::before,
.speech-wrap.sbs-think .speech-balloon.has-<?php echo $slug; ?>-border-color::after{
  border-color: <?php echo $color; ?>;
}
.sbs-think .speech-balloon.has-<?php echo $slug; ?>-background-color::before,
.sbs-think .speech-balloon.has-<?php echo $slug; ?>-background-color::after{
  background-color: <?php echo $color; ?>;
}


    <?php
    }//カラーパレットのループ
    $btn_wrap_bk_color = 'transparent';
    if (is_admin()) {
        $btn_wrap_bk_color = '#f8e58c';
    }
    //1回だけ呼び出す ?>
.main .btn-wrap{
    background-color: <?php echo $btn_wrap_bk_color; ?>;
    color: #333;
    border-color: transparent;
    font-size: 16px;
}


.toggle-wrap.has-border-color .toggle-button{
    color: #fff;
}

    <?php // フォントサイズ ?>
.btn-wrap.has-small-font-size > a {
  font-size: 13px;
}
.btn-wrap.has-medium-font-size > a {
  font-size: 20px;
}
.btn-wrap.has-large-font-size > a {
  font-size: 36px;
}
.btn-wrap.has-huge-font-size > a, .btn-wrap.has-larger-font-size > a {
  font-size: 42px;
}
    <?php
    $css = ob_get_clean();
    return $css;
}
endif;
