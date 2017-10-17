<?php get_header(); ?>

<?php
////////////////////////////
//一覧表示
///////////////////////
get_template_part('tmp/list') ?>

<?php
////////////////////////////
//インデクスボトム広告
////////////////////////////
if (is_ad_pos_index_bottom_visible() && is_all_adsenses_visible()){
  //レスポンシブ広告のフォーマットにrectangleを指定する
  get_template_part_with_ad_format(get_ad_pos_index_bottom_format(), 'ad-index-bottom');
}; ?>

<?php
////////////////////////////
//ページネーション
///////////////////////
get_template_part('tmp/pagination') ?>

<?php get_footer(); ?>