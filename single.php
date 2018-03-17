<?php //通常ページとAMPページの切り分け
if (!is_amp()) {
   get_header();
 } else {
   get_template_part('tmp/amp-header');
 }
?>

<?php //投稿ページ内容
get_template_part('tmp/single-contents'); ?>

<?php get_footer(); ?>