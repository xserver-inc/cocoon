<?php //SNSページのフォローボタン ?>
<?php if ( is_any_sns_follow_buttons_exist() ): //全てのフォローボタンを表示するかどうか?>
<!-- SNSページ -->
<aside class="sns-follow">

  <?php if ( get_sns_follow_message() ): //フォローメッセージがあるか?>
  <div class="sns-follow-message"><?php echo sprintf(get_sns_follow_message(), get_the_author_meta('display_name', get_the_posts_author_id())); //フォローメッセージの取得?></div>
  <?php endif; ?>
  <div class="sns-follow-buttons">

  <?php if ( get_the_author_website_url() )://ウェブサイトフォローボタンを表示するか ?>
    <a href="<?php echo get_the_author_website_url(); //ウェブサイトフォローIDの取得?>" class="follow-button website-follow-button" target="_blank" title="<?php _e( '著者サイト', THEME_NAME ) ?>" rel="nofollow"><span class="icon-home"></span></a>
  <?php endif; ?>

  <?php if ( get_the_author_twitter_url() )://Twitterフォローボタンを表示するか ?>
    <a href="<?php echo get_the_author_twitter_url(); //TwitterフォローIDの取得?>" class="follow-button twitter-follow-button" target="_blank" title="<?php _e( 'Twitterをフォロー', THEME_NAME ) ?>" rel="nofollow"><span class="icon-twitter-logo"></span></a>
  <?php endif; ?>

  <?php if ( get_the_author_facebook_url() )://Facebookフォローボタンを表示するか ?>
    <a href="<?php echo get_the_author_facebook_url(); //FacebookフォローIDの取得?>" class="follow-button facebook-follow-button" target="_blank" title="<?php _e( 'Facebookをフォロー', THEME_NAME ) ?>" rel="nofollow"><span class="icon-facebook-logo"></span></a>
  <?php endif; ?>

  <?php if ( get_the_author_hatebu_url() )://はてブフォローボタンを表示するか ?>
    <a href="<?php echo get_the_author_hatebu_url(); //はてブフォローIDの取得 ?>" class="follow-button hatebu-follow-button" target="_blank" title="<?php _e( 'はてブをフォロー', THEME_NAME ) ?>" rel="nofollow"><span class="icon-hatebu-logo"></span></a>
  <?php endif; ?>

  <?php if ( get_the_author_google_plus_url() )://Google＋フォローボタンを表示するか ?>
    <a href="<?php echo get_the_author_google_plus_url(); //Google＋フォローIDの取得 ?>" class="follow-button google-plus-follow-button" target="_blank" title="<?php _e( 'Google＋をフォロー', THEME_NAME ) ?>" rel="nofollow publisher"><span class="icon-google-plus-logo"></span></a>
  <?php endif; ?>

  <?php if ( get_the_author_instagram_url() )://Instagramフォローボタンを表示するか ?>
    <a href="<?php echo get_the_author_instagram_url(); //InstagramフォローIDの取得 ?>" class="follow-button instagram-follow-button" target="_blank" title="<?php _e( 'Instagramをフォロー', THEME_NAME ) ?>" rel="nofollow"><span class="icon-instagram-new"></span></a>
  <?php endif; ?>

  <?php if ( get_the_author_pinterest_url() )://Pinterestフォローボタンを表示するか ?>
    <a href="<?php echo get_the_author_pinterest_url(); //PinterestフォローIDの取得 ?>" class="follow-button pinterest-follow-button" target="_blank" title="<?php _e( 'Pinterestをフォロー', THEME_NAME ) ?>" rel="nofollow"><span class="icon-pinterest-logo"></span></a>
  <?php endif; ?>

  <?php if (  get_the_author_youtube_url() )://YouTubeフォローボタンを表示するか ?>
    <a href="<?php echo get_the_author_youtube_url(); //YouTubeフォローURLの取得 ?>" class="follow-button youtube-follow-button" target="_blank" title="<?php _e( 'YouTubeをフォロー', THEME_NAME ) ?>" rel="nofollow"><span class="icon-youtube-logo"></span></a>
  <?php endif; ?>

  <?php if (  get_the_author_flickr_url() )://Flickrフォローボタンを表示するか ?>
    <a href="<?php echo get_the_author_flickr_url(); //YFlickrフォローIDの取得 ?>" class="follow-button flickr-follow-button" target="_blank" title="<?php _e( 'Flickrをフォロー', THEME_NAME ) ?>" rel="nofollow"><span class="icon-flickr-logo"></span></a>
  <?php endif; ?>

  <?php if ( get_the_author_line_at_url() )://LINE@フォローボタンを表示するか ?>
    <a href="<?php echo get_the_author_line_at_url(); //LINE@フォローURLの取得 ?>" class="follow-button line-at-follow-button" target="_blank" title="<?php _e( 'LINE@をフォロー', THEME_NAME ) ?>" rel="nofollow"><span class="icon-line-logo"></span></a>
  <?php endif; ?>

  <?php if ( get_the_author_github_url() )://GitHubフォローボタンを表示するか ?>
    <a href="<?php echo get_the_author_github_url(); //GitHubフォローURLの取得 ?>" class="follow-button github-follow-button" target="_blank" title="<?php _e( 'GitHubをフォロー', THEME_NAME ) ?>" rel="nofollow"><span class="icon-github-logo"></span></a>
  <?php endif; ?>

  <?php if ( is_feedly_follow_button_visible() )://feedlyフォローボタンを表示するか ?>
    <a href="//feedly.com/i/subscription/feed/<?php bloginfo("rss2_url"); ?>" class="follow-button feedly-follow-button" target="blank" title="<?php _e( 'feedlyで更新情報を購読', THEME_NAME ) ?>" rel="nofollow"><span class="icon-feedly-logo"></span></a>
  <?php endif; ?>

  <?php if ( is_rss_follow_button_visible() )://RSSフォローボタンを表示するか ?>
    <a href="<?php bloginfo('rss2_url'); ?>" class="follow-button rss-follow-button" target="_blank" title="<?php _e( 'RSSで更新情報をフォロー', THEME_NAME ) ?>" rel="nofollow"><span class="icon-rss-logo"></span></a>
  <?php endif; ?>

  </div><!-- /.sns-follow-buttons -->

</aside><!-- /.sns-follow -->
<?php endif; ?>