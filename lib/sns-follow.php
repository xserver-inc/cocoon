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


// SNSフォローサービスの定義（プロフィール項目・設定画面のリスト・フォローボタン出力の単一のソース）
// キー: ユーザーメタのキー名
// 値: name=サービス表示名, title=ボタンのtitle文言, class=ボタンの追加クラス, icon=アイコンクラス
//     aria=aria-label文言（省略時はtitleと同じ文言を使用）
// ※配列の並び順がそのままフォローボタンの表示順になる
if ( !function_exists( 'get_theme_sns_follow_services' ) ):
function get_theme_sns_follow_services(){
  return array(
    'twitter_url' => array(
      'name'  => __( 'X（旧Twitter）', THEME_NAME ),
      'title' => __( 'Xをフォロー', THEME_NAME ),
      'class' => 'twitter-button twitter-follow-button-sq x-corp-button x-corp-follow-button-sq',
      'icon'  => 'icon-x-corp-logo',
    ),
    'mastodon_url' => array(
      'name'  => __( 'Mastodon', THEME_NAME ),
      'title' => __( 'Mastodonをフォロー', THEME_NAME ),
      'class' => 'mastodon-button mastodon-follow-button-sq',
      'icon'  => 'icon-mastodon-logo',
    ),
    'bluesky_url' => array(
      'name'  => __( 'Bluesky', THEME_NAME ),
      'title' => __( 'Blueskyをフォロー', THEME_NAME ),
      'class' => 'bluesky-button bluesky-follow-button-sq',
      'icon'  => 'icon-bluesky-logo',
    ),
    'misskey_url' => array(
      'name'  => __( 'Misskey', THEME_NAME ),
      'title' => __( 'Misskeyをフォロー', THEME_NAME ),
      'class' => 'misskey-button misskey-follow-button-sq',
      'icon'  => 'icon-misskey-logo',
    ),
    'facebook_url' => array(
      'name'  => __( 'Facebook', THEME_NAME ),
      'title' => __( 'Facebookをフォロー', THEME_NAME ),
      'class' => 'facebook-button facebook-follow-button-sq',
      'icon'  => 'icon-facebook-logo',
    ),
    'threads_url' => array(
      'name'  => __( 'Threads', THEME_NAME ),
      'title' => __( 'Threadsをフォロー', THEME_NAME ),
      'class' => 'threads-button threads-follow-button-sq',
      'icon'  => 'icon-threads-logo',
    ),
    'reddit_url' => array(
      'name'  => __( 'Reddit', THEME_NAME ),
      'title' => __( 'Redditをフォロー', THEME_NAME ),
      'class' => 'reddit-button reddit-follow-button-sq',
      'icon'  => 'icon-reddit-logo',
    ),
    'hatebu_url' => array(
      'name'  => __( 'はてなブックマーク', THEME_NAME ),
      'title' => __( 'はてブをフォロー', THEME_NAME ),
      'class' => 'hatebu-button hatebu-follow-button-sq',
      'icon'  => 'icon-hatebu-logo',
    ),
    'instagram_url' => array(
      'name'  => __( 'Instagram', THEME_NAME ),
      'title' => __( 'Instagramをフォロー', THEME_NAME ),
      'class' => 'instagram-button instagram-follow-button-sq',
      'icon'  => 'icon-instagram-logo',
    ),
    'youtube_url' => array(
      'name'  => __( 'YouTube', THEME_NAME ),
      'title' => __( 'YouTubeをフォロー', THEME_NAME ),
      'class' => 'youtube-button youtube-follow-button-sq',
      'icon'  => 'icon-youtube-logo',
    ),
    'tiktok_url' => array(
      'name'  => __( 'TikTok', THEME_NAME ),
      'title' => __( 'TikTokをフォロー', THEME_NAME ),
      'class' => 'tiktok-button tiktok-follow-button-sq',
      'icon'  => 'icon-tiktok-logo',
    ),
    'linkedin_url' => array(
      'name'  => __( 'LinkedIn', THEME_NAME ),
      'title' => __( 'LinkedInをフォロー', THEME_NAME ),
      'class' => 'linkedin-button linkedin-follow-button-sq',
      'icon'  => 'icon-linkedin-logo',
    ),
    'note_url' => array(
      'name'  => __( 'note', THEME_NAME ),
      'title' => __( 'noteをフォロー', THEME_NAME ),
      'class' => 'note-button note-follow-button-sq',
      'icon'  => 'icon-note-logo',
    ),
    'soundcloud_url' => array(
      'name'  => __( 'SoundCloud', THEME_NAME ),
      'title' => __( 'SoundCloudをフォロー', THEME_NAME ),
      'class' => 'soundcloud-button soundcloud-follow-button-sq',
      'icon'  => 'icon-soundcloud-logo',
    ),
    'flickr_url' => array(
      'name'  => __( 'Flickr', THEME_NAME ),
      'title' => __( 'Flickrをフォロー', THEME_NAME ),
      'class' => 'flickr-button flickr-follow-button-sq',
      'icon'  => 'icon-flickr-logo',
    ),
    'pinterest_url' => array(
      'name'  => __( 'Pinterest', THEME_NAME ),
      'title' => __( 'Pinterestをフォロー', THEME_NAME ),
      'class' => 'pinterest-button pinterest-follow-button-sq',
      'icon'  => 'icon-pinterest-logo',
    ),
    'line_at_url' => array(
      'name'  => __( 'LINE@', THEME_NAME ),
      'title' => __( 'LINE@をフォロー', THEME_NAME ),
      'class' => 'line-button line-follow-button-sq',
      'icon'  => 'icon-line-logo',
    ),
    'amazon_url' => array(
      'name'  => __( 'Amazon欲しい物リスト', THEME_NAME ),
      'title' => __( 'Amazon欲しい物リスト', THEME_NAME ),
      'class' => 'amazon-button amazon-follow-button-sq',
      'icon'  => 'icon-amazon-logo',
      'aria'  => __( 'Amazonほしい物リストをチェック', THEME_NAME ),
    ),
    'twitch_url' => array(
      'name'  => __( 'Twitch', THEME_NAME ),
      'title' => __( 'Twitchをフォロー', THEME_NAME ),
      'class' => 'twitch-button twitch-follow-button-sq',
      'icon'  => 'icon-twitch-logo',
    ),
    'rakuten_room_url' => array(
      'name'  => __( '楽天ROOM', THEME_NAME ),
      'title' => __( '楽天ROOMをフォロー', THEME_NAME ),
      'class' => 'rakuten-room-button rakuten-room-follow-button-sq',
      'icon'  => 'icon-rakuten-room-logo',
    ),
    'slack_url' => array(
      'name'  => __( 'Slack', THEME_NAME ),
      'title' => __( 'Slackをフォロー', THEME_NAME ),
      'class' => 'slack-button slack-follow-button-sq',
      'icon'  => 'icon-slack-logo',
    ),
    'github_url' => array(
      'name'  => __( 'GitHub', THEME_NAME ),
      'title' => __( 'GitHubをフォロー', THEME_NAME ),
      'class' => 'github-button github-follow-button-sq',
      'icon'  => 'icon-github-logo',
    ),
    'codepen_url' => array(
      'name'  => __( 'CodePen', THEME_NAME ),
      'title' => __( 'CodePenをフォロー', THEME_NAME ),
      'class' => 'codepen-button codepen-follow-button-sq',
      'icon'  => 'icon-codepen-logo',
    ),
  );
}
endif;

// ユーザープロフィールの項目のカスタマイズ
add_filter('user_contactmethods', 'user_contactmethods_custom');
if ( !function_exists( 'user_contactmethods_custom' ) ):
function user_contactmethods_custom($prof_items){
  //定義済みのSNSフォローサービスを項目として追加
  foreach ( get_theme_sns_follow_services() as $key => $data ) {
    $label = $data['name'];
    /* translators: %s: SNSサービス名 */
    $format = __( '%s URL', THEME_NAME );
    //位置指定プレースホルダー（%1$s）で書かれた翻訳も通常形式に揃える
    $format = str_replace( '%1$s', '%s', $format );
    //翻訳文に%sプレースホルダーが欠落している場合のフォールバック（訳文不備によるサービス名消失を防止）
    if ( strpos( $format, '%s' ) === false ) {
      $format = '%s URL';
    }
    //sprintfではなくstr_replaceを使い、誤訳（%sの重複等）による致命的エラーを防ぐ
    $prof_items[$key] = str_replace( '%s', $label, $format );
  }

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
  return esc_url(get_the_author_meta('url', $user_id));
}
endif;

//プロフィール画面で設定したTwitter URLの取得
if ( !function_exists( 'get_the_author_twitter_url' ) ):
function get_the_author_twitter_url($id = null){
  $user_id = $id ? $id : get_the_posts_author_id();
  return esc_url(get_the_author_meta('twitter_url', $user_id));
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
  return esc_url(get_the_author_meta('mastodon_url', $user_id));
}
endif;

//プロフィール画面で設定したBluesky URLの取得
if ( !function_exists( 'get_the_author_bluesky_url' ) ):
function get_the_author_bluesky_url($id = null){
  $user_id = $id ? $id : get_the_posts_author_id();
  return esc_url(get_the_author_meta('bluesky_url', $user_id));
}
endif;

//プロフィール画面で設定したMisskey URLの取得
if ( !function_exists( 'get_the_author_misskey_url' ) ):
function get_the_author_misskey_url($id = null){
  $user_id = $id ? $id : get_the_posts_author_id();
  return esc_url(get_the_author_meta('misskey_url', $user_id));
}
endif;

//プロフィール画面で設定したFacebook URLの取得
if ( !function_exists( 'get_the_author_facebook_url' ) ):
function get_the_author_facebook_url($id = null){
  $user_id = $id ? $id : get_the_posts_author_id();
  return esc_url(get_the_author_meta('facebook_url', $user_id));
}
endif;

//プロフィール画面で設定したThreads URLの取得
if ( !function_exists( 'get_the_author_threads_url' ) ):
function get_the_author_threads_url($id = null){
  $user_id = $id ? $id : get_the_posts_author_id();
  return esc_url(get_the_author_meta('threads_url', $user_id));
}
endif;

//プロフィール画面で設定したReddit URLの取得
if ( !function_exists( 'get_the_author_reddit_url' ) ):
function get_the_author_reddit_url($id = null){
  $user_id = $id ? $id : get_the_posts_author_id();
  return esc_url(get_the_author_meta('reddit_url', $user_id));
}
endif;

//プロフィール画面で設定したGoogle+ URLの取得
if ( !function_exists( 'get_the_author_google_plus_url' ) ):
function get_the_author_google_plus_url($id = null){
  $user_id = $id ? $id : get_the_posts_author_id();
  return null;//esc_url(get_the_author_meta('google_plus_url', $user_id));
}
endif;

//プロフィール画面で設定したはてブ URLの取得
if ( !function_exists( 'get_the_author_hatebu_url' ) ):
function get_the_author_hatebu_url($id = null){
  $user_id = $id ? $id : get_the_posts_author_id();
  return esc_url(get_the_author_meta('hatebu_url', $user_id));
}
endif;

//プロフィール画面で設定したInstagram URLの取得
if ( !function_exists( 'get_the_author_instagram_url' ) ):
function get_the_author_instagram_url($id = null){
  $user_id = $id ? $id : get_the_posts_author_id();
  return esc_url(get_the_author_meta('instagram_url', $user_id));
}
endif;

//プロフィール画面で設定したPinterest URLの取得
if ( !function_exists( 'get_the_author_pinterest_url' ) ):
function get_the_author_pinterest_url($id = null){
  $user_id = $id ? $id : get_the_posts_author_id();
  return esc_url(get_the_author_meta('pinterest_url', $user_id));
}
endif;

//プロフィール画面で設定したYouTube URLの取得
if ( !function_exists( 'get_the_author_youtube_url' ) ):
function get_the_author_youtube_url($id = null){
  $user_id = $id ? $id : get_the_posts_author_id();
  return esc_url(get_the_author_meta('youtube_url', $user_id));
}
endif;

//プロフィール画面で設定したTikTok URLの取得
if ( !function_exists( 'get_the_author_tiktok_url' ) ):
function get_the_author_tiktok_url($id = null){
  $user_id = $id ? $id : get_the_posts_author_id();
  return esc_url(get_the_author_meta('tiktok_url', $user_id));
}
endif;

//プロフィール画面で設定したLinkedIn URLの取得
if ( !function_exists( 'get_the_author_linkedin_url' ) ):
function get_the_author_linkedin_url($id = null){
  $user_id = $id ? $id : get_the_posts_author_id();
  return esc_url(get_the_author_meta('linkedin_url', $user_id));
}
endif;

//プロフィール画面で設定したnote URLの取得
if ( !function_exists( 'get_the_author_note_url' ) ):
function get_the_author_note_url($id = null){
  $user_id = $id ? $id : get_the_posts_author_id();
  return esc_url(get_the_author_meta('note_url', $user_id));
}
endif;

//プロフィール画面で設定しSoundCloud URLの取得
if ( !function_exists( 'get_the_author_soundcloud_url' ) ):
function get_the_author_soundcloud_url($id = null){
  $user_id = $id ? $id : get_the_posts_author_id();
  return esc_url(get_the_author_meta('soundcloud_url', $user_id));
}
endif;

//プロフィール画面で設定しFlickr URLの取得
if ( !function_exists( 'get_the_author_flickr_url' ) ):
function get_the_author_flickr_url($id = null){
  $user_id = $id ? $id : get_the_posts_author_id();
  return esc_url(get_the_author_meta('flickr_url', $user_id));
}
endif;

//プロフィール画面で設定したLINE@ URLの取得
if ( !function_exists( 'get_the_author_line_at_url' ) ):
function get_the_author_line_at_url($id = null){
  $user_id = $id ? $id : get_the_posts_author_id();
  return esc_url(get_the_author_meta('line_at_url', $user_id));
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
  return esc_url(get_the_author_meta('amazon_url', $user_id));
}
endif;

//プロフィール画面で設定したTwitch URLの取得
if ( !function_exists( 'get_the_author_twitch_url' ) ):
function get_the_author_twitch_url($id = null){
  $user_id = $id ? $id : get_the_posts_author_id();
  return esc_url(get_the_author_meta('twitch_url', $user_id));
}
endif;

//プロフィール画面で設定した楽天ROOM URLの取得
if ( !function_exists( 'get_the_author_rakuten_room_url' ) ):
function get_the_author_rakuten_room_url($id = null){
  $user_id = $id ? $id : get_the_posts_author_id();
  return esc_url(get_the_author_meta('rakuten_room_url', $user_id));
}
endif;

//プロフィール画面で設定したSlack URLの取得
if ( !function_exists( 'get_the_author_slack_url' ) ):
function get_the_author_slack_url($id = null){
  $user_id = $id ? $id : get_the_posts_author_id();
  return esc_url(get_the_author_meta('slack_url', $user_id));
}
endif;

//プロフィール画面で設定したGitHub URLの取得
if ( !function_exists( 'get_the_author_github_url' ) ):
function get_the_author_github_url($id = null){
  $user_id = $id ? $id : get_the_posts_author_id();
  return esc_url(get_the_author_meta('github_url', $user_id));
}
endif;

//プロフィール画面で設定したCodePen URLの取得
if ( !function_exists( 'get_the_author_codepen_url' ) ):
function get_the_author_codepen_url($id = null){
  $user_id = $id ? $id : get_the_posts_author_id();
  return esc_url(get_the_author_meta('codepen_url', $user_id));
}
endif;

//フォローボタンが存在する場合（feedly、RSS以外）
if ( !function_exists( 'is_author_follow_buttons_exits' ) ):
function is_author_follow_buttons_exits(){
  //WordPress標準のウェブサイトURLをチェック
  if ( get_the_author_website_url() ) {
    return true;
  }

  //定義済みのSNSフォローサービスのうち、どれか一つでもURLが登録されていればtrue
  foreach ( get_theme_sns_follow_services() as $meta_key => $data ) {
    //従来の個別取得関数（子テーマで上書き可能）があれば優先して判定
    $get_func = 'get_the_author_' . $meta_key;
    $url = function_exists($get_func) ? call_user_func($get_func) : esc_url(get_the_author_meta($meta_key, get_the_posts_author_id()));
    if ( $url ) {
      return true;
    }
  }

  return false;
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

