<?php //SEO設定に必要な定数や関数

//canonicalタグの追加
define('OP_CANONICAL_TAG_ENABLE', 'canonical_tag_enable');
if ( !function_exists( 'is_canonical_tag_enable' ) ):
function is_canonical_tag_enable(){
  return get_theme_option(OP_CANONICAL_TAG_ENABLE, 1);
}
endif;

//分割ページにrel="next"/"prev"タグの追加
define('OP_PREV_NEXT_ENABLE', 'prev_next_enable');
if ( !function_exists( 'is_prev_next_enable' ) ):
function is_prev_next_enable(){
  return get_theme_option(OP_PREV_NEXT_ENABLE, 1);
}
endif;

//カテゴリページの2ページ目以降をnoindexとする
define('OP_PAGED_CATEGORY_PAGE_NOINDEX', 'paged_category_page_noindex');
if ( !function_exists( 'is_paged_category_page_noindex' ) ):
function is_paged_category_page_noindex(){
  return get_theme_option(OP_PAGED_CATEGORY_PAGE_NOINDEX);
}
endif;

//検索エンジンに知らせる日付
define('OP_SEO_DATE_TYPE', 'seo_date_type');
if ( !function_exists( 'get_seo_date_type' ) ):
function get_seo_date_type(){
  return get_theme_option(OP_SEO_DATE_TYPE, 'post_date');
}
endif;
//投稿日・更新日タグを取得する
if ( !function_exists( 'get_the_date_tags' ) ):
function get_the_date_tags(){
  //更新日が存在するときは、投稿日にupdatedクラスを出力しない
  $updated = !get_update_time() ? ' updated' : null;
  $date_modified = !get_update_time() || (get_seo_date_type() == 'post_date_only') ? ' dateModified' : null;
  $date_published = (get_seo_date_type() == 'update_date_only') ? 'datePublished ' : null;
  //timeタグがある投稿日
  $time_post_date_tag = '<span class="post-date"><time class="entry-date date published'.$updated.'" datetime="'.get_the_time('c').'" itemprop="datePublished'.$date_modified.'">'.get_the_time('Y.m.d').'</time></span>';
  //通常の投稿日
  $post_date_tag = '<span class="post-date"><span class="entry-date date published" itemprop="datePublished">'.get_the_time('Y.m.d').'</span></span>';
  //timeタグがある更新日
  $time_update_date_tag = '<span class="post-update"><time class="entry-date date updated" datetime="'.get_update_time('c').'" itemprop="'.$date_published.'dateModified">'.get_update_time('Y.m.d').'</time></span>';
  //通常の更新日
  $update_date_tag = '<span class="post-update"><span class="entry-date date updated" itemprop="dateModified">'.get_update_time('Y.m.d').'</span></span>';
  switch (get_seo_date_type()) {
    //投稿日を伝える
    case 'post_date':
      $date_tags = $time_post_date_tag;
      //更新日があるとき
      if (get_update_time()) {
        $date_tags .= $update_date_tag;
      }
      break;
    //更新日を伝える
    case 'update_date':
      //$date_tags = $post_date_tag;
      if (get_update_time()) {
        $date_tags = $post_date_tag.$time_update_date_tag; //更新時
      } else {
        $date_tags = $time_post_date_tag;  //投稿日
      }
      break;
    //投稿日のみを伝える
    case 'post_date_only':
      $date_tags = $time_post_date_tag;
      break;
    //更新日のみを伝える
    default:
      //更新日がある場合
      if (get_update_time()) {
        $date_tags = $time_update_date_tag;
      } else {
        $date_tags = $time_post_date_tag;
      }


      break;
  }
  return $date_tags;
}
endif;
