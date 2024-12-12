<?php //アップデートカスタムフィールドを設置する
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

///////////////////////////////////////
// カスタムボックスの追加
///////////////////////////////////////
add_action( 'admin_menu', 'add_update_custom_box' );
if( !function_exists( 'add_update_custom_box' ) ):
function add_update_custom_box() {
  add_meta_box( 'singular_update_settings', __( '更新日の変更', THEME_NAME ), 'update_custom_box_view', 'post', 'side', 'default' );
  add_meta_box( 'singular_update_settings', __( '更新日の変更', THEME_NAME ), 'update_custom_box_view', 'page', 'side', 'default' );
  //add_meta_box( 'singular_update_settings', __( '更新日の変更', THEME_NAME ), 'update_custom_box_view', 'topic', 'side', 'default' );
  //カスタム投稿タイプに登録
  add_meta_box_custom_post_types( 'singular_update_settings', __( '更新日の変更', THEME_NAME ), 'update_custom_box_view', 'custum_post', 'side', 'default' );
}
endif;

//メインフォーム
if( !function_exists( 'update_custom_box_view' ) ):
function update_custom_box_view() {
  global $post;
  $update_level = get_the_page_update_level();
  if (empty($update_level)) {
    $update_level = 'high';
  }
?>
  <div style="padding-top: 5px; overflow: hidden;">
  <div style="padding:5px 0"><input name="update_level" type="radio" value="high" <?php the_checkbox_checked($update_level, 'high'); ?> /><?php _e( '更新', THEME_NAME ) ?></div>
  <div style="padding: 5px 0"><input name="update_level" type="radio" value="low" <?php the_checkbox_checked($update_level, 'low'); ?> /><?php _e( '更新しない', THEME_NAME ) ?></div>
  <div style="padding: 5px 0"><input name="update_level" type="radio" value="del" <?php the_checkbox_checked($update_level, 'del'); ?> /><?php _e( '更新日の消去', THEME_NAME ) ?></div>
  <div style="padding: 5px 0; margin-bottom: 10px"><input id="update_level_edit"  <?php the_checkbox_checked($update_level, 'edit'); ?>name="update_level" type="radio" value="edit" /><?php _e( '更新日を設定', THEME_NAME ) ?></div>
  <?php
    if( get_the_modified_date( 'c' ) ) {
      $stamp = __( '更新日時:', THEME_NAME ).' <span style="font-weight:bold">' . get_the_modified_date( __( 'M j, Y @ H:i' ) ) . '</span>';
    }
    else {
      $stamp = __( '更新日時:', THEME_NAME ).'<span style="font-weight:bold">未更新</span>';
    }
    $date = date_i18n( get_option('date_format') . ' @ ' . get_option('time_format'), strtotime( $post->post_modified ) );
  ?>
  <style>
  .modtime { padding: 2px 0 1px 0; display: inline !important; height: auto !important; }
  .modtime:before { font: normal 20px/1 'dashicons'; content: '\f145'; color: #888; padding: 0 5px 0 0; top: -1px; left: -1px; position: relative; vertical-align: top; }
  #timestamp_mod_div { padding-top: 5px; line-height: 23px; }
  #timestamp_mod_div p { margin: 8px 0 6px; }
  #timestamp_mod_div input { border-width: 1px; border-style: solid; }
  #timestamp_mod_div select { height: 21px; line-height: 14px; padding:0 24px 0 8px; vertical-align: top;font-size: 12px; }
  #aa_mod, #jj_mod, #hh_mod, #mn_mod { padding: 1px; font-size: 12px; }
  #jj_mod, #hh_mod, #mn_mod { width: 2em; }
  #aa_mod { width: 3.4em; }
  </style>
  <span class="modtime"><?php printf( $stamp, $date ); ?></span>
  <div id="timestamp_mod_div" onkeydown="document.getElementById('update_level_edit').checked=true" onclick="document.getElementById('update_level_edit').checked=true">
  <?php time_mod_form_view(); ?>
  </div>
  </div>
<?php
}
endif;

//更新日時変更の入力フォーム
if( !function_exists( 'time_mod_form_view' ) ):
function time_mod_form_view() {
  global $wp_locale, $post;

  $tab_index = 0;
  $tab_index_attribute = '';
  if ( (int) $tab_index > 0 ) {
    $tab_index_attribute = ' tabindex="' . $tab_index . '"';
  }

  $jj_mod = mysql2date( 'd', $post->post_modified, false );
  $mm_mod = mysql2date( 'm', $post->post_modified, false );
  $aa_mod = mysql2date( 'Y', $post->post_modified, false );
  $hh_mod = mysql2date( 'H', $post->post_modified, false );
  $mn_mod = mysql2date( 'i', $post->post_modified, false );
  $ss_mod = mysql2date( 's', $post->post_modified, false );

  $year = '<label for="aa_mod" class="screen-reader-text">' . __( '年', THEME_NAME ) .
    '</label><input type="text" id="aa_mod" name="aa_mod" value="' .
    $aa_mod . '" size="4" maxlength="4"' . $tab_index_attribute . ' />' . __( '年', THEME_NAME );

  $month = '<label for="mm_mod" class="screen-reader-text">' . __( '月', THEME_NAME )  .
    '</label><select id="mm_mod" name="mm_mod"' . $tab_index_attribute . ">\n";
  for( $i = 1; $i < 13; $i = $i +1 ) {
    $monthnum = zeroise($i, 2);
    $month .= "\t\t\t" . '<option value="' . $monthnum . '" ' . selected( $monthnum, $mm_mod, false ) . '>';
    $month .= $wp_locale->get_month_abbrev( $wp_locale->get_month( $i ) );
    $month .= "</option>\n";
  }
  $month .= '</select>';

  $day = '<label for="jj_mod" class="screen-reader-text">' . __( '日', THEME_NAME )  .
    '</label><input type="text" id="jj_mod" name="jj_mod" value="' .
    $jj_mod . '" size="2" maxlength="2"' . $tab_index_attribute . ' />' . __( '日', THEME_NAME );
  $hour = '<label for="hh_mod" class="screen-reader-text">時' .
    '</label><input type="text" id="hh_mod" name="hh_mod" value="' . $hh_mod .
    '" size="2" maxlength="2"' . $tab_index_attribute . ' />';
  $minute = '<label for="mn_mod" class="screen-reader-text">分' .
    '</label><input type="text" id="mn_mod" name="mn_mod" value="' . $mn_mod .
    '" size="2" maxlength="2"' . $tab_index_attribute . ' />';

  printf( '%1$s %2$s %3$s @ %4$s : %5$s', $year, $month, $day, $hour, $minute );
  echo '<input type="hidden" id="ss_mod" name="ss_mod" value="' . $ss_mod . '" />';
}
endif;

//「修正のみ」は更新しない。それ以外は、それぞれの更新日時に変更する
if (isset( $_POST['update_level'] )) {
  add_filter( 'wp_insert_post_data', 'update_custom_insert_post_data', 10, 2 );
}
if( !function_exists( 'update_custom_insert_post_data' ) ):
function update_custom_insert_post_data( $data, $postarr ){
  $mydata = isset( $_POST['update_level'] ) ? $_POST['update_level'] : null;

  if( ($mydata === 'del') || ($data['post_status'] == 'future') ) {
    $data['post_modified'] = $data['post_date'];
    $data['post_modified_gmt'] = get_gmt_from_date( $data['post_date'] );
  }
  elseif( $mydata === 'low' ){
    unset( $data['post_modified'] );
    unset( $data['post_modified_gmt'] );

    $last_modified = get_post_meta( $postarr['ID'], 'last_modified', true );
    if ( isset($last_modified) ) {
      $data['post_modified'] = $last_modified;
      $data['post_modified_gmt'] = get_gmt_from_date( $last_modified );
    }
  }
  elseif( $mydata === 'edit' ) {
    $aa_mod = $_POST['aa_mod'] <= 0 ? date_i18n('Y') : $_POST['aa_mod'];
    $mm_mod = $_POST['mm_mod'] <= 0 ? date_i18n('n') : $_POST['mm_mod'];
    $jj_mod = $_POST['jj_mod'] > 31 ? 31 : $_POST['jj_mod'];
    $jj_mod = $jj_mod <= 0 ? date_i18n('j') : $jj_mod;
    $hh_mod = $_POST['hh_mod'] > 23 ? $_POST['hh_mod'] -24 : $_POST['hh_mod'];
    $mn_mod = $_POST['mn_mod'] > 59 ? $_POST['mn_mod'] -60 : $_POST['mn_mod'];
    $ss_mod = $_POST['ss_mod'] > 59 ? $_POST['ss_mod'] -60 : $_POST['ss_mod'];
    $modified_date = sprintf( '%04d-%02d-%02d %02d:%02d:%02d', $aa_mod, $mm_mod, $jj_mod, $hh_mod, $mn_mod, $ss_mod );
    $post_date = get_the_date('Y-m-d H:i:s', $post->ID);
if (( strtotime($modified_date) < strtotime($post_date) ) ||
    ( ! wp_checkdate( $mm_mod, $jj_mod, $aa_mod, $modified_date ))) {
      unset( $data['post_modified'] );
      unset( $data['post_modified_gmt'] );
      return $data;
    }
    $data['post_modified'] = $modified_date;
    $data['post_modified_gmt'] = get_gmt_from_date( $modified_date );
  }

  add_post_meta( $postarr['ID'], 'last_modified', $data['post_modified'], true);
  update_post_meta( $postarr['ID'], 'last_modified', $data['post_modified']);

  return $data;
}
endif;

//データ保存
add_action('save_post', 'update_custom_box_save_data');
if ( !function_exists( 'update_custom_box_save_data' ) ):
function update_custom_box_save_data(){
  $id = get_the_ID();
  //タイトル
  $update_level = null;
  if ( isset( $_POST['update_level'] ) ){
    $update_level = $_POST['update_level'];
    $update_level_key = 'update_level';
    add_post_meta($id, $update_level_key, $update_level, true);
    update_post_meta($id, $update_level_key, $update_level);
  }
}
endif;

//アップデートレベルの取得
if ( !function_exists( 'get_the_page_update_level' ) ):
function get_the_page_update_level(){
  $the_id = get_the_ID();
  $value = trim(get_post_meta($the_id, 'update_level', true));
  return $value;
}
endif;
