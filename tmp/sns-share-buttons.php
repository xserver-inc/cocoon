<?php //シェアボタン
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit; ?>
<?php if ( is_sns_share_buttons_visible($option) ): ?>
<div class="sns-share<?php echo esc_attr(get_additional_sns_share_button_classes($option)); ?>">
  <?php if ( get_sns_bottom_share_message() && $option == SS_BOTTOM ): //シェアボタン用のメッセージを取得?>
    <div class="sns-share-message"><?php echo get_sns_bottom_share_message(); ?></div>
  <?php endif; ?>

  <div class="sns-share-buttons sns-buttons">
    <?php if ( is_twitter_share_button_visible($option) )://Twitterボタンを表示するか ?>
      <a href="<?php echo esc_url(get_twitter_share_url()); ?>" class="sns-button share-button twitter-button twitter-share-button-sq x-corp-button x-corp-share-button-sq" target="_blank" title="<?php _e( 'Xでシェア', THEME_NAME ) ?>" rel="nofollow noopener noreferrer" aria-label="<?php _e( 'Xでシェア', THEME_NAME ) ?>"><span class="social-icon icon-x-corp"></span><span class="button-caption"><?php _e( 'X', THEME_NAME ) ?></span><span class="share-count twitter-share-count x-share-count"><?php echo get_twitter_count(); ?></span></a>
    <?php endif; ?>

    <?php if ( is_mastodon_share_button_visible($option) )://Mastodonボタンを表示するか ?>
      <a href="<?php echo esc_url(get_mastodon_share_url()); ?>" class="sns-button share-button mastodon-button mastodon-share-button-sq" target="_blank" title="<?php _e( 'Mastodonでシェア', THEME_NAME ) ?>" rel="nofollow noopener noreferrer" aria-label="<?php _e( 'Mastodonでシェア', THEME_NAME ) ?>"><span class="social-icon icon-mastodon"></span><span class="button-caption"><?php _e( 'Mastodon', THEME_NAME ) ?></span><span class="share-count mastodon-share-count"><?php echo get_mastodon_count(); ?></span></a>
    <?php endif; ?>

    <?php if ( is_bluesky_share_button_visible($option) )://Blueskyボタンを表示するか ?>
      <a href="<?php echo esc_url(get_bluesky_share_url()); ?>" class="sns-button share-button bluesky-button bluesky-share-button-sq" target="_blank" title="<?php _e( 'Blueskyでシェア', THEME_NAME ) ?>" rel="nofollow noopener noreferrer" aria-label="<?php _e( 'Blueskyでシェア', THEME_NAME ) ?>"><span class="social-icon icon-bluesky"></span><span class="button-caption"><?php _e( 'Bluesky', THEME_NAME ) ?></span><span class="share-count bluesky-share-count"><?php echo get_bluesky_count(); ?></span></a>
    <?php endif; ?>

    <?php if ( is_misskey_share_button_visible($option) )://Misskeyボタンを表示するか ?>
      <a href="<?php echo esc_url(get_misskey_share_url()); ?>" class="sns-button share-button misskey-button misskey-share-button-sq" target="_blank" title="<?php _e( 'Misskeyでシェア', THEME_NAME ) ?>" rel="nofollow noopener noreferrer" aria-label="<?php _e( 'Misskeyでシェア', THEME_NAME ) ?>"><span class="social-icon icon-misskey"></span><span class="button-caption"><?php _e( 'Misskey', THEME_NAME ) ?></span><span class="share-count misskey-share-count"><?php echo get_misskey_count(); ?></span></a>
    <?php endif; ?>

    <?php if ( is_facebook_share_button_visible($option) )://Facebookボタンを表示するか ?>
      <a href="<?php echo esc_url(get_facebook_share_url()); ?>" class="sns-button share-button facebook-button facebook-share-button-sq" target="_blank" title="<?php _e( 'Facebookでシェア', THEME_NAME ) ?>" rel="nofollow noopener noreferrer" aria-label="<?php _e( 'Facebookでシェア', THEME_NAME ) ?>"><span class="social-icon icon-facebook"></span><span class="button-caption"><?php _e( 'Facebook', THEME_NAME ) ?></span><span class="share-count facebook-share-count"><?php echo get_facebook_count(); ?></span></a>
    <?php endif; ?>

    <?php if ( is_hatebu_share_button_visible($option) )://はてなボタンを表示するか ?>
      <a href="<?php echo esc_url(get_hatebu_share_url()); ?>" class="sns-button share-button hatebu-button hatena-bookmark-button hatebu-share-button-sq" data-hatena-bookmark-layout="simple" target="_blank" title="<?php _e( 'はてブでブックマーク', THEME_NAME ) ?>" rel="nofollow noopener noreferrer" aria-label="<?php _e( 'はてブでブックマーク', THEME_NAME ) ?>"><span class="social-icon icon-hatena"></span><span class="button-caption"><?php _e( 'はてブ', THEME_NAME ) ?></span><span class="share-count hatebu-share-count"><?php echo get_hatebu_count(); ?></span></a>
    <?php endif; ?>

    <?php if ( is_pocket_share_button_visible($option) )://pocketボタンを表示するか ?>
      <a href="<?php echo esc_url(get_pocket_share_url()); ?>" class="sns-button share-button pocket-button pocket-share-button-sq" target="_blank" title="<?php _e( 'Pocketに保存', THEME_NAME ) ?>" rel="nofollow noopener noreferrer" aria-label="<?php _e( 'Pocketに保存', THEME_NAME ) ?>"><span class="social-icon icon-pocket"></span><span class="button-caption"><?php _e( 'Pocket', THEME_NAME ) ?></span><span class="share-count pocket-share-count"><?php echo get_pocket_count(); ?></span></a>
    <?php endif; ?>

    <?php if ( is_line_at_share_button_visible($option) )://LINEボタンを表示するか ?>
      <a href="<?php echo esc_url(get_line_share_url()); ?>" class="sns-button share-button line-button line-share-button-sq" target="_blank" title="<?php _e( 'LINEでシェア', THEME_NAME ) ?>" rel="nofollow noopener noreferrer" aria-label="<?php _e( 'LINEでシェア', THEME_NAME ) ?>"><span class="social-icon icon-line"></span><span class="button-caption"><?php _e( 'LINE', THEME_NAME ) ?></span><span class="share-count line-share-count"></span></a>
    <?php endif; ?>

    <?php if ( is_pinterest_share_button_visible($option) )://Pinterestボタンを表示するか ?>
      <a href="<?php echo esc_url(get_pinterest_share_url()); ?>" class="sns-button share-button pinterest-button pinterest-share-button-sq" target="_blank" title="<?php _e( 'Pinterestでシェア', THEME_NAME ) ?>" rel="nofollow noopener noreferrer" data-pin-do="buttonBookmark" data-pin-custom="true" aria-label="<?php _e( 'Pinterestでシェア', THEME_NAME ) ?>"><span class="social-icon icon-pinterest"></span><span class="button-caption"><?php _e( 'Pinterest', THEME_NAME ) ?></span><span class="share-count pinterest-share-count"></span></a>
    <?php endif; ?>

    <?php if ( is_linkedin_share_button_visible($option) )://LinkedInボタンを表示するか ?>
      <a href="<?php echo esc_url(get_linkedin_share_url()); ?>" class="sns-button share-button linkedin-button linkedin-share-button-sq" target="_blank" title="<?php _e( 'LinkedInでシェア', THEME_NAME ) ?>" rel="nofollow noopener noreferrer" aria-label="<?php _e( 'LinkedInでシェア', THEME_NAME ) ?>"><span class="social-icon icon-linkedin"></span><span class="button-caption"><?php _e( 'LinkedIn', THEME_NAME ) ?></span><span class="share-count linkedin-share-count"></span></a>
    <?php endif; ?>

    <?php if ( is_copy_share_button_visible($option) && !is_amp() )://コピーボタンを表示するか
        global $_MOBILE_COPY_BUTTON;
        $_MOBILE_COPY_BUTTON = true; ?>
      <a role="button" tabindex="0" class="sns-button share-button copy-button copy-share-button-sq" data-clipboard-text="<?php echo esc_attr(get_share_page_title()); ?> <?php the_permalink(); ?>" title="<?php _e( 'タイトルとURLをコピーする', THEME_NAME ) ?>" aria-label="<?php _e( 'タイトルとURLをコピーする', THEME_NAME ) ?>"><span class="social-icon icon-copy"></span><span class="button-caption"><?php _e( 'コピー', THEME_NAME ) ?></span><span class="share-count copy-share-count"></span></a>
    <?php endif; ?>

    <?php if ( is_comment_share_button_visible($option) )://コメントボタンを表示するか ?>
      <a href="#comments" class="sns-button share-button comment-button comment-share-button-sq" title="<?php _e( 'コメントする', THEME_NAME ) ?>" aria-label="<?php _e( 'コメントする', THEME_NAME ) ?>"><span class="social-icon icon-comment"></span><span class="button-caption"><?php _e( 'コメント', THEME_NAME ) ?></span><span class="share-count comment-share-count"></span></a>
    <?php endif; ?>

  </div><!-- /.sns-share-buttons -->

</div><!-- /.sns-share -->
<?php endif; ?>
