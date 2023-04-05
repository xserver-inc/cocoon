<?php //Facebookボックス
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit; ?>

<?php if (is_singular() && ($_FACEBOOK_URL || $_TWITTER_ID || $_LINE_ID)): ?>
  <?php $thumb = get_the_post_thumbnail(get_the_ID(), THUMB320, array('class' => 'fb-like-thumb-image card-thumb-image', 'alt' => '') ) ?>
  <div class="fb-like-box cf">
    <?php //アイキャッチがある場合
    if ($thumb): ?>
      <figure class="fb-like-thumb">
        <?php echo $thumb; //サムネイルを呼び出す?>
      </figure>
    <?php endif ?>
    <div class="fb-like-content">
      <div class="fb-like-message"><?php _e( $_MESSAGE, THEME_NAME ) ?></div>
      <div class="fb-like-buttons">
        <?php if ($_FACEBOOK_URL): ?>
          <div class="fb-like-facebook">
            <div class="fb-like" data-href="<?php echo $_FACEBOOK_URL; ?>" data-layout="button_count" data-action="like" data-size="large" data-show-faces="true" data-share="false"></div>
            <?php //通常ページの場合
            if (!is_amp()): ?>
              <?php generate_facebook_sdk_code(); ?>
            <?php else: //AMPページの場合 ?>
              <a class="facebook-follow-button" href="<?php echo $_FACEBOOK_URL; ?>"><?php _e( 'いいね！', THEME_NAME ) ?></a>
            <?php endif ?>
          </div>
        <?php endif ?>
        <?php //_v($_TWITTER_ID);
        if ($_TWITTER_ID): ?>
          <div class="fb-like-twitter">
            <a href="https://twitter.com/<?php echo $_TWITTER_ID; ?>?ref_src=twsrc%5Etfw" class="twitter-follow-button" data-show-count="false"><?php echo sprintf(__( '@%sさんをフォロー', THEME_NAME ), $_TWITTER_ID); ?></a><script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>
          </div>
        <?php endif ?>
        <?php if ($_LINE_ID): ?>
          <div class="fb-like-line">
            <?php //通常ページの場合
            if (!is_amp()): ?>
              <div class="line-it-button" style="display: none;" data-lang="ja" data-type="friend" data-lineid="@<?php echo $_LINE_ID; ?>"></div>
    <script src="https://d.line-scdn.net/r/web/social-plugin/js/thirdparty/loader.min.js" async="async" defer="defer"></script>
            <?php else: //AMPページの場合 ?>
              <a class="line-follow-button" href="http://line.naver.jp/ti/p/@<?php echo $_LINE_ID; ?>"><?php _e( '友だち追加', THEME_NAME ) ?></a>
            <?php endif ?>
          </div>
        <?php endif ?>
        <?php do_action('fb_like_box_other_buttons'); ?>
      </div>
      <div class="fb-like-sub-message"><?php _e( $_SUB_MESSAGE, THEME_NAME ) ?></div>
    </div>
  </div>
<?php endif ?>
