<?php //広告関係の関数

//広告が表示可能かどうか
if ( !function_exists( 'is_ads_visible' ) ):
function is_ads_visible(){
  //$ads_visible = is_all_ads_visible();
  $post_ids = explode(',', get_ad_exclude_post_ids());
  $category_ids = explode(',', get_ad_exclude_category_ids());

  //広告の除外（いずれかがあてはまれば表示しない）
  $is_exclude_ids = (
    //記事の除外
    is_single( $post_ids ) || //投稿ページの除外
    is_page( $post_ids ) ||   //個別ページの除外
    //カテゴリの除外
    (is_single() && in_category( $category_ids ) ) ||//投稿ページの除外
    is_category( $category_ids ) //アーカイブページの除外
  );

  return is_all_ads_visible() &&
    !$is_exclude_ids && //除外ページでない場合広告を表示
    //!is_ads_removed_in_page() && //ページで除外していない場合
    !is_attachment() && //添付ページではない場合
    !is_404() && //404ページではない場合
    !is_search(); //検索結果ページで無い場合
}
endif;

//アドセンスID（data-ad-clientとdata-ad-slot）を取得する
if ( !function_exists( 'get_adsense_ids' ) ):
function get_adsense_ids(){
  $code = get_ad_code();
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
function get_adsense_data_ad_client(){
  $ids = get_adsense_ids();
  if ($ids && isset($ids[DATA_AD_CLIENT])) {
    return $ids[DATA_AD_CLIENT];
  }
}
endif;

//アドセンスのdata-ad-slotを取得する
if ( !function_exists( 'get_adsense_data_ad_slot' ) ):
function get_adsense_data_ad_slot(){
  $ids = get_adsense_ids();
  if ($ids && isset($ids[DATA_AD_SLOT])) {
    return $ids[DATA_AD_SLOT];
  }
}
endif;

//アドセンスのレスポンシブコードを生成する
if ( !function_exists( 'generate_adsense_responsive_code' ) ):
function generate_adsense_responsive_code($format = DATA_AD_FORMAT_AUTO){
  if (get_adsense_ids()) {
    return
'<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
<!-- レスポンシブコード -->
<ins class="adsbygoogle"
     style="display:block"
     data-ad-client="'.get_adsense_data_ad_client().'"
     data-ad-slot="'.get_adsense_data_ad_slot().'"
     data-ad-format="'.$format.'"></ins>
<script>
(adsbygoogle = window.adsbygoogle || []).push({});
</script>';
  }
}
endif;

//H2見出しを判別する正規表現を定数にする
define('H2_REG', '/<h2.*?>/i');//H2見出しのパターン

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
if ( !function_exists( 'add_ads_before_1st_h2' ) ):
function add_ads_before_1st_h2($the_content) {
  // if ( is_amp() ) {
  //   return $the_content;
  // }
  if ( is_singular() && //投稿日・固定ページのとき
       is_ad_pos_content_middle_visible() //設定で表示が許可されているとき
  ){
    //広告（AdSense）タグを記入
    ob_start();//バッファリング
    //レスポンシブ広告のフォーマットにrectangleを指定する
    get_template_part_with_ad_format(DATA_AD_FORMAT_RECTANGLE, 'ad-content-middle');
    $ad_template = ob_get_clean();
    $h2result = get_h2_included_in_body( $the_content );//本文にH2タグが含まれていれば取得
    if ( $h2result ) {//H2見出しが本文中にある場合のみ
      //最初のH2の手前に広告を挿入（最初のH2を置換）
      $count = 1;
      $the_content = preg_replace(H2_REG, $ad_template.$h2result, $the_content, 1);
    }
  }
  return $the_content;
}
endif;
add_filter('the_content', 'add_ads_before_1st_h2');