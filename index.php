<?php
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit; ?>
<?php get_header(); ?>

<?php
////////////////////////////
//一覧表示
///////////////////////
if (!is_user_agent_live_writer()) {
  //通常表示
  get_template_part('tmp/list');
} else {
  //ブログエディターLive Writerでテーマ取得の際
  get_template_part('tmp/live-writer');
}
?>

<?php get_footer(); ?>
