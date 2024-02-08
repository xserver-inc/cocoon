<?php //SNSページのフォローボタン　-> skin-grayish-topfullでPC版ヘッダーに流用
/**
 * Cocoon WordPress Theme
 * @author: yhira modify Na2factory
 * @link: https://cocoon-grayish.na2-factory.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if (!defined('ABSPATH')) exit; ?>

<?php
global $skin_gnavi_snsbtn_options;
global $skin_gnavi_snsbtn_On;

if (
  is_any_sns_follow_buttons_exist() && ($skin_gnavi_snsbtn_On === 'true')
  //  && (
  //   is_author_administrator()
  //   || (is_author_exits() && is_author_follow_buttons_exits())
  // )
) : //全てのフォローボタンを表示するかどうか

  //呼び出し前にユーザーIDが設定されている場合
  $user_id = isset($_USER_ID) ? $_USER_ID : get_the_posts_author_id();

  // プロフィール欄に入力　かつ　カスタマイザーONの組み合わせが１つもなければリストは出力しない
  $skin_gnavi_nooutput = (($skin_gnavi_snsbtn_options['gnavi_sns_twitter'] === 'snsbtn_select_On') && get_the_author_twitter_url($user_id)) ||
    (($skin_gnavi_snsbtn_options['gnavi_sns_mastodon'] === 'snsbtn_select_On') && get_the_author_mastodon_url($user_id)) ||
    (($skin_gnavi_snsbtn_options['gnavi_sns_bluesky'] === 'snsbtn_select_On') && get_the_author_bluesky_url($user_id)) ||
    (($skin_gnavi_snsbtn_options['gnavi_sns_misskey'] === 'snsbtn_select_On') && get_the_author_misskey_url($user_id)) ||
    (($skin_gnavi_snsbtn_options['gnavi_sns_facebook'] === 'snsbtn_select_On') && get_the_author_facebook_url($user_id)) ||
    (($skin_gnavi_snsbtn_options['gnavi_sns_hatena'] === 'snsbtn_select_On') && get_the_author_hatebu_url($user_id)) ||
    (($skin_gnavi_snsbtn_options['gnavi_sns_instagram'] === 'snsbtn_select_On') && get_the_author_instagram_url($user_id)) ||
    (($skin_gnavi_snsbtn_options['gnavi_sns_pinterest'] === 'snsbtn_select_On') && get_the_author_pinterest_url($user_id)) ||
    (($skin_gnavi_snsbtn_options['gnavi_sns_youtube'] === 'snsbtn_select_On') && get_the_author_youtube_url($user_id)) ||
    (($skin_gnavi_snsbtn_options['gnavi_sns_linkedin'] === 'snsbtn_select_On') && get_the_author_linkedin_url($user_id)) ||
    (($skin_gnavi_snsbtn_options['gnavi_sns_note'] === 'snsbtn_select_On') && get_the_author_note_url($user_id)) ||
    (($skin_gnavi_snsbtn_options['gnavi_sns_flickr'] === 'snsbtn_select_On') && get_the_author_flickr_url($user_id)) ||
    (($skin_gnavi_snsbtn_options['gnavi_sns_amazon'] === 'snsbtn_select_On') && get_the_author_amazon_url($user_id)) ||
    (($skin_gnavi_snsbtn_options['gnavi_sns_twitch'] === 'snsbtn_select_On') && get_the_author_twitch_url($user_id)) ||
    (($skin_gnavi_snsbtn_options['gnavi_sns_rakuten'] === 'snsbtn_select_On') && get_the_author_rakuten_room_url($user_id)) ||
    (($skin_gnavi_snsbtn_options['gnavi_sns_slack'] === 'snsbtn_select_On') && get_the_author_slack_url($user_id)) ||
    (($skin_gnavi_snsbtn_options['gnavi_sns_github'] === 'snsbtn_select_On') && get_the_author_github_url($user_id)) ||
    (($skin_gnavi_snsbtn_options['gnavi_sns_codepen'] === 'snsbtn_select_On') && get_the_author_codepen_url($user_id)) ||
    (($skin_gnavi_snsbtn_options['gnavi_sns_tiktok'] === 'snsbtn_select_On') && get_the_author_tiktok_url($user_id)) ||
    (($skin_gnavi_snsbtn_options['gnavi_sns_soundcloud'] === 'snsbtn_select_On') && get_the_author_soundcloud_url($user_id)) ||
    (($skin_gnavi_snsbtn_options['gnavi_sns_line'] === 'snsbtn_select_On') && get_the_author_line_at_url($user_id));
  // echo $skin_gnavi_nooutput;
  if ($skin_gnavi_nooutput) :
?>
    <!-- skin gnavi SNS folllow btn -->
    <li class="header-snsicon-submenu">
      <ul class="header-snsicon-submenu__list sub-menu">
        <?php if (($skin_gnavi_snsbtn_options['gnavi_sns_twitter'] === 'snsbtn_select_On') && get_the_author_twitter_url($user_id)) : //Twitterフォローボタンを表示するか
        ?>
          <li class="header-snsicon-submenu__listitem">
            <a href="<?php echo esc_url(get_the_author_twitter_url($user_id)); //TwitterフォローIDの取得
                      ?>" class="" target="_blank" title="<?php _e('Xをフォロー', THEME_NAME) ?>" rel="nofollow noopener noreferrer" aria-label="<?php _e('Xをフォロー', THEME_NAME) ?>">
              <div class="caption-wrap">
                <div class="item-label"><span class="icon-x-corp-logo"></span></div>
              </div>
            </a>
          </li>
        <?php endif; ?>

        <?php if (($skin_gnavi_snsbtn_options['gnavi_sns_mastodon'] === 'snsbtn_select_On') && get_the_author_mastodon_url($user_id)) : //Mastodonフォローボタンを表示するか
        ?>
          <li class="header-snsicon-submenu__listitem">
            <a href="<?php echo esc_url(get_the_author_mastodon_url($user_id)); //MastodonフォローIDの取得
                      ?>" class="" target="_blank" title="<?php _e('Mastodonをフォロー', THEME_NAME) ?>" rel="nofollow noopener noreferrer" aria-label="<?php _e('Mastodonをフォロー', THEME_NAME) ?>">
              <div class="caption-wrap">
                <div class="item-label"><span class="icon-mastodon"></span>
                </div>
              </div>
            </a>
          </li>
        <?php endif; ?>

        <?php if (($skin_gnavi_snsbtn_options['gnavi_sns_bluesky'] === 'snsbtn_select_On') && get_the_author_bluesky_url($user_id)) : //Blueskyフォローボタンを表示するか
        ?>
          <li class="header-snsicon-submenu__listitem">
            <a href="<?php echo esc_url(get_the_author_bluesky_url($user_id)); //BlueskyフォローIDの取得
                      ?>" class="" target="_blank" title="<?php _e('Blueskyをフォロー', THEME_NAME) ?>" rel="nofollow noopener noreferrer" aria-label="<?php _e('Blueskyをフォロー', THEME_NAME) ?>">
              <div class="caption-wrap">
                <div class="item-label"><span class="icon-bluesky"></span>
                </div>
              </div>
            </a>
          </li>
        <?php endif; ?>

        <?php if (($skin_gnavi_snsbtn_options['gnavi_sns_misskey'] === 'snsbtn_select_On') && get_the_author_misskey_url($user_id)) : //Misskeyフォローボタンを表示するか
        ?>
          <li class="header-snsicon-submenu__listitem">
            <a href="<?php echo esc_url(get_the_author_misskey_url($user_id)); //MisskeyフォローIDの取得
                      ?>" class="" target="_blank" title="<?php _e('Misskeyをフォロー', THEME_NAME) ?>" rel="nofollow noopener noreferrer" aria-label="<?php _e('Misskeyをフォロー', THEME_NAME) ?>">
              <div class="caption-wrap">
                <div class="item-label"><span class="icon-misskey"></span>
                </div>
              </div>
            </a>
          </li>
        <?php endif; ?>

        <?php if (($skin_gnavi_snsbtn_options['gnavi_sns_facebook'] === 'snsbtn_select_On') && get_the_author_facebook_url($user_id)) : //Facebookフォローボタンを表示するか
        ?>
          <li class="header-snsicon-submenu__listitem">
            <a href="<?php echo esc_url(get_the_author_facebook_url($user_id)); //FacebookフォローIDの取得
                      ?>" class="" target="_blank" title="<?php _e('Facebookをフォロー', THEME_NAME) ?>" rel="nofollow noopener noreferrer" aria-label="<?php _e('Facebookをフォロー', THEME_NAME) ?>">
              <div class="caption-wrap">
                <div class="item-label"><span class="icon-facebook"></span>
                </div>
              </div>
            </a>
          </li>
        <?php endif; ?>

        <?php if (($skin_gnavi_snsbtn_options['gnavi_sns_hatena'] === 'snsbtn_select_On') && get_the_author_hatebu_url($user_id)) : //はてブフォローボタンを表示するか
        ?>
          <li class="header-snsicon-submenu__listitem">
            <a href="<?php echo esc_url(get_the_author_hatebu_url($user_id)); //はてブフォローIDの取得
                      ?>" class="" target="_blank" title="<?php _e('はてブをフォロー', THEME_NAME) ?>" rel="nofollow noopener noreferrer" aria-label="<?php _e('はてブをフォロー', THEME_NAME) ?>">
              <div class="caption-wrap">
                <div class="item-label"><span class="icon-hatena"></span>
                </div>
              </div>
            </a>
          </li>
        <?php endif; ?>

        <?php if (($skin_gnavi_snsbtn_options['gnavi_sns_instagram'] === 'snsbtn_select_On') && get_the_author_instagram_url($user_id)) : //Instagramフォローボタンを表示するか
        ?>
          <li class="header-snsicon-submenu__listitem">
            <a href="<?php echo esc_url(get_the_author_instagram_url($user_id)); //InstagramフォローIDの取得
                      ?>" class="" target="_blank" title="<?php _e('Instagramをフォロー', THEME_NAME) ?>" rel="nofollow noopener noreferrer" aria-label="<?php _e('Instagramをフォロー', THEME_NAME) ?>">
              <div class="caption-wrap">
                <div class="item-label"><span class="icon-instagram-logo"></span>
                </div>
              </div>
            </a>
          </li>
        <?php endif; ?>

        <?php if (($skin_gnavi_snsbtn_options['gnavi_sns_pinterest'] === 'snsbtn_select_On') && get_the_author_pinterest_url($user_id)) : //Pinterestフォローボタンを表示するか
        ?>
          <li class="header-snsicon-submenu__listitem">
            <a href="<?php echo esc_url(get_the_author_pinterest_url($user_id)); //PinterestフォローIDの取得
                      ?>" class="" target="_blank" title="<?php _e('Pinterestをフォロー', THEME_NAME) ?>" rel="nofollow noopener noreferrer" aria-label="<?php _e('Pinterestをフォロー', THEME_NAME) ?>">
              <div class="caption-wrap">
                <div class="item-label"><span class="icon-pinterest"></span>
                </div>
              </div>
            </a>
          </li>
        <?php endif; ?>

        <?php if (($skin_gnavi_snsbtn_options['gnavi_sns_youtube'] === 'snsbtn_select_On') && get_the_author_youtube_url($user_id)) : //YouTubeフォローボタンを表示するか
        ?>
          <li class="header-snsicon-submenu__listitem">
            <a href="<?php echo esc_url(get_the_author_youtube_url($user_id)); //YouTubeフォローURLの取得
                      ?>" class="" target="_blank" title="<?php _e('YouTubeをフォロー', THEME_NAME) ?>" rel="nofollow noopener noreferrer" aria-label="<?php _e('YouTubeをフォロー', THEME_NAME) ?>">
              <div class="caption-wrap">
                <div class="item-label"><span class="icon-youtube"></span>
                </div>
              </div>
            </a>
          </li>
        <?php endif; ?>

        <?php if (($skin_gnavi_snsbtn_options['gnavi_sns_linkedin'] === 'snsbtn_select_On') && get_the_author_linkedin_url($user_id)) : //LinkedInフォローボタンを表示するか
        ?>
          <li class="header-snsicon-submenu__listitem">
            <a href="<?php echo esc_url(get_the_author_linkedin_url($user_id)); //LinkedInフォローURLの取得
                      ?>" class="" target="_blank" title="<?php _e('LinkedInをフォロー', THEME_NAME) ?>" rel="nofollow noopener noreferrer" aria-label="<?php _e('LinkedInをフォロー', THEME_NAME) ?>">
              <div class="caption-wrap">
                <div class="item-label"><span class="icon-linkedin"></span>
                </div>
              </div>
            </a>
          </li>
        <?php endif; ?>

        <?php if (($skin_gnavi_snsbtn_options['gnavi_sns_note'] === 'snsbtn_select_On') && get_the_author_note_url($user_id)) : //noteフォローボタンを表示するか
        ?>
          <li class="header-snsicon-submenu__listitem">
            <a href="<?php echo esc_url(get_the_author_note_url($user_id)); //noteフォローIDの取得
                      ?>" class="" target="_blank" title="<?php _e('noteをフォロー', THEME_NAME) ?>" rel="nofollow noopener noreferrer" aria-label="<?php _e('noteをフォロー', THEME_NAME) ?>">
              <div class="caption-wrap">
                <div class="item-label"><span class="icon-note-logo"></span>
                </div>
              </div>
            </a>
          </li>
        <?php endif; ?>

        <?php if (($skin_gnavi_snsbtn_options['gnavi_sns_flickr'] === 'snsbtn_select_On') && get_the_author_flickr_url($user_id)) : //Flickrフォローボタンを表示するか
        ?>
          <li class="header-snsicon-submenu__listitem">
            <a href="<?php echo esc_url(get_the_author_flickr_url($user_id)); //FlickrフォローIDの取得
                      ?>" class="" target="_blank" title="<?php _e('Flickrをフォロー', THEME_NAME) ?>" rel="nofollow noopener noreferrer" aria-label="<?php _e('Flickrをフォロー', THEME_NAME) ?>">
              <div class="caption-wrap">
                <div class="item-label"><span class="icon-flickr2"></span>
                </div>
              </div>
            </a>
          </li>
        <?php endif; ?>

        <?php if (($skin_gnavi_snsbtn_options['gnavi_sns_amazon'] === 'snsbtn_select_On') && get_the_author_amazon_url($user_id)) : //Amazon欲しい物リストボタンを表示するか
        ?>
          <li class="header-snsicon-submenu__listitem">
            <a href="<?php echo esc_url(get_the_author_amazon_url($user_id)); //Amazon欲しい物リストURLの取得
                      ?>" class="" target="_blank" title="<?php _e('Amazon欲しい物リスト', THEME_NAME) ?>" rel="nofollow noopener noreferrer" aria-label="<?php _e('Amazonほしい物リストをチェック', THEME_NAME) ?>">
              <div class="caption-wrap">
                <div class="item-label"><span class="icon-amazon-logo"></span>
                </div>
              </div>
            </a>
          </li>
        <?php endif; ?>

        <?php if (($skin_gnavi_snsbtn_options['gnavi_sns_twitch'] === 'snsbtn_select_On') && get_the_author_twitch_url($user_id)) : //Twitchボタンを表示するか
        ?>
          <li class="header-snsicon-submenu__listitem">
            <a href="<?php echo esc_url(get_the_author_twitch_url($user_id)); //Twitch URLの取得
                      ?>" class="" target="_blank" title="<?php _e('Twitchをフォロー', THEME_NAME) ?>" rel="nofollow noopener noreferrer" aria-label="<?php _e('Twitchをフォロー', THEME_NAME) ?>">
              <div class="caption-wrap">
                <div class="item-label"><span class="icon-twitch-logo"></span>
                </div>
              </div>
            </a>
          </li>
        <?php endif; ?>

        <?php if (($skin_gnavi_snsbtn_options['gnavi_sns_rakuten'] === 'snsbtn_select_On') && get_the_author_rakuten_room_url($user_id)) : //楽天ROOMボタンを表示するか
        ?>
          <li class="header-snsicon-submenu__listitem">
            <a href="<?php echo esc_url(get_the_author_rakuten_room_url($user_id)); //楽天ROOM URLの取得
                      ?>" class="" target="_blank" title="<?php _e('楽天ROOMをフォロー', THEME_NAME) ?>" rel="nofollow noopener noreferrer" aria-label="<?php _e('楽天ROOMをフォロー', THEME_NAME) ?>">
              <div class="caption-wrap">
                <div class="item-label"><span class="icon-rakuten-room-logo"></span>
                </div>
              </div>
            </a>
          </li>
        <?php endif; ?>

        <?php if (($skin_gnavi_snsbtn_options['gnavi_sns_slack'] === 'snsbtn_select_On') && get_the_author_slack_url($user_id)) : //Slackフォローボタンを表示するか
        ?>
          <li class="header-snsicon-submenu__listitem">
            <a href="<?php echo esc_url(get_the_author_slack_url($user_id)); //SlackフォローURLの取得
                      ?>" class="" target="_blank" title="<?php _e('Slackをフォロー', THEME_NAME) ?>" rel="nofollow noopener noreferrer" aria-label="<?php _e('Slackをフォロー', THEME_NAME) ?>">
              <div class="caption-wrap">
                <div class="item-label"><span class="icon-slack-logo"></span>
                </div>
              </div>
            </a>
          </li>
        <?php endif; ?>

        <?php if (($skin_gnavi_snsbtn_options['gnavi_sns_github'] === 'snsbtn_select_On') && get_the_author_github_url($user_id)) : //GitHubフォローボタンを表示するか
        ?>
          <li class="header-snsicon-submenu__listitem">
            <a href="<?php echo esc_url(get_the_author_github_url($user_id)); //GitHubフォローURLの取得
                      ?>" class="" target="_blank" title="<?php _e('GitHubをフォロー', THEME_NAME) ?>" rel="nofollow noopener noreferrer" aria-label="<?php _e('GitHubをフォロー', THEME_NAME) ?>">
              <div class="caption-wrap">
                <div class="item-label"><span class="icon-github-logo"></span>
                </div>
              </div>
            </a>
          </li>
        <?php endif; ?>

        <?php if (($skin_gnavi_snsbtn_options['gnavi_sns_codepen'] === 'snsbtn_select_On') && get_the_author_codepen_url($user_id)) : //CodePenフォローボタンを表示するか
        ?>
          <li class="header-snsicon-submenu__listitem">
            <a href="<?php echo esc_url(get_the_author_codepen_url($user_id)); //CodePenフォローURLの取得
                      ?>" class="" target="_blank" title="<?php _e('CodePenをフォロー', THEME_NAME) ?>" rel="nofollow noopener noreferrer" aria-label="<?php _e('CodePenをフォロー', THEME_NAME) ?>">
              <div class="caption-wrap">
                <div class="item-label"><span class="icon-codepen-logo"></span>
                </div>
              </div>
            </a>
          </li>
        <?php endif; ?>

        <?php if (($skin_gnavi_snsbtn_options['gnavi_sns_tiktok'] === 'snsbtn_select_On') && get_the_author_tiktok_url($user_id)) : //TikTokフォローボタンを表示するか
        ?>
          <li class="header-snsicon-submenu__listitem">
            <a href="<?php echo esc_url(get_the_author_tiktok_url($user_id)); //tiktokフォローURLの取得
                      ?>" class="" target="_blank" title="<?php _e('TikTokをフォロー', THEME_NAME) ?>" rel="nofollow noopener noreferrer" aria-label="<?php _e('TikTokをフォロー', THEME_NAME) ?>">
              <div class="caption-wrap">
                <div class="item-label"><span class="icon-tiktok-logo"></span>
                </div>
              </div>
            </a>
          </li>
        <?php endif; ?>

        <?php if (($skin_gnavi_snsbtn_options['gnavi_sns_soundcloud'] === 'snsbtn_select_On') && get_the_author_soundcloud_url($user_id)) : //SoundCloudフォローボタンを表示するか
        ?>
          <li class="header-snsicon-submenu__listitem">
            <a href="<?php echo esc_url(get_the_author_soundcloud_url($user_id)); //SoundCloudフォローIDの取得
                      ?>" class="" target="_blank" title="<?php _e('SoundCloudをフォロー', THEME_NAME) ?>" rel="nofollow noopener noreferrer" aria-label="<?php _e('SoundCloudをフォロー', THEME_NAME) ?>">
              <div class="caption-wrap">
                <div class="item-label"><span class="icon-soundcloud-logo"></span>
                </div>
              </div>
            </a>
          </li>
        <?php endif; ?>

        <?php if (($skin_gnavi_snsbtn_options['gnavi_sns_line'] === 'snsbtn_select_On') && get_the_author_line_at_url($user_id)) : //LINE@フォローボタンを表示するか
        ?>
          <li class="header-snsicon-submenu__listitem">
            <a href="<?php echo esc_url(get_the_author_line_at_url($user_id)); //LINE@フォローURLの取得
                      ?>" class="" target="_blank" title="<?php _e('LINE@をフォロー', THEME_NAME) ?>" rel="nofollow noopener noreferrer" aria-label="<?php _e('LINE@をフォロー', THEME_NAME) ?>">
              <div class="caption-wrap">
                <div class="item-label"><span class="icon-line"></span>
                </div>
              </div>
            </a>
          </li>
        <?php endif; ?>

      </ul>
    </li>
  <?php endif; ?>

<?php endif; ?>