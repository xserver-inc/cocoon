<?php //SEO設定に必要な定数や関数
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

//canonicalタグを追加する
define('OP_CANONICAL_TAG_ENABLE', 'canonical_tag_enable');
if ( !function_exists( 'is_canonical_tag_enable' ) ):
function is_canonical_tag_enable(){
  return get_theme_option(OP_CANONICAL_TAG_ENABLE, 1);
}
endif;

//分割ページにrel="next"/"prev"タグを追加する
define('OP_PREV_NEXT_ENABLE', 'prev_next_enable');
if ( !function_exists( 'is_prev_next_enable' ) ):
function is_prev_next_enable(){
  return get_theme_option(OP_PREV_NEXT_ENABLE, 1);
}
endif;

//カテゴリーページをnoindexとする
define('OP_CATEGORY_PAGE_NOINDEX', 'category_page_noindex');
if ( !function_exists( 'is_category_page_noindex' ) ):
function is_category_page_noindex(){
  return get_theme_option(OP_CATEGORY_PAGE_NOINDEX);
}
endif;

//カテゴリーページの2ページ目以降をnoindexとする
define('OP_PAGED_CATEGORY_PAGE_NOINDEX', 'paged_category_page_noindex');
if ( !function_exists( 'is_paged_category_page_noindex' ) ):
function is_paged_category_page_noindex(){
  return get_theme_option(OP_PAGED_CATEGORY_PAGE_NOINDEX);
}
endif;

//タグページをnoindexとする
define('OP_TAG_PAGE_NOINDEX', 'tag_page_noindex');
if ( !function_exists( 'is_tag_page_noindex' ) ):
function is_tag_page_noindex(){
  return get_theme_option(OP_TAG_PAGE_NOINDEX);
}
endif;

//タグページの2ページ目以降をnoindexとする
define('OP_PAGED_TAG_PAGE_NOINDEX', 'paged_tag_page_noindex');
if ( !function_exists( 'is_paged_tag_page_noindex' ) ):
function is_paged_tag_page_noindex(){
  return get_theme_option(OP_PAGED_TAG_PAGE_NOINDEX, 1);
}
endif;

//その他のアーカイブページをnoindexとする
define('OP_OTHER_ARCHIVE_PAGE_NOINDEX', 'other_archive_page_noindex');
if ( !function_exists( 'is_other_archive_page_noindex' ) ):
function is_other_archive_page_noindex(){
  return get_theme_option(OP_OTHER_ARCHIVE_PAGE_NOINDEX, 1);
}
endif;

//添付ファイルページをnoindexとする
define('OP_ATTACHMENT_PAGE_NOINDEX', 'attachment_page_noindex');
if ( !function_exists( 'is_attachment_page_noindex' ) ):
function is_attachment_page_noindex(){
  return get_theme_option(OP_ATTACHMENT_PAGE_NOINDEX, 1);
}
endif;

//検索エンジンに知らせる日付
define('OP_SEO_DATE_TYPE', 'seo_date_type');
if ( !function_exists( 'get_seo_date_type' ) ):
function get_seo_date_type(){
  return get_theme_option(OP_SEO_DATE_TYPE, 'both_date');
}
endif;
if ( !function_exists( 'is_seo_date_type_both_date' ) ):
function is_seo_date_type_both_date(){
  return get_seo_date_type() == 'both_date';
}
endif;
if ( !function_exists( 'is_seo_date_type_post_date_only' ) ):
function is_seo_date_type_post_date_only(){
  return get_seo_date_type() == 'post_date_only';
}
endif;
if ( !function_exists( 'is_seo_date_type_update_date_only' ) ):
function is_seo_date_type_update_date_only(){
  return get_seo_date_type() == 'update_date_only';
}
endif;
if ( !function_exists( 'is_seo_date_type_none' ) ):
function is_seo_date_type_none(){
  return get_seo_date_type() == 'none';
}
endif;
//投稿日・更新日タグを取得する
if ( !function_exists( 'get_the_date_tags' ) ):
function get_the_date_tags(){
  $is_reservation_post = (get_the_time('U') >= get_update_time('U'));

  $update_time = get_update_time();
  $published = is_seo_date_type_update_date_only() ? ' published' : null;
  $date_published = is_seo_date_type_update_date_only() ? 'datePublished ' : null;
  $is_update_output = !$update_time || $is_reservation_post || is_seo_date_type_post_date_only();
  //更新日が存在するときは、投稿日にupdatedクラスを出力しない
  $updated = $is_update_output ? ' updated' : null;
  $date_modified = $is_update_output ? ' dateModified' : null;
  $display_none = is_seo_date_type_none() ? ' display-none' : null;
  $post_date_icon_tag = '<span class="fa fa-clock-o" aria-hidden="true"></span>';
  if (!is_post_date_visible() && is_post_update_visible()) {
    $post_date_icon_tag = '<span class="fa fa-history" aria-hidden="true"></span>';
  }
  $post_date_tag_wrap_before = '<span class="post-date'.$display_none.'">'.$post_date_icon_tag;
  $post_date_tag_wrap_after = '</span>';
  //spanタグの投稿日
  $span_post_date_tag =
    $post_date_tag_wrap_before.
      '<span class="entry-date date published'.$updated.'"><meta itemprop="datePublished'.$date_modified.'" content="'.get_the_time('c').'">'.get_the_time(get_site_date_format()).'</span>'.
    $post_date_tag_wrap_after;
  //timeタグがある投稿日
  $time_post_date_tag =
    $post_date_tag_wrap_before.
      '<time class="entry-date date published'.$updated.'" datetime="'.get_the_time('c').'" itemprop="datePublished'.$date_modified.'">'.get_the_time(get_site_date_format()).'</time>'.
    $post_date_tag_wrap_after;
  //timeタグがある更新日
  $time_update_date_tag = '<span class="post-update'.$display_none.'"><span class="fa fa-history" aria-hidden="true"></span><time class="entry-date date'.$published.' updated" datetime="'.get_update_time('c').'" itemprop="'.$date_published.'dateModified">'.get_update_time(get_site_date_format()).'</time></span>';
  switch (get_seo_date_type()) {
    //投稿日のみを伝える
    case 'post_date_only':
      $date_tags = $time_post_date_tag;
      break;
    //更新日のみを伝える
    case 'update_date_only':
      //更新日がある場合
      if ($update_time) {
        $date_tags = $time_update_date_tag;
      } else {
        $date_tags = $time_post_date_tag;
      }
      break;
    //更新日・投稿日を伝える
    default:
      $date_tags = null;
      //更新日があるとき
      if ($update_time && !$is_reservation_post) {
        //投稿日
        $date_tags .= $span_post_date_tag;

        //更新日
        $date_tags .= $time_update_date_tag;
      } else {
        //投稿日
        $date_tags .= $time_post_date_tag;
      }

      break;
  }
  return $date_tags;
}
endif;

//JSON-LDを出力する
define('OP_JSON_LD_TAG_ENABLE', 'json_ld_tag_enable');
if ( !function_exists( 'is_json_ld_tag_enable' ) ):
function is_json_ld_tag_enable(){
  return get_theme_option(OP_JSON_LD_TAG_ENABLE, 1);
}
endif;

//メタディスクリプションのリファラ
define('OP_META_REFERRER_CONTENT', 'meta_referrer_content');
if ( !function_exists( 'get_meta_referrer_content' ) ):
function get_meta_referrer_content(){
  return get_theme_option(OP_META_REFERRER_CONTENT, 'no-referrer-when-downgrade');
}
endif;
