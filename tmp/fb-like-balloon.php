<?php //フェイスブックバルーン ?>
<div class="fb-like-balloon">
  <div class="fb-like-balloon-thumb">
    <?php if ( has_post_thumbnail() ): // サムネイルを持っているとき ?>
      <?php the_post_thumbnail( 'thumb100', array('class' => 'fb-like-balloon-entry-thumnail', 'alt' => '') ); ?>
    <?php else: // サムネイルを持っていない ?>
      <img src="<?php echo get_template_directory_uri();//get_stylesheet_directory_uri();子テーマに画像を置いた場合 ?>/images/no-image.png" alt="NO IMAGE" class="fb-like-balloon-entry-thumnail no-image" />
    <?php endif; ?>
  </div>
  <div class="fb-like-balloon-arrow-box">
    <div class="fb-like-balloon-arrow-box-in">
      <div class="fb-like-balloon-button">
        <div class="fb-like fb-like-pc" data-href="<?php echo $_FACEBOOK_URL; ?>" data-layout="box_count" data-action="like" data-show-faces="false" data-share="false"></div>

        <div class="fb-like fb-like-mobile" data-href="<?php echo $_FACEBOOK_URL; ?>" data-layout="button_count" data-action="like" data-show-faces="false" data-share="false"></div>

        <?php generate_facebook_sdk_code(); ?>

      </div>
      <div class="fb-like-balloon-body">
        <?php //メッセージの呼び出し
          echo $_FACEBOOK_PAGE_LIKE_TEXT; ?>
      </div>
    </div>
  </div>
  <div class="clear"></div>
</div>