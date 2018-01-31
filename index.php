<?php get_header(); ?>

<?php
////////////////////////////
//一覧表示
///////////////////////
if (!is_user_agent_live_writer()) {
  //一般的なサイト表示
  get_template_part('tmp/list');
} else {
  //Windows Live Writer・Open Live Writerでテーマ取得の際
  get_template_part('tmp/live-writer');
}
?>


<?php get_footer(); ?>