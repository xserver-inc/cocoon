<?php //フェイスブックバルーン
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit; ?>

<div class="fb-like-balloon">
  <div class="fb-like-balloon-thumb">
    <?php if ( has_post_thumbnail() ): // サムネイルを持っているとき ?>
      <?php the_post_thumbnail( THUMB150, array('class' => 'fb-like-balloon-entry-thumnail', 'alt' => '') ); ?>
    <?php else: // サムネイルを持っていない ?>
      <img src="<?php echo get_no_image_150x150_url(); ?>" alt="" class="fb-like-balloon-entry-thumnail no-image" width="<?php echo THUMB150WIDTH; ?>" height="<?php echo THUMB150HEIGHT; ?>" />
    <?php endif; ?>
  </div>
  <div class="fb-like-balloon-arrow-box">
    <div class="fb-like-balloon-arrow-box-in">
      <div class="fb-like-balloon-button">
        <div class="fb-like fb-like-pc" data-href="<?php echo $_FACEBOOK_URL; ?>" data-layout="box_count" data-action="like" data-show-faces="false" data-share="false"></div>

        <div class="fb-like fb-like-mobile" data-href="<?php echo $_FACEBOOK_URL; ?>" data-layout="button_count" data-action="like" data-show-faces="false" data-share="false"></div>
        <?php //通常ページの場合
        if (!is_amp()): ?>
          <?php generate_facebook_sdk_code(); ?>
        <?php else: //AMPページの場合 ?>
          <a class="facebook-follow-button" href="<?php echo $_FACEBOOK_URL; ?>"><?php _e( 'いいね！', THEME_NAME ) ?></a>
        <?php endif ?>
      </div>
      <div class="fb-like-balloon-body">
        <?php //メッセージの呼び出し
          echo $_FACEBOOK_PAGE_LIKE_TEXT; ?>
      </div>
    </div>
  </div>
  <div class="clear"></div>
</div>
