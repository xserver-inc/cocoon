<?php
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;
?>
<!-- ブログカード関連ヘルプ -->
<div id="single-page" class="postbox">
  <h2 class="hndle"><?php _e( 'ブログカード関連ヘルプ', THEME_NAME ) ?></h2>
  <div class="inside">

    <p><?php _e( 'ブログカードに関する記事です。', THEME_NAME ) ?></p>

    <ul>
      <li><?php echo get_help_page_tag('https://wp-cocoon.com/how-to-use-internal-blogcard/', __( '内部ブログカードの利用方法', THEME_NAME )); ?></li>
      <li><?php echo get_help_page_tag('https://wp-cocoon.com/how-to-use-external-blogcard/', __( '外部ブログカードの利用方法', THEME_NAME )); ?></li>
      <li><?php echo get_help_page_tag('https://wp-cocoon.com/blogcard-types/', __( 'ブログカードの装飾', THEME_NAME )); ?></li>
      <li><?php echo get_help_page_tag('https://wp-cocoon.com/not-blogcard/', __( 'ブログカードの無効化', THEME_NAME )); ?></li>
    </ul>

  </div>
</div>
