<?php //SEOカスタムフィールドを設置する

///////////////////////////////////////
// カスタムボックスの追加
///////////////////////////////////////
add_action('admin_menu', 'add_seo_custom_box');
if ( !function_exists( 'add_seo_custom_box' ) ):
function add_seo_custom_box(){
  //SEOボックス
  add_meta_box( 'singular_seo_settings',__( 'SEO設定', THEME_NAME ), 'seo_custom_box_view', 'post', 'normal', 'high' );
  add_meta_box( 'singular_seo_settings',__( 'SEO設定', THEME_NAME ), 'seo_custom_box_view', 'page', 'normal', 'high' );
  add_meta_box( 'singular_seo_settings',__( 'SEO設定', THEME_NAME ), 'seo_custom_box_view', 'topic', 'normal', 'high' );
}
endif;

///////////////////////////////////////
// SEO設定
///////////////////////////////////////
//SEO設定の文字カウント
add_action( 'admin_head-post-new.php', 'seo_settings_admin_script' );
add_action( 'admin_head-post.php', 'seo_settings_admin_script' );
add_action( 'admin_head-page-new.php', 'seo_settings_admin_script' );
add_action( 'admin_head-page.php', 'seo_settings_admin_script' );
add_action( 'admin_head-topic-new.php', 'seo_settings_admin_script' );
add_action( 'admin_head-topic.php', 'seo_settings_admin_script' );
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
  $('#titlewrap').after('<div class="str-wp-title-count"><?php _e( '文字数', THEME_NAME ); ?>:<span class="wp-title-count">0</span></div>');
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
  $seo_title = get_post_meta(get_the_ID(),'seo_title', true);
  $seo_title = htmlspecialchars($seo_title);
  $seo_meta_description = get_post_meta(get_the_ID(),'seo_meta_description', true);
  $seo_meta_description = htmlspecialchars($seo_meta_description);
  $seo_meta_keywords = get_post_meta(get_the_ID(),'seo_meta_keywords', true);
  $seo_meta_keywords = htmlspecialchars($seo_meta_keywords);
  $seo_noindex = get_post_meta(get_the_ID(), 'seo_noindex', true);
  $seo_nofollow = get_post_meta(get_the_ID(), 'seo_nofollow', true);

  //タイトル
  echo '<label class="box-label">'.__( 'SEOタイトル', THEME_NAME ).'<span class="str-count">'.__( '文字数', THEME_NAME ).':<span class="seo-title-count">0</span></span></label>';
  echo '<input id="seo-title" type="text" style="width:100%" placeholder="'.__( 'タイトルを入力してください。', THEME_NAME ).'" name="seo_title" value="'.$seo_title.'" />';
  echo '<p class="howto" style="margin-top:0;">'.__( '検索エンジンに表示させたいタイトルを入力してください。記事のタイトルより、こちらに入力したテキストが優先的にタイトルタグ(&lt;title&gt;)に挿入されます。一般的に日本語の場合は、32文字以内が最適とされています。（※ページやインデックスの見出し部分には「記事のタイトル」が利用されます）', THEME_NAME ).'</p>';


  //メタディスクリプション
  echo '<label class="box-label">'.__( 'メタディスクリプション', THEME_NAME ).'<span class="str-count">'.__( '文字数', THEME_NAME ).':<span class="meta-description-count">0</span></span></label>';
  echo '<textarea id="meta-description" style="width:100%" placeholder="'.__( '記事の説明文を入力してください。', THEME_NAME ).'" name="seo_meta_description" rows="3">'.$seo_meta_description.'</textarea>';
  echo '<p class="howto" style="margin-top:0;">'.__( '記事の説明を入力してください。日本語では、およそ120文字前後の入力をおすすめします。スマホではそのうちの約50文字が表示されます。こちらに入力したメタディスクリプションはブログカードのスニペット（抜粋文部分）にも利用されます。こちらに入力しない場合は、「抜粋」に入力したものがメタディスクリプションとして挿入されます。', THEME_NAME ).'</p>';

  //メタキーワード
  echo '<label class="box-label">'.__( 'メタキーワード', THEME_NAME ).'</label>';
  echo '<input type="text" style="width:100%" placeholder="'.__( '記事の関連キーワードを半角カンマ区切りで入力してください。', THEME_NAME ).'" name="seo_meta_keywords" value="'.$seo_meta_keywords.'" />';
  echo '<p class="howto" style="margin-top:0;">'.__( '記事に関連するキーワードを,（カンマ）区切りで入力してください。入力しない場合は、カテゴリ名などから自動で設定されます。', THEME_NAME ).'</p>';

  //noindex
  echo '<label><input type="checkbox" name="seo_noindex"';
  if( $seo_noindex ){echo " checked";}
  echo '>'.__( 'インデックスしない（noindex）', THEME_NAME ).'</label>';
  echo '<p class="howto" style="margin-top:0;">'.__( 'このページが検索エンジンにインデックスされないようにメタタグを設定します。', THEME_NAME ).'</p>';

  //nofollow
  echo '<label><input type="checkbox" name="seo_nofollow"';
  if( $seo_nofollow ){echo " checked";}
  echo '>'.__( 'リンクをフォローしない（nofollow）', THEME_NAME ).'</label>';
  echo '<p class="howto" style="margin-top:0;">'.__( '検索エンジンがこのページ上のリンクをフォローしないようにメタタグを設定します。', THEME_NAME ).'</p>';

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
  $seo_title = null;
  if ( isset( $_POST['seo_title'] ) ){
    $seo_title = $_POST['seo_title'];
    $seo_title_key = 'seo_title';
    add_post_meta($id, $seo_title_key, $seo_title, true);
    update_post_meta($id, $seo_title_key, $seo_title);
  }
  //メタディスクリプション
  $seo_meta_description = null;
  if ( isset( $_POST['seo_meta_description'] ) ){
    $seo_meta_description = $_POST['seo_meta_description'];
    $seo_meta_description_key = 'seo_meta_description';
    add_post_meta($id, $seo_meta_description_key, $seo_meta_description, true);
    update_post_meta($id, $seo_meta_description_key, $seo_meta_description);
  }
  //メタキーワード
  $seo_meta_keywords = null;
  if ( isset( $_POST['seo_meta_keywords'] ) ){
    $seo_meta_keywords = $_POST['seo_meta_keywords'];
    $seo_meta_keywords_key = 'seo_meta_keywords';
    add_post_meta($id, $seo_meta_keywords_key, $seo_meta_keywords, true);
    update_post_meta($id, $seo_meta_keywords_key, $seo_meta_keywords);
  }
  //noindex
  $seo_noindex = null;
  if ( isset( $_POST['seo_noindex'] ) ){
    $seo_noindex = $_POST['seo_noindex'];
  }
  $seo_noindex_key = 'seo_noindex';
  add_post_meta($id, $seo_noindex_key, $seo_noindex, true);
  update_post_meta($id, $seo_noindex_key, $seo_noindex);

  //nofollow
  $seo_nofollow = null;
  if ( isset( $_POST['seo_nofollow'] ) ){
    $seo_nofollow = $_POST['seo_nofollow'];
  }
  $seo_nofollow_key = 'seo_nofollow';
  add_post_meta($id, $seo_nofollow_key, $seo_nofollow, true);
  update_post_meta($id, $seo_nofollow_key, $seo_nofollow);
}
endif;


//SEO向けのタイトルを取得
function get_seo_title_singular_page(){
  return trim(get_post_meta(get_the_ID(), 'seo_title', true));
}

//メタディスクリプションを取得
function get_meta_description_singular_page(){
  return trim(get_post_meta(get_the_ID(), 'seo_meta_description', true));
}

//メタディスクリプションを取得
function get_meta_description_blogcard_snippet($id){
  return trim(get_post_meta($id, 'seo_meta_description', true));
}

//メタキーワードを取得
if ( !function_exists( 'get_singular_page_meta_keywords' ) ):
function get_singular_page_meta_keywords(){
  return trim(strip_tags(get_post_meta(get_the_ID(), 'seo_meta_keywords', true)));
}
endif;


//noindexか
if ( !function_exists( 'is_singular_page_noindex' ) ):
function is_singular_page_noindex(){
  return get_post_meta(get_the_ID(), 'seo_noindex', true);
}
endif;


//nofollowか
if ( !function_exists( 'is_singular_page_nofollow' ) ):
function is_singular_page_nofollow(){
  return get_post_meta(get_the_ID(), 'seo_nofollow', true);
}
endif;

