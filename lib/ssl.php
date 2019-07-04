<?php //SSL関係の処理
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

//投稿内容をSSL対応する
if ( !function_exists( 'chagne_http_to_https' ) ):
function chagne_site_url_html_to_https($the_content){
  //httpとhttpsURLの取得
  if (strpos(site_url(), 'https://') !== false) {
    $http_url = str_replace('https://', 'http://', site_url());
    $https_url = site_url();
  } else {
    $http_url = site_url();
    $https_url = str_replace('http://', 'https://', site_url());
  }
  //投稿本文の内部リンクを置換
  $the_content = str_replace($http_url, $https_url, $the_content);

  //AmazonアソシエイトのSSL対応
  $search  = 'http://ecx.images-amazon.com';
  $replace = 'https://images-fe.ssl-images-amazon.com';
  $the_content = str_replace($search, $replace, $the_content);
  $search  = 'http://ir-jp.amazon-adsystem.com';
  $replace = 'https://ir-jp.amazon-adsystem.com';
  $the_content = str_replace($search, $replace, $the_content);


  //バリューコマースのSSL対応
  $search  = 'http://ck.jp.ap.valuecommerce.com';
  $replace = 'https://ck.jp.ap.valuecommerce.com';
  $the_content = str_replace($search, $replace, $the_content);
  $search  = 'http://ad.jp.ap.valuecommerce.com';
  $replace = 'https://ad.jp.ap.valuecommerce.com';
  $the_content = str_replace($search, $replace, $the_content);

  //もしもアフィリエイトのSSL対応
  $search  = 'http://c.af.moshimo.com';
  $replace = 'https://af.moshimo.com';
  $the_content = str_replace($search, $replace, $the_content);
  $search  = 'http://i.af.moshimo.com';
  $replace = 'https://i.moshimo.com';
  $the_content = str_replace($search, $replace, $the_content);
  $search  = 'http://image.moshimo.com';
  $replace = 'https://image.moshimo.com';
  $the_content = str_replace($search, $replace, $the_content);

  //A8.netのSSL対応
  $search  = 'http://px.a8.net';
  $replace = 'https://px.a8.net';
  $the_content = str_replace($search, $replace, $the_content);
  // $search  = 'http://www14.a8.net/0.gif';
  // $replace = 'https://www14.a8.net/0.gif';
  // $the_content = str_replace($search, $replace, $the_content);
  // $search  = 'http://www16.a8.net/0.gif';
  // $replace = 'https://www16.a8.net/0.gif';
  // $the_content = str_replace($search, $replace, $the_content);
  $search  = '{http://www(\d+).a8.net/0.gif}';
  $replace = "https://www$1.a8.net/0.gif";
  $the_content = preg_replace($search, $replace, $the_content);

  //アクセストレードのSSL対応
  $search  = 'http://h.accesstrade.net';
  $replace = 'https://h.accesstrade.net';
  $the_content = str_replace($search, $replace, $the_content);

  //はてなブログカードのSSL対応
  $search  = 'http://hatenablog.com/embed?url=';
  $replace = 'https://hatenablog-parts.com/embed?url=';
  $the_content = str_replace($search, $replace, $the_content);

  //はてブ数画像のSSL対応
  $search  = 'http://b.hatena.ne.jp/entry/image/';
  $replace = 'https://b.hatena.ne.jp/entry/image/';
  $the_content = str_replace($search, $replace, $the_content);

  //楽天商品画像のSSL対応
  $search  = 'http://hbb.afl.rakuten.co.jp';
  $replace = 'https://hbb.afl.rakuten.co.jp';
  $the_content = str_replace($search, $replace, $the_content);

  //リンクシェアのSSL対応
  $search  = 'http://ad.linksynergy.com';
  $replace = 'https://ad.linksynergy.com';
  $the_content = str_replace($search, $replace, $the_content);

  //Google検索ボックスのSSL対応
  $search  = 'http://www.google.co.jp/cse';
  $replace = 'https://www.google.co.jp/cse';
  $the_content = str_replace($search, $replace, $the_content);
  $search  = 'http://www.google.co.jp/coop/cse/brand';
  $replace = 'https://www.google.co.jp/coop/cse/brand';
  $the_content = str_replace($search, $replace, $the_content);

  //ここに新しい置換条件を追加していく

  // //のSSL対応
  // $search  = '';
  // $replace = '';
  // $the_content = str_replace($search, $replace, $the_content);

  return $the_content;
}
endif;
if (is_easy_ssl_enable()) {
  add_filter('the_content', 'chagne_site_url_html_to_https', 1);
  add_filter('widget_text', 'chagne_site_url_html_to_https', 1);
  add_filter('widget_text_pc_text', 'chagne_site_url_html_to_https', 1);
  //add_filter('widget_classic_text', 'chagne_site_url_html_to_https', 1);
  add_filter('widget_text_mobile_text', 'chagne_site_url_html_to_https', 1);
  add_filter('comment_text', 'chagne_site_url_html_to_https', 1);
  add_filter('the_category_tag_content', 'chagne_site_url_html_to_https', 1);
}
