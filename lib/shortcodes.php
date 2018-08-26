<?php //ショートコード
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */

//プロフィールショートコード関数
add_shortcode('author_box', 'author_box_shortcode');
if ( !function_exists( 'author_box_shortcode' ) ):
function author_box_shortcode($atts) {
  extract(shortcode_atts(array(
    'label' => '',
  ), $atts));
  ob_start();
  generate_author_box_tag($label);
  $res = ob_get_clean();
  return $res;
}
endif;

//新着記事ショートコード関数
add_shortcode('new_list', 'new_entries_shortcode');
if ( !function_exists( 'new_entries_shortcode' ) ):
function new_entries_shortcode($atts) {
  extract(shortcode_atts(array(
    'count' => 5,
    'cats' => 'all',
    'type' => 'default',
    'children' => 0,
    'post_type' => 'post',
    'taxonomy' => 'category',
  ), $atts));
  $categories = array();
  //var_dump($cats);
  if ($cats && $cats != 'all') {
    $categories = explode(',', $cats);
  }
  ob_start();
  generate_new_entries_tag($count, $type, $categories, $children, $post_type, $taxonomy);
  $res = ob_get_clean();
  return $res;
}
endif;

//人気記事ショートコード関数
add_shortcode('popular_list', 'popular_entries_shortcode');
if ( !function_exists( 'popular_entries_shortcode' ) ):
function popular_entries_shortcode($atts) {
  extract(shortcode_atts(array(
    'days' => 'all',
    'count' => 5,
    'type' => 'default',
    'rank' => 0,
    'pv' => 0,
    'cats' => 'all',
  ), $atts));
  $categories = array();
  if ($cats && $cats != 'all') {
    $categories = explode(',', $cats);
  }
  ob_start();
  generate_popular_entries_tag($days, $count, $type, $rank, $pv, $categories);
  $res = ob_get_clean();
  return $res;
}
endif;

define('AFFI_SHORTCODE', 'affi');
//アフィリエイトタグショートコード関数
add_shortcode(AFFI_SHORTCODE, 'affiliate_tag_shortcode');
if ( !function_exists( 'affiliate_tag_shortcode' ) ):
function affiliate_tag_shortcode($atts) {
  extract(shortcode_atts(array(
    'id' => 0,
  ), $atts));
  if ($id) {
    if ($recode = get_affiliate_tag($id)) {

      global $post;
      $atag = $recode->text;

      //無限ループ要素の除去
      // $shortcode = get_affiliate_tag_shortcode($id);
      // $atag = str_replace($shortcode, '', $atag);
      $atag = preg_replace('{\['.AFFI_SHORTCODE.'[^\]]*?id=[\'"]?'.$id.'[\'"]?[^\]]*?\]}i', '', $atag);

      $post_id = null;
      if (isset($post->ID)) {
        $post_id = 'data-post-id="'.$post->ID.'" ';
      }
      //計測用の属性付与
      $atag = str_replace('<a ', '<a data-atag-id="'.$id.'" '.$post_id, $atag);

      return do_shortcode($atag);
    }
  }

}
endif;
//アフィリエイトショートコードの生成
if ( !function_exists( 'get_affiliate_tag_shortcode' ) ):
function get_affiliate_tag_shortcode($id) {
  return "[".AFFI_SHORTCODE." id={$id}]";
}
endif;

define('TEMPLATE_SHORTCODE', 'temp');
//関数テキストショートコード関数
add_shortcode(TEMPLATE_SHORTCODE, 'function_text_shortcode');
if ( !function_exists( 'function_text_shortcode' ) ):
function function_text_shortcode($atts) {
  extract(shortcode_atts(array(
    'id' => 0,
  ), $atts));
  if ($id) {
    if ($recode = get_function_text($id)) {
      //無限ループ要素の除去
      //$shortcode = get_function_text_shortcode($id);
      $template = preg_replace('{\['.TEMPLATE_SHORTCODE.'[^\]]*?id=[\'"]?'.$id.'[\'"]?[^\]]*?\]}i', '', $recode->text);

      return do_shortcode($template);
    }
  }
}
endif;

//テンプレートショートコードの取得
if ( !function_exists( 'get_function_text_shortcode' ) ):
function get_function_text_shortcode($id) {
  return "[".TEMPLATE_SHORTCODE." id={$id}]";
}
endif;

define('RANKING_SHORTCODE', 'rank');
//ランキングショートコード関数
add_shortcode(RANKING_SHORTCODE, 'item_ranking_shortcode');
if ( !function_exists( 'item_ranking_shortcode' ) ):
function item_ranking_shortcode($atts) {
  extract(shortcode_atts(array(
    'id' => 0,
  ), $atts));
  if ($id) {
    // //無限ループ回避
    // if ($recode->id == $id) return;

    ob_start();
    generate_item_ranking_tag($id);
    return ob_get_clean();
  }

}
endif;

//ショートコードの取得
if ( !function_exists( 'get_item_ranking_shortcode' ) ):
function get_item_ranking_shortcode($id) {
  return "[".RANKING_SHORTCODE." id={$id}]";
}
endif;

//ログインユーザーのみに表示するコンテンツ
add_shortcode('login_user_only', 'login_user_only_shortcode');
if ( !function_exists( 'login_user_only_shortcode' ) ):
function login_user_only_shortcode( $atts, $content = null ) {
  extract( shortcode_atts( array(
      'msg' => __( 'こちらのコンテンツはログインユーザーのみに表示されます。', THEME_NAME ),
  ), $atts ) );
  if (is_user_logged_in()) {
    return do_shortcode($content);
  } else {
    return '<div class="login-user-only">'.htmlspecialchars_decode($msg).'</div>';
  }
}
endif;

if ( !function_exists( 'get_http_content' ) ):
function get_http_content($url){
  try {
    $ch = curl_init();
    curl_setopt_array($ch, array(
      CURLOPT_URL => $url,
      CURLOPT_RETURNTRANSFER => true,
    ));
    $body = curl_exec($ch);
    $errno = curl_errno($ch);
    $error = curl_error($ch);
    curl_close($ch);
    if (CURLE_OK !== $errno) {
      throw new RuntimeException($error, $errno);
    }
    return $body;
  } catch (Exception $e) {
    return false;
    //echo $e->getMessage();
  }
}
endif;

//Amazon商品紹介リンクの外枠で囲む
if ( !function_exists( 'wrap_product_item_box' ) ):
function wrap_product_item_box($message, $type = 'amazon'){
  return '<div class="product-item-box '.$type.'-item-box no-icon product-item-error cf"><div>'.$message.'</div></div>';
}
endif;

// //Amazon商品リンクボタンを表示するか
// if ( !function_exists( 'is_amazon_box_buttons_visible' ) ):
// function is_amazon_box_buttons_visible(){
//   return trim()
// }
// endif;

//シンプルなアソシエイトURLの作成
if ( !function_exists( 'get_amazon_associate_url' ) ):
function get_amazon_associate_url($asin, $associate_tracking_id = null){
  $base_url = 'https://'.__( 'www.amazon.co.jp', THEME_NAME ).'/exec/obidos/ASIN';
  $associate_url = $base_url.'/'.$asin.'/';
  if (!empty($associate_tracking_id)) {
    $associate_url .= $associate_tracking_id.'/';
  }
  $associate_url = esc_url($associate_url);
  return $associate_url;
}
endif;

//Amazon検索用のURLを生成する
if ( !function_exists( 'get_amazon_search_url' ) ):
function get_amazon_search_url($keyword, $associate_tracking_id = null){
  $res = 'https://'.__( 'www.amazon.co.jp', THEME_NAME ).'/gp/search?keywords='.urlencode($keyword);
  if ($associate_tracking_id) {
    $res .= '&tag='.$associate_tracking_id;
  }
  return $res;
}
endif;

//もしもアフィリエイトでAmazon検索用のURLを生成する
if ( !function_exists( 'get_moshimo_amazon_search_url' ) ):
function get_moshimo_amazon_search_url($keyword, $moshimo_amazon_id){
  return 'https://af.moshimo.com/af/c/click?a_id='.$moshimo_amazon_id.'&p_id=170&pc_id=185&pl_id=4062&url='.urlencode(get_amazon_search_url($keyword));
}
endif;

//楽天アフィリエイト検索用のURL生成
if ( !function_exists( 'get_rakuten_affiliate_search_url' ) ):
function get_rakuten_affiliate_search_url($keyword, $rakuten_affiliate_id){
  return 'https://hb.afl.rakuten.co.jp/hgc/'.$rakuten_affiliate_id.'/?pc=https%3A%2F%2Fsearch.rakuten.co.jp%2Fsearch%2Fmall%2F'.urlencode($keyword).'%2F-%2Ff.1-p.1-s.1-sf.0-st.A-v.2%3Fx%3D0%26scid%3Daf_ich_link_urltxt%26m%3Dhttp%3A%2F%2Fm.rakuten.co.jp%2F';;
}
endif;

//楽天検索用のURL生成
if ( !function_exists( 'get_rakuten_search_url' ) ):
function get_rakuten_search_url($keyword){
  return 'https://search.rakuten.co.jp/search/mall/'.urlencode($keyword).'/';
}
endif;

//もしもアフィリエイトの楽天検索用のURL生成
if ( !function_exists( 'get_moshimo_rakuten_search_url' ) ):
function get_moshimo_rakuten_search_url($keyword, $moshimo_rakuten_id){
  return 'https://af.moshimo.com/af/c/click?a_id='.$moshimo_rakuten_id.'&p_id=54&pc_id=54&pl_id=616&url='.urlencode(get_rakuten_search_url($keyword));
}
endif;

//バリューコマースのYahoo!検索用のURL生成
if ( !function_exists( 'get_valucomace_yahoo_search_url' ) ):
function get_valucomace_yahoo_search_url($keyword, $sid, $pid){
  return 'https://ck.jp.ap.valuecommerce.com/servlet/referral?sid='.$sid.'&pid='.$pid.'&vc_url=http%3A%2F%2Fsearch.shopping.yahoo.co.jp%2Fsearch%3Fp%3D'.urlencode($keyword);
}
endif;

//Yahoo!ショッピング検索用のURL生成
if ( !function_exists( 'get_yahoo_search_url' ) ):
function get_yahoo_search_url($keyword){
  return 'https://search.shopping.yahoo.co.jp/search?p='.urlencode($keyword);
}
endif;

//もしもアフィリエイトのYahoo!ショッピング検索用のURL生成
if ( !function_exists( 'get_moshimo_yahoo_search_url' ) ):
function get_moshimo_yahoo_search_url($keyword, $moshimo_yahoo_id){
  return 'https://af.moshimo.com/af/c/click?a_id='.$moshimo_yahoo_id.'&p_id=1225&pc_id=1925&pl_id=18502&url='.urlencode(get_yahoo_search_url($keyword));
}
endif;

if ( !function_exists( 'get_long_str_to_md5_hash' ) ):
function get_long_str_to_md5_hash($str, $length = 50){
  if (strlen($str) > $length) {
    $str = md5($str);
  }
  return $str;
}
endif;

//Amazon APIキャッシュIDの取得
if ( !function_exists( 'get_amazon_api_transient_id' ) ):
function get_amazon_api_transient_id($asin){
  return TRANSIENT_AMAZON_API_PREFIX.get_long_str_to_md5_hash($asin);
}
endif;

//Amazon APIバックアップキャッシュIDの取得
if ( !function_exists( 'get_amazon_api_transient_bk_id' ) ):
function get_amazon_api_transient_bk_id($asin){
  return TRANSIENT_BACKUP_AMAZON_API_PREFIX.get_long_str_to_md5_hash($asin);
}
endif;

//楽天APIキャッシュIDの取得
if ( !function_exists( 'get_rakuten_api_transient_id' ) ):
function get_rakuten_api_transient_id($id){
  return TRANSIENT_RAKUTEN_API_PREFIX.get_long_str_to_md5_hash($id);
}
endif;

//楽天APIバックアップキャッシュIDの取得
if ( !function_exists( 'get_rakuten_api_transient_bk_id' ) ):
function get_rakuten_api_transient_bk_id($id){
  return TRANSIENT_BACKUP_RAKUTEN_API_PREFIX.get_long_str_to_md5_hash($id);
}
endif;

if ( !function_exists( 'get_amazon_itemlookup_xml' ) ):
function get_amazon_itemlookup_xml($asin){
  //アクセスキー
  $access_key_id = trim(get_amazon_api_access_key_id());
  //シークレットキー
  $secret_access_key = trim(get_amazon_api_secret_key());
  //アソシエイトタグ
  $associate_tracking_id = trim(get_amazon_associate_tracking_id());
  //キャッシュ更新間隔
  $days = intval(get_api_cache_retention_period());
  //_v($access_key_id);

  //キャッシュの存在
  $transient_id = get_amazon_api_transient_id($asin);
  $transient_bk_id = get_amazon_api_transient_bk_id($asin);
  $xml_cache = get_transient( $transient_id );
  //_v($xml_cache);
  if ($xml_cache) {
    //_v($xml_cache);
    return $xml_cache;
  }

  //APIエンドポイントURL
  $endpoint = 'https://ecs.amazonaws.jp/onca/xml';

  // パラメータ
  $params = array(
    //共通↓
    'Service' => 'AWSECommerceService',
    'AWSAccessKeyId' => $access_key_id,
    'AssociateTag' => $associate_tracking_id,
    //リクエストにより変更↓
    'Operation' => 'ItemLookup',
    'ItemId' => $asin,
    'ResponseGroup' => 'ItemAttributes,Images',
    //署名用タイムスタンプ
    'Timestamp' => gmdate('Y-m-d\TH:i:s\Z'),
  );

  //パラメータと値のペアをバイト順？で並べかえ。
  ksort($params);


  // エンドポイントを指定します。
  $endpoint = __( 'webservices.amazon.co.jp', THEME_NAME );

  $uri = '/onca/xml';

  $pairs = array();

  // パラメータを key=value の形式に編集します。
  // 同時にURLエンコードを行います。
  foreach ($params as $key => $value) {
    array_push($pairs, rawurlencode($key)."=".rawurlencode($value));
  }

  // パラメータを&で連結します。
  $canonical_query_string = join("&", $pairs);

  // 署名に必要な文字列を先頭に追加します。
  $string_to_sign = "GET\n".$endpoint."\n".$uri."\n".$canonical_query_string;

  // RFC2104準拠のHMAC-SHA256ハッシュアルゴリズムの計算を行います。
  // これがSignatureの値になります。
  $signature = base64_encode(hash_hmac("sha256", $string_to_sign, $secret_access_key, true));

  // Siginatureの値のURLエンコードを行い、リクエストの最後に追加します。
  $request_url = 'https://'.$endpoint.$uri.'?'.$canonical_query_string.'&Signature='.rawurlencode($signature);

  $res = get_http_content($request_url);
  //var_dump($res);

  //_v($res);
  if ($res) {
    //xml取得
    $xml = simplexml_load_string($res);
    if (property_exists($xml->Error, 'Code')) {
      //バックアップキャッシュの確認
      $xml_cache = get_transient( $transient_bk_id );
      if ($xml_cache) {
        return $xml_cache;
      }
      return $res;
    }
    //キャッシュ更新間隔（randで次回の同時読み込みを防ぐ）
    $expiration = DAY_IN_SECONDS * $days + (rand(0, 60) * 60);
    //Amazon APIキャッシュの保存
    set_transient($transient_id, $res, $expiration);
    //Amazon APIバックアップキャッシュの保存
    set_transient($transient_bk_id, $res, $expiration * 2);

    return $res;
  }
  return false;
}
endif;

//検索ボタンの作成
if ( !function_exists( 'get_search_buttons_tag' ) ):
function get_search_buttons_tag($keyword, $associate_tracking_id, $rakuten_affiliate_id, $sid, $pid, $moshimo_amazon_id, $moshimo_rakuten_id, $moshimo_yahoo_id, $amazon, $rakuten, $yahoo){
  $buttons_tag = null;
  if ($keyword) {
    //Amazonボタンの取得
    $amazon_btn_tag = null;
    if (is_amazon_search_button_visible() && $amazon) {
      //$amazon_url = 'https://'.__( 'www.amazon.co.jp', THEME_NAME ).'/gp/search?keywords='.urlencode($keyword).'&tag='.$associate_tracking_id;
      //もしもアフィリエイトIDがある場合
      $amazon_url = get_amazon_search_url($keyword, $associate_tracking_id);
      if ($moshimo_amazon_id && is_moshimo_affiliate_link_enable()) {
        $amazon_url = get_moshimo_amazon_search_url($keyword, $moshimo_amazon_id);
      }
      $amazon_btn_tag =
        '<div class="shoplinkamazon">'.
          '<a href="'.$amazon_url.'" target="_blank" rel="nofollow">'.get_amazon_search_button_text().'</a>'.
        '</div>';
    }

    //楽天ボタンの取得
    $rakuten_btn_tag = null;
    $is_moshimo_rakuten = $moshimo_rakuten_id && is_moshimo_affiliate_link_enable();
    if (($rakuten_affiliate_id || $is_moshimo_rakuten) && is_rakuten_search_button_visible() && $rakuten) {
      //$rakuten_url = 'https://hb.afl.rakuten.co.jp/hgc/'.$rakuten_affiliate_id.'/?pc=https%3A%2F%2Fsearch.rakuten.co.jp%2Fsearch%2Fmall%2F'.urlencode($keyword).'%2F-%2Ff.1-p.1-s.1-sf.0-st.A-v.2%3Fx%3D0%26scid%3Daf_ich_link_urltxt%26m%3Dhttp%3A%2F%2Fm.rakuten.co.jp%2F';
      $rakuten_url = get_rakuten_affiliate_search_url($keyword, $rakuten_affiliate_id);
      //もしもアフィリエイトIDがある場合
      if ($is_moshimo_rakuten) {
        $rakuten_url = get_moshimo_rakuten_search_url($keyword, $moshimo_rakuten_id);
      }
      $rakuten_btn_tag =
        '<div class="shoplinkrakuten">'.
          '<a href="'.$rakuten_url.'" target="_blank" rel="nofollow">'.get_rakuten_search_button_text().'</a>'.
        '</div>';
    }
    //Yahoo!ボタンの取得
    $yahoo_tag = null;
    $is_moshimo_yahoo = $moshimo_yahoo_id && is_moshimo_affiliate_link_enable();
    if ((($sid && $pid) || $is_moshimo_yahoo) && is_yahoo_search_button_visible() && $yahoo) {
      //$yahoo_url = 'https://ck.jp.ap.valuecommerce.com/servlet/referral?sid='.$sid.'&pid='.$pid.'&vc_url=http%3A%2F%2Fsearch.shopping.yahoo.co.jp%2Fsearch%3Fp%3D'.$keyword;
      $yahoo_url = get_valucomace_yahoo_search_url($keyword, $sid, $pid);
      //もしもアフィリエイトIDがある場合
      if ($is_moshimo_yahoo) {
        $yahoo_url = get_moshimo_yahoo_search_url($keyword, $moshimo_yahoo_id);
      }
      $yahoo_tag =
        '<div class="shoplinkyahoo">'.
          '<a href="'.$yahoo_url.'" target="_blank" rel="nofollow">'.get_yahoo_search_button_text().'</a>'.
        '</div>';
    }
    //ボタンコンテナ
    $buttons_tag =
      '<div class="amazon-item-buttons product-item-buttons">'.
        $amazon_btn_tag.
        $rakuten_btn_tag.
        $yahoo_tag.
      '</div>';
  }
  return $buttons_tag;
}
endif;

//キャッシュの削除リンク作成
if ( !function_exists( 'get_cache_delete_tag' ) ):
function get_cache_delete_tag($mode = 'amazon', $id){
  switch ($mode) {
    case 'rakuten':
    $url = add_query_arg(array('page' => 'theme-cache', 'cache' => 'rakuten_id_cache', 'id' => $id), admin_url().'admin.php');
      break;
    default:
      $url = add_query_arg(array('page' => 'theme-cache', 'cache' => 'amazon_asin_cache', 'asin' => $id), admin_url().'admin.php');
      break;
  }
  $cache_delete_tag = null;
  if (is_user_administrator()) {
    $cache_delete_tag = '<a href="'.$url.'" class="cache-delete-link" target="_blank" rel="nofollow"'.ONCLICK_DELETE_CONFIRM.'>'.__( 'キャッシュ削除', THEME_NAME ).'</a>';
  }
  return $cache_delete_tag;
}
endif;

//商品リンク説明文タグ
if ( !function_exists( 'get_item_price_tag' ) ):
function get_item_price_tag($price, $date = null){
  $item_price_tag = null;
  if ($price) {
    $date_tag = null;
    if ($date) {
      $date_tag = '<span class="acquired-date">'.__( '（', THEME_NAME ).esc_html($date).__( '時点', THEME_NAME ).__( '）', THEME_NAME ).'</span>';
    }
    $item_price_tag = '<div class="product-item-price">'.
      '<span class="item-price">'.esc_html($price).'</span>'.
      $date_tag.
    '</div>';
  }
  return $item_price_tag;
}
endif;

//商品リンク説明文タグ
if ( !function_exists( 'get_item_description_tag' ) ):
function get_item_description_tag($description){
  $description_tag = null;
  if ($description) {
    $description_tag = '<div class="product-item-description">'.esc_html($description).'</div>';
  }
  return $description_tag;
}
endif;

//管理者情報タグ
if ( !function_exists( 'get_product_item_admin_tag' ) ):
function get_product_item_admin_tag($cache_delete_tag, $affiliate_rate_tag = null){
  $tag = null;
  if (is_user_administrator()) {
    $tag = '<div class="product-item-admin">'.
              $cache_delete_tag.
              $affiliate_rate_tag.
            '</div>';
  }
  return $tag;
}
endif;

//Amazon商品リンク作成
add_shortcode('amazon', 'generate_amazon_product_link');
if ( !function_exists( 'generate_amazon_product_link' ) ):
function generate_amazon_product_link($atts){
  extract( shortcode_atts( array(
    'asin' => null,
    'id' => null,
    //'search ' => null,
    'kw' => null,
    'title' => null,
    'desc' => null,
    'price' => null,
    'size' => 'm',
    'amazon' => 1,
    'rakuten' => 1,
    'yahoo' => 1,
  ), $atts ) );

  $asin = sanitize_shortcode_value($asin);

  //ASINが取得できない場合はID
  if (empty($asin)) {
    $asin = sanitize_shortcode_value($id);
  }
  //キーワード
  $keyword = sanitize_shortcode_value($kw);
  $description = sanitize_shortcode_value($desc);

  //アクセスキー
  $access_key_id = trim(get_amazon_api_access_key_id());
  //シークレットキー
  $secret_access_key = trim(get_amazon_api_secret_key());
  //アソシエイトタグ
  $associate_tracking_id = trim(get_amazon_associate_tracking_id());
  //楽天アフィリエイトID
  $rakuten_affiliate_id = trim(get_rakuten_affiliate_id());
  //Yahoo!バリューコマースSID
  $sid = trim(get_yahoo_valuecommerce_sid());
  //Yahoo!バリューコマースPID
  $pid = trim(get_yahoo_valuecommerce_pid());

  // $moshimo_amazon_id = null;
  // $moshimo_rakuten_id = null;
  // $moshimo_yahoo_id = null;

  //もしもID
  $moshimo_amazon_id  = trim(get_moshimo_amazon_id());
  $moshimo_rakuten_id = trim(get_moshimo_rakuten_id());
  $moshimo_yahoo_id   = trim(get_moshimo_yahoo_id());

  //アクセスキーもしくはシークレットキーがない場合
  if (empty($access_key_id) || empty($secret_access_key)) {
    $error_message = __( 'Amazon APIのアクセスキーもしくはシークレットキーが設定されていません。「Cocoon設定」の「API」タブから入力してください。', THEME_NAME );
    return wrap_product_item_box($error_message);
  }

  //ASINがない場合
  if (empty($asin)) {
    $error_message = __( 'Amazon商品リンクショートコード内にASINが入力されていません。', THEME_NAME );
    return wrap_product_item_box($error_message);
  }

  //アソシエイトurlの取得
  $associate_url = get_amazon_associate_url($asin, $associate_tracking_id);


  $res = get_amazon_itemlookup_xml($asin);
  if ($res) {
    // xml取得
    $xml = simplexml_load_string($res);
    //_v($xml);

    if (property_exists($xml->Error, 'Code')) {
      $error_message = '<a href="'.$associate_url.'" target="_blank">'.__( 'Amazonで詳細を見る', THEME_NAME ).'</a>';

      if (is_user_administrator()) {
        $admin_message = '<b>'.__( '管理者用エラーメッセージ', THEME_NAME ).'</b><br>';
        $admin_message .= __( 'アイテムを取得できませんでした。', THEME_NAME ).'<br>';
        $admin_message .= '<pre class="nohighlight"><b>'.$xml->Error->Code.'</b><br>'.preg_replace('/AWS Access Key ID: .+?\. /', '', $xml->Error->Message).'</pre>';
        $admin_message .= '<span class="red">'.__( 'このエラーメッセージは"サイト管理者のみ"に表示されています。少し時間おいてリロードしてください。それでも改善されない場合は、以下の不具合フォーラムにエラーメッセージとともにご連絡ください。', THEME_NAME ).'</span><br><a href="" target="_blank">'.__( '不具合報告フォーラム', THEME_NAME ).'</a>';
        $error_message .= '<br><br>'.get_message_box_tag($admin_message, 'warning-box fz-14px');
      }
      return wrap_product_item_box($error_message);
    }

    //var_dump($item);

    if (!property_exists($xml->Items, 'Item')) {
      $error_message = __( '商品を取得できませんでした。存在しないASINを指定している可能性があります。', THEME_NAME );
      return wrap_product_item_box($error_message);
    }

    if (property_exists($xml->Items, 'Item')) {
      $item = $xml->Items->Item;

      //_v($xml);

      //var_dump($xml->Items->Errors);
      // _v($item);
      $ASIN = esc_html($item->ASIN);

      ///////////////////////////////////////
      // アマゾンURL
      ///////////////////////////////////////
      $moshimo_amazon_base_url = 'https://af.moshimo.com/af/c/click?a_id='.$moshimo_amazon_id.'&p_id=170&pc_id=185&pl_id=4062&url=';
      $DetailPageURL = esc_url($item->DetailPageURL);
      if ($DetailPageURL) {
        $associate_url = $DetailPageURL;
      }
      if ($moshimo_amazon_id && is_moshimo_affiliate_link_enable()) {
        $associate_url = $moshimo_amazon_base_url.urlencode(get_amazon_associate_url($asin));
      }

      $SmallImage = $item->SmallImage;
      $SmallImageUrl = esc_url($SmallImage->URL);
      $SmallImageWidth = esc_html($SmallImage->Width);
      $SmallImageHeight = esc_html($SmallImage->Height);
      $MediumImage = $item->MediumImage;
      $MediumImageUrl = esc_url($MediumImage->URL);
      $MediumImageWidth = esc_html($MediumImage->Width);
      $MediumImageHeight = esc_html($MediumImage->Height);
      $LargeImage = $item->LargeImage;
      $LargeImageUrl = esc_url($LargeImage->URL);
      $LargeImageWidth = esc_html($LargeImage->Width);
      $LargeImageHeight = esc_html($LargeImage->Height);

      //サイズ設定
      $size = strtolower($size);
      switch ($size) {
        case 's':
          $size_class = 'pis-s';
          if ($SmallImageUrl) {
            $ImageUrl = $SmallImageUrl;
            $ImageWidth = $SmallImageWidth;
            $ImageHeight = $SmallImageHeight;
          } else {
            $ImageUrl = 'https://images-fe.ssl-images-amazon.com/images/G/09/nav2/dp/no-image-no-ciu._SL75_.gif';
            $ImageWidth = '75';
            $ImageHeight = '75';
          }
          break;
        case 'l':
          $size_class = 'pis-l';
          if ($LargeImageUrl) {
            $ImageUrl = $LargeImageUrl;
            $ImageWidth = $LargeImageWidth;
            $ImageHeight = $LargeImageHeight;
          } else {
            $ImageUrl = 'https://images-fe.ssl-images-amazon.com/images/G/09/nav2/dp/no-image-no-ciu._SL500_.gif';
            $ImageWidth = '500';
            $ImageHeight = '500';
          }
          break;
        default:
          $size_class = 'pis-m';
          if ($MediumImageUrl) {
            $ImageUrl = $MediumImageUrl;
            $ImageWidth = $MediumImageWidth;
            $ImageHeight = $MediumImageHeight;
          } else {
            $ImageUrl = 'https://images-fe.ssl-images-amazon.com/images/G/09/nav2/dp/no-image-no-ciu._SL160_.gif';
            $ImageWidth = '160';
            $ImageHeight = '160';
          }
          break;
      }

      $ItemAttributes = $item->ItemAttributes;

      ///////////////////////////////////////////
      // 商品リンク出力用の変数設定
      ///////////////////////////////////////////
      if ($title) {
        $Title = $title;
      } else {
        $Title = $ItemAttributes->Title;
      }

      $TitleAttr = esc_attr($Title);
      $TitleHtml = esc_html($Title);

      $ProductGroup = esc_html($ItemAttributes->ProductGroup);
      $ProductGroupClass = strtolower($ProductGroup);
      $ProductGroupClass = str_replace(' ', '-', $ProductGroupClass);
      $Publisher = esc_html($ItemAttributes->Publisher);
      $Manufacturer = esc_html($ItemAttributes->Manufacturer);
      $Binding = esc_html($ItemAttributes->Binding);
      if ($Publisher) {
        $maker = $Publisher;
      } elseif ($Manufacturer) {
        $maker = $Manufacturer;
      } else {
        $maker = $Binding;
      }

      $ListPrice = $item->ItemAttributes->ListPrice;
      $Price = esc_html($ListPrice->Amount);
      $FormattedPrice = esc_html($ListPrice->FormattedPrice);

      //$associate_url = esc_url($base_url.$ASIN.'/'.$associate_tracking_id.'/');

      ///////////////////////////////////////////
      // 値段表記
      ///////////////////////////////////////////
      $item_price_tag = null;
      //XMLのOperationRequesから時間情報を取得
      $unix_date = (string)$xml->OperationRequest->Arguments->Argument[6]->attributes()->Value;
      if ($unix_date) {
        $timestamp = strtotime($unix_date);//UNIX形式の日付文字列をタイムスタンプに変換
        $acquired_date = date(__( 'Y/m/d H:i', THEME_NAME ), $timestamp);//フォーマット変更
        //_v($acquired_date);
        //_v($FormattedPrice);
        if ((is_amazon_item_price_visible() || $price === '1')
             && $ListPrice
             && $price !== '0'
           ) {
          $item_price_tag = get_item_price_tag($FormattedPrice, $acquired_date);
        }
      }


      ///////////////////////////////////////////
      // 説明文タグ
      ///////////////////////////////////////////
      $description_tag = get_item_description_tag($description);

      ///////////////////////////////////////////
      // 検索ボタンの作成
      ///////////////////////////////////////////
      $buttons_tag = get_search_buttons_tag($keyword, $associate_tracking_id, $rakuten_affiliate_id, $sid, $pid, $moshimo_amazon_id, $moshimo_rakuten_id, $moshimo_yahoo_id, $amazon, $rakuten, $yahoo);

      ///////////////////////////////////////////
      // キャッシュ削除リンク
      ///////////////////////////////////////////
      $cache_delete_tag = get_cache_delete_tag('amazon', $asin);

      ///////////////////////////////////////////
      // 管理者情報タグ
      ///////////////////////////////////////////
      $product_item_admin_tag = get_product_item_admin_tag($cache_delete_tag);

      ///////////////////////////////////////////
      // 商品リンクタグの生成
      ///////////////////////////////////////////
      $tag =
        '<div class="amazon-item-box product-item-box no-icon '.$size_class.' '.$ProductGroupClass.' '.$asin.' cf">'.
          '<figure class="amazon-item-thumb product-item-thumb">'.
            '<a href="'.$associate_url.'" class="amazon-item-thumb-link product-item-thumb-link" target="_blank" title="'.$TitleAttr.'" rel="nofollow">'.
              '<img src="'.$ImageUrl.'" alt="'.$TitleAttr.'" width="'.$ImageWidth.'" height="'.$ImageHeight.'" class="amazon-item-thumb-image product-item-thumb-image">'.
            '</a>'.
          '</figure>'.
          '<div class="amazon-item-content product-item-content">'.
            '<div class="amazon-item-title product-item-title">'.
              '<a href="'.$associate_url.'" class="amazon-item-title-link product-item-title-link" target="_blank" title="'.$TitleAttr.'" rel="nofollow">'.
                 $TitleHtml.
              '</a>'.
            '</div>'.
            '<div class="amazon-item-snippet product-item-snippet">'.
              '<div class="amazon-item-maker product-item-maker">'.
                $maker.
              '</div>'.
              $item_price_tag.
              $description_tag.
              $buttons_tag.
            '</div>'.
          '</div>'.
          $product_item_admin_tag.
        '</div>';
    } else {
      $error_message = __( '商品を取得できませんでした。存在しないASINを指定している可能性があります。', THEME_NAME );
      $tag = wrap_product_item_box($error_message);
    }

    return $tag;
  }

}
endif;

if ( !function_exists( 'get_rakuten_image_size' ) ):
function get_rakuten_image_size($url){
  preg_match('{ex=(\d+)x(\d+)}i', $url, $m);
  if ($m[1] && $m[2]) {
    $sizes = array();
    $sizes['width'] =  intval($m[1]);
    $sizes['height'] =  intval($m[2]);
    return $sizes;
  }
}
endif;

//楽天APIで商品情報が取得できなかった際のデフォルトリンク作成
if ( !function_exists( 'get_default_rakuten_link_tag' ) ):
function get_default_rakuten_link_tag($rakuten_affiliate_id, $id, $keyword){
  $search_keyword = $id;
  if ($keyword) {
    $search_keyword = $keyword;
  }
  $rakuten_url = get_rakuten_affiliate_search_url(urlencode($search_keyword), $rakuten_affiliate_id);
  return '<a href="'.$rakuten_url.'" target="_blank">'.__( '楽天で商品を見る', THEME_NAME ).'</a>';
}
endif;

if ( !function_exists( 'get_rakuten_error_message_tag' ) ):
function get_rakuten_error_message_tag($link, $admin_message){
  $error_message = $link;
  if (is_user_administrator()) {
    $error_message .= '<br><br>'.get_message_box_tag($admin_message, 'warning-box fz-14px');
  }
  return wrap_product_item_box($error_message, 'rakuten');
}
endif;

//楽天商品リンク作成
add_shortcode('rakuten', 'generate_rakuten_product_link');
if ( !function_exists( 'generate_rakuten_product_link' ) ):
function generate_rakuten_product_link($atts){
  extract( shortcode_atts( array(
    'id' => null,
    'no' => null,
    'search' => null,
    'shop' => null,
    'kw' => null,
    'title' => null,
    'desc' => null,
    'size' => 'm',
    'amazon' => 1,
    'rakuten' => 1,
    'yahoo' => 1,
    'sort' => null,
  ), $atts ) );

  $id = sanitize_shortcode_value($id);

  if ($no) {
    $search = $no;
  }
  $search = sanitize_shortcode_value($search);

  //キーワード
  $keyword = sanitize_shortcode_value($kw);
  $description = sanitize_shortcode_value($desc);

  $shop = sanitize_shortcode_value($shop);
  $sort = sanitize_shortcode_value($sort);


  //楽天アプリケーションID
  $rakuten_application_id = trim(get_rakuten_application_id());
  //楽天アフィリエイトID
  $rakuten_affiliate_id = trim(get_rakuten_affiliate_id());
  //アソシエイトタグ
  $associate_tracking_id = trim(get_amazon_associate_tracking_id());
  //Yahoo!バリューコマースSID
  $sid = trim(get_yahoo_valuecommerce_sid());
  //Yahoo!バリューコマースPID
  $pid = trim(get_yahoo_valuecommerce_pid());
  //キャッシュ更新間隔
  $days = intval(get_api_cache_retention_period());

  //もしもID
  $moshimo_amazon_id  = trim(get_moshimo_amazon_id());
  $moshimo_rakuten_id = trim(get_moshimo_rakuten_id());
  $moshimo_yahoo_id   = trim(get_moshimo_yahoo_id());



  //楽天アフィリエイトIDがない場合
  if (empty($rakuten_application_id) || empty($rakuten_affiliate_id)) {
    $error_message = __( '「楽天アプリケーションID」もしくは「楽天アフィリエイトID」が設定されていません。「Cocoon設定」の「API」タブから入力してください。', THEME_NAME );
    return wrap_product_item_box($error_message);
  }

  //商品IDがない場合
  if (empty($id) && empty($search)) {
    $error_message = __( 'id, no, searchオークションのいずれかが入力されていません。', THEME_NAME );
    return wrap_product_item_box($error_message);
  }

  $default_rakuten_link_tag = get_default_rakuten_link_tag($rakuten_affiliate_id, $id, $keyword);

  if ($id) {
    $cache_id = $id;
  } else {
    $cache_id = $search.$shop;
  }


  //キャッシュの取得
  $transient_id = get_rakuten_api_transient_id($cache_id);
  $transient_bk_id = get_rakuten_api_transient_bk_id($cache_id);
  $json_cache = get_transient( $transient_id );

  //キャッシュがある場合はキャッシュを利用する
  if ($json_cache) {
    // _v('cahce');
    $json = $json_cache;
  } else {
    // _v('api');
    $itemCode = null;
    if ($id) {
      $itemCode = '&itemCode='.$id;
    }


    $sortQuery = '&sort='.get_rakuten_api_sort();
    if ($sort && !$id) {
      $sortQuery = '&sort='.$sort;
    }
    $sortQuery = str_replace('+', '%2B', $sortQuery);

    $shopCode = null;
    if ($shop && !$id) {
      $shopCode = '&shopCode='.$shop;
    }
    $searchkw = null;
    if ($search && !$id) {
      $searchkw = '&keyword='.$search;
    }
    $request_url = 'https://app.rakuten.co.jp/services/api/IchibaItem/Search/20170706?applicationId='.$rakuten_application_id.'&affiliateId='.$rakuten_affiliate_id.'&imageFlag=1'.$sortQuery.$shopCode.'&hits=1'.$searchkw.$itemCode;
    //_v($request_url);
    $args = array( 'sslverify' => true );
    $json = wp_remote_get( $request_url, $args );

    //ジェイソンのリクエスト結果チェック
    $is_request_success = !is_wp_error( $json ) && $json['response']['code'] === 200;
    //JSON取得に失敗した場合はバックアップキャッシュを取得
    if (!$is_request_success) {
      $json_cache = get_transient( $transient_bk_id );
      if ($json_cache) {
        $json = $json_cache;
        // _v('bk');
        // _v($json);
      }
    }
  }


  if ($json) {
    //ジェイソンのリクエスト結果チェック
    $is_request_success = !is_wp_error( $json ) && $json['response']['code'] === 200;
    //リクエストが成功した時タグを作成する
    if ($is_request_success) {
      $acquired_date = date(__( 'Y/m/d H:i', THEME_NAME ));

      //キャッシュの保存
      if (!$json_cache) {
        //キャッシュ更新間隔（randで次回の同時読み込みを防ぐ）
        $expiration = DAY_IN_SECONDS * $days + (rand(0, 60) * 60);
        $jb = $json['body'];
        if ($jb) {
          $jb = preg_replace('/{/', '{"date":"'.$acquired_date.'",', $jb, 1);
            $json['body'] = $jb;
        }
        //楽天APIキャッシュの保存
        set_transient($transient_id, $json, $expiration);
        //楽天APIバックアップキャッシュの保存
        set_transient($transient_bk_id, $json, $expiration * 2);
      }

      $body = $json["body"];
      //ジェイソンの配列化
      $body = json_decode( $body );
      //IDの商品が見つからなかった場合
      if (intval($body->{'count'}) > 0) {
        if ($body->{'date'}) {
          $acquired_date = $body->{'date'};
        }

        $Item = $body->{'Items'}['0']->{'Item'};
        if ($Item) {
          $itemName = $Item->{'itemName'};
          $itemCode = $Item->{'itemCode'};
          $itemPrice = $Item->{'itemPrice'};
          $itemCaption = esc_html($Item->{'itemCaption'});
          $itemUrl = esc_attr($Item->{'itemUrl'});//affiliateUrlと同じ
          $shopUrl = esc_attr($Item->{'shopUrl'});//shopAffiliateUrlと同じ
          $affiliateUrl = esc_attr($Item->{'affiliateUrl'});//itemUrlと同じ
          $shopAffiliateUrl = esc_attr($Item->{'shopAffiliateUrl'});//shopUrlと同じ
          $shopName = esc_html($Item->{'shopName'});
          $shopCode = $Item->{'shopCode'};
          $affiliateRate = $Item->{'affiliateRate'};


          //小さな画像
          $smallImageUrls = $Item->{'smallImageUrls'};
          $smallImageUrl = $smallImageUrls['0']->{'imageUrl'};
          //画像サイズの取得
          $sizes = get_rakuten_image_size($smallImageUrl);
          if ($sizes) {
            $smallImageWidth = $sizes['width'];
            $smallImageHeight = $sizes['height'];
          } else {
            $smallImageUrl = null;
            $smallImageWidth = null;
            $smallImageHeight = null;
          }

          //標準画像
          $mediumImageUrls = $Item->{'mediumImageUrls'};
          $mediumImageUrl = $mediumImageUrls['0']->{'imageUrl'};
          //画像サイズの取得
          $sizes = get_rakuten_image_size($mediumImageUrl);
          if ($sizes) {
            $mediumImageWidth = $sizes['width'];
            $mediumImageHeight = $sizes['height'];
          } else {
            $mediumImageUrl = null;
            $mediumImageWidth = null;
            $mediumImageHeight = null;
          }

          //サイズ設定
          $size = strtolower($size);
          switch ($size) {
            case 's':
              $size_class = 'pis-s';
              if ($smallImageUrl) {
                $ImageUrl = $smallImageUrl;
                $ImageWidth = $smallImageWidth;
                $ImageHeight = $smallImageHeight;
              } else {
                $ImageUrl = NO_IMAGE_150;
                $ImageWidth = '64';
                $ImageHeight = '64';
              }
              break;
            default:
              $size_class = 'pis-m';
              if ($mediumImageUrl) {
                $ImageUrl = $mediumImageUrl;
                $ImageWidth = $mediumImageWidth;
                $ImageHeight = $mediumImageHeight;
              } else {
                $ImageUrl = NO_IMAGE_150;
                $ImageWidth = '128';
                $ImageHeight = '128';
              }
              break;
            }


          ///////////////////////////////////////////
          // 商品リンク出力用の変数設定
          ///////////////////////////////////////////
          if ($title) {
            $Title = $title;
          } else {
            $Title = $itemName;
          }

          $TitleAttr = esc_attr($Title);
          $TitleHtml = esc_html($Title);

          ///////////////////////////////////////////
          // 検索ボタンの作成
          ///////////////////////////////////////////
          $buttons_tag = get_search_buttons_tag($keyword, $associate_tracking_id, $rakuten_affiliate_id, $sid, $pid, $moshimo_amazon_id, $moshimo_rakuten_id, $moshimo_yahoo_id, $amazon, $rakuten, $yahoo);

          ///////////////////////////////////////////
          // 説明文タグ
          ///////////////////////////////////////////
          $description_tag = get_item_description_tag($description);

          ///////////////////////////////////////////
          // キャッシュ削除リンク
          ///////////////////////////////////////////

          $cache_delete_tag = get_cache_delete_tag('rakuten', $cache_id);

          ///////////////////////////////////////////
          // アフィリエイト料率タグ
          ///////////////////////////////////////////
          $affiliate_rate_tag = null;
          if (is_user_administrator()) {
            $affiliate_rate_tag = '<span class="product-affiliate-rate">'.__('料率：', THEME_NAME).$affiliateRate.'%</span>';
          }

          ///////////////////////////////////////////
          // 管理者情報タグ
          ///////////////////////////////////////////
          $product_item_admin_tag = get_product_item_admin_tag($cache_delete_tag, $affiliate_rate_tag);

          ///////////////////////////////////////////
          // 商品リンクタグの生成
          ///////////////////////////////////////////
          $tag =
            '<div class="rakuten-item-box product-item-box no-icon '.$size_class.' '.$id.' cf">'.
              '<figure class="rakuten-item-thumb product-item-thumb">'.
                '<a href="'.$affiliateUrl.'" class="rakuten-item-thumb-link product-item-thumb-link" target="_blank" title="'.$TitleAttr.'" rel="nofollow">'.
                  '<img src="'.$ImageUrl.'" alt="'.$TitleAttr.'" width="'.$ImageWidth.'" height="'.$ImageHeight.'" class="rakuten-item-thumb-image product-item-thumb-image">'.
                '</a>'.
              '</figure>'.
              '<div class="rakuten-item-content product-item-content">'.
                '<div class="rakuten-item-title product-item-title">'.
                  '<a href="'.$affiliateUrl.'" class="rakuten-item-title-link product-item-title-link" target="_blank" title="'.$TitleAttr.'" rel="nofollow">'.
                    $TitleHtml.
                  '</a>'.
                '</div>'.
                '<div class="rakuten-item-snippet product-item-snippet">'.
                  '<div class="rakuten-item-maker product-item-maker">'.
                    $shopName.
                  '</div>'.
                  $description_tag.
                  $buttons_tag.
                '</div>'.
              '</div>'.
              $product_item_admin_tag.
            '</div>';

          //_v($tag);
          return $tag;
        }
      } else {
        $error_message = __( '商品が見つかりませんでした。', THEME_NAME );
        return get_rakuten_error_message_tag($default_rakuten_link_tag, $error_message);
      }

    } else {

      $ebody = json_decode( $json['body'] );
      $error = $ebody->{'error'};
      $error_description = $ebody->{'error_description'};
      switch ($error) {
        case 'wrong_parameter':
        $error_message = $error_description.':'.__( 'ショートコードの値が正しく記入されていない可能性があります。', THEME_NAME );
          break;
        default:
        $error_message = $error_description.':'.__( 'Bad Requestが返されました。リクエスト制限を受けた可能性があります。しばらく時間を置いたとリロードすると商品リンクが表示される可能性があります。', THEME_NAME );
          break;
      }
      return get_rakuten_error_message_tag($default_rakuten_link_tag, $error_message);

    }
  } else {
    $error_message = __( 'JSONを取得できませんでした。接続環境に問題がある可能性があります。', THEME_NAME );
    return get_rakuten_error_message_tag($default_rakuten_link_tag, $error_message);
  }

}
endif;