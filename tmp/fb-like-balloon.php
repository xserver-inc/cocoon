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
        <a class="facebook-follow-button" target="_blank" href="<?php echo esc_url($_FACEBOOK_URL); ?>"><?php _e( 'フォロー', THEME_NAME ) ?></a>
      </div>
      <div class="fb-like-balloon-body">
        <?php //メッセージの呼び出し
          echo $_FACEBOOK_PAGE_LIKE_TEXT; ?>
      </div>
    </div>
  </div>
  <div class="clear"></div>
</div>
