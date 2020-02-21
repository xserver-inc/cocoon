<?php //SNS関係の関数
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

if ( !function_exists( 'is_sns_share_buttons_count_visible' ) ):
function is_sns_share_buttons_count_visible(){
  return is_sns_top_share_buttons_count_visible() || is_sns_bottom_share_buttons_count_visible();
}
endif;
//_vis_numeric(0));

//ツイート数取得
if ( !function_exists( 'fetch_twitter_count_raw' ) ):
function fetch_twitter_count_raw($url){
  $url = rawurlencode( $url );
  $args = array( 'sslverify' => true );
  $subscribers = wp_remote_get( "https://jsoon.digitiminimi.com/twitter/count.json?url=$url", $args );
  $res = '0';
  if (!is_wp_error( $subscribers ) && $subscribers["response"]["code"] === 200) {
       $body = $subscribers['body'];
    $json = json_decode( $body );
    $res = ($json->{"count"} ? $json->{"count"} : '0');
  }
  return intval($res);
}
endif;

//count.jsoonからTwitterのツイート数を取得
if ( !function_exists( 'fetch_twitter_count' ) ):
function fetch_twitter_count($url = null) {
  $transient_id = TRANSIENT_SHARE_PREFIX.'twitter_'.get_share_cache_ID();
  //DBキャッシュからカウントの取得
  if (is_sns_share_count_cache_enable()) {
    $count = get_transient( $transient_id );
    if ( is_numeric($count) ) {
      return $count;
    }
  }

  if (!$url) {
    $url = get_the_permalink();
  }
  $res = fetch_twitter_count_raw($url);
  //別スキームカウントの取得
  if (is_sns_share_count_cache_enable() && is_another_scheme_sns_share_count()) {
    $res = $res + fetch_twitter_count_raw(get_another_scheme_url($url));
  }

  //DBキャッシュへ保存
  if (is_sns_share_count_cache_enable()) {
    set_transient( $transient_id, $res, HOUR_IN_SECONDS * get_sns_share_count_cache_interval() );
  }
  return $res;
}
endif;

//Twitterカウントの取得
if ( !function_exists( 'get_twitter_count' ) ):
function get_twitter_count($url = null) {
  if (!is_sns_share_buttons_count_visible())
    return null;

  if (is_scc_twitter_exists()) {
    return scc_get_share_twitter();
  } else {
    return null;
  }
}
endif;

//Facebookシェア数の取得
if ( !function_exists( 'fetch_facebook_count_raw' ) ):
function fetch_facebook_count_raw($url){
  //URLをURLエンコード
  $encoded_url = rawurlencode( $url );
  //オプションの設定
  $args = array( 'sslverify' => true );
  //Facebookアクセストークンがある場合
  if (get_facebook_access_token()) {
    //Facebookにリクエストを送る
    $request_url = 'https://graph.facebook.com/?id='.$encoded_url.'&fields=engagement&access_token='.trim(get_facebook_access_token());
    $response = wp_remote_get( $request_url, $args );
    $res = 0;

    //取得に成功した場合
    if (!is_wp_error( $response ) && $response["response"]["code"] === 200) {
      $body = $response['body'];
      $json = json_decode( $body ); //ジェイソンオブジェクトに変換する
      $reaction_count = isset($json->{'engagement'}->{'reaction_count'}) ? $json->{'engagement'}->{'reaction_count'} : 0;
      $comment_count = isset($json->{'engagement'}->{'comment_count'}) ? $json->{'engagement'}->{'comment_count'} : 0;
      $share_count = isset($json->{'engagement'}->{'share_count'}) ? $json->{'engagement'}->{'share_count'} : 0;
      $comment_plugin_count = isset($json->{'engagement'}->{'comment_plugin_count'}) ? $json->{'engagement'}->{'comment_plugin_count'} : 0;
      $res = intval($reaction_count) + intval($comment_count) + intval($share_count) + intval($comment_plugin_count);
    }
  } else {//Facebookアクセストークンがない場合
    //Facebookにリクエストを送る
    $request_url = 'https://graph.facebook.com?id='.$encoded_url.'&fields=og_object{engagement}';
    $response = wp_remote_get( $request_url );
    $res = 0;
    //取得に成功した場合
    if (!is_wp_error( $response ) && $response["response"]["code"] === 200) {
      $body = $response['body'];
      //ジェイソンオブジェクトに変換する
      $json = json_decode( $body );
      //エンゲージメントカウントをシェア数として取得する
      $res = (isset($json->{'og_object'}->{'engagement'}->{'count'}) ? $json->{'og_object'}->{'engagement'}->{'count'} : 0);
    }
  }


  return intval($res);

  //Facebookにリクエストを送る
  $request_url = 'https://graph.facebook.com/?id='.$encoded_url.'&fields=engagement&access_token='.trim(get_facebook_access_token());
  $response = wp_remote_get( $request_url, $args );
  $res = 0;

  //取得に成功した場合
  if (!is_wp_error( $response ) && $response["response"]["code"] === 200) {
    $body = $response['body'];
    $json = json_decode( $body ); //ジェイソンオブジェクトに変換する
    $res = (isset($json->{'engagement'}->{'reaction_count'}) ? $json->{'engagement'}->{'reaction_count'} : 0);
  }
  // return intval($res);
}
endif;

//Facebookシェア数を取得する
if ( !function_exists( 'fetch_facebook_count' ) ):
function fetch_facebook_count($url = null) {
  $transient_id = TRANSIENT_SHARE_PREFIX.'facebook_'.get_share_cache_ID();
  //DBキャッシュからカウントの取得
  if (is_sns_share_count_cache_enable()) {
    $count = get_transient( $transient_id );
    if ( is_numeric($count) ) {
      return $count;
    }
  }


  if (!$url) {
    $url = get_the_permalink();
  }
  $res = fetch_facebook_count_raw($url);
  //別スキームカウントの取得
  if (is_sns_share_count_cache_enable() && is_another_scheme_sns_share_count()) {
    $res = $res + fetch_facebook_count_raw(get_another_scheme_url($url));
  }
  //_v($res);

  //DBキャッシュへ保存
  if (is_sns_share_count_cache_enable()) {
    set_transient( $transient_id, $res, HOUR_IN_SECONDS * get_sns_share_count_cache_interval() );
  }
  return $res;
}
endif;

//Facebookカウントの取得
if ( !function_exists( 'get_facebook_count' ) ):
function get_facebook_count($url = null) {
  if (!is_sns_share_buttons_count_visible())
    return null;

  if (is_scc_facebook_exists()) {
    return scc_get_share_facebook();
  } else {
    return fetch_facebook_count($url);
  }
}
endif;

//はてブ数の取得
if ( !function_exists( 'fetch_hatebu_count_raw' ) ):
function fetch_hatebu_count_raw($url){
  //取得するURL(ついでにURLエンコード)
  $encoded_url = rawurlencode($url);
  //オプションの設定
  $args = array( 'sslverify' => true );
  //Facebookにリクエストを送る
  $response = wp_remote_get( 'http://api.b.st-hatena.com/entry.count?url='.$encoded_url, $args );
  $res = 0;

  //取得に成功した場合
  if (!is_wp_error( $response ) && $response["response"]["code"] === 200) {
    $body = $response['body'];
    $res = !empty($body) ? $body : 0;
  }
  return intval($res);
}
endif;

if ( !function_exists( 'fetch_hatebu_count' ) ):
function fetch_hatebu_count($url = null) {
  $transient_id = TRANSIENT_SHARE_PREFIX.'hatebu_'.get_share_cache_ID();
  //DBキャッシュからカウントの取得
  if (is_sns_share_count_cache_enable()) {
    $count = get_transient( $transient_id );
    if ( is_numeric($count) ) {
      // _edump(
      //   array('value' => $transient_id.'-'.$count, 'file' => __FILE__, 'line' => __LINE__),
      //   'label', 'tag', 'ade5ac'
      // );
      return $count;
    }
  }


  if (!$url) {
    $url = get_the_permalink();
  }
  $res = fetch_hatebu_count_raw($url);
  //別スキームカウントの取得
  if (is_sns_share_count_cache_enable() && is_another_scheme_sns_share_count()) {
    $res = $res + fetch_hatebu_count_raw(get_another_scheme_url($url));
  }

  //DBキャッシュへ保存
  if (is_sns_share_count_cache_enable()) {
    set_transient( $transient_id, $res, HOUR_IN_SECONDS * get_sns_share_count_cache_interval() );
  }

  return $res;
}
endif;

//はてブカウントの取得
if ( !function_exists( 'get_hatebu_count' ) ):
function get_hatebu_count($url = null) {
  if (!is_sns_share_buttons_count_visible())
    return null;

  if (is_scc_hatebu_exists()) {
    return scc_get_share_hatebu();
  } else {
    return fetch_hatebu_count($url);
  }
}
endif;

//Google+シェア数の取得
if ( !function_exists( 'fetch_google_plus_count_raw' ) ):
function fetch_google_plus_count_raw($url){
  $query = 'https://apis.google.com/_/+1/fastbutton?url=' . urlencode( $url );
  //URL（クエリ）先の情報を取得
  $args = array( 'sslverify' => true );
  $result = wp_remote_get($query, $args);
  $res = 0;
  if (!is_wp_error($result)) {
    // 正規表現でカウント数のところだけを抽出
    preg_match( '/\[2,([0-9.]+),\[/', $result["body"], $count );
    $res = isset($count[1]) ? intval($count[1]) : 0;
  }
  return intval($res);
}
endif;

//Google＋カウントの取得
if ( !function_exists( 'fetch_google_plus_count' ) ):
function fetch_google_plus_count($url = null) {
  $transient_id = TRANSIENT_SHARE_PREFIX.'google_plus_'.get_share_cache_ID();
  //DBキャッシュからカウントの取得
  if (is_sns_share_count_cache_enable()) {
    $count = get_transient( $transient_id );
    if ( is_numeric($count) ) {
      return $count;
    }
  }

  if (!$url) {
    $url = get_the_permalink();
  }
  $res = fetch_google_plus_count_raw($url);
  //別スキームカウントの取得
  if (is_sns_share_count_cache_enable() && is_another_scheme_sns_share_count()) {
    $res = $res + fetch_google_plus_count_raw(get_another_scheme_url($url));
  }

  //DBキャッシュへ保存
  if (is_sns_share_count_cache_enable()) {
    set_transient( $transient_id, $res, HOUR_IN_SECONDS * get_sns_share_count_cache_interval() );
  }

  // 共有数を表示
  return $res;
}
endif;

//Google＋カウントの取得
if ( !function_exists( 'get_google_plus_count' ) ):
function get_google_plus_count($url = null) {
  if (!is_sns_share_buttons_count_visible())
    return null;

  if (is_scc_gplus_exists()) {
    return scc_get_share_gplus();
  } else {
    return null;
  }
}
endif;

//Pocketストック数の取得
if ( !function_exists( 'fetch_pocket_count_raw' ) ):
function fetch_pocket_count_raw($url){
  $res = 0;
  $url = urlencode($url);
  $query = 'https://widgets.getpocket.com/api/saves?url='.$url;
  $args = array( 'sslverify' => true );
  //URL（クエリ）先の情報を取得
  $result = wp_remote_get($query, $args);
  //エラーチェック
  if (!is_wp_error($result)) {
    $body = isset($result["body"]) ? $result["body"] : null;
    if ($body) {
      $json = json_decode($body); //ジェイソンオブジェクトに変換する
      $res = isset($json->{'saves'}) ? $json->{'saves'} : 0;
    }
  }
  return intval($res);
}
endif;

//Pocketカウントの取得
if ( !function_exists( 'fetch_pocket_count' ) ):
function fetch_pocket_count($url = null) {
  $transient_id = TRANSIENT_SHARE_PREFIX.'pocket_'.get_share_cache_ID();
  //DBキャッシュからカウントの取得
  if (is_sns_share_count_cache_enable()) {
    $count = get_transient( $transient_id );
    if ( is_numeric($count) ) {
      return $count;
    }
  }
  $res = 0;

  if (!$url) {
    $url = get_the_permalink();
  }
  $res = fetch_pocket_count_raw($url);
  //別スキームカウントの取得
  if (is_sns_share_count_cache_enable() && is_another_scheme_sns_share_count()) {
    $res = $res + fetch_pocket_count_raw(get_another_scheme_url($url));
  }

  //DBキャッシュへ保存
  if (is_sns_share_count_cache_enable()) {
    set_transient( $transient_id, $res, HOUR_IN_SECONDS * get_sns_share_count_cache_interval() );
  }

  // 共有数を表示
  return $res;
}
endif;

//Pocketカウントの取得
if ( !function_exists( 'get_pocket_count' ) ):
function get_pocket_count($url = null) {
  if (!is_sns_share_buttons_count_visible())
    return null;

  if (is_scc_pocket_exists()) {
    return scc_get_share_pocket();
  } else {
    return fetch_pocket_count($url);
  }
}
endif;

//SNS Count Cacheプラグインはインストールされているか
function is_scc_exists(){
  return function_exists('scc_get_share_twitter');
}

//ツイート数取得関数が存在しているか
function is_scc_twitter_exists(){
  return function_exists('scc_get_share_twitter');
}

//Facebookシェア数取得関数が存在しているか
function is_scc_facebook_exists(){
  return function_exists('scc_get_share_facebook');
}

//Google＋シェア数取得関数が存在しているか
function is_scc_gplus_exists(){
  return function_exists('scc_get_share_gplus');
}

//はてブ数取得関数が存在しているか
function is_scc_hatebu_exists(){
  return function_exists('scc_get_share_hatebu');
}

//Pocketストック数取得関数が存在しているか
function is_scc_pocket_exists(){
  return function_exists('scc_get_share_pocket');
}

//トータルシェア数取得関数が存在しているか
function is_scc_total_exists(){
  return function_exists('scc_get_share_total');
}

//feedly購読者数取得関数が存在しているか
function is_scc_feedly_exists(){
  return function_exists('scc_get_follow_feedly');
}

//Push7購読者数取得関数が存在しているか
function is_scc_push7_exists(){
  return function_exists('scc_get_follow_push7');
}


//シェア対象ページのURLを取得する
if ( !function_exists( 'get_share_page_url' ) ):
function get_share_page_url(){
  $url = get_requested_url();
  if ( is_singular() ) {
    $url = get_the_permalink();
  } elseif (is_category() && !is_paged()) {
    //カテゴリートップページ
    $cat_id = get_query_var('cat');
    $url = get_category_link($cat_id);
  } elseif (is_tag() && !is_paged()) {
    //タグトップページ
    $name = single_tag_title('', false);
    $tag = get_term_by('name', $name, 'post_tag');
    $url = get_tag_link($tag->term_id);
  } elseif (is_front_page() && !is_paged()) {
    //フロントトップページ
    $url = user_trailingslashit(get_home_url());
  }
  return $url;
}
endif;

//シェア対象ページのタイトルを取得する
if ( !function_exists( 'get_share_page_title' ) ):
function get_share_page_title(){
  if ( is_singular() ) {
    $title = get_the_title();
  } else {
    $title = wp_get_document_title();
  }
  return html_entity_decode($title);
}
endif;

//Twitter IDを含めるURLパラメータを取得
function get_twitter_via_param(){
  if ( get_the_author_twitter_id() && is_twitter_id_include() ) {
    return '&amp;via='.get_the_author_twitter_id();
  }
}

//ツイート後にフォローを促すパラメータを取得
function get_twitter_related_param(){
  if ( get_the_author_twitter_id() && is_twitter_related_follow_enable() ) {
    return '&amp;related='.get_the_author_twitter_id();//.':フォロー用の説明文';
  }
}

//TwitterのシェアURLを取得
if ( !function_exists( 'get_twitter_share_url' ) ):
function get_twitter_share_url(){
  $hash_tag = null;
  if (get_twitter_hash_tag()) {
    $hash_tag = '+'.urlencode( get_twitter_hash_tag() );
  }
  return 'https://twitter.com/intent/tweet?text='.urlencode( get_share_page_title() ).$hash_tag.'&amp;url='.
  urlencode( get_share_page_url() ).
  get_twitter_via_param(). //ツイートにメンションを含める
  get_twitter_related_param();//ツイート後にフォローを促す
}
endif;

//FacebookのシェアURLを取得
if ( !function_exists( 'get_facebook_share_url' ) ):
function get_facebook_share_url(){
  return '//www.facebook.com/sharer/sharer.php?u='.urlencode( get_share_page_url() ).'&amp;t='. urlencode( get_share_page_title() );//ツイート後にフォローを促す
}
endif;

//はてブのシェアURLを取得
if ( !function_exists( 'get_hatebu_share_url' ) ):
function get_hatebu_share_url(){
  $url = get_share_page_url();
  if (strpos($url, 'https://') === 0) {
    $u = preg_replace('/https:\/\//', 's/', $url);
  } else {
    $u = preg_replace('/http:\/\//', '', $url);
  }
  return '//b.hatena.ne.jp/entry/'.htmlspecialchars($u, ENT_QUOTES, 'UTF-8');;
}
endif;

//Google+のシェアURLを取得
if ( !function_exists( 'get_google_plus_share_url' ) ):
function get_google_plus_share_url(){
  return '//plus.google.com/share?url='.rawurlencode( get_share_page_url() );
}
endif;

//PocketのシェアURLを取得
if ( !function_exists( 'get_pocket_share_url' ) ):
function get_pocket_share_url(){
  return '//getpocket.com/edit?url='.get_share_page_url();
}
endif;

//LINEのシェアURLを取得
if ( !function_exists( 'get_line_share_url' ) ):
function get_line_share_url(){
  return '//timeline.line.me/social-plugin/share?url='.urlencode(get_share_page_url());
}
endif;

//PinterestのシェアURLを取得
if ( !function_exists( 'get_pinterest_share_url' ) ):
function get_pinterest_share_url(){
  return '//www.pinterest.com/pin/create/button/?url='.urlencode(get_share_page_url());
}
endif;

//LinkedInのシェアURLを取得
if ( !function_exists( 'get_linkedin_share_url' ) ):
function get_linkedin_share_url(){
  return '//www.linkedin.com/shareArticle?mini=true&url='.urlencode(get_share_page_url());
}
endif;

//コピーURLを取得
if ( !function_exists( 'get_copy_share_url' ) ):
function get_copy_share_url(){
  if (is_amp()) {
    return get_template_directory_uri().'/lib/common/copy.php?title='.urlencode( get_share_page_title() ).'&amp;url='.urlencode(get_share_page_url());
  } else {
    return 'javascript:void(0)';
  }
}
endif;

//シェアボタンを表示するか
if ( !function_exists( 'is_sns_share_buttons_visible' ) ):
function is_sns_share_buttons_visible($option){
  $res = (is_sns_bottom_share_buttons_visible() && $option == SS_BOTTOM) ||
         (is_sns_top_share_buttons_visible() && $option == SS_TOP) ||
         ($option == SS_MOBILE);
  return apply_filters('is_sns_share_buttons_visible', $res, $option);
}
endif;

//Twitterシェアボタンを表示するか
if ( !function_exists( 'is_twitter_share_button_visible' ) ):
function is_twitter_share_button_visible($option){
  $res = (is_bottom_twitter_share_button_visible() && $option == SS_BOTTOM) ||
         (is_top_twitter_share_button_visible() && $option == SS_TOP) ||
         ($option == SS_MOBILE);
  return apply_filters('is_twitter_share_button_visible', $res, $option);
}
endif;

//Facebookシェアボタンを表示するか
if ( !function_exists( 'is_facebook_share_button_visible' ) ):
function is_facebook_share_button_visible($option){
  $res = (is_bottom_facebook_share_button_visible() && $option == SS_BOTTOM) ||
         (is_top_facebook_share_button_visible() && $option == SS_TOP) ||
         ($option == SS_MOBILE);
  return apply_filters('is_facebook_share_button_visible', $res, $option);
}
endif;

//はてブシェアボタンを表示するか
if ( !function_exists( 'is_hatebu_share_button_visible' ) ):
function is_hatebu_share_button_visible($option){
  $res = (is_bottom_hatebu_share_button_visible() && $option == SS_BOTTOM) ||
         (is_top_hatebu_share_button_visible() && $option == SS_TOP) ||
         ($option == SS_MOBILE);
  return apply_filters('is_hatebu_share_button_visible', $res, $option);
}
endif;

//Google+シェアボタンを表示するか
if ( !function_exists( 'is_google_plus_share_button_visible' ) ):
function is_google_plus_share_button_visible($option){
  $res = (is_bottom_google_plus_share_button_visible() && $option == SS_BOTTOM) ||
         (is_top_google_plus_share_button_visible() && $option == SS_TOP) ||
         ($option == SS_MOBILE);
  return apply_filters('is_google_plus_share_button_visible', $res, $option);
}
endif;

//Pocketシェアボタンを表示するか
if ( !function_exists( 'is_pocket_share_button_visible' ) ):
function is_pocket_share_button_visible($option){
  $res = (is_bottom_pocket_share_button_visible() && $option == SS_BOTTOM) ||
         (is_top_pocket_share_button_visible() && $option == SS_TOP) ||
         ($option == SS_MOBILE);
  return apply_filters('is_pocket_share_button_visible', $res, $option);
}
endif;

//LINE@シェアボタンを表示するか
if ( !function_exists( 'is_line_at_share_button_visible' ) ):
function is_line_at_share_button_visible($option){
  $res = (is_bottom_line_at_share_button_visible() && $option == SS_BOTTOM) ||
         (is_top_line_at_share_button_visible() && $option == SS_TOP) ||
         ($option == SS_MOBILE);
  return apply_filters('is_line_at_share_button_visible', $res, $option);
}
endif;

//Pinterestシェアボタンを表示するか
if ( !function_exists( 'is_pinterest_share_button_visible' ) ):
function is_pinterest_share_button_visible($option){
  $res = (is_bottom_pinterest_share_button_visible() && $option == SS_BOTTOM) ||
         (is_top_pinterest_share_button_visible() && $option == SS_TOP) ||
         ($option == SS_MOBILE);
  return apply_filters('is_pinterest_share_button_visible', $res, $option);
}
endif;

//LinkedInシェアボタンを表示するか
if ( !function_exists( 'is_linkedin_share_button_visible' ) ):
function is_linkedin_share_button_visible($option){
  $res = (is_bottom_linkedin_share_button_visible() && $option == SS_BOTTOM) ||
         (is_top_linkedin_share_button_visible() && $option == SS_TOP) ||
         ($option == SS_MOBILE);
  return apply_filters('is_linkedin_share_button_visible', $res, $option);
}
endif;

//コピーシェアボタンを表示するか
if ( !function_exists( 'is_copy_share_button_visible' ) ):
function is_copy_share_button_visible($option){
  $res = (is_bottom_copy_share_button_visible() && $option == SS_BOTTOM) ||
         (is_top_copy_share_button_visible() && $option == SS_TOP) ||
         ($option == SS_MOBILE);
  return apply_filters('is_copy_share_button_visible', $res, $option);
}
endif;

//シェアページのIDを取得する
if ( !function_exists( 'get_share_cache_ID' ) ):
function get_share_cache_ID(){
  $id = 'nuknown';
  if ( is_singular() ) {
    global $post;
    $id = $post->ID;
  } elseif (is_category() && !is_paged()) {
    //カテゴリートップページ
    $cat_id = get_query_var('cat');
    $id = 'cat_'.$cat_id;
  } elseif (is_tag() && !is_paged()) {
    //タグトップページ
    $name = single_tag_title('', false);
    $tag = get_term_by('name', $name, 'post_tag');
    $id = 'tag_'.$tag->term_id;
  } elseif (is_front_page() && !is_paged()) {
    //フロントトップページ
    $id = 'front_top_page';
  }
  return $id;
}
endif;
