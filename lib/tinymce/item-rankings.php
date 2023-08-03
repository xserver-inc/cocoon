<?php //ビジュアルエディターのランキングドロップダウン
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

//テーブル内にレコードが存在するとき
if (!is_item_rankings_record_empty() && is_admin_post_page()) {
  add_action('admin_init', 'add_item_rankings_dropdown');
  add_action('admin_head', 'generate_item_rankings');
}


if ( !function_exists( 'add_item_rankings_dropdown' ) ):
function add_item_rankings_dropdown(){
  //if( current_user_can('edit_posts') &&  current_user_can('edit_pages') )  {
    add_filter( 'mce_external_plugins',  'add_item_rankings_to_mce_external_plugins' );
    add_filter( 'mce_buttons_3',  'register_item_rankings' );
  //}
}
endif;

//ボタン用スクリプトの登録
if ( !function_exists( 'add_item_rankings_to_mce_external_plugins' ) ):
function add_item_rankings_to_mce_external_plugins( $plugin_array ){
  $path=get_template_directory_uri() . '/js/item-rankings.js';
  $plugin_array['item_rankings'] = $path;

  return $plugin_array;
}
endif;

//ドロップダウンをTinyMCEに登録
if ( !function_exists( 'register_item_rankings' ) ):
function register_item_rankings( $buttons ){
  array_push( $buttons, 'separator', 'item_rankings' );
  return $buttons;
}
endif;

//値渡し用のJavaScriptを生成
if ( !function_exists( 'generate_item_rankings' ) ):
function generate_item_rankings($value){
  $records = get_item_rankings(null, 'title');
  //_v($records);
  if ($records) {
    echo '<script type="text/javascript">
    var itemRankingsTitle = "'.__( 'ランキング', THEME_NAME ).'";
    var itemRankings = new Array();';

    $count = 0;

    foreach($records as $record){
      //非表示の場合は跳ばす
      if (!$record->visible) {
        continue;
      }
      ?>

      var count = <?php echo $count; ?>;
      itemRankings[count] = new Array();
      itemRankings[count].title  = '<?php echo $record->title; ?>';
      itemRankings[count].id     = '<?php echo $record->id; ?>';
      itemRankings[count].shrotecode = '<?php echo get_item_ranking_shortcode($record->id); ?>';

      <?php
      $count++;
    }
    echo '</script>';
  }

}
endif;

