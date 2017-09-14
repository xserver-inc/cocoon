<?php //SNS用の関数など


// ユーザープロフィールの項目のカスタマイズ
if ( !function_exists( 'user_contactmethods_custom' ) ):
function user_contactmethods_custom($prof_items){
  //項目の追加
  $prof_items['twitter_url'] = __( 'Twitter URL', THEME_NAME );
  $prof_items['facebook_url'] = __( 'Facebook URL', THEME_NAME );
  $prof_items['google_plus_url'] = __( 'Google+ URL', THEME_NAME );
  $prof_items['hatebu_url'] = __( 'はてブ URL', THEME_NAME );
  $prof_items['instagram_url'] = __( 'Instagram URL', THEME_NAME );
  $prof_items['pinterest_url'] = __( 'Pinterest URL', THEME_NAME );
  $prof_items['youtube_url'] = __( 'YouTube URL', THEME_NAME );
  $prof_items['flickr_url'] = __( 'Flickr URL', THEME_NAME );
  $prof_items['line_t_url'] = __( 'LINE@ URL', THEME_NAME );
  $prof_items['github_url'] = __( 'GitHub URL', THEME_NAME );

  return $prof_items;
}
endif;
add_filter('user_contactmethods', 'user_contactmethods_custom');

//ユーザーIDの取得
if ( !function_exists( 'get_the_posts_author_id' ) ):
function get_the_posts_author_id(){
  $author_id = get_sns_default_follow_user();
  if (is_singular()) {
    global $post_id;
    $post = get_post($post_id);
    if ($post){
      $author = get_userdata($post->post_author);
      $author_id =$author->ID;
    }
  }
  return $author_id;
}
endif;

//プロフィール画面で設定したウェブサイトURLの取得
if ( !function_exists( 'get_the_author_website_url' ) ):
function get_the_author_website_url(){
  return esc_html(get_the_author_meta('url', get_the_posts_author_id()));
}
endif;

//プロフィール画面で設定したTwitter URLの取得
if ( !function_exists( 'get_the_author_twitter_url' ) ):
function get_the_author_twitter_url(){
  return esc_html(get_the_author_meta('twitter_url', get_the_posts_author_id()));
}
endif;

//プロフィール画面で設定したFacebook URLの取得
if ( !function_exists( 'get_the_author_facebook_url' ) ):
function get_the_author_facebook_url(){
  return esc_html(get_the_author_meta('facebook_url', get_the_posts_author_id()));
}
endif;

//プロフィール画面で設定したGoogle+ URLの取得
if ( !function_exists( 'get_the_author_google_plus_url' ) ):
function get_the_author_google_plus_url(){
  return esc_html(get_the_author_meta('google_plus_url', get_the_posts_author_id()));
}
endif;

//プロフィール画面で設定したはてブ URLの取得
if ( !function_exists( 'get_the_author_hatebu_url' ) ):
function get_the_author_hatebu_url(){
  return esc_html(get_the_author_meta('hatebu_url', get_the_posts_author_id()));
}
endif;

//プロフィール画面で設定したInstagram URLの取得
if ( !function_exists( 'get_the_author_instagram_url' ) ):
function get_the_author_instagram_url(){
  return esc_html(get_the_author_meta('instagram_url', get_the_posts_author_id()));
}
endif;

//プロフィール画面で設定したPinterest URLの取得
if ( !function_exists( 'get_the_author_pinterest_url' ) ):
function get_the_author_pinterest_url(){
  return esc_html(get_the_author_meta('pinterest_url', get_the_posts_author_id()));
}
endif;

//プロフィール画面で設定したYouTube URLの取得
if ( !function_exists( 'get_the_author_youtube_url' ) ):
function get_the_author_youtube_url(){
  return esc_html(get_the_author_meta('youtube_url', get_the_posts_author_id()));
}
endif;

//プロフィール画面で設定した立夏 URLの取得
if ( !function_exists( 'get_the_author_flickr_url' ) ):
function get_the_author_flickr_url(){
  return esc_html(get_the_author_meta('flickr_url', get_the_posts_author_id()));
}
endif;

//プロフィール画面で設定したLINE@ URLの取得
if ( !function_exists( 'get_the_author_line_at_url' ) ):
function get_the_author_line_at_url(){
  return esc_html(get_the_author_meta('line_at_url', get_the_posts_author_id()));
}
endif;

//プロフィール画面で設定したGitHub URLの取得
if ( !function_exists( 'get_the_author_github_url' ) ):
function get_the_author_github_url(){
  return esc_html(get_the_author_meta('github_url', get_the_posts_author_id()));
}
endif;

//全てのフォローボタンのうちどれかが表示されているか
if ( !function_exists( 'is_any_sns_follow_buttons_exist' ) ):
function is_any_sns_follow_buttons_exist(){
  return get_the_author_website_url() || get_the_author_twitter_url() || get_the_author_facebook_url() || get_the_author_google_plus_url() || get_the_author_hatebu_url() || get_the_author_instagram_url() || get_the_author_pinterest_url() || get_the_author_youtube_url() || get_the_author_flickr_url() || get_the_author_line_at_url() || get_the_author_github_url() || is_feedly_follow_button_visible() || is_rss_follow_button_visible();
}
endif;

