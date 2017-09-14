<?php //主にモバイル用に表情は早くするためのアイコンボタン ?>
<?php if ( 1 ): ?>
<div class="sns-share">
  <?php if ( 0 ): //シェアボタン用のメッセージを取得?>
    <p class="sns-share-message"><?php echo esc_html( get_share_message_label() ) ?></p>
  <?php endif; ?>

  <div class="sns-share-buttons">
    <?php if ( 1/*is_twitter_btn_visible()*/ )://Twitterボタンを表示するか ?>
      <a href="<?php //echo get_twitter_share_url(); ?>" class="share-button twitter-share-button" target="blank" rel="nofollow"><span class="social-icon icon-twitter"></span><span class="share-count twitter-share-count"></span></a>
    <?php endif; ?>

    <?php if ( 1/*is_facebook_btn_visible()*/ )://Facebookボタンを表示するか ?>
      <a href="//www.facebook.com/sharer/sharer.php?u=<?php the_permalink() ?>&amp;t=<?php echo urlencode( get_the_title() ); ?>" class="share-button facebook-share-button" target="blank" rel="nofollow"><span class="social-icon icon-facebook"></span><span class="share-count facebook-share-count"></span></a>
    <?php endif; ?>

    <?php if ( 1/*is_hatena_btn_visible()*/ )://はてなボタンを表示するか ?>
      <a href="<?php //echo get_hatebu_url(get_permalink()); ?>" class="share-button hatebu-share-button" data-hatena-bookmark-layout="simple" title="<?php the_title(); ?>" rel="nofollow"><span class="social-icon icon-hatena"></span><span class="share-count hatebu-share-count"></span></a>
    <?php endif; ?>

    <?php if ( 1/*is_google_plus_btn_visible()*/ )://Google＋ボタンを表示するか ?>
      <a href="//plus.google.com/share?url=<?php echo rawurlencode(get_permalink($post->ID)) ?>" onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;" class="share-button googleplus-share-button" target="blank" rel="nofollow"><span class="social-icon icon-googleplus"></span><span class="share-count googleplus-share-count"></span></a>
    <?php endif; ?>

    <?php if ( 1/*is_pocket_btn_visible()*/ )://pocketボタンを表示するか ?>
      <a href="//getpocket.com/edit?url=<?php the_permalink() ?>" class="share-button pocket-share-button" target="blank" rel="nofollow"><span class="social-icon icon-pocket"></span><span class="share-count pocket-share-count"></span></a>
    <?php endif; ?>

    <?php if ( 1/*is_line_btn_visible()*/ )://LINEボタンを表示するか ?>
      <a href="<?php //echo get_line_share_url(); ?>" class="share-button line-share-button" target="blank" rel="nofollow"><span class="social-icon icon-line"></span></a>
    <?php endif; ?>

  </div><!-- /.sns-share-buttons -->

</div><!-- /.sns-share -->
<?php endif; ?>