<?php //通常ページとAMPページの切り分け
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

if (!is_amp()) {
   get_header();
 } else {
   get_template_part('tmp/amp-header');
 }
?>

<?php //投稿ページ内容
get_template_part('tmp/single-contents'); ?>

<?php get_footer(); ?>
