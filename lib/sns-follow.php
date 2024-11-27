<?php //SNS用の関数など
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

//feedly購読者数の取得
if ( !function_exists( 'fetch_feedly_count_raw' ) ):
function fetch_feedly_count_raw($url){
  $url = rawurlencode( $url );
  $res = 0;
  $args = array( 'sslverify' => true );
  $subscribers = wp_remote_get( 'http://cloud.feedly.com/v3/feeds/feed%2F'.$url, $args );
  //var_dump($subscribers);
  // _v('http://cloud.feedly.com/v3/feeds/feed%2F'.$url);
  // _v($subscribers);
  if (!is_wp_error( $subscribers ) && $subscribers["response"]["code"] === 200) {
    $subscribers = json_decode( $subscribers['body'] );
    if ( $subscribers ) {
      $subscribers = $subscribers->subscribers;
      if ($subscribers) {
        $res = $subscribers;
      }
    }
  }
  return intval($res);
}
endif;

//feedlyの購読者数取得
if ( !function_exists( 'fetch_feedly_count' ) ):
function fetch_feedly_count(){
  $count = 0;
  $transient_id = TRANSIENT_FOLLOW_PREFIX.'_feedly';
  //DBキャッシュからカウントの取得
  if (is_sns_follow_count_cache_enable()) {
    $count = get_transient( $transient_id );
    //_v($count);
    if ( is_numeric($count) ) {
      return $count;
    }
  }

  $url = get_bloginfo( 'rss2_url' );
  $res = fetch_feedly_count_raw($url);
  //別スキームカウントの取得
  if (is_sns_follow_count_cache_enable() && is_another_scheme_sns_follow_count()) {
    $res = $res + fetch_feedly_count_raw(get_another_scheme_url($url));
  }

  //DBキャッシュにカウントを保存
  if (is_sns_follow_count_cache_enable()) {
    set_transient( $transient_id, $res, HOUR_IN_SECONDS * get_sns_follow_count_cache_interval() );
  }

  return $res;
}
endif;

//feedlyの購読者数の取得
if ( !function_exists( 'get_feedly_count' ) ):
function get_feedly_count(){
  //_v(is_sns_follow_buttons_count_visible());
  if (!is_sns_follow_buttons_count_visible())
    return null;

  if (is_scc_feedly_exists()) {
    return scc_get_follow_feedly();
  } else {
    $count = fetch_feedly_count();
    //feedly購読者数がネットで取得できなかった場合にはユーザー設定数を表示
    if (!$count) {
      $count = get_sns_feedly_follow_count();
    }
    return $count;
  }
}
endif;

//Push7情報の取得
if ( !function_exists( 'fetch_push7_info_raw' ) ):
function fetch_push7_info_raw($app_no){
  $url = 'https://api.push7.jp/api/v1/'.$app_no.'/head';//要https:
  $args = array( 'sslverify' => true );
  //$args = array('sslverify' => true);
  $info = wp_remote_get( $url, $args );
  if (!is_wp_error( $info ) && $info["response"]["code"] === 200) {
    $info = json_decode( $info['body'] );
    if ( $info ) {
      $res = $info;
    }
  }
}
endif;

//Push7情報取得
if ( !function_exists( 'fetch_push7_info' ) ):
function fetch_push7_info(){
  if (!is_sns_follow_buttons_count_visible())
    return null;

  $transient_id = TRANSIENT_FOLLOW_PREFIX.'_push7_info';
  //DBキャッシュからカウントの取得
  if (is_sns_follow_count_cache_enable()) {
    $info = get_transient( $transient_id  );
    if ( $info ) {
      return $info;
    }
  }

  $res = null;
  $app_no = get_push7_follow_app_no();
  if ( $app_no ) {
    $info = fetch_push7_info_raw($app_no);


    //Push7情報をキャッシュに保存
    if (is_sns_follow_count_cache_enable()) {
      set_transient( $transient_id , $info, HOUR_IN_SECONDS * get_sns_follow_count_cache_interval() );
    }
  }
  return $res;
}
endif;


// ユーザープロフィールの項目のカスタマイズ
add_filter('user_contactmethods', 'user_contactmethods_custom');
if ( !function_exists( 'user_contactmethods_custom' ) ):
function user_contactmethods_custom($prof_items){
  //項目の追加
  $prof_items['twitter_url'] = __( 'X（旧Twitter） URL', THEME_NAME );
  $prof_items['mastodon_url'] = __( 'Mastodon URL', THEME_NAME );
  $prof_items['bluesky_url'] = __( 'Bluesky URL', THEME_NAME );
  $prof_items['misskey_url'] = __( 'Misskey URL', THEME_NAME );
  $prof_items['facebook_url'] = __( 'Facebook URL', THEME_NAME );
  //$prof_items['google_plus_url'] = __( 'Google+ URL', THEME_NAME );
  $prof_items['hatebu_url'] = __( 'はてブ URL', THEME_NAME );
  $prof_items['instagram_url'] = __( 'Instagram URL', THEME_NAME );
  $prof_items['pinterest_url'] = __( 'Pinterest URL', THEME_NAME );
  $prof_items['youtube_url'] = __( 'YouTube URL', THEME_NAME );
  $prof_items['tiktok_url'] = __( 'TikTok URL', THEME_NAME );
  $prof_items['linkedin_url'] = __( 'LinkedIn URL', THEME_NAME );
  $prof_items['note_url'] = __( 'note URL', THEME_NAME );
  $prof_items['soundcloud_url'] = __( 'SoundCloud URL', THEME_NAME );
  $prof_items['flickr_url'] = __( 'Flickr URL', THEME_NAME );
  $prof_items['line_at_url'] = __( 'LINE@ URL', THEME_NAME );
  $prof_items['amazon_url'] = __( 'Amazon URL', THEME_NAME );
  $prof_items['twitch_url'] = __( 'Twitch URL', THEME_NAME );
  $prof_items['rakuten_room_url'] = __( '楽天 ROOM URL', THEME_NAME );
  $prof_items['slack_url'] = __( 'Slack URL', THEME_NAME );
  $prof_items['github_url'] = __( 'GitHub URL', THEME_NAME );
  $prof_items['codepen_url'] = __( 'CodePen URL', THEME_NAME );

  return $prof_items;
}
endif;


//ユーザーIDの取得
if ( !function_exists( 'get_the_posts_author_id' ) ):
function get_the_posts_author_id(){
  $author_id = get_the_author_meta( 'ID' );
  if (is_singular()) {
    global $post_id;
    $post = get_post($post_id);
    if ($post){
      $author = get_userdata($post->post_author);
      if ($author) {
        $author_id = $author->ID;
      }
    }
  }
  if (!$author_id) {
    $author_id = get_sns_default_follow_user();
  }
  return $author_id;
}
endif;

//投稿者情報がある場合
if ( !function_exists( 'is_author_exits' ) ):
function is_author_exits(){
  return get_the_author_meta( 'ID' );
}
endif;

//投稿者は管理者かどうか
if ( !function_exists( 'is_author_administrator' ) ):
function is_author_administrator(){
  $author_id = get_the_author_meta( 'ID' );
  if ($author_id) {
    $author = get_userdata( $author_id );
    if ($author && in_array( 'administrator', $author->roles )) {
      return true;
    }
  }
}
endif;

//投稿者名の取得
if ( !function_exists( 'get_the_author_display_name' ) ):
function get_the_author_display_name($id = null){
  $user_id = $id ? $id : get_the_posts_author_id();
  return esc_html(get_the_author_meta('display_name', $user_id));
}
endif;

//投稿者情報の取得
if ( !function_exists( 'get_the_author_description_text' ) ):
function get_the_author_description_text($id = null){
  $user_id = $id ? $id : get_the_posts_author_id();
  return get_the_author_meta('description', $user_id);
}
endif;


//プロフィール画面で設定したウェブサイトURLの取得
if ( !function_exists( 'get_the_author_website_url' ) ):
function get_the_author_website_url($id = null){
  $user_id = $id ? $id : get_the_posts_author_id();
  return esc_html(get_the_author_meta('url', $user_id));
}
endif;

//プロフィール画面で設定したTwitter URLの取得
if ( !function_exists( 'get_the_author_twitter_url' ) ):
function get_the_author_twitter_url($id = null){
  $user_id = $id ? $id : get_the_posts_author_id();
  return esc_html(get_the_author_meta('twitter_url', $user_id));
}
endif;

//プロフィール画面で設定したTwitter URLからTwitter IDの取得
if ( !function_exists( 'get_the_author_twitter_id' ) ):
function get_the_author_twitter_id($url = null){
  if (!$url) {
   $url = get_the_author_twitter_url();
  }
  $res = preg_match('/(twitter|x)\.com\/(.+?)\/?$/i', $url, $m);
  if ($res && $m && $m[2]) {
    return $m[2];
  }
}
endif;

//プロフィール画面で設定したMastodon URLの取得
if ( !function_exists( 'get_the_author_mastodon_url' ) ):
function get_the_author_mastodon_url($id = null){
  $user_id = $id ? $id : get_the_posts_author_id();
  return esc_html(get_the_author_meta('mastodon_url', $user_id));
}
endif;

//プロフィール画面で設定したBluesky URLの取得
if ( !function_exists( 'get_the_author_bluesky_url' ) ):
function get_the_author_bluesky_url($id = null){
  $user_id = $id ? $id : get_the_posts_author_id();
  return esc_html(get_the_author_meta('bluesky_url', $user_id));
}
endif;

//プロフィール画面で設定したMisskey URLの取得
if ( !function_exists( 'get_the_author_misskey_url' ) ):
function get_the_author_misskey_url($id = null){
  $user_id = $id ? $id : get_the_posts_author_id();
  return esc_html(get_the_author_meta('misskey_url', $user_id));
}
endif;

//プロフィール画面で設定したFacebook URLの取得
if ( !function_exists( 'get_the_author_facebook_url' ) ):
function get_the_author_facebook_url($id = null){
  $user_id = $id ? $id : get_the_posts_author_id();
  return esc_html(get_the_author_meta('facebook_url', $user_id));
}
endif;

//プロフィール画面で設定したGoogle+ URLの取得
if ( !function_exists( 'get_the_author_google_plus_url' ) ):
function get_the_author_google_plus_url($id = null){
  $user_id = $id ? $id : get_the_posts_author_id();
  return null;//esc_html(get_the_author_meta('google_plus_url', $user_id));
}
endif;

//プロフィール画面で設定したはてブ URLの取得
if ( !function_exists( 'get_the_author_hatebu_url' ) ):
function get_the_author_hatebu_url($id = null){
  $user_id = $id ? $id : get_the_posts_author_id();
  return esc_html(get_the_author_meta('hatebu_url', $user_id));
}
endif;

//プロフィール画面で設定したInstagram URLの取得
if ( !function_exists( 'get_the_author_instagram_url' ) ):
function get_the_author_instagram_url($id = null){
  $user_id = $id ? $id : get_the_posts_author_id();
  return esc_html(get_the_author_meta('instagram_url', $user_id));
}
endif;

//プロフィール画面で設定したPinterest URLの取得
if ( !function_exists( 'get_the_author_pinterest_url' ) ):
function get_the_author_pinterest_url($id = null){
  $user_id = $id ? $id : get_the_posts_author_id();
  return esc_html(get_the_author_meta('pinterest_url', $user_id));
}
endif;

//プロフィール画面で設定したYouTube URLの取得
if ( !function_exists( 'get_the_author_youtube_url' ) ):
function get_the_author_youtube_url($id = null){
  $user_id = $id ? $id : get_the_posts_author_id();
  return esc_html(get_the_author_meta('youtube_url', $user_id));
}
endif;

//プロフィール画面で設定したTikTok URLの取得
if ( !function_exists( 'get_the_author_tiktok_url' ) ):
function get_the_author_tiktok_url($id = null){
  $user_id = $id ? $id : get_the_posts_author_id();
  return esc_html(get_the_author_meta('tiktok_url', $user_id));
}
endif;

//プロフィール画面で設定したLinkedIn URLの取得
if ( !function_exists( 'get_the_author_linkedin_url' ) ):
function get_the_author_linkedin_url($id = null){
  $user_id = $id ? $id : get_the_posts_author_id();
  return esc_html(get_the_author_meta('linkedin_url', $user_id));
}
endif;

//プロフィール画面で設定したnote URLの取得
if ( !function_exists( 'get_the_author_note_url' ) ):
function get_the_author_note_url($id = null){
  $user_id = $id ? $id : get_the_posts_author_id();
  return esc_html(get_the_author_meta('note_url', $user_id));
}
endif;

//プロフィール画面で設定しSoundCloud URLの取得
if ( !function_exists( 'get_the_author_soundcloud_url' ) ):
function get_the_author_soundcloud_url($id = null){
  $user_id = $id ? $id : get_the_posts_author_id();
  return esc_html(get_the_author_meta('soundcloud_url', $user_id));
}
endif;

//プロフィール画面で設定しFlickr URLの取得
if ( !function_exists( 'get_the_author_flickr_url' ) ):
function get_the_author_flickr_url($id = null){
  $user_id = $id ? $id : get_the_posts_author_id();
  return esc_html(get_the_author_meta('flickr_url', $user_id));
}
endif;

//プロフィール画面で設定したLINE@ URLの取得
if ( !function_exists( 'get_the_author_line_at_url' ) ):
function get_the_author_line_at_url($id = null){
  $user_id = $id ? $id : get_the_posts_author_id();
  return esc_html(get_the_author_meta('line_at_url', $user_id));
}
endif;
//プロフィール画面で設定したTwitter URLからLINE IDの取得
if ( !function_exists( 'get_the_author_line_id' ) ):
function get_the_author_line_id($url = null){
  if (!$url) {
   $url = get_the_author_line_at_url();
  }
  $res = preg_match('{.*@([^/]+)/?$}i', $url, $m);
  //URLでなかった場合は文字列をそのままIDとして取得
  if (!$res) {
    return $url;
  }
  if ($res && $m && $m[1]) {
    return $m[1];
  }
}
endif;

//プロフィール画面で設定したAmazon URLの取得
if ( !function_exists( 'get_the_author_amazon_url' ) ):
function get_the_author_amazon_url($id = null){
  $user_id = $id ? $id : get_the_posts_author_id();
  return esc_html(get_the_author_meta('amazon_url', $user_id));
}
endif;

//プロフィール画面で設定したTwitch URLの取得
if ( !function_exists( 'get_the_author_twitch_url' ) ):
function get_the_author_twitch_url($id = null){
  $user_id = $id ? $id : get_the_posts_author_id();
  return esc_html(get_the_author_meta('twitch_url', $user_id));
}
endif;

//プロフィール画面で設定した楽天ROOM URLの取得
if ( !function_exists( 'get_the_author_rakuten_room_url' ) ):
function get_the_author_rakuten_room_url($id = null){
  $user_id = $id ? $id : get_the_posts_author_id();
  return esc_html(get_the_author_meta('rakuten_room_url', $user_id));
}
endif;

//プロフィール画面で設定したSlack URLの取得
if ( !function_exists( 'get_the_author_slack_url' ) ):
function get_the_author_slack_url($id = null){
  $user_id = $id ? $id : get_the_posts_author_id();
  return esc_html(get_the_author_meta('slack_url', $user_id));
}
endif;

//プロフィール画面で設定したGitHub URLの取得
if ( !function_exists( 'get_the_author_github_url' ) ):
function get_the_author_github_url($id = null){
  $user_id = $id ? $id : get_the_posts_author_id();
  return esc_html(get_the_author_meta('github_url', $user_id));
}
endif;

//プロフィール画面で設定したCodePen URLの取得
if ( !function_exists( 'get_the_author_codepen_url' ) ):
function get_the_author_codepen_url($id = null){
  $user_id = $id ? $id : get_the_posts_author_id();
  return esc_html(get_the_author_meta('codepen_url', $user_id));
}
endif;

//フォローボタンが存在する場合（feedly、RSS以外）
if ( !function_exists( 'is_author_follow_buttons_exits' ) ):
function is_author_follow_buttons_exits(){
  return get_the_author_website_url()
         || get_the_author_twitter_url()
         || get_the_author_mastodon_url()
         || get_the_author_bluesky_url()
         || get_the_author_misskey_url()
         || get_the_author_facebook_url()
         //|| get_the_author_google_plus_url()
         || get_the_author_hatebu_url()
         || get_the_author_instagram_url()
         || get_the_author_pinterest_url()
         || get_the_author_youtube_url()
         || get_the_author_linkedin_url()
         || get_the_author_note_url()
         || get_the_author_soundcloud_url()
         || get_the_author_flickr_url()
         || get_the_author_line_at_url()
         || get_the_author_amazon_url()
         || get_the_author_twitch_url()
         || get_the_author_rakuten_room_url()
         || get_the_author_slack_url()
         || get_the_author_github_url()
         || get_the_author_codepen_url();
}
endif;

//全てのフォローボタンのうちどれかが表示されているか
if ( !function_exists( 'is_any_sns_follow_buttons_exist' ) ):
function is_any_sns_follow_buttons_exist(){
  return is_author_follow_buttons_exits()
      || is_feedly_follow_button_visible()
      || is_rss_follow_button_visible();
}
endif;

