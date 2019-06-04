<?php //SEOカスタムフィールドを設置する
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
add_action('admin_menu', 'add_seo_custom_box');
if ( !function_exists( 'add_seo_custom_box' ) ):
function add_seo_custom_box(){
  //SEOボックス
  add_meta_box( 'singular_seo_settings',__( 'SEO設定', THEME_NAME ), 'seo_custom_box_view', 'post', 'normal', 'core' );
  add_meta_box( 'singular_seo_settings',__( 'SEO設定', THEME_NAME ), 'seo_custom_box_view', 'page', 'normal', 'core' );
  //カスタム投稿タイプに登録
  add_meta_box_custom_post_types( 'singular_seo_settings',__( 'SEO設定', THEME_NAME ), 'seo_custom_box_view', 'custum_post', 'normal', 'core' );
}
endif;

///////////////////////////////////////
// SEO設定
///////////////////////////////////////
//SEO設定の文字カウント
if (is_admin_editor_counter_visible()) {
  add_action( 'admin_head-post-new.php', 'seo_settings_admin_script' );
  add_action( 'admin_head-post.php', 'seo_settings_admin_script' );
  add_action( 'admin_head-page-new.php', 'seo_settings_admin_script' );
  add_action( 'admin_head-page.php', 'seo_settings_admin_script' );
  add_action( 'admin_head-topic-new.php', 'seo_settings_admin_script' );
  add_action( 'admin_head-topic.php', 'seo_settings_admin_script' );
}

if ( !function_exists( 'seo_settings_admin_script' ) ):
function seo_settings_admin_script() {?>
<script type="text/javascript">
jQuery(document).ready(function($){
  //in_selの文字数をカウントしてout_selに出力する
  function count_characters(in_sel, out_sel) {
    var val = $(in_sel).val();
    if ( val ) {
      $(out_sel).html(val.length);
    }
  }
  //SEOタイトルの文字数取得
  $("#seo-title").bind("keydown keyup keypress change",function(){
    count_characters("#seo-title", ".seo-title-count");
  });
  count_characters("#seo-title", ".seo-title-count");

  //SEOメタディスクリプションの文字数取得
  $("#meta-description").bind("keydown keyup keypress change",function(){
    count_characters("#meta-description", ".meta-description-count");
  });
  count_characters("#meta-description", ".meta-description-count");

  //Wordpressタイトルの文字数
  $('#titlewrap').after('<div class="str-wp-title-cou" style="position:absolute;top:-23px;right:0;color:#666;background-color:#f7f7f7;padding:1px 2px;border-radius:5px;border:1px solid #ccc;"><?php _e( '文字数', THEME_NAME ); ?>:<span class="wp-title-count">0</span></div>');
  $('#title').bind("keydown keyup keypress change",function(){
    count_characters('#title', '.wp-title-count');
  });
  count_characters('#title', '.wp-title-count');
});
</script><?php
}
endif;

if ( !function_exists( 'seo_custom_box_view' ) ):
function seo_custom_box_view(){
  $the_page_seo_title = get_the_page_seo_title();
  $the_page_meta_description = get_the_page_meta_description();
  $the_page_meta_keywords = get_the_page_meta_keywords();
  $the_page_noindex = is_the_page_noindex();
  $the_page_nofollow = is_the_page_nofollow();

  //タイトル
  echo '<label class="box-label">'.__( 'SEOタイトル', THEME_NAME );
  if (is_admin_editor_counter_visible()) {
    echo '<span class="str-count">'.__( '文字数', THEME_NAME ).':<span class="seo-title-count">0</span></span>';
  }
  echo '</label>';
  echo '<input id="seo-title" type="text" style="width:100%" placeholder="'.__( 'タイトルを入力してください。', THEME_NAME ).'" name="the_page_seo_title" value="'.$the_page_seo_title.'" />';
  echo '<p class="howto">'.__( '検索エンジンに表示させたいタイトルを入力してください。記事のタイトルより、こちらに入力したテキストが優先的にタイトルタグ(&lt;title&gt;)に挿入されます。一般的に日本語の場合は、32文字以内が最適とされています。（※ページやインデックスの見出し部分には「記事のタイトル」が利用されます）', THEME_NAME ).'</p>';


  //メタディスクリプション
  echo '<label class="box-label">'.__( 'メタディスクリプション', THEME_NAME );
  if (is_admin_editor_counter_visible()) {
    echo '<span class="str-count">'.__( '文字数', THEME_NAME ).':<span class="meta-description-count">0</span></span>';
  }
  echo '</label>';
  echo '<textarea id="meta-description" style="width:100%" placeholder="'.__( '記事の説明文を入力してください。', THEME_NAME ).'" name="the_page_meta_description" rows="3">'.$the_page_meta_description.'</textarea>';
  echo '<p class="howto">'.__( '記事の説明を入力してください。日本語では、およそ120文字前後の入力をおすすめします。スマホではそのうちの約50文字が表示されます。こちらに入力したメタディスクリプションはブログカードのスニペット（抜粋文部分）にも利用されます。こちらに入力しない場合は、「抜粋」に入力したものがメタディスクリプションとして挿入されます。', THEME_NAME ).'</p>';

  //メタキーワード
  echo '<label class="box-label">'.__( 'メタキーワード', THEME_NAME ).'</label>';
  echo '<input type="text" style="width:100%" placeholder="'.__( '記事の関連キーワードを半角カンマ区切りで入力してください。', THEME_NAME ).'" name="the_page_meta_keywords" value="'.$the_page_meta_keywords.'" />';
  echo '<p class="howto">'.__( '記事に関連するキーワードを,（カンマ）区切りで入力してください。入力しない場合は、カテゴリ名などから自動で設定されます。', THEME_NAME ).'</p>';

  //noindex
  echo '<label><input type="checkbox" name="the_page_noindex"';
  if( $the_page_noindex ){echo " checked";}
  echo '>'.__( 'インデックスしない（noindex）', THEME_NAME ).'</label>';
  echo '<p class="howto">'.__( 'このページが検索エンジンにインデックスされないようにメタタグを設定します。', THEME_NAME ).'</p>';

  //nofollow
  echo '<label><input type="checkbox" name="the_page_nofollow"';
  if( $the_page_nofollow ){echo " checked";}
  echo '>'.__( 'リンクをフォローしない（nofollow）', THEME_NAME ).'</label>';
  echo '<p class="howto">'.__( '検索エンジンがこのページ上のリンクをフォローしないようにメタタグを設定します。', THEME_NAME ).'</p>';

  //SEO設定ページへのリンク
  //echo '<p><a href="https://wp-simplicity.com/singular-seo-settings/" target="_blank">'.__( 'SEO項目の設定方法', THEME_NAME ).'</a></p>';
}
endif;

//SEO保存データ
add_action('save_post', 'seo_custom_box_save_data');
if ( !function_exists( 'seo_custom_box_save_data' ) ):
function seo_custom_box_save_data(){
  $id = get_the_ID();
  //タイトル
  $the_page_seo_title = null;
  if ( isset( $_POST['the_page_seo_title'] ) ){
    $the_page_seo_title = $_POST['the_page_seo_title'];
    $the_page_seo_title_key = 'the_page_seo_title';
    add_post_meta($id, $the_page_seo_title_key, $the_page_seo_title, true);
    update_post_meta($id, $the_page_seo_title_key, $the_page_seo_title);
    if (is_migrate_from_simplicity()) {
      add_post_meta($id, 'seo_title', $the_page_seo_title, true);
      update_post_meta($id, 'seo_title', $the_page_seo_title);
    }
  }
  //メタディスクリプション
  $the_page_meta_description = null;
  if ( isset( $_POST['the_page_meta_description'] ) ){
    $the_page_meta_description = $_POST['the_page_meta_description'];
    $the_page_meta_description_key = 'the_page_meta_description';
    add_post_meta($id, $the_page_meta_description_key, $the_page_meta_description, true);
    update_post_meta($id, $the_page_meta_description_key, $the_page_meta_description);
    if (is_migrate_from_simplicity()) {
      add_post_meta($id, 'meta_description', $the_page_meta_description, true);
      update_post_meta($id, 'meta_description', $the_page_meta_description);
    }
  }
  //メタキーワード
  $the_page_meta_keywords = null;
  if ( isset( $_POST['the_page_meta_keywords'] ) ){
    $the_page_meta_keywords = $_POST['the_page_meta_keywords'];
    $the_page_meta_keywords_key = 'the_page_meta_keywords';
    add_post_meta($id, $the_page_meta_keywords_key, $the_page_meta_keywords, true);
    update_post_meta($id, $the_page_meta_keywords_key, $the_page_meta_keywords);
    if (is_migrate_from_simplicity()) {
      add_post_meta($id, 'meta_keywords', $the_page_meta_keywords, true);
      update_post_meta($id, 'meta_keywords', $the_page_meta_keywords);
    }
  }
  //noindex
  $the_page_noindex = !empty($_POST['the_page_noindex']) ? 1 : 0;
  $the_page_noindex_key = 'the_page_noindex';
  add_post_meta($id, $the_page_noindex_key, $the_page_noindex, true);
  update_post_meta($id, $the_page_noindex_key, $the_page_noindex);
  if (is_migrate_from_simplicity()) {
    add_post_meta($id, 'is_noindex', $the_page_noindex, true);
    update_post_meta($id, 'is_noindex', $the_page_noindex);
  }

  //nofollow
  $the_page_nofollow = !empty($_POST['the_page_nofollow']) ? 1 : 0;
  $the_page_nofollow_key = 'the_page_nofollow';
  add_post_meta($id, $the_page_nofollow_key, $the_page_nofollow, true);
  update_post_meta($id, $the_page_nofollow_key, $the_page_nofollow);
  if (is_migrate_from_simplicity()) {
    add_post_meta($id, 'is_nofollow', $the_page_nofollow, true);
    update_post_meta($id, 'is_nofollow', $the_page_nofollow);
  }
}
endif;


//SEO向けのタイトルを取得
if ( !function_exists( 'get_the_page_seo_title' ) ):
function get_the_page_seo_title($id = null){
  $the_id = $id ? $id : get_the_ID();
  $value = trim(get_post_meta($the_id, 'the_page_seo_title', true));

  if (is_migrate_from_simplicity())
    $value = $value !== '' ? $value : trim(get_post_meta($the_id,'seo_title', true));

  $value = htmlspecialchars($value);

  return apply_filters('the_page_seo_title', $value);
}
endif;


//メタディスクリプションを取得
if ( !function_exists( 'get_the_page_meta_description' ) ):
function get_the_page_meta_description($id = null){
  $the_id = $id ? $id : get_the_ID();
  $value = trim(get_post_meta($the_id, 'the_page_meta_description', true));

  if (is_migrate_from_simplicity())
    $value = $value ? $value : trim(get_post_meta($the_id,'meta_description', true));

  return htmlspecialchars($value);
}
endif;

//メタキーワードを取得
if ( !function_exists( 'get_the_page_meta_keywords' ) ):
function get_the_page_meta_keywords($id = null){
  $the_id = $id ? $id : get_the_ID();
  $value = trim(get_post_meta($the_id, 'the_page_meta_keywords', true));

  if (is_migrate_from_simplicity())
    $value = $value ? $value : trim(get_post_meta($the_id,'meta_keywords', true));

  return htmlspecialchars($value);
}
endif;


//noindexか
if ( !function_exists( 'is_the_page_noindex' ) ):
function is_the_page_noindex($id = null){
  $the_id = $id ? $id : get_the_ID();
  $value = get_post_meta($the_id, 'the_page_noindex', true);

  if (is_migrate_from_simplicity()){
    $simplicity_value = get_post_meta($the_id, 'is_noindex', true) ? 1 : 0;
    $value = $value ? $value : $simplicity_value;
  }

  return $value;
}
endif;


//nofollowか
if ( !function_exists( 'is_the_page_nofollow' ) ):
function is_the_page_nofollow($id = null){
  $the_id = $id ? $id : get_the_ID();
  $value = get_post_meta($the_id, 'the_page_nofollow', true);

  if (is_migrate_from_simplicity()){
    $simplicity_value = get_post_meta($the_id, 'is_nofollow', true) ? 1 : 0;
    $value = $value ? $value : $simplicity_value;
  }

  return $value;
}
endif;

