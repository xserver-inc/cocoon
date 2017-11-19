<?php //シェアボタン
// function is_ssb_top(){
//   global $option;
//   return $option == SS_TOP;
// }

// function is_ssb_bottom(){
//   global $option;
//   return $option == SS_BOTTOM;
// }
// var_dump((is_sns_top_share_buttons_visible() && $option == SS_TOP));
// var_dump((is_sns_bottom_share_buttons_visible() && $option == SS_BOTTOM));
?>
<?php if ( is_sns_share_buttons_visible($option) ):
//var_dump($option) ?>
<div class="sns-share<?php echo get_additional_sns_share_button_classes($option); ?>">
  <?php if ( get_sns_bottom_share_message() && $option == SS_BOTTOM ): //シェアボタン用のメッセージを取得?>
    <div class="sns-share-message"><?php echo get_sns_bottom_share_message(); ?></div>
  <?php endif; ?>

  <div class="sns-share-buttons">
    <?php if ( is_twitter_share_button_visible($option) )://Twitterボタンを表示するか ?>
      <a href="<?php echo get_twitter_share_url(); ?>" class="share-button twitter-button twitter-share-button-sq" target="blank" rel="nofollow"><span class="social-icon icon-twitter"></span><span class="button-caption"><?php _e( 'Twitter', THEME_NAME ) ?></span><span class="share-count twitter-share-count"><?php echo get_twitter_count(); ?></span></a>
    <?php endif; ?>

    <?php if ( is_facebook_share_button_visible($option) )://Facebookボタンを表示するか ?>
      <a href="<?php echo get_facebook_share_url(); ?>" class="share-button facebook-button facebook-share-button-sq" target="blank" rel="nofollow"><span class="social-icon icon-facebook"></span><span class="button-caption"><?php _e( 'Facebook', THEME_NAME ) ?></span><span class="share-count facebook-share-count"><?php echo get_facebook_count(); ?></span></a>
    <?php endif; ?>

    <?php if ( is_hatebu_share_button_visible($option) )://はてなボタンを表示するか ?>
      <a href="<?php echo get_hatebu_share_url(); ?>" class="share-button hatebu-button hatena-bookmark-button hatebu-share-button-sq" data-hatena-bookmark-layout="simple" title="<?php the_title(); ?>" target="blank" rel="nofollow"><span class="social-icon icon-hatena"></span><span class="button-caption"><?php _e( 'はてブ', THEME_NAME ) ?></span><span class="share-count hatebu-share-count"><?php echo get_hatebu_count(); ?></span></a>
    <?php endif; ?>

    <?php if ( is_google_plus_share_button_visible($option) )://Google＋ボタンを表示するか ?>
      <a href="//plus.google.com/share?url=<?php echo get_google_plus_share_url(); ?>" onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;" class="share-button google-plus-button google-plus-share-button-sq" target="blank" rel="nofollow"><span class="social-icon icon-googleplus"></span><span class="button-caption"><?php _e( 'Google+', THEME_NAME ) ?></span><span class="share-count googleplus-share-count"><?php echo get_google_plus_count(); ?></span></a>
    <?php endif; ?>

    <?php if ( is_pocket_share_button_visible($option) )://pocketボタンを表示するか ?>
      <a href="<?php echo get_pocket_share_url(); ?>" class="share-button pocket-button pocket-share-button-sq" target="blank" rel="nofollow"><span class="social-icon icon-pocket"></span><span class="button-caption"><?php _e( 'Pocket', THEME_NAME ) ?></span><span class="share-count pocket-share-count"><?php echo get_pocket_count(); ?></span></a>
    <?php endif; ?>

    <?php if ( is_line_at_share_button_visible($option) )://LINEボタンを表示するか ?>
      <a href="<?php echo get_line_share_url(); ?>" class="share-button line-button line-share-button-sq" target="blank" rel="nofollow"><span class="social-icon icon-line"></span><span class="button-caption"><?php _e( 'LINE', THEME_NAME ) ?></span></a>
    <?php endif; ?>

  </div><!-- /.sns-share-buttons -->

</div><!-- /.sns-share -->
<?php endif; ?>