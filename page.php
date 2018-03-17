<?php //通常ページとAMPページの切り分け
if (!is_amp()) {
   get_header();
 } else {
   get_template_part('tmp/amp-header');
 }
?>


<?php //固定ページ内容
get_template_part('tmp/page-contents'); ?>

<?php get_footer(); ?>