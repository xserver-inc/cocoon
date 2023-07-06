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
   cocoon_template_part('tmp/amp-header');
 }
?>


<?php //固定ページ内容
cocoon_template_part('tmp/page-contents'); ?>

<?php get_footer(); ?>
