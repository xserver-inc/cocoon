<?php //SNSページのフォローボタン ?>
<?php if ( 1/*is_all_sns_follow_btns_visible()*/ ): //全てのフォローボタンを表示するかどうか?>
<!-- SNSページ -->
<div class="sns-pages">
<?php if ( 0/*get_follow_message_label()*/ ): //フォローメッセージがあるか?>
<p class="sns-follow-msg"><?php echo esc_html( get_follow_message_label() ); //フォローメッセージの取得?></p>
<?php endif; ?>
<?php if ( get_the_author_twitter_url() )://Twitterフォローボタンを表示するか ?><a href="<?php echo esc_html(get_the_author_twitter_url()); //TwitterフォローIDの取得?>" class="twitter-page" target="_blank" title="<?php _e( 'Twitterをフォロー', THEME_NAME ) ?>" rel="nofollow"><span class="icon-twitter-logo"></span></a><?php endif; ?>
<?php if ( get_the_author_facebook_url() )://Facebookフォローボタンを表示するか ?><a href="<?php echo esc_html( get_the_author_facebook_url() ); //FacebookフォローIDの取得?>" class="facebook-page" target="_blank" title="<?php _e( 'Facebookをフォロー', THEME_NAME ) ?>" rel="nofollow"><span class="icon-facebook-logo"></span></a><?php endif; ?>
<?php if ( get_the_author_google_plus_url() )://Google＋フォローボタンを表示するか ?><a href="<?php echo esc_html( get_the_author_google_plus_url() ); //Google＋フォローIDの取得 ?>" class="google-plus-page" target="_blank" title="<?php _e( 'Google＋をフォロー', THEME_NAME ) ?>" rel="nofollow publisher"><span class="icon-google-plus-logo"></span></a><?php endif; ?>
<?php if ( get_the_author_hatebu_url() )://はてブフォローボタンを表示するか ?><a href="<?php echo esc_html( get_the_author_hatebu_url() ); //はてブフォローIDの取得 ?>" class="hatebu-page" target="_blank" title="<?php _e( 'はてブをフォロー', THEME_NAME ) ?>" rel="nofollow"><span class="icon-hatebu-logo"></span></a><?php endif; ?>
<?php if ( get_the_author_instagram_url() )://Instagramフォローボタンを表示するか ?><a href="<?php echo esc_html( get_the_author_instagram_url() ); //InstagramフォローIDの取得 ?>" class="instagram-page" target="_blank" title="<?php _e( 'Instagramをフォロー', THEME_NAME ) ?>" rel="nofollow"><span class="icon-instagram-logo"></span></a><?php endif; ?>
<?php if ( get_the_author_pinterest_url() )://Pinterestフォローボタンを表示するか ?><a href="<?php echo esc_html( get_the_author_pinterest_url() ); //PinterestフォローIDの取得 ?>" class="pinterest-page" target="_blank" title="<?php _e( 'Pinterestをフォロー', THEME_NAME ) ?>" rel="nofollow"><span class="icon-pinterest-logo"></span></a><?php endif; ?>
<?php if (  get_the_author_youtube_url() )://YouTubeフォローボタンを表示するか ?><a href="<?php echo esc_html(  get_the_author_youtube_url() ); //YouTubeフォローURLの取得 ?>" class="youtube-page" target="_blank" title="<?php _e( 'YouTubeをフォロー', THEME_NAME ) ?>" rel="nofollow"><span class="icon-youtube-logo"></span></a><?php endif; ?>
<?php if (  get_the_author_flickr_url() )://Flickrフォローボタンを表示するか ?><a href="<?php echo esc_html(  get_the_author_flickr_url() ); //YFlickrフォローIDの取得 ?>" class="flickr-page" target="_blank" title="<?php _e( 'Flickrをフォロー', THEME_NAME ) ?>" rel="nofollow"><span class="icon-flickr-logo"></span></a><?php endif; ?>
<?php if ( get_the_author_line_at_url() )://LINE@フォローボタンを表示するか ?><a href="<?php echo esc_html(  get_the_author_line_at_url() ); //LINE@フォローURLの取得 ?>" class="line-at-page" target="_blank" title="<?php _e( 'LINE@をフォロー', THEME_NAME ) ?>" rel="nofollow"><span class="icon-line-logo"></span></a><?php endif; ?>
<?php if ( get_the_author_github_url() )://GitHubフォローボタンを表示するか ?><a href="<?php echo esc_html(  get_the_author_github_url() ); //GitHubフォローURLの取得 ?>" class="github-page" target="_blank" title="<?php _e( 'GitHubをフォロー', THEME_NAME ) ?>" rel="nofollow"><span class="icon-github-logo"></span></a><?php endif; ?>
<?php if ( 0/*is_rss_follow_btn_visible()*/ )://RSSフォローボタンを表示するか ?><a href="<?php bloginfo('rss2_url'); ?>" class="rss-page" target="_blank" title="<?php _e( 'RSSで更新情報をフォロー', THEME_NAME ) ?>" rel="nofollow"><span class="icon-rss-logo"></span></a><?php endif; ?>

<?php endif; ?>