<?php //広告カスタムフィールドを設置する

///////////////////////////////////////
// カスタムボックスの追加
///////////////////////////////////////
add_action('admin_menu', 'add_ad_custom_box');
if ( !function_exists( 'add_ad_custom_box' ) ):
function add_ad_custom_box(){
  //広告ボックス
  add_meta_box( 'singular_ad_settings',__( '広告の設定', THEME_NAME ), 'view_ad_custom_box', 'post', 'side' );
  add_meta_box( 'singular_ad_settings',__( '広告の設定', THEME_NAME ), 'view_ad_custom_box', 'page', 'side' );
  add_meta_box( 'singular_ad_settings',__( '広告の設定', THEME_NAME ), 'view_ad_custom_box', 'topic', 'side' );
}
endif;


///////////////////////////////////////
// AdSenseの設定
///////////////////////////////////////
if ( !function_exists( 'view_ad_custom_box' ) ):
function view_ad_custom_box(){
  $the_page_ads_novisible = get_post_meta(get_the_ID(), 'the_page_ads_novisible', true);

  //var_dump($the_page_ads_novisible);

  echo '<label><input type="checkbox" name="the_page_ads_novisible"';
  if( $the_page_ads_novisible ){echo " checked";}
  echo '>'.__( '広告の除外', THEME_NAME ).'</label>';
  echo '<p class="howto">'.__( 'ページ上の広告（AdSenseなど）をページ上から取り除きます。テーマカスタマイザーの「広告」項目からカテゴリごとの設定も行えます。', THEME_NAME ).'</p>';

  // $options = array(
  //   'display' => '表示する',
  //   'nondisplay' => '表示しない',
  // );
  // generate_radiobox_tag('the_page_ads_novisible', $options, $the_page_ads_novisible);
  // generate_tips_tag(__( 'ページ上の広告（AdSenseなど）をページ上から取り除きます。テーマカスタマイザーの「広告」項目からカテゴリごとの設定も行えます。', THEME_NAME ));
}
endif;


add_action('save_post', 'ad_custom_box_save_data');
if ( !function_exists( 'ad_custom_box_save_data' ) ):
function ad_custom_box_save_data(){
  $id = get_the_ID();
  //広告の除外
  $the_page_ads_novisible = !empty($_POST['the_page_ads_novisible']) ? 1 : 0;
  $the_page_ads_novisible_key = 'the_page_ads_novisible';
  add_post_meta($id, $the_page_ads_novisible_key, $the_page_ads_novisible, true);
  update_post_meta($id, $the_page_ads_novisible_key, $the_page_ads_novisible);
}
endif;


//広告を除外しているか
if ( !function_exists( 'is_the_page_ads_novisible' ) ):
function is_the_page_ads_novisible(){
  return get_post_meta(get_the_ID(), 'the_page_ads_novisible', true);
}
endif;


//広告を表示するか
if ( !function_exists( 'is_the_page_ads_visible' ) ):
function is_the_page_ads_visible(){
  return !is_the_page_ads_novisible();
}
endif;
