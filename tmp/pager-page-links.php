<?php //マルチページ用のページャーリンク
$args = array(
  'before' => '<div class="pager-links">',
  'after' => '</div>',
  'link_before' => '<span class="page-numbers">',
  'link_after' => '</span>',
);
wp_link_pages($args); ?>