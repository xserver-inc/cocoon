<?php //マルチページ用のページャーリンク
$args = array(
  'before' => '<div class="pager-links pager-prev-next">',
  'after' => '</div>',
  'link_before' => '<span class="page-numbers">',
  'link_after' => '</span>',
  'next_or_number' => 'next',
  'previouspagelink' => __( '前へ', THEME_NAME ),
  'nextpagelink' => __( '次へ', THEME_NAME ),
);
wp_link_pages($args);

$args = array(
  'before' => '<div class="pager-links">',
  'after' => '</div>',
  'link_before' => '<span class="page-numbers">',
  'link_after' => '</span>',
  'next_or_number' => 'number',
);
wp_link_pages($args); ?>