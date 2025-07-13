<?php //広告関係の関数
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

//広告が表示可能かどうか
if ( !function_exists( 'is_ads_visible' ) ):
function is_ads_visible(){
  //$ads_visible = is_all_ads_visible();
  $tmp_post_ids = get_ad_exclude_post_ids();
  $post_ids = explode(',', $tmp_post_ids);
  $post_ids_empty = empty($tmp_post_ids);

  $category_ids = get_ad_exclude_category_ids();
  $category_ids_empty = empty($category_ids);

  //広告の除外（いずれかがあてはまれば表示しない）
  $is_exclude_ids = (
    //記事の除外
    (!$post_ids_empty && is_single( $post_ids ))//投稿ページの除外
    || (!$post_ids_empty && is_page( $post_ids )) //個別ページの除外
    //カテゴリーの除外
    || (!$category_ids_empty && is_single() && in_category( $category_ids )) //投稿ページの除外
    || (!$category_ids_empty && is_category( $category_ids )) //カテゴリーアーカイブページの除外
  );

  return is_all_ads_visible() &&
    //get_ad_code() && //広告コードが挿入されている
    !$is_exclude_ids && //除外ページでない場合広告を表示
    is_the_page_ads_visible() && //ページで除外していない場合
    !is_attachment() && //添付ページではない場合
    !is_404() && //404ページではない場合
    !is_search(); //検索結果ページで無い場合
}
endif;

//アドセンスID（data-ad-clientとdata-ad-slot）を取得する
if ( !function_exists( 'get_adsense_ids' ) ):
function get_adsense_ids($code = null){
  if (!$code) {
    $code = get_ad_code();
  }

  //AdSenseコードからIDを取得する
  $res = preg_match(
    '/'.preg_quote(DATA_AD_CLIENT).'="([^"]+?)".+?'.preg_quote(DATA_AD_SLOT).'="([^"]+?)"/is', $code, $m);
  if ($res && isset($m[1]) && isset($m[2])) {
    return array(
      DATA_AD_CLIENT => $m[1],
      DATA_AD_SLOT   => $m[2],
      );
  }
}
endif;

//アドセンスのdata-ad-clientを取得する
if ( !function_exists( 'get_adsense_data_ad_client' ) ):
function get_adsense_data_ad_client($code = null){
  $ids = get_adsense_ids($code);
  if ($ids && isset($ids[DATA_AD_CLIENT])) {
    return $ids[DATA_AD_CLIENT];
  }
}
endif;

//アドセンスのdata-ad-slotを取得する
if ( !function_exists( 'get_adsense_data_ad_slot' ) ):
function get_adsense_data_ad_slot($code = null){
  $ids = get_adsense_ids($code);
  if ($ids && isset($ids[DATA_AD_SLOT])) {
    return $ids[DATA_AD_SLOT];
  }
}
endif;

//アドセンスの通常ページ用レスポンシブコードを生成する
if ( !function_exists( 'get_normal_adsense_responsive_code' ) ):
function get_normal_adsense_responsive_code($format = DATA_AD_FORMAT_AUTO, $code = null){
  //フォーマットが設定されていない場合はそのまま表示
  if ($format == DATA_AD_FORMAT_NONE) {
    return $code;
  }

  //$codeに広告コードが入っている場合はそこから取得する（無い場合はテーマ設定のAdSenseコードを利用）
  if (get_adsense_ids($code)) {
    $data_ad_layout = null;

    //記事内広告の場合は付け加える
    if ($format == DATA_AD_FORMAT_FLUID) {
      $data_ad_layout = '     data-ad-layout="in-article"';
    }

    global $_IS_ADSENSE_EXIST;
    $_IS_ADSENSE_EXIST = true;

    $adsense_script = null;

    $data_full_width_responsive = 'false';
    if (is_mobile_adsense_width_wide()) {
      $data_full_width_responsive = 'true';
    }

    return $adsense_script.
'<!-- レスポンシブコード -->
<ins class="adsbygoogle"
  style="display:block"
  data-ad-client="'.get_adsense_data_ad_client($code).'"
  data-ad-slot="'.get_adsense_data_ad_slot($code).'"'.
  $data_ad_layout.'
  data-ad-format="'.$format.'"
  data-full-width-responsive="'.$data_full_width_responsive.'"></ins>
<script>
(adsbygoogle = window.adsbygoogle || []).push({});
</script>';
  }
  //コードが設定されていない場合はそのままコードを出力する
  return $code;
}
endif;

//アドセンスのAMP用レスポンシブコードを生成する
if ( !function_exists( 'get_amp_adsense_responsive_code' ) ):
function get_amp_adsense_responsive_code($format = DATA_AD_FORMAT_AUTO, $code = null){
  //$codeに広告コードが入っている場合はそこから取得する
  if (get_adsense_ids($code)) {
    $ad_client = get_adsense_data_ad_client($code);
    $ad_slot = get_adsense_data_ad_slot($code);
    //広告コード
    $code = '<amp-ad
          width="100vw"
          height="320"
          type="adsense"
          data-ad-client="'.$ad_client.'"
          data-ad-slot="'.$ad_slot.'"
          data-auto-format="rspv"
          data-full-width>
            <div overflow></div>
        </amp-ad>';

    return $code;
  }
  //AdSense広告でない場合はそのままコードを出力する
  return $code;
}
endif;

//アドセンスのレスポンシブコードを生成する
if ( !function_exists( 'get_adsense_responsive_code' ) ):
function get_adsense_responsive_code($format = DATA_AD_FORMAT_AUTO, $code = null){
  if (!is_amp()) {
    //通常ページの場合
    $ad = get_normal_adsense_responsive_code($format, $code);
  } else {
    //AMPページの場合
    $ad = get_amp_adsense_responsive_code($format, $code);
  }
  return $ad;
}
endif;

//H2見出しを判別する正規表現を定数にする
if ( !defined('H2_REG') )
  define('H2_REG', apply_filters('insertion_heading_regexp', '/<h2/i'));//H2見出しのパターン

//本文中にH2見出しが最初に含まれている箇所を返す（含まれない場合はnullを返す）
//H3-H6しか使っていない場合は、h2部分を変更してください
if ( !function_exists( 'get_h2_included_in_body' ) ):
function get_h2_included_in_body( $the_content ){
  if ( preg_match( H2_REG, $the_content, $h2results )) {//H2見出しが本文中にあるかどうか
    return $h2results[0];
  }
}
endif;

//最初のH2の手前に広告を挿入（最初のH2を置換）
add_filter('the_content', 'add_ads_before_1st_h2', BEFORE_1ST_H2_AD_PRIORITY_STANDARD);
if ( !function_exists( 'add_ads_before_1st_h2' ) ):
function add_ads_before_1st_h2($the_content) {

  if ( is_singular() //投稿日・固定ページのとき
       && is_ad_pos_content_middle_visible() //設定で表示が許可されているとき
       && is_all_adsenses_visible() //AdSense設定項目で表示が許可されているか
       //&& !is_multi_paged() //マルチページの2ページ目以降でない場合
  ){
    //広告（AdSense）タグを記入
    ob_start();//バッファリング
    get_template_part_with_ad_format(get_ad_pos_content_middle_format(), 'ad-content-middle', is_ad_pos_content_middle_label_visible());
    $ad_template = ob_get_clean();
    $h2result = get_h2_included_in_body( $the_content );//本文にH2タグが含まれていれば取得
    //H2見出しが本文中にある場合のみ
    if ( $h2result ) {
      //本文全てのH2見出し手前に広告を表示しない場合
      if (!is_ad_pos_all_content_middle_visible()) {
        //最初のH2の手前のみに広告を挿入（最初のH2を置換）
        $limit = 1;
      } else {
        //無制限に置換する
        $limit = intval(get_ad_pos_content_middle_count());
        $limit = $limit ? $limit : -1;
      }
      $the_content = preg_replace(H2_REG, $ad_template.PHP_EOL.PHP_EOL.$h2result, $the_content, $limit);
    }
  }
  return $the_content;
}
endif;


//インデックスページの最後のページかどうか
if ( !function_exists( 'is_pagination_last_page' ) ):
function is_pagination_last_page(){
  global $wp_query;
  //現在のページ数
  $now_page = get_query_var('paged') ? get_query_var('paged') : 1;
  //インデックスリストのページ数
  $max_page = intval($wp_query->max_num_pages);
  return ( $now_page == $max_page );
}
endif;

//インデックスページの最後のページかどうか
if ( !function_exists( 'is_posts_per_page_6_and_over' ) ):
function is_posts_per_page_6_and_over(){
  return get_option_posts_per_page() >= 6;
}
endif;

//全ての公開されている投稿の数
if ( !function_exists( 'get_all_post_count_in_publish' ) ):
function get_all_post_count_in_publish(){
  global $wpdb;
  return intval($wpdb->get_var("SELECT count(*) FROM $wpdb->posts WHERE post_status = 'publish' AND post_type = 'post'"));
}
endif;

//ウィジェットをトップページのリスト表示中間に掲載するか
if ( !function_exists( 'is_index_middle_widget_visible' ) ):
function is_index_middle_widget_visible($count){
  $display_count_condition = apply_filters('index_middle_display_count_condition', $count == 3);
  $is_visible =
    //3個目の表示のときのみ
    $display_count_condition && //何番目に表示するかの表示条件（デフォルト：3）
    //1ページに表示する最大投稿数が6以上の時
    is_posts_per_page_6_and_over() &&
    //タイル表示じゃないとき
    !is_entry_card_type_tile_card() &&
    //&&//公開記事が6以上の時
    (get_all_post_count_in_publish() >= 6);

  $is_visible = apply_filters('is_index_middle_widget_visible', $is_visible, $count);

  if ($is_visible) {
    return true;
  }
}
endif;

//広告をトップページのリスト表示中間に掲載するか
if ( !function_exists( 'is_index_middle_ad_visible' ) ):
function is_index_middle_ad_visible($count){
  $is_visible =
    //広告表示設定が有効な時
    is_ad_pos_index_middle_visible() &&
    //ページネーションの最終ページでないとき
    !is_pagination_last_page() &&
    is_index_middle_widget_visible($count);

  $is_visible = apply_filters('is_index_middle_ad_visible', $is_visible, $count);

  if ($is_visible) {
    return true;
  }
}
endif;


//PR表記が表示可能かどうか
if ( !function_exists( 'is_pr_labels_visible' ) ):
  function is_pr_labels_visible(){
    //投稿・固定ページ除外
    $tmp_post_ids = get_pr_label_exclude_post_ids();
    $post_ids = explode(',', $tmp_post_ids);
    $post_ids_empty = empty($tmp_post_ids);

    //カテゴリーページ除外
    $category_ids = get_pr_label_exclude_category_ids();
    $category_ids_empty = empty($category_ids);

    //タグページ除外
    $tmp_tag_ids = get_pr_label_exclude_tag_ids();
    $tag_ids = explode(',', $tmp_tag_ids);
    $tag_ids_empty = empty($tmp_tag_ids);

    //PR表記の除外（いずれかがあてはまれば表示しない）
    $is_exclude_ids = (
      //記事の除外
      (!$post_ids_empty && is_single( $post_ids ))//投稿ページの除外
      || (!$post_ids_empty && is_page( $post_ids )) //個別ページの除外
      //カテゴリーの除外
      || (!$category_ids_empty && is_single() && in_category( $category_ids )) //投稿ページの除外
      || (!$category_ids_empty && is_category( $category_ids )) //カテゴリーアーカイブページの除外
      //タグの除外
      || (!$tag_ids_empty && is_single() && has_tag( $tag_ids )) //投稿ページの除外
      || (!$tag_ids_empty && is_tag( $tag_ids )) //カテゴリーアーカイブページの除外
    );

    // _v(is_the_page_pr_labels_visible() && is_singular());
    return !$is_exclude_ids //除外ページでない場合PR表記を表示
      && !is_attachment() //添付ファイルページでない場合
      && (is_pr_label_single_visible() || is_pr_label_page_visible() || is_pr_label_category_page_visible() || is_pr_label_tag_page_visible()) //投稿・固定・カテゴリー・タグページの場合
      && (is_pr_label_small_visible() || is_pr_label_large_visible()) //PR表記小・大が有効の場合
    ;
  }
  endif;


  //PR表記小が表示可能かどうか
  if ( !function_exists( 'is_small_pr_labels_visible' ) ):
  function is_small_pr_labels_visible(){
    return is_pr_labels_visible() //除外ページでないかどうか
      && is_pr_label_small_visible() //PR表記小が有効
      &&
      (
        (is_pr_label_single_visible() && is_single()) //投稿ページ
        || (is_pr_label_page_visible() && is_page()) //固定ページ
        || (is_pr_label_category_page_visible() && is_category()) //カテゴリーページ
        || (is_pr_label_tag_page_visible() && is_tag()) //タグページ
      )
      && is_the_page_pr_labels_visible() //ページで除外していない場合
    ;
  }
  endif;


  //PR表記大が表示可能かどうか
  if ( !function_exists( 'is_large_pr_labels_visible' ) ):
  function is_large_pr_labels_visible(){
    return is_pr_labels_visible() //除外ページでないかどうか
      && is_pr_label_large_visible() //PR表記大が有効
      &&
      (
        (is_pr_label_single_visible() && is_single()) //投稿ページ
        || (is_pr_label_page_visible() && is_page()) //固定ページ
        || (is_pr_label_category_page_visible() && is_category()) //カテゴリーページ
        || (is_pr_label_tag_page_visible() && is_tag()) //タグページ
      )
      && is_the_page_pr_labels_visible() //ページで除外していない場合
    ;

  }
  endif;

