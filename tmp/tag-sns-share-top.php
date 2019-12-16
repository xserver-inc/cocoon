<?php //カテゴリー用の内容
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;
//SNSトップシェアボタンの表示
if (is_sns_top_share_buttons_visible() && !is_paged() &&
  //カテゴリーページトップシェアボタンの表示
  (is_tag() && is_sns_tag_top_share_buttons_visible())
){
  get_template_part_with_option('tmp/sns-share-buttons', SS_TOP);
}
