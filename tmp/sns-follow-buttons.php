<?php //SNSページのフォローボタン
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit; ?>
<?php
if ( is_any_sns_follow_buttons_exist()
      //  && (
      //   is_author_administrator()
      //   || (is_author_exits() && is_author_follow_buttons_exits())
      // )
    ): //全てのフォローボタンを表示するかどうか

    //呼び出し前にユーザーIDが設定されている場合
    $user_id = isset($_USER_ID) ? $_USER_ID : get_the_posts_author_id();
    ?>
<!-- SNSページ -->
<div class="sns-follow<?php echo get_additional_sns_follow_button_classes($option); ?>">

  <?php if ( get_sns_follow_message() ): //フォローメッセージがあるか?>
  <div class="sns-follow-message"><?php echo get_sns_follow_display_message(); //フォローメッセージの取得?></div>
  <?php endif; ?>
  <div class="sns-follow-buttons sns-buttons">

  <?php if ( get_the_author_website_url($user_id) )://ウェブサイトフォローボタンを表示するか ?>
    <a href="<?php echo esc_url(get_the_author_website_url($user_id)); //ウェブサイトフォローIDの取得?>" class="sns-button follow-button website-button website-follow-button-sq" target="_blank" title="<?php _e( '著者サイト', THEME_NAME ) ?>" rel="nofollow noopener noreferrer" aria-label="<?php _e( '著作サイトをチェック', THEME_NAME ) ?>"><span class="icon-home-logo"></span></a>
  <?php endif; ?>

  <?php if ( get_the_author_twitter_url($user_id) )://Twitterフォローボタンを表示するか ?>
    <a href="<?php echo esc_url(get_the_author_twitter_url($user_id)); //TwitterフォローIDの取得?>" class="sns-button follow-button twitter-button twitter-follow-button-sq x-corp-button x-corp-follow-button-sq" target="_blank" title="<?php _e( 'Xをフォロー', THEME_NAME ) ?>" rel="nofollow noopener noreferrer" aria-label="<?php _e( 'Xをフォロー', THEME_NAME ) ?>"><span class="icon-x-corp-logo"></span></a>
  <?php endif; ?>

  <?php if ( get_the_author_mastodon_url($user_id) )://Mastodonフォローボタンを表示するか ?>
    <a href="<?php echo esc_url(get_the_author_mastodon_url($user_id)); //MastodonフォローIDの取得?>" class="sns-button follow-button mastodon-button mastodon-follow-button-sq" target="_blank" title="<?php _e( 'Mastodonをフォロー', THEME_NAME ) ?>" rel="nofollow noopener noreferrer" aria-label="<?php _e( 'Mastodonをフォロー', THEME_NAME ) ?>"><span class="icon-mastodon-logo"></span></a>
  <?php endif; ?>

  <?php if ( get_the_author_bluesky_url($user_id) )://Blueskyフォローボタンを表示するか ?>
    <a href="<?php echo esc_url(get_the_author_bluesky_url($user_id)); //BlueskyフォローIDの取得?>" class="sns-button follow-button bluesky-button bluesky-follow-button-sq" target="_blank" title="<?php _e( 'Blueskyをフォロー', THEME_NAME ) ?>" rel="nofollow noopener noreferrer" aria-label="<?php _e( 'Blueskyをフォロー', THEME_NAME ) ?>"><span class="icon-bluesky-logo"></span></a>
  <?php endif; ?>

  <?php if ( get_the_author_misskey_url($user_id) )://Misskeyフォローボタンを表示するか ?>
    <a href="<?php echo esc_url(get_the_author_misskey_url($user_id)); //MisskeyフォローIDの取得?>" class="sns-button follow-button misskey-button misskey-follow-button-sq" target="_blank" title="<?php _e( 'Misskeyをフォロー', THEME_NAME ) ?>" rel="nofollow noopener noreferrer" aria-label="<?php _e( 'Misskeyをフォロー', THEME_NAME ) ?>"><span class="icon-misskey-logo"></span></a>
  <?php endif; ?>

  <?php if ( get_the_author_facebook_url($user_id) )://Facebookフォローボタンを表示するか ?>
    <a href="<?php echo esc_url(get_the_author_facebook_url($user_id)); //FacebookフォローIDの取得?>" class="sns-button follow-button facebook-button facebook-follow-button-sq" target="_blank" title="<?php _e( 'Facebookをフォロー', THEME_NAME ) ?>" rel="nofollow noopener noreferrer" aria-label="<?php _e( 'Facebookをフォロー', THEME_NAME ) ?>"><span class="icon-facebook-logo"></span></a>
  <?php endif; ?>

  <?php if ( get_the_author_hatebu_url($user_id) )://はてブフォローボタンを表示するか ?>
    <a href="<?php echo esc_url(get_the_author_hatebu_url($user_id)); //はてブフォローIDの取得 ?>" class="sns-button follow-button hatebu-button hatebu-follow-button-sq" target="_blank" title="<?php _e( 'はてブをフォロー', THEME_NAME ) ?>" rel="nofollow noopener noreferrer" aria-label="<?php _e( 'はてブをフォロー', THEME_NAME ) ?>"><span class="icon-hatebu-logo"></span></a>
  <?php endif; ?>

  <?php if ( get_the_author_google_plus_url($user_id) )://Google＋フォローボタンを表示するか ?>
    <a href="<?php echo esc_url(get_the_author_google_plus_url($user_id)); //Google＋フォローIDの取得 ?>" class="sns-button follow-button google-plus-button google-plus-follow-button-sq" target="_blank" title="<?php _e( 'Google＋をフォロー', THEME_NAME ) ?>" rel="nofollow publisher"><span class="icon-google-plus-logo"></span></a>
  <?php endif; ?>

  <?php if ( get_the_author_instagram_url($user_id) )://Instagramフォローボタンを表示するか ?>
    <a href="<?php echo esc_url(get_the_author_instagram_url($user_id)); //InstagramフォローIDの取得 ?>" class="sns-button follow-button instagram-button instagram-follow-button-sq" target="_blank" title="<?php _e( 'Instagramをフォロー', THEME_NAME ) ?>" rel="nofollow noopener noreferrer" aria-label="<?php _e( 'Instagramをフォロー', THEME_NAME ) ?>"><span class="icon-instagram-logo"></span></a>
  <?php endif; ?>

  <?php if (  get_the_author_youtube_url($user_id) )://YouTubeフォローボタンを表示するか ?>
    <a href="<?php echo esc_url(get_the_author_youtube_url($user_id)); //YouTubeフォローURLの取得 ?>" class="sns-button follow-button youtube-button youtube-follow-button-sq" target="_blank" title="<?php _e( 'YouTubeをフォロー', THEME_NAME ) ?>" rel="nofollow noopener noreferrer" aria-label="<?php _e( 'YouTubeをフォロー', THEME_NAME ) ?>"><span class="icon-youtube-logo"></span></a>
  <?php endif; ?>

  <?php if (  get_the_author_tiktok_url($user_id) )://TikTokフォローボタンを表示するか ?>
    <a href="<?php echo esc_url(get_the_author_tiktok_url($user_id)); //tiktokフォローURLの取得 ?>" class="sns-button follow-button tiktok-button tiktok-follow-button-sq" target="_blank" title="<?php _e( 'TikTokをフォロー', THEME_NAME ) ?>" rel="nofollow noopener noreferrer" aria-label="<?php _e( 'TikTokをフォロー', THEME_NAME ) ?>"><span class="icon-tiktok-logo"></span></a>
  <?php endif; ?>

  <?php if (  get_the_author_linkedin_url($user_id) )://LinkedInフォローボタンを表示するか ?>
    <a href="<?php echo esc_url(get_the_author_linkedin_url($user_id)); //LinkedInフォローURLの取得 ?>" class="sns-button follow-button linkedin-button linkedin-follow-button-sq" target="_blank" title="<?php _e( 'LinkedInをフォロー', THEME_NAME ) ?>" rel="nofollow noopener noreferrer" aria-label="<?php _e( 'LinkedInをフォロー', THEME_NAME ) ?>"><span class="icon-linkedin-logo"></span></a>
  <?php endif; ?>

  <?php if (  get_the_author_note_url($user_id) )://noteフォローボタンを表示するか ?>
    <a href="<?php echo esc_url(get_the_author_note_url($user_id)); //noteフォローIDの取得 ?>" class="sns-button follow-button note-button note-follow-button-sq" target="_blank" title="<?php _e( 'noteをフォロー', THEME_NAME ) ?>" rel="nofollow noopener noreferrer" aria-label="<?php _e( 'noteをフォロー', THEME_NAME ) ?>"><span class="icon-note-logo"></span></a>
  <?php endif; ?>

  <?php if (  get_the_author_soundcloud_url($user_id) )://SoundCloudフォローボタンを表示するか ?>
    <a href="<?php echo esc_url(get_the_author_soundcloud_url($user_id)); //SoundCloudフォローIDの取得 ?>" class="sns-button follow-button soundcloud-button soundcloud-follow-button-sq" target="_blank" title="<?php _e( 'SoundCloudをフォロー', THEME_NAME ) ?>" rel="nofollow noopener noreferrer" aria-label="<?php _e( 'SoundCloudをフォロー', THEME_NAME ) ?>"><span class="icon-soundcloud-logo"></span></a>
  <?php endif; ?>

  <?php if (  get_the_author_flickr_url($user_id) )://Flickrフォローボタンを表示するか ?>
    <a href="<?php echo esc_url(get_the_author_flickr_url($user_id)); //FlickrフォローIDの取得 ?>" class="sns-button follow-button flickr-button flickr-follow-button-sq" target="_blank" title="<?php _e( 'Flickrをフォロー', THEME_NAME ) ?>" rel="nofollow noopener noreferrer" aria-label="<?php _e( 'Flickrをフォロー', THEME_NAME ) ?>"><span class="icon-flickr-logo"></span></a>
  <?php endif; ?>

  <?php if ( get_the_author_pinterest_url($user_id) )://Pinterestフォローボタンを表示するか ?>
    <a href="<?php echo esc_url(get_the_author_pinterest_url($user_id)); //PinterestフォローIDの取得 ?>" class="sns-button follow-button pinterest-button pinterest-follow-button-sq" target="_blank" title="<?php _e( 'Pinterestをフォロー', THEME_NAME ) ?>" rel="nofollow noopener noreferrer" aria-label="<?php _e( 'Pinterestをフォロー', THEME_NAME ) ?>"><span class="icon-pinterest-logo"></span></a>
  <?php endif; ?>

  <?php if ( get_the_author_line_at_url($user_id) )://LINE@フォローボタンを表示するか ?>
    <a href="<?php echo esc_url(get_the_author_line_at_url($user_id)); //LINE@フォローURLの取得 ?>" class="sns-button follow-button line-button line-follow-button-sq" target="_blank" title="<?php _e( 'LINE@をフォロー', THEME_NAME ) ?>" rel="nofollow noopener noreferrer" aria-label="<?php _e( 'LINE@をフォロー', THEME_NAME ) ?>"><span class="icon-line-logo"></span></a>
  <?php endif; ?>

  <?php if ( get_the_author_amazon_url($user_id) )://Amazon欲しい物リストボタンを表示するか ?>
    <a href="<?php echo esc_url(get_the_author_amazon_url($user_id)); //Amazon欲しい物リストURLの取得 ?>" class="sns-button follow-button amazon-button amazon-follow-button-sq" target="_blank" title="<?php _e( 'Amazon欲しい物リスト', THEME_NAME ) ?>" rel="nofollow noopener noreferrer" aria-label="<?php _e( 'Amazonほしい物リストをチェック', THEME_NAME ) ?>"><span class="icon-amazon-logo"></span></a>
  <?php endif; ?>

  <?php if ( get_the_author_twitch_url($user_id) )://Twitchボタンを表示するか ?>
    <a href="<?php echo esc_url(get_the_author_twitch_url($user_id)); //Twitch URLの取得 ?>" class="sns-button follow-button twitch-button twitch-follow-button-sq" target="_blank" title="<?php _e( 'Twitchをフォロー', THEME_NAME ) ?>" rel="nofollow noopener noreferrer" aria-label="<?php _e( 'Twitchをフォロー', THEME_NAME ) ?>"><span class="icon-twitch-logo"></span></a>
  <?php endif; ?>

  <?php if ( get_the_author_rakuten_room_url($user_id) )://楽天ROOMボタンを表示するか ?>
    <a href="<?php echo esc_url(get_the_author_rakuten_room_url($user_id)); //楽天ROOM URLの取得 ?>" class="sns-button follow-button rakuten-room-button rakuten-room-follow-button-sq" target="_blank" title="<?php _e( '楽天ROOMをフォロー', THEME_NAME ) ?>" rel="nofollow noopener noreferrer" aria-label="<?php _e( '楽天ROOMをフォロー', THEME_NAME ) ?>"><span class="icon-rakuten-room-logo"></span></a>
  <?php endif; ?>

  <?php if ( get_the_author_slack_url($user_id) )://Slackフォローボタンを表示するか ?>
    <a href="<?php echo esc_url(get_the_author_slack_url($user_id)); //SlackフォローURLの取得 ?>" class="sns-button follow-button slack-button slack-follow-button-sq" target="_blank" title="<?php _e( 'Slackをフォロー', THEME_NAME ) ?>" rel="nofollow noopener noreferrer" aria-label="<?php _e( 'Slackをフォロー', THEME_NAME ) ?>"><span class="icon-slack-logo"></span></a>
  <?php endif; ?>

  <?php if ( get_the_author_github_url($user_id) )://GitHubフォローボタンを表示するか ?>
    <a href="<?php echo esc_url(get_the_author_github_url($user_id)); //GitHubフォローURLの取得 ?>" class="sns-button follow-button github-button github-follow-button-sq" target="_blank" title="<?php _e( 'GitHubをフォロー', THEME_NAME ) ?>" rel="nofollow noopener noreferrer" aria-label="<?php _e( 'GitHubをフォロー', THEME_NAME ) ?>"><span class="icon-github-logo"></span></a>
  <?php endif; ?>

    <?php if ( get_the_author_codepen_url($user_id) )://CodePenフォローボタンを表示するか ?>
      <a href="<?php echo esc_url(get_the_author_codepen_url($user_id)); //CodePenフォローURLの取得 ?>" class="sns-button follow-button codepen-button codepen-follow-button-sq" target="_blank" title="<?php _e( 'CodePenをフォロー', THEME_NAME ) ?>" rel="nofollow noopener noreferrer" aria-label="<?php _e( 'CodePenをフォロー', THEME_NAME ) ?>"><span class="icon-codepen-logo"></span></a>
    <?php endif; ?>

  <?php if ( is_feedly_follow_button_visible() )://feedlyフォローボタンを表示するか ?>
    <a href="//feedly.com/i/discover/sources/search/feed/<?php echo urlencode(get_site_url()); ?>" class="sns-button follow-button feedly-button feedly-follow-button-sq" target="_blank" title="<?php _e( 'feedlyで更新情報を購読', THEME_NAME ) ?>" rel="nofollow noopener noreferrer" aria-label="<?php _e( 'feedlyで更新情報を購読', THEME_NAME ) ?>"><span class="icon-feedly-logo"></span><span class="follow-count feedly-follow-count"><?php echo get_feedly_count(); ?></span></a>
  <?php endif; ?>

  <?php if ( is_rss_follow_button_visible() )://RSSフォローボタンを表示するか ?>
    <a href="<?php bloginfo('rss2_url'); ?>" class="sns-button follow-button rss-button rss-follow-button-sq" target="_blank" title="<?php _e( 'RSSで更新情報を購読', THEME_NAME ) ?>" rel="nofollow noopener noreferrer" aria-label="<?php _e( 'RSSで更新情報を購読', THEME_NAME ) ?>"><span class="icon-rss-logo"></span></a>
  <?php endif; ?>

  </div><!-- /.sns-follow-buttons -->

</div><!-- /.sns-follow -->
<?php endif; ?>
