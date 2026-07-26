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

  //DBキャッシュへ保存
  if (is_sns_share_count_cache_enable()) {
    do_action('set_transient_facebook_share_count', get_the_ID(), $res);
    set_transient( $transient_id, $res, HOUR_IN_SECONDS * get_sns_share_count_cache_interval() );
  }
  return $res;
}
endif;

//Mastodonカウントの取得（取得方法が出てきた時・カスタマイズ用）
if ( !function_exists( 'get_mastodon_count' ) ):
function get_mastodon_count($url = null) {
  return '';
}
endif;

//Blueskyカウントの取得（取得方法が出てきた時・カスタマイズ用）
if ( !function_exists( 'get_bluesky_count' ) ):
function get_bluesky_count($url = null) {
  return '';
}
endif;

//Misskeyカウントの取得（取得方法が出てきた時・カスタマイズ用）
if ( !function_exists( 'get_misskey_count' ) ):
function get_misskey_count($url = null) {
  return '';
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
    do_action('set_transient_hatebu_share_count', get_the_ID(), $res);
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
    do_action('set_transient_pocket_share_count', get_the_ID(), $res);
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
  } elseif (is_tag() && !is_paged() && isset($tag->term_id)) {
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
  return 'https://x.com/intent/tweet?text='.urlencode( get_share_page_title() ).$hash_tag.'&amp;url='.
  urlencode( get_share_page_url() ).
  get_twitter_via_param(). //ツイートにメンションを含める
  get_twitter_related_param();//ツイート後にフォローを促す
}
endif;

//MastodonのシェアURLを取得
if ( !function_exists( 'get_mastodon_share_url' ) ):
function get_mastodon_share_url(){
  return '//www.addtoany.com/add_to/mastodon?linkurl='.urlencode( get_share_page_url() ).'&linkname='.urlencode( get_share_page_title() );
}
endif;

//BlueskyのシェアURLを取得
if ( !function_exists( 'get_bluesky_share_url' ) ):
function get_bluesky_share_url(){
  return '//bsky.app/intent/compose?text='.urlencode( get_share_page_title() ).' '.urlencode( get_share_page_url() );
}
endif;

//MisskeyのシェアURLを取得
if ( !function_exists( 'get_misskey_share_url' ) ):
function get_misskey_share_url(){
  return '//misskey-hub.net/share/?text='.urlencode( get_share_page_title() ).'&url='.urlencode( get_share_page_url() ).'&visibility=public&localOnly=0';
}
endif;

//FacebookのシェアURLを取得
if ( !function_exists( 'get_facebook_share_url' ) ):
function get_facebook_share_url(){
  return '//www.facebook.com/sharer/sharer.php?u='.urlencode( get_share_page_url() ).'&amp;t='. urlencode( get_share_page_title() );//ツイート後にフォローを促す
}
endif;

//ThreadsのシェアURLを取得
if ( !function_exists( 'get_threads_share_url' ) ):
function get_threads_share_url(){
  return 'https://www.threads.net/intent/post?text='.urlencode( get_share_page_title().' '.get_share_page_url() );
}
endif;

//Threadsカウントの取得（取得方法が出てきた時・カスタマイズ用）
if ( !function_exists( 'get_threads_count' ) ):
function get_threads_count($url = null) {
  return '';
}
endif;

//RedditのシェアURLを取得
if ( !function_exists( 'get_reddit_share_url' ) ):
function get_reddit_share_url(){
  return 'https://www.reddit.com/submit?url='.urlencode( get_share_page_url() ).'&title='.urlencode( get_share_page_title() );
}
endif;

//Redditカウントの取得（取得方法が出てきた時・カスタマイズ用）
if ( !function_exists( 'get_reddit_count' ) ):
function get_reddit_count($url = null) {
  return '';
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
    return get_cocoon_template_directory_uri().'/lib/common/copy.php?title='.urlencode( get_share_page_title() ).'&amp;url='.urlencode(get_share_page_url());
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

//Mastodonシェアボタンを表示するか
if ( !function_exists( 'is_mastodon_share_button_visible' ) ):
function is_mastodon_share_button_visible($option){
  $res = (is_bottom_mastodon_share_button_visible() && $option == SS_BOTTOM) ||
         (is_top_mastodon_share_button_visible() && $option == SS_TOP) ||
         ($option == SS_MOBILE);
  return apply_filters('is_mastodon_share_button_visible', $res, $option);
}
endif;

//Blueskyシェアボタンを表示するか
if ( !function_exists( 'is_bluesky_share_button_visible' ) ):
function is_bluesky_share_button_visible($option){
  $res = (is_bottom_bluesky_share_button_visible() && $option == SS_BOTTOM) ||
         (is_top_bluesky_share_button_visible() && $option == SS_TOP) ||
         ($option == SS_MOBILE);
  return apply_filters('is_bluesky_share_button_visible', $res, $option);
}
endif;


//Misskeyシェアボタンを表示するか
if ( !function_exists( 'is_misskey_share_button_visible' ) ):
function is_misskey_share_button_visible($option){
  $res = (is_bottom_misskey_share_button_visible() && $option == SS_BOTTOM) ||
         (is_top_misskey_share_button_visible() && $option == SS_TOP) ||
         ($option == SS_MOBILE);
  return apply_filters('is_misskey_share_button_visible', $res, $option);
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

//Threadsシェアボタンを表示するか
if ( !function_exists( 'is_threads_share_button_visible' ) ):
function is_threads_share_button_visible($option){
  $res = (is_bottom_threads_share_button_visible() && $option == SS_BOTTOM) ||
         (is_top_threads_share_button_visible() && $option == SS_TOP) ||
         ($option == SS_MOBILE);
  return apply_filters('is_threads_share_button_visible', $res, $option);
}
endif;

//Redditシェアボタンを表示するか
if ( !function_exists( 'is_reddit_share_button_visible' ) ):
function is_reddit_share_button_visible($option){
  $res = (is_bottom_reddit_share_button_visible() && $option == SS_BOTTOM) ||
         (is_top_reddit_share_button_visible() && $option == SS_TOP) ||
         ($option == SS_MOBILE);
  return apply_filters('is_reddit_share_button_visible', $res, $option);
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
  // $res = (is_bottom_pocket_share_button_visible() && $option == SS_BOTTOM) ||
  //        (is_top_pocket_share_button_visible() && $option == SS_TOP) ||
  //        ($option == SS_MOBILE);
  $res = 0;
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

//コメントボタンを表示するか
if ( !function_exists( 'is_comment_share_button_visible' ) ):
function is_comment_share_button_visible($option){
  $res = (is_bottom_comment_share_button_visible() && $option == SS_BOTTOM) ||
         (is_top_comment_share_button_visible() && $option == SS_TOP) ||
         ($option == SS_MOBILE);
  return apply_filters('is_comment_share_button_visible', $res, $option);
}
endif;

//シェアページのIDを取得する
if ( !function_exists( 'get_share_cache_ID' ) ):
function get_share_cache_ID(){
  $id = 'nuknown';
  if ( is_singular() ) {
    global $post;
    if (isset($post->ID)) {
      $id = $post->ID;
    }
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

//Cocoon設定「表示切替」とフロントのシェアボタン出力で共用するSNSの定義一覧を取得する
//フォーム生成（sns-share-forms-*.php）・設定保存（sns-share-posts-*.php）・
//シェアボタン描画（tmp/sns-share-buttons.php）の3か所から参照する
if ( !function_exists( 'get_cocoon_sns_share_options' ) ):
function get_cocoon_sns_share_options(){
  //定義の組み立て・翻訳・フィルター適用・エスケープは1リクエストにつき1回だけ行う
  //（シェアボタンは1ページに何度も描画されるため、毎回作り直すと無駄になる）
  static $options = null;
  if ( $options !== null ) {
    return $options;
  }
  $options = array(
    'twitter' => array(
      'label'             => __( 'X（旧Twitter）', THEME_NAME ),
      'top_key'           => OP_TOP_TWITTER_SHARE_BUTTON_VISIBLE,
      'bottom_key'        => OP_BOTTOM_TWITTER_SHARE_BUTTON_VISIBLE,
      'top_visible_fn'    => 'is_top_twitter_share_button_visible',
      'bottom_visible_fn' => 'is_bottom_twitter_share_button_visible',
      'visible_fn'        => 'is_twitter_share_button_visible',
      'url_fn'            => 'get_twitter_share_url',
      'count_fn'          => 'get_twitter_count',
      'class'             => 'twitter-button twitter-share-button-sq x-corp-button x-corp-share-button-sq',
      'icon'              => 'icon-x-corp',
      'count_class'       => 'twitter-share-count x-share-count',
      'caption'           => __( 'X', THEME_NAME ),
      'title'             => __( 'Xでシェア', THEME_NAME ),
    ),
    'mastodon' => array(
      'label'             => __( 'Mastodon', THEME_NAME ),
      'top_key'           => OP_TOP_MASTODON_SHARE_BUTTON_VISIBLE,
      'bottom_key'        => OP_BOTTOM_MASTODON_SHARE_BUTTON_VISIBLE,
      'top_visible_fn'    => 'is_top_mastodon_share_button_visible',
      'bottom_visible_fn' => 'is_bottom_mastodon_share_button_visible',
      'visible_fn'        => 'is_mastodon_share_button_visible',
      'url_fn'            => 'get_mastodon_share_url',
      'count_fn'          => 'get_mastodon_count',
      'class'             => 'mastodon-button mastodon-share-button-sq',
      'icon'              => 'icon-mastodon',
      'count_class'       => 'mastodon-share-count',
      'caption'           => __( 'Mastodon', THEME_NAME ),
      'title'             => __( 'Mastodonでシェア', THEME_NAME ),
    ),
    'bluesky' => array(
      'label'             => __( 'Bluesky', THEME_NAME ),
      'top_key'           => OP_TOP_BLUESKY_SHARE_BUTTON_VISIBLE,
      'bottom_key'        => OP_BOTTOM_BLUESKY_SHARE_BUTTON_VISIBLE,
      'top_visible_fn'    => 'is_top_bluesky_share_button_visible',
      'bottom_visible_fn' => 'is_bottom_bluesky_share_button_visible',
      'visible_fn'        => 'is_bluesky_share_button_visible',
      'url_fn'            => 'get_bluesky_share_url',
      'count_fn'          => 'get_bluesky_count',
      'class'             => 'bluesky-button bluesky-share-button-sq',
      'icon'              => 'icon-bluesky',
      'count_class'       => 'bluesky-share-count',
      'caption'           => __( 'Bluesky', THEME_NAME ),
      'title'             => __( 'Blueskyでシェア', THEME_NAME ),
    ),
    'misskey' => array(
      'label'             => __( 'Misskey', THEME_NAME ),
      'top_key'           => OP_TOP_MISSKEY_SHARE_BUTTON_VISIBLE,
      'bottom_key'        => OP_BOTTOM_MISSKEY_SHARE_BUTTON_VISIBLE,
      'top_visible_fn'    => 'is_top_misskey_share_button_visible',
      'bottom_visible_fn' => 'is_bottom_misskey_share_button_visible',
      'visible_fn'        => 'is_misskey_share_button_visible',
      'url_fn'            => 'get_misskey_share_url',
      'count_fn'          => 'get_misskey_count',
      'class'             => 'misskey-button misskey-share-button-sq',
      'icon'              => 'icon-misskey',
      'count_class'       => 'misskey-share-count',
      'caption'           => __( 'Misskey', THEME_NAME ),
      'title'             => __( 'Misskeyでシェア', THEME_NAME ),
    ),
    'facebook' => array(
      'label'             => __( 'Facebook', THEME_NAME ),
      'top_key'           => OP_TOP_FACEBOOK_SHARE_BUTTON_VISIBLE,
      'bottom_key'        => OP_BOTTOM_FACEBOOK_SHARE_BUTTON_VISIBLE,
      'top_visible_fn'    => 'is_top_facebook_share_button_visible',
      'bottom_visible_fn' => 'is_bottom_facebook_share_button_visible',
      'visible_fn'        => 'is_facebook_share_button_visible',
      'url_fn'            => 'get_facebook_share_url',
      'count_fn'          => 'get_facebook_count',
      'class'             => 'facebook-button facebook-share-button-sq',
      'icon'              => 'icon-facebook',
      'count_class'       => 'facebook-share-count',
      'caption'           => __( 'Facebook', THEME_NAME ),
      'title'             => __( 'Facebookでシェア', THEME_NAME ),
    ),
    'threads' => array(
      'label'             => __( 'Threads', THEME_NAME ),
      'top_key'           => OP_TOP_THREADS_SHARE_BUTTON_VISIBLE,
      'bottom_key'        => OP_BOTTOM_THREADS_SHARE_BUTTON_VISIBLE,
      'top_visible_fn'    => 'is_top_threads_share_button_visible',
      'bottom_visible_fn' => 'is_bottom_threads_share_button_visible',
      'visible_fn'        => 'is_threads_share_button_visible',
      'url_fn'            => 'get_threads_share_url',
      'count_fn'          => 'get_threads_count',
      'class'             => 'threads-button threads-share-button-sq',
      'icon'              => 'icon-threads',
      'count_class'       => 'threads-share-count',
      'caption'           => __( 'Threads', THEME_NAME ),
      'title'             => __( 'Threadsでシェア', THEME_NAME ),
    ),
    //Redditは get_reddit_count() が存在するものの、従来からシェア数を表示していないため count_fn は持たせない
    'reddit' => array(
      'label'             => __( 'Reddit', THEME_NAME ),
      'top_key'           => OP_TOP_REDDIT_SHARE_BUTTON_VISIBLE,
      'bottom_key'        => OP_BOTTOM_REDDIT_SHARE_BUTTON_VISIBLE,
      'top_visible_fn'    => 'is_top_reddit_share_button_visible',
      'bottom_visible_fn' => 'is_bottom_reddit_share_button_visible',
      'visible_fn'        => 'is_reddit_share_button_visible',
      'url_fn'            => 'get_reddit_share_url',
      'class'             => 'reddit-button reddit-share-button-sq',
      'icon'              => 'icon-reddit',
      'count_class'       => 'reddit-share-count',
      'caption'           => __( 'Reddit', THEME_NAME ),
      'title'             => __( 'Redditでシェア', THEME_NAME ),
    ),
    'hatebu' => array(
      'label'             => __( 'はてなブックマーク', THEME_NAME ),
      'top_key'           => OP_TOP_HATEBU_SHARE_BUTTON_VISIBLE,
      'bottom_key'        => OP_BOTTOM_HATEBU_SHARE_BUTTON_VISIBLE,
      'top_visible_fn'    => 'is_top_hatebu_share_button_visible',
      'bottom_visible_fn' => 'is_bottom_hatebu_share_button_visible',
      'visible_fn'        => 'is_hatebu_share_button_visible',
      'url_fn'            => 'get_hatebu_share_url',
      'count_fn'          => 'get_hatebu_count',
      'class'             => 'hatebu-button hatena-bookmark-button hatebu-share-button-sq',
      'icon'              => 'icon-hatena',
      'count_class'       => 'hatebu-share-count',
      'caption'           => __( 'はてブ', THEME_NAME ),
      'title'             => __( 'はてブでブックマーク', THEME_NAME ),
      //はてなブックマークのJSが参照する属性。従来のHTMLと同じくclass属性の直後に出力する
      'attrs_after_class' => array( 'data-hatena-bookmark-layout' => 'simple' ),
    ),
    //Pocketはサービス終了により既定で非表示（is_pocket_share_button_visible() が0を返す）。
    //フィルターで復活させている利用者のために定義は残すが、設定画面には出さないので top_key / bottom_key は持たせない
    'pocket' => array(
      'visible_fn'        => 'is_pocket_share_button_visible',
      'url_fn'            => 'get_pocket_share_url',
      'count_fn'          => 'get_pocket_count',
      'class'             => 'pocket-button pocket-share-button-sq',
      'icon'              => 'icon-pocket',
      'count_class'       => 'pocket-share-count',
      'caption'           => __( 'Pocket', THEME_NAME ),
      'title'             => __( 'Pocketに保存', THEME_NAME ),
    ),
    'line_at' => array(
      'label'             => __( 'LINE@', THEME_NAME ),
      'top_key'           => OP_TOP_LINE_AT_SHARE_BUTTON_VISIBLE,
      'bottom_key'        => OP_BOTTOM_LINE_AT_SHARE_BUTTON_VISIBLE,
      'top_visible_fn'    => 'is_top_line_at_share_button_visible',
      'bottom_visible_fn' => 'is_bottom_line_at_share_button_visible',
      'visible_fn'        => 'is_line_at_share_button_visible',
      'url_fn'            => 'get_line_share_url',
      'class'             => 'line-button line-share-button-sq',
      'icon'              => 'icon-line',
      'count_class'       => 'line-share-count',
      'caption'           => __( 'LINE', THEME_NAME ),
      'title'             => __( 'LINEでシェア', THEME_NAME ),
    ),
    'pinterest' => array(
      'label'             => __( 'Pinterest', THEME_NAME ),
      'top_key'           => OP_TOP_PINTEREST_SHARE_BUTTON_VISIBLE,
      'bottom_key'        => OP_BOTTOM_PINTEREST_SHARE_BUTTON_VISIBLE,
      'top_visible_fn'    => 'is_top_pinterest_share_button_visible',
      'bottom_visible_fn' => 'is_bottom_pinterest_share_button_visible',
      'visible_fn'        => 'is_pinterest_share_button_visible',
      'url_fn'            => 'get_pinterest_share_url',
      'class'             => 'pinterest-button pinterest-share-button-sq',
      'icon'              => 'icon-pinterest',
      'count_class'       => 'pinterest-share-count',
      'caption'           => __( 'Pinterest', THEME_NAME ),
      'title'             => __( 'Pinterestでシェア', THEME_NAME ),
      //PinterestのJSが参照する属性。従来のHTMLと同じくrel属性の直後に出力する
      'attrs_after_rel'   => array( 'data-pin-do' => 'buttonBookmark', 'data-pin-custom' => 'true' ),
    ),
    'linkedin' => array(
      'label'             => __( 'LinkedIn', THEME_NAME ),
      'top_key'           => OP_TOP_LINKEDIN_SHARE_BUTTON_VISIBLE,
      'bottom_key'        => OP_BOTTOM_LINKEDIN_SHARE_BUTTON_VISIBLE,
      'top_visible_fn'    => 'is_top_linkedin_share_button_visible',
      'bottom_visible_fn' => 'is_bottom_linkedin_share_button_visible',
      'visible_fn'        => 'is_linkedin_share_button_visible',
      'url_fn'            => 'get_linkedin_share_url',
      'class'             => 'linkedin-button linkedin-share-button-sq',
      'icon'              => 'icon-linkedin',
      'count_class'       => 'linkedin-share-count',
      'caption'           => __( 'LinkedIn', THEME_NAME ),
      'title'             => __( 'LinkedInでシェア', THEME_NAME ),
    ),
    //コピーボタンはリンクではなくクリップボード操作用のボタンなので type で描画方法を切り替える
    'copy' => array(
      'label'             => __( 'タイトルとURLをコピー', THEME_NAME ),
      'top_key'           => OP_TOP_COPY_SHARE_BUTTON_VISIBLE,
      'bottom_key'        => OP_BOTTOM_COPY_SHARE_BUTTON_VISIBLE,
      'top_visible_fn'    => 'is_top_copy_share_button_visible',
      'bottom_visible_fn' => 'is_bottom_copy_share_button_visible',
      'type'              => 'copy',
      'visible_fn'        => 'is_copy_share_button_visible',
      //AMPページではJavaScriptが動かないため出力しない
      'exclude_amp'       => true,
      'class'             => 'copy-button copy-share-button-sq',
      'icon'              => 'icon-copy',
      'count_class'       => 'copy-share-count',
      'caption'           => __( 'コピー', THEME_NAME ),
      'title'             => __( 'タイトルとURLをコピーする', THEME_NAME ),
    ),
    //コメントボタンは同一ページ内のコメント欄へのアンカー
    'comment' => array(
      'label'             => __( 'コメント', THEME_NAME ),
      'top_key'           => OP_TOP_COMMENT_SHARE_BUTTON_VISIBLE,
      'bottom_key'        => OP_BOTTOM_COMMENT_SHARE_BUTTON_VISIBLE,
      'top_visible_fn'    => 'is_top_comment_share_button_visible',
      'bottom_visible_fn' => 'is_bottom_comment_share_button_visible',
      'type'              => 'comment',
      'visible_fn'        => 'is_comment_share_button_visible',
      //コメントが投稿できる状態かどうかの追加判定
      'extra_visible_fn'  => 'is_comment_share_button_displayable',
      'url'               => '#comments',
      'class'             => 'comment-button comment-share-button-sq',
      'icon'              => 'icon-comment',
      'count_class'       => 'comment-share-count',
      'caption'           => __( 'コメント', THEME_NAME ),
      'title'             => __( 'コメントする', THEME_NAME ),
    ),
  );
  //子テーマやプラグインからSNSの追加・削除・並び替えができるようにする
  //※ フィルターの適用は1リクエストにつき1回だけなので、フックは init までに登録すること
  $options = apply_filters( 'cocoon_sns_share_options', $options );
  //描画のたびに同じ処理を繰り返さないよう、エスケープと関数の存在確認をここで済ませておく
  foreach ( $options as $sns_key => $sns ) {
    $sns['type']              = !empty($sns['type']) ? $sns['type'] : 'share';
    $sns['class']             = esc_attr(get_cocoon_sns_share_option_text($sns, 'class'));
    $sns['icon']              = esc_attr(get_cocoon_sns_share_option_text($sns, 'icon'));
    $sns['count_class']       = esc_attr(get_cocoon_sns_share_option_text($sns, 'count_class'));
    $sns['title']             = esc_attr(get_cocoon_sns_share_option_text($sns, 'title'));
    $sns['caption']           = esc_html(get_cocoon_sns_share_option_text($sns, 'caption'));
    //data属性は「 name="value"」の形にまとめておく
    $sns['attrs_after_class'] = get_cocoon_sns_share_option_attrs($sns, 'attrs_after_class');
    $sns['attrs_after_rel']   = get_cocoon_sns_share_option_attrs($sns, 'attrs_after_rel');
    //存在しない関数が指定されていた場合は空にして、描画時に存在確認をしなくて済むようにする
    foreach ( array( 'visible_fn', 'url_fn', 'count_fn', 'extra_visible_fn' ) as $fn_key ) {
      if ( !empty($sns[$fn_key]) && !function_exists($sns[$fn_key]) ) {
        $sns[$fn_key] = '';
      }
    }
    $options[$sns_key] = $sns;
  }
  return $options;
}
endif;

//SNSシェアボタン定義から、指定位置の現在の表示状態を取得する
//$position には 'top' もしくは 'bottom' を渡す
if ( !function_exists( 'is_cocoon_sns_share_option_visible' ) ):
function is_cocoon_sns_share_option_visible($sns_option, $position){
  $fn_name = $position.'_visible_fn';
  //フィルターで追加されたSNSなどで判定関数が未定義の場合は非表示扱いにする
  if ( empty($sns_option[$fn_name]) || !function_exists($sns_option[$fn_name]) ) {
    return 0;
  }
  return call_user_func($sns_option[$fn_name]);
}
endif;

//SNSシェアボタン定義のテキスト（label / caption / title）を取得する
//フィルターで追加されたSNSで未指定だった場合は $default を返す
if ( !function_exists( 'get_cocoon_sns_share_option_text' ) ):
function get_cocoon_sns_share_option_text($sns_option, $key, $default = ''){
  return !empty($sns_option[$key]) ? $sns_option[$key] : $default;
}
endif;

//フロントのシェアボタンとして表示するかを判定する
//$option には SS_TOP / SS_BOTTOM / SS_MOBILE のいずれかを渡す
if ( !function_exists( 'is_cocoon_sns_share_button_visible' ) ):
function is_cocoon_sns_share_button_visible($sns_option, $option){
  //判定関数が無いものは表示しない（関数の存在確認は定義一覧の組み立て時に済んでいる）
  if ( empty($sns_option['visible_fn']) ) {
    return false;
  }
  $visible_fn = $sns_option['visible_fn'];
  if ( !$visible_fn($option) ) {
    return false;
  }
  //AMPページで出力できないボタン（コピーボタンなど）を除外する
  if ( !empty($sns_option['exclude_amp']) && is_amp() ) {
    return false;
  }
  //コメントボタンのように追加条件があるものを判定する
  if ( !empty($sns_option['extra_visible_fn']) ) {
    $extra_visible_fn = $sns_option['extra_visible_fn'];
    if ( !$extra_visible_fn($option) ) {
      return false;
    }
  }
  return true;
}
endif;

//SNSシェアボタン定義のdata属性などを「 name="value"」形式の文字列に組み立てる
if ( !function_exists( 'get_cocoon_sns_share_option_attrs' ) ):
function get_cocoon_sns_share_option_attrs($sns_option, $key){
  if ( empty($sns_option[$key]) || !is_array($sns_option[$key]) ) {
    return '';
  }
  $attrs = '';
  foreach ( $sns_option[$key] as $attr_name => $attr_value ) {
    $attrs .= sprintf(' %s="%s"', esc_attr($attr_name), esc_attr($attr_value));
  }
  return $attrs;
}
endif;

//コメントシェアボタンを実際に表示できる状態か
//投稿・固定ページで、コメントが開いているかコメントが付いていて、かつCocoon設定でもコメント表示が有効な場合に表示する
if ( !function_exists( 'is_comment_share_button_displayable' ) ):
function is_comment_share_button_displayable(){
  //投稿・固定ページ以外、またはコメントが閉じていてコメントも無い場合は表示しない
  if ( !is_singular() || (!is_comment_open() && !get_comments_number()) ) {
    return false;
  }
  //Cocoon設定の固定ページ／投稿ページのコメント表示設定に従う
  return (is_page() && is_page_comment_visible()) || (is_single() && is_single_comment_visible());
}
endif;
