<?php //よく利用するHTML（レイアウトなど）
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */

add_action('admin_init', 'add_shortcodes_dropdown');
add_action('admin_head', 'generate_shortcodes_is');

if ( !function_exists( 'add_shortcodes_dropdown' ) ):
function add_shortcodes_dropdown(){
  //if( current_user_can('edit_posts') &&  current_user_can('edit_pages') )  {
    add_filter( 'mce_external_plugins',  'add_shortcodes_to_mce_external_plugins' );
    add_filter( 'mce_buttons_3',  'register_shortcodes' );
  //}
}
endif;

//ボタン用スクリプトの登録
if ( !function_exists( 'add_shortcodes_to_mce_external_plugins' ) ):
function add_shortcodes_to_mce_external_plugins( $plugin_array ){
  $path=get_template_directory_uri() . '/js/shortcodes.js';
  $plugin_array['shortcodes'] = $path;
  return $plugin_array;
}
endif;

//ドロップダウンをTinyMCEに登録
if ( !function_exists( 'register_shortcodes' ) ):
function register_shortcodes( $buttons ){
  array_push( $buttons, 'separator', 'shortcodes' );
  return $buttons;
}
endif;

//吹き出しの値渡し用のJavaScriptを生成
if ( !function_exists( 'generate_shortcodes_is' ) ):
function generate_shortcodes_is($value){
  echo '<script type="text/javascript">
  var shortcodesTitle = "'.__( 'ショートコード', THEME_NAME ).'";
  var shortcodes = new Array();';

  //広告ショートコード
  $before = '[ad]';
  $after = '';
  ?>

  shortcodes[0] = new Array();
  shortcodes[0].title  = '<?php echo __( '広告 [ad]', THEME_NAME ); ?>';
  shortcodes[0].tag = '<?php echo $before; ?>';
  shortcodes[0].before = '<?php echo $before; ?>';
  shortcodes[0].after = '<?php echo $after; ?>';

  <?php
  $msg = __( 'こちらのコンテンツはログインユーザーのみに表示されます。', THEME_NAME );
  $content = __( 'ログインユーザーに表示するコンテンツを入力してください。', THEME_NAME );
  //ログインユーザーのみショートコード
  $before = '[login_user_only msg="'.$msg.'"]';
  $after = '[/login_user_only]';
   ?>
  shortcodes[1] = new Array();
  shortcodes[1].title  = '<?php echo __( 'ログインユーザーのみ表示', THEME_NAME ); ?>';
  shortcodes[1].tag = '<?php echo $before.$content.$after; ?>';
  shortcodes[1].before = '<?php echo $before; ?>';
  shortcodes[1].after = '<?php echo $after; ?>';

  <?php //新着記事一覧のショートコード
  $before = '[new_list count="5" type="default" cats="all" children="0" post_type="post"]';
  $after = '';
   ?>
  shortcodes[2] = new Array();
  shortcodes[2].title  = '<?php echo __( '新着記事一覧', THEME_NAME ); ?>';
  shortcodes[2].tag = '<?php echo $before.$after; ?>';
  shortcodes[2].before = '<?php echo $before; ?>';
  shortcodes[2].after = '<?php echo $after; ?>';

  <?php //人気記事一覧のショートコード
  $before = '[popular_list days="all" rank="0" pv="0" count="5" type="default" cats="all"]';
  $after = '';
   ?>
  shortcodes[3] = new Array();
  shortcodes[3].title  = '<?php echo __( '人気記事一覧', THEME_NAME ); ?>';
  shortcodes[3].tag = '<?php echo $before.$after; ?>';
  shortcodes[3].before = '<?php echo $before; ?>';
  shortcodes[3].after = '<?php echo $after; ?>';

  <?php //プロフィールボックスのショートコード
  $msg = __( 'この記事を書いた人', THEME_NAME );
  $before = '[author_box label=';
  $after = ']';
   ?>
  shortcodes[4] = new Array();
  shortcodes[4].title  = '<?php echo __( 'プロフィールボックス', THEME_NAME ); ?>';
  shortcodes[4].tag = '<?php echo $before.$msg.$after; ?>';
  shortcodes[4].before = '<?php echo $before; ?>';
  shortcodes[4].after = '<?php echo $after; ?>';

  <?php //商品追加ショートコード
  $msg = __( 'ASIN', THEME_NAME );
  $kw = __( 'キーワード', THEME_NAME );
  $before = '[amazon asin="';
  $after = '" kw="'.$kw.'"]';
   ?>
  shortcodes[5] = new Array();
  shortcodes[5].title  = '<?php echo __( 'Amazon商品リンク', THEME_NAME ); ?>';
  shortcodes[5].tag = '<?php echo $before.$msg.$after; ?>';
  shortcodes[5].before = '<?php echo $before; ?>';
  shortcodes[5].after = '<?php echo $after; ?>';

  <?php //商品追加ショートコード
  $title = __( '新しい商品のタイトル', THEME_NAME );
  $after = '" kw="'.$kw.'" title="'.$title.'"]';
   ?>
  shortcodes[6] = new Array();
  shortcodes[6].title  = '<?php echo __( 'Amazon商品リンク（商品タイトル変更）', THEME_NAME ); ?>';
  shortcodes[6].tag = '<?php echo $before.$msg.$after; ?>';
  shortcodes[6].before = '<?php echo $before; ?>';
  shortcodes[6].after = '<?php echo $after; ?>';

  <?php //商品追加ショートコード
  $after = '" kw="'.$kw.'" amazon=0 rakuten=0 yahoo=0]';
   ?>
  shortcodes[7] = new Array();
  shortcodes[7].title  = '<?php echo __( 'Amazon商品リンク（全ボタン非表示）', THEME_NAME ); ?>';
  shortcodes[7].tag = '<?php echo $before.$msg.$after; ?>';
  shortcodes[7].before = '<?php echo $before; ?>';
  shortcodes[7].after = '<?php echo $after; ?>';

  <?php //商品追加ショートコード
  $msg = __( '商品番号', THEME_NAME );
  $before = '[rakuten id="';
  $after = '" kw="'.$kw.'"]';
  ?>
  shortcodes[8] = new Array();
  shortcodes[8].title  = '<?php echo __( '楽天商品リンク', THEME_NAME ); ?>';
  shortcodes[8].tag = '<?php echo $before.$msg.$after; ?>';
  shortcodes[8].before = '<?php echo $before; ?>';
  shortcodes[8].after = '<?php echo $after; ?>';

  <?php //商品追加ショートコード
  $before = '[rakuten id="';
  $after = '" kw="'.$kw.'" title="'.$title.'"]';
  ?>
  shortcodes[9] = new Array();
  shortcodes[9].title  = '<?php echo __( '楽天商品リンク（商品タイトル変更）', THEME_NAME ); ?>';
  shortcodes[9].tag = '<?php echo $before.$msg.$after; ?>';
  shortcodes[9].before = '<?php echo $before; ?>';
  shortcodes[9].after = '<?php echo $after; ?>';

  <?php //商品追加ショートコード
  $before = '[rakuten id="';
  $after = '" kw="'.$kw.'" amazon=0 rakuten=0 yahoo=0]';
  ?>
  shortcodes[10] = new Array();
  shortcodes[10].title  = '<?php echo __( '楽天商品リンク（全ボタン非表示）', THEME_NAME ); ?>';
  shortcodes[10].tag = '<?php echo $before.$msg.$after; ?>';
  shortcodes[10].before = '<?php echo $before; ?>';
  shortcodes[10].after = '<?php echo $after; ?>';

  <?php
  echo '</script>';
}
endif;

