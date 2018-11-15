<?php //SNSページのフォローボタン
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */ ?>
<?php
if ( is_any_sns_follow_buttons_exist()
      //  && (
      //   is_author_administrator()
      //   || (is_author_exits() && is_author_follow_buttons_exits())
      // )
    ): //全てのフォローボタンを表示するかどうか?>
<!-- SNSページ -->
<div class="sns-follow<?php echo get_additional_sns_follow_button_classes(); ?>">

  <?php if ( get_sns_follow_message() ): //フォローメッセージがあるか?>
  <div class="sns-follow-message"><?php echo get_sns_follow_display_message(); //フォローメッセージの取得?></div>
  <?php endif; ?>
  <div class="sns-follow-buttons sns-buttons">

  <?php if ( get_the_author_website_url() )://ウェブサイトフォローボタンを表示するか ?>
    <a href="<?php echo get_the_author_website_url(); //ウェブサイトフォローIDの取得?>" class="follow-button website-button website-follow-button-sq" target="_blank" title="<?php _e( '著者サイト', THEME_NAME ) ?>" rel="nofollow"><span class="icon-home-logo"></span></a>
  <?php endif; ?>

  <?php if ( get_the_author_twitter_url() )://Twitterフォローボタンを表示するか ?>
    <a href="<?php echo get_the_author_twitter_url(); //TwitterフォローIDの取得?>" class="follow-button twitter-button twitter-follow-button-sq" target="_blank" title="<?php _e( 'Twitterをフォロー', THEME_NAME ) ?>" rel="nofollow"><span class="icon-twitter-logo"></span></a>
  <?php endif; ?>

  <?php if ( get_the_author_facebook_url() )://Facebookフォローボタンを表示するか ?>
    <a href="<?php echo get_the_author_facebook_url(); //FacebookフォローIDの取得?>" class="follow-button facebook-button facebook-follow-button-sq" target="_blank" title="<?php _e( 'Facebookをフォロー', THEME_NAME ) ?>" rel="nofollow"><span class="icon-facebook-logo"></span></a>
  <?php endif; ?>

  <?php if ( get_the_author_hatebu_url() )://はてブフォローボタンを表示するか ?>
    <a href="<?php echo get_the_author_hatebu_url(); //はてブフォローIDの取得 ?>" class="follow-button hatebu-button hatebu-follow-button-sq" target="_blank" title="<?php _e( 'はてブをフォロー', THEME_NAME ) ?>" rel="nofollow"><span class="icon-hatebu-logo"></span></a>
  <?php endif; ?>

  <?php if ( get_the_author_google_plus_url() )://Google＋フォローボタンを表示するか ?>
    <a href="<?php echo get_the_author_google_plus_url(); //Google＋フォローIDの取得 ?>" class="follow-button google-plus-button google-plus-follow-button-sq" target="_blank" title="<?php _e( 'Google＋をフォロー', THEME_NAME ) ?>" rel="nofollow publisher"><span class="icon-google-plus-logo"></span></a>
  <?php endif; ?>

  <?php if ( get_the_author_instagram_url() )://Instagramフォローボタンを表示するか ?>
    <a href="<?php echo get_the_author_instagram_url(); //InstagramフォローIDの取得 ?>" class="follow-button instagram-button instagram-follow-button-sq" target="_blank" title="<?php _e( 'Instagramをフォロー', THEME_NAME ) ?>" rel="nofollow"><span class="icon-instagram-new"></span></a>
  <?php endif; ?>

  <?php if (  get_the_author_youtube_url() )://YouTubeフォローボタンを表示するか ?>
    <a href="<?php echo get_the_author_youtube_url(); //YouTubeフォローURLの取得 ?>" class="follow-button youtube-button youtube-follow-button-sq" target="_blank" title="<?php _e( 'YouTubeをフォロー', THEME_NAME ) ?>" rel="nofollow"><span class="icon-youtube-logo"></span></a>
  <?php endif; ?>

  <?php if (  get_the_author_flickr_url() )://Flickrフォローボタンを表示するか ?>
    <a href="<?php echo get_the_author_flickr_url(); //YFlickrフォローIDの取得 ?>" class="follow-button flickr-button flickr-follow-button-sq" target="_blank" title="<?php _e( 'Flickrをフォロー', THEME_NAME ) ?>" rel="nofollow"><span class="icon-flickr-logo"></span></a>
  <?php endif; ?>

  <?php if ( get_the_author_pinterest_url() )://Pinterestフォローボタンを表示するか ?>
    <a href="<?php echo get_the_author_pinterest_url(); //PinterestフォローIDの取得 ?>" class="follow-button pinterest-button pinterest-follow-button-sq" target="_blank" title="<?php _e( 'Pinterestをフォロー', THEME_NAME ) ?>" rel="nofollow"><span class="icon-pinterest-logo"></span></a>
  <?php endif; ?>

  <?php if ( get_the_author_line_at_url() )://LINE@フォローボタンを表示するか ?>
    <a href="<?php echo get_the_author_line_at_url(); //LINE@フォローURLの取得 ?>" class="follow-button line-button line-follow-button-sq" target="_blank" title="<?php _e( 'LINE@をフォロー', THEME_NAME ) ?>" rel="nofollow"><span class="icon-line-logo"></span></a>
  <?php endif; ?>

  <?php if ( get_the_author_amazon_url() )://Amazon欲しい物リストボタンを表示するか ?>
    <a href="<?php echo get_the_author_amazon_url(); //Amazon欲しい物リストURLの取得 ?>" class="follow-button amazon-button amazon-follow-button-sq" target="_blank" title="<?php _e( 'Amazon欲しい物リスト', THEME_NAME ) ?>" rel="nofollow"><span class="icon-amazon-logo"></span></a>
  <?php endif; ?>

  <?php if ( get_the_author_rakuten_room_url() )://楽天ROOMボタンを表示するか ?>
    <a href="<?php echo get_the_author_rakuten_room_url(); //楽天ROOM URLの取得 ?>" class="follow-button rakuten-room-button rakuten-room-follow-button-sq" target="_blank" title="<?php _e( '楽天ROOM', THEME_NAME ) ?>" rel="nofollow"><span class="icon-rakuten-room-logo"></span></a>
  <?php endif; ?>

  <?php if ( get_the_author_github_url() )://GitHubフォローボタンを表示するか ?>
    <a href="<?php echo get_the_author_github_url(); //GitHubフォローURLの取得 ?>" class="follow-button github-button github-follow-button-sq" target="_blank" title="<?php _e( 'GitHubをフォロー', THEME_NAME ) ?>" rel="nofollow"><span class="icon-github-logo"></span></a>
  <?php endif; ?>

  <?php if ( is_feedly_follow_button_visible() )://feedlyフォローボタンを表示するか ?>
    <a href="//feedly.com/i/subscription/feed/<?php bloginfo("rss2_url"); ?>" class="follow-button feedly-button feedly-follow-button-sq" target="blank" title="<?php _e( 'feedlyで更新情報を購読', THEME_NAME ) ?>" rel="nofollow"><span class="icon-feedly-logo"></span><span class="follow-count feedly-follow-count"><?php echo get_feedly_count(); ?></span></a>
  <?php endif; ?>

  <?php if ( is_rss_follow_button_visible() )://RSSフォローボタンを表示するか ?>
    <a href="<?php bloginfo('rss2_url'); ?>" class="follow-button rss-button rss-follow-button-sq" target="_blank" title="<?php _e( 'RSSで更新情報をフォロー', THEME_NAME ) ?>" rel="nofollow"><span class="icon-rss-logo"></span></a>
  <?php endif; ?>

  </div><!-- /.sns-follow-buttons -->

</div><!-- /.sns-follow -->
<?php endif; ?>
