<?php //SNSページのフォローボタン
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit; ?>
<?php
if ( is_any_sns_follow_buttons_exist()
      //  && (
      //   is_author_administrator()
      //   || (is_author_exits() && is_author_follow_buttons_exits())
      // )
    ): //全てのフォローボタンを表示するかどうか

    //呼び出し前にユーザーIDが設定されている場合
    $user_id = isset($_USER_ID) ? $_USER_ID : get_the_posts_author_id();
    ?>
<!-- SNSページ -->
<div class="sns-follow<?php echo get_additional_sns_follow_button_classes($option); ?>">

  <?php if ( get_sns_follow_message() ): //フォローメッセージがあるか?>
  <div class="sns-follow-message"><?php echo get_sns_follow_display_message(); //フォローメッセージの取得?></div>
  <?php endif; ?>
  <div class="sns-follow-buttons sns-buttons">

  <?php if ( $url = get_the_author_website_url($user_id) )://ウェブサイトフォローボタンを表示するか ?>
    <a href="<?php echo esc_url($url); //ウェブサイトフォローIDの取得?>" class="sns-button follow-button website-button website-follow-button-sq" target="_blank" title="<?php _e( '著者サイト', THEME_NAME ) ?>" rel="nofollow noopener noreferrer" aria-label="<?php _e( '著作サイトをチェック', THEME_NAME ) ?>"><span class="icon-home-logo"></span></a>
  <?php endif; ?>

  <?php
  //定義済みのSNSフォローサービスをループしてフォローボタンを出力
  foreach ( get_theme_sns_follow_services() as $meta_key => $data ):
    //従来の個別取得関数（子テーマで上書き可能）があれば優先してURLを取得
    $get_func = 'get_the_author_' . $meta_key;
    $url = function_exists($get_func) ? call_user_func($get_func, $user_id) : esc_url(get_the_author_meta($meta_key, $user_id));
    if ( !$url ) {
      continue;
    }
    //aria-label文言（個別指定がなければtitleと同じ文言を使用）
    $aria_label = isset($data['aria']) ? $data['aria'] : $data['title'];
    ?>
    <a href="<?php echo esc_url($url); ?>" class="sns-button follow-button <?php echo esc_attr($data['class']); ?>" target="_blank" title="<?php echo esc_attr($data['title']); ?>" rel="nofollow noopener noreferrer" aria-label="<?php echo esc_attr($aria_label); ?>"><span class="<?php echo esc_attr($data['icon']); ?>"></span></a>
  <?php endforeach; ?>

  <?php if ( is_feedly_follow_button_visible() )://feedlyフォローボタンを表示するか ?>
    <a href="//feedly.com/i/discover/sources/search/feed/<?php echo urlencode(get_site_url()); ?>" class="sns-button follow-button feedly-button feedly-follow-button-sq" target="_blank" title="<?php _e( 'feedlyで更新情報を購読', THEME_NAME ) ?>" rel="nofollow noopener noreferrer" aria-label="<?php _e( 'feedlyで更新情報を購読', THEME_NAME ) ?>"><span class="icon-feedly-logo"></span><span class="follow-count feedly-follow-count"><?php echo get_feedly_count(); ?></span></a>
  <?php endif; ?>

  <?php if ( is_rss_follow_button_visible() )://RSSフォローボタンを表示するか ?>
    <a href="<?php bloginfo('rss2_url'); ?>" class="sns-button follow-button rss-button rss-follow-button-sq" target="_blank" title="<?php _e( 'RSSで更新情報を購読', THEME_NAME ) ?>" rel="nofollow noopener noreferrer" aria-label="<?php _e( 'RSSで更新情報を購読', THEME_NAME ) ?>"><span class="icon-rss-logo"></span></a>
  <?php endif; ?>

  </div><!-- /.sns-follow-buttons -->

</div><!-- /.sns-follow -->
<?php endif; ?>
