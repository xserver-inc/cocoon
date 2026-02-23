<?php //楽天商品リンクブロック REST API & 静的HTML生成
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

///////////////////////////////////////////
// REST APIエンドポイントの登録
///////////////////////////////////////////
add_action('rest_api_init', 'cocoon_rakuten_block_register_routes');
if ( !function_exists( 'cocoon_rakuten_block_register_routes' ) ):
function cocoon_rakuten_block_register_routes(){
  // 商品検索エンドポイント
  register_rest_route('cocoon/v1', '/rakuten/search', array(
    'methods'  => 'POST',
    'callback' => 'cocoon_rakuten_block_search',
    'permission_callback' => function(){ return current_user_can('edit_posts'); },
  ));
  // 商品詳細取得エンドポイント
  register_rest_route('cocoon/v1', '/rakuten/get-item', array(
    'methods'  => 'POST',
    'callback' => 'cocoon_rakuten_block_get_item',
    'permission_callback' => function(){ return current_user_can('edit_posts'); },
  ));
  // 静的HTMLプレビュー生成エンドポイント
  register_rest_route('cocoon/v1', '/rakuten/render-preview', array(
    'methods'  => 'POST',
    'callback' => 'cocoon_rakuten_block_render_preview',
    'permission_callback' => function(){ return current_user_can('edit_posts'); },
  ));
}
endif;

///////////////////////////////////////////
// 商品検索エンドポイントのコールバック
///////////////////////////////////////////
if ( !function_exists( 'cocoon_rakuten_block_search' ) ):
function cocoon_rakuten_block_search($request){
  // リクエストパラメータを取得
  $keyword       = sanitize_text_field($request->get_param('keyword'));
  $page          = (int)$request->get_param('page');
  $purchase_type = (int)$request->get_param('purchaseType');
  if ($page < 1) $page = 1;

  // キーワードが空なら400エラー
  if (empty($keyword)) {
    return new WP_Error('missing_keyword', __('キーワードを入力してください。', THEME_NAME), array('status' => 400));
  }

  // 楽天アプリケーションID・アフィリエイトIDの取得
  $rakuten_application_id = trim(get_rakuten_application_id());
  $rakuten_affiliate_id   = trim(get_rakuten_affiliate_id());

  // IDが未設定の場合はエラー
  if (empty($rakuten_application_id) || empty($rakuten_affiliate_id)) {
    return new WP_Error('missing_config', __('「楽天アプリケーションID」もしくは「楽天アフィリエイトID」が設定されていません。「Cocoon設定」の「API」タブから入力してください。', THEME_NAME), array('status' => 400));
  }

  // ソート順をCocoon設定から取得
  $sort = str_replace('+', '%2B', '&sort='.get_rakuten_api_sort());

  // 購入タイプパラメータの構築
  $purchase_type_query = '';
  if ($purchase_type > 0) {
    $purchase_type_query = '&purchaseType='.$purchase_type;
  }

  // 楽天APIリクエストURLの構築
  $request_url = 'https://app.rakuten.co.jp/services/api/IchibaItem/Search/20220601'
    .'?applicationId='.$rakuten_application_id
    .'&affiliateId='.$rakuten_affiliate_id
    .'&imageFlag=1'
    .$sort
    .'&hits=30'
    .'&page='.$page
    .'&keyword='.urlencode($keyword)
    .$purchase_type_query;

  // APIリクエストの実行
  $args = array( 'sslverify' => true );
  $args = apply_filters('wp_remote_get_rakuten_args', $args);
  $response = wp_remote_get( $request_url, $args );

  // リクエスト失敗のチェック
  if (is_wp_error($response)) {
    return new WP_Error('api_error', __('楽天APIに接続できませんでした。', THEME_NAME), array('status' => 500));
  }
  // HTTPステータスコードを(int)キャストして型の不一致による誤判定を防ぐ
  if ((int)$response['response']['code'] !== 200) {
    // エラーレスポンスの解析
    $ebody = json_decode($response['body']);
    $error_desc = isset($ebody->error_description) ? $ebody->error_description : '';
    return new WP_Error('api_error', $error_desc ?: __('楽天APIからエラーが返されました。', THEME_NAME), array('status' => 400));
  }

  // レスポンスをJSONデコード
  $body = json_decode($response['body']);
  if (!$body) {
    return new WP_Error('parse_error', __('APIレスポンスの解析に失敗しました。', THEME_NAME), array('status' => 500));
  }

  // 検索結果を整形して返却
  $items = array();
  $total = isset($body->count) ? (int)$body->count : 0;

  if (isset($body->Items) && is_array($body->Items)) {
    foreach ($body->Items as $rawItem) {
      // formatVersion=1の場合: Items[n]->Item
      $Item = isset($rawItem->Item) ? $rawItem->Item : $rawItem;
      $items[] = cocoon_rakuten_block_format_search_item($Item);
    }
  }

  return rest_ensure_response(array(
    'success'      => true,
    'items'        => $items,
    'totalResults' => $total,
    'page'         => $page,
  ));
}
endif;

///////////////////////////////////////////
// 検索結果アイテムのフォーマッタ
///////////////////////////////////////////
if ( !function_exists( 'cocoon_rakuten_block_format_search_item' ) ):
function cocoon_rakuten_block_format_search_item($Item){
  // 商品コードの取得
  $itemCode = isset($Item->itemCode) ? $Item->itemCode : '';

  // タイトルの取得
  $title = isset($Item->itemName) ? $Item->itemName : '';

  // ショップ名の取得
  $shopName = isset($Item->shopName) ? $Item->shopName : '';

  // 価格の取得
  $price = '';
  if (isset($Item->itemPrice) && $Item->itemPrice) {
    $price = '￥ '.number_format($Item->itemPrice);
  }

  // 中画像URLの取得
  $imageUrl = '';
  if (isset($Item->mediumImageUrls) && is_array($Item->mediumImageUrls) && isset($Item->mediumImageUrls[0])) {
    $imgObj = $Item->mediumImageUrls[0];
    $imageUrl = isset($imgObj->imageUrl) ? $imgObj->imageUrl : '';
  }

  // アフィリエイトURLの取得
  $affiliateUrl = isset($Item->affiliateUrl) ? $Item->affiliateUrl : '';

  return array(
    'itemCode'     => $itemCode,
    'title'        => $title,
    'shopName'     => $shopName,
    'price'        => $price,
    'imageUrl'     => $imageUrl,
    'affiliateUrl' => $affiliateUrl,
  );
}
endif;

///////////////////////////////////////////
// 商品詳細取得エンドポイントのコールバック
///////////////////////////////////////////
if ( !function_exists( 'cocoon_rakuten_block_get_item' ) ):
function cocoon_rakuten_block_get_item($request){
  $itemCode = sanitize_text_field($request->get_param('itemCode'));
  if (empty($itemCode)) {
    return new WP_Error('missing_item_code', __('商品コードを指定してください。', THEME_NAME), array('status' => 400));
  }

  // 楽天APIで商品を取得
  $Item = cocoon_rakuten_block_fetch_item($itemCode);
  if (is_wp_error($Item)) {
    return $Item;
  }

  // アイテムデータの抽出
  $itemData = cocoon_rakuten_block_extract_item_data($Item);

  return rest_ensure_response(array(
    'success' => true,
    'item'    => $itemData,
  ));
}
endif;

///////////////////////////////////////////
// 楽天APIから商品を1件取得するヘルパー関数
///////////////////////////////////////////
if ( !function_exists( 'cocoon_rakuten_block_fetch_item' ) ):
function cocoon_rakuten_block_fetch_item($itemCode){
  $rakuten_application_id = trim(get_rakuten_application_id());
  $rakuten_affiliate_id   = trim(get_rakuten_affiliate_id());

  if (empty($rakuten_application_id) || empty($rakuten_affiliate_id)) {
    return new WP_Error('missing_config', __('楽天APIの設定が不足しています。', THEME_NAME), array('status' => 400));
  }

  // 楽天APIリクエストURLの構築（商品コードで検索）
  $request_url = 'https://app.rakuten.co.jp/services/api/IchibaItem/Search/20220601'
    .'?applicationId='.$rakuten_application_id
    .'&affiliateId='.$rakuten_affiliate_id
    .'&imageFlag=1'
    .'&hits=1'
  // 商品コードには「shopCode:itemCode」形式でコロンを含む場傐があるため urlencode() でエンコードする
    .'&itemCode='.urlencode($itemCode);

  // キャッシュの取得
  $transient_id = get_rakuten_api_transient_id($itemCode);
  $transient_bk_id = get_rakuten_api_transient_bk_id($itemCode);
  $json_cache = get_transient( $transient_id );

  if ($json_cache && DEBUG_CACHE_ENABLE) {
    $response = $json_cache;
  } else {
    $args = array( 'sslverify' => true );
    $args = apply_filters('wp_remote_get_rakuten_args', $args);
    $response = wp_remote_get( $request_url, $args );
  }

  // ジェイソンのリクエスト結果チェック
  $is_request_success = !is_wp_error( $response ) && isset($response['response']['code']) && $response['response']['code'] === 200;

  // JSON取得に失敗した場合はバックアップキャッシュを取得
  if (!$is_request_success) {
    $json_bk_cache = get_transient( $transient_bk_id );
    if ($json_bk_cache && DEBUG_CACHE_ENABLE) {
      $response = $json_bk_cache;
      $is_request_success = true;
    }
  }

  // リクエスト失敗のチェック
  if (is_wp_error($response)) {
    return new WP_Error('api_error', __('楽天APIに接続できませんでした。', THEME_NAME), array('status' => 500));
  }
  if (!isset($response['response']['code']) || $response['response']['code'] !== 200) {
    return new WP_Error('api_error', __('楽天APIからエラーが返されました。', THEME_NAME), array('status' => 400));
  }

  $body = is_string($response['body']) ? json_decode($response['body']) : $response['body'];
  if (!$body || !isset($body->Items) || empty($body->Items)) {
    return new WP_Error('not_found', __('商品が見つかりませんでした。', THEME_NAME), array('status' => 404));
  }

  // リクエストが成功し、キャッシュがなかった場合のみ保存
  if (!$json_cache && $is_request_success && DEBUG_CACHE_ENABLE) {
    $cache_expiration = DAY_IN_SECONDS + (rand(0, 60) * 60);
    $acquired_date = date_i18n(__( 'Y/m/d H:i', THEME_NAME ));
    $save_response = $response;
    if (is_string($save_response['body'])) {
      $save_response['body'] = preg_replace('/{/', '{"date":"'.$acquired_date.'",', $save_response['body'], 1);
    }
    set_transient($transient_id, $save_response, $cache_expiration);
    set_transient($transient_bk_id, $save_response, $cache_expiration * 2);
  }

  // formatVersion=1の場合: Items[0]->Item
  $rawItem = $body->Items[0];
  return isset($rawItem->Item) ? $rawItem->Item : $rawItem;
}
endif;

///////////////////////////////////////////
// アイテム情報を抽出するヘルパー関数
///////////////////////////////////////////
if ( !function_exists( 'cocoon_rakuten_block_extract_item_data' ) ):
function cocoon_rakuten_block_extract_item_data($Item){
  // 小画像の取得
  $smallImageUrl = '';
  $smallImageWidth = 64;
  $smallImageHeight = 64;
  if (isset($Item->smallImageUrls) && is_array($Item->smallImageUrls) && isset($Item->smallImageUrls[0])) {
    $imgObj = $Item->smallImageUrls[0];
    $smallImageUrl = isset($imgObj->imageUrl) ? $imgObj->imageUrl : '';
    // 画像サイズの取得
    $sizes = get_rakuten_image_size($smallImageUrl);
    if ($sizes) {
      $smallImageWidth = $sizes['width'];
      $smallImageHeight = $sizes['height'];
    }
  }

  // 中画像の取得
  $mediumImageUrl = '';
  $mediumImageWidth = 128;
  $mediumImageHeight = 128;
  if (isset($Item->mediumImageUrls) && is_array($Item->mediumImageUrls) && isset($Item->mediumImageUrls[0])) {
    $imgObj = $Item->mediumImageUrls[0];
    $mediumImageUrl = isset($imgObj->imageUrl) ? $imgObj->imageUrl : '';
    // 画像サイズの取得
    $sizes = get_rakuten_image_size($mediumImageUrl);
    if ($sizes) {
      $mediumImageWidth = $sizes['width'];
      $mediumImageHeight = $sizes['height'];
    }
  }

  return array(
    'itemCode'         => isset($Item->itemCode) ? $Item->itemCode : '',
    'title'            => isset($Item->itemName) ? $Item->itemName : '',
    'shopName'         => isset($Item->shopName) ? $Item->shopName : '',
    'shopCode'         => isset($Item->shopCode) ? $Item->shopCode : '',
    'itemPrice'        => isset($Item->itemPrice) ? (int)$Item->itemPrice : 0,
    'itemCaption'      => isset($Item->itemCaption) ? $Item->itemCaption : '',
    'affiliateUrl'     => isset($Item->affiliateUrl) ? $Item->affiliateUrl : '',
    'affiliateRate'    => isset($Item->affiliateRate) ? (float)$Item->affiliateRate : 0,
    'imageSmallUrl'    => $smallImageUrl,
    'imageSmallWidth'  => $smallImageWidth,
    'imageSmallHeight' => $smallImageHeight,
    'imageUrl'         => $mediumImageUrl,
    'imageWidth'       => $mediumImageWidth,
    'imageHeight'      => $mediumImageHeight,
  );
}
endif;

///////////////////////////////////////////
// 静的HTMLプレビュー生成エンドポイントのコールバック
///////////////////////////////////////////
if ( !function_exists( 'cocoon_rakuten_block_render_preview' ) ):
function cocoon_rakuten_block_render_preview($request){
  $itemCode = sanitize_text_field($request->get_param('itemCode'));
  if (empty($itemCode)) {
    return new WP_Error('missing_item_code', __('商品コードを指定してください。', THEME_NAME), array('status' => 400));
  }

  // ブロック設定を取得
  $settings = array(
    'size'               => sanitize_text_field($request->get_param('size') ?: 'm'),
    'displayMode'        => sanitize_text_field($request->get_param('displayMode') ?: 'normal'),
    'showPrice'          => $request->get_param('showPrice') !== null ? (bool)$request->get_param('showPrice') : true,
    'showDescription'    => (bool)$request->get_param('showDescription'),
    'showLogo'           => $request->get_param('showLogo') !== null ? (bool)$request->get_param('showLogo') : true,
    'showBorder'         => $request->get_param('showBorder') !== null ? (bool)$request->get_param('showBorder') : true,
    'showAmazonButton'   => $request->get_param('showAmazonButton') !== null ? (bool)$request->get_param('showAmazonButton') : true,
    'showRakutenButton'  => $request->get_param('showRakutenButton') !== null ? (bool)$request->get_param('showRakutenButton') : true,
    'showYahooButton'    => $request->get_param('showYahooButton') !== null ? (bool)$request->get_param('showYahooButton') : true,
    'showMercariButton'  => $request->get_param('showMercariButton') !== null ? (bool)$request->get_param('showMercariButton') : true,
    'customTitle'        => sanitize_text_field($request->get_param('customTitle') ?: ''),
    'customDescription'  => sanitize_textarea_field($request->get_param('customDescription') ?: ''),
    'searchKeyword'      => sanitize_text_field($request->get_param('searchKeyword') ?: ''),
    'btn1Url'            => esc_url_raw($request->get_param('btn1Url') ?: ''),
    'btn1Text'           => sanitize_text_field($request->get_param('btn1Text') ?: ''),
    'btn1Tag'            => wp_kses_post($request->get_param('btn1Tag') ?: ''),
    'btn2Url'            => esc_url_raw($request->get_param('btn2Url') ?: ''),
    'btn2Text'           => sanitize_text_field($request->get_param('btn2Text') ?: ''),
    'btn2Tag'            => wp_kses_post($request->get_param('btn2Tag') ?: ''),
    'btn3Url'            => esc_url_raw($request->get_param('btn3Url') ?: ''),
    'btn3Text'           => sanitize_text_field($request->get_param('btn3Text') ?: ''),
    'btn3Tag'            => wp_kses_post($request->get_param('btn3Tag') ?: ''),
    'useMoshimoAffiliate' => $request->get_param('useMoshimoAffiliate') !== null ? (bool)$request->get_param('useMoshimoAffiliate') : false,
    // JSから価格取得時刻が渡された場合はそれを使用（プレビュー再生成時に時刻が変わらないようにする）
    'priceFetchedAt'     => sanitize_text_field($request->get_param('priceFetchedAt') ?: ''),
  );

  // 楽天APIで商品情報を取得
  $Item = cocoon_rakuten_block_fetch_item($itemCode);
  if (is_wp_error($Item)) {
    return $Item;
  }

  // 静的HTMLを生成
  $html = cocoon_rakuten_block_generate_static_html($Item, $itemCode, $settings);

  // アイテムデータも返す（attributesの保存用）
  $itemData = cocoon_rakuten_block_extract_item_data($Item);

  return rest_ensure_response(array(
    'success'  => true,
    'html'     => $html,
    'itemData' => $itemData,
  ));
}
endif;

///////////////////////////////////////////
// 静的HTML生成関数（既存ショートコードと同一構造）
///////////////////////////////////////////
if ( !function_exists( 'cocoon_rakuten_block_generate_static_html' ) ):
function cocoon_rakuten_block_generate_static_html($Item, $itemCode, $settings){
  // アフィリエイトIDをCocoon設定から取得
  $associate_tracking_id = trim(get_amazon_associate_tracking_id());
  $rakuten_affiliate_id  = trim(get_rakuten_affiliate_id());
  $sid                   = trim(get_yahoo_valuecommerce_sid());
  $pid                   = trim(get_yahoo_valuecommerce_pid());
  $mercari_affiliate_id  = trim(get_mercari_affiliate_id());
  $dmm_affiliate_id      = trim(get_dmm_affiliate_id());
  $moshimo_amazon_id     = trim(get_moshimo_amazon_id());
  $moshimo_rakuten_id    = trim(get_moshimo_rakuten_id());
  $moshimo_yahoo_id      = trim(get_moshimo_yahoo_id());

  // 表示設定の展開
  $size              = strtolower($settings['size']);
  $displayMode       = $settings['displayMode'];
  $showPrice         = $settings['showPrice'];
  $showDescription   = $settings['showDescription'];
  $showLogo          = $settings['showLogo'];
  $showBorder        = $settings['showBorder'];
  $customTitle       = $settings['customTitle'];
  $customDescription = $settings['customDescription'];
  $keyword           = $settings['searchKeyword'];

  // 商品情報の取得
  $itemName      = isset($Item->itemName) ? $Item->itemName : '';
  $itemPrice     = isset($Item->itemPrice) ? $Item->itemPrice : 0;
  // ショップ名の取得（エスケープは出力時に行う）
  $shopName      = isset($Item->shopName) ? $Item->shopName : '';
  // ロジック中は生URLを保持（HTML出力時にesc_url()でエスケープする）
  $affiliateUrl  = isset($Item->affiliateUrl) ? $Item->affiliateUrl : '';

  // 画像情報の取得
  $smallImageUrl = '';
  $smallImageWidth = 64;
  $smallImageHeight = 64;
  if (isset($Item->smallImageUrls) && is_array($Item->smallImageUrls) && isset($Item->smallImageUrls[0])) {
    $imgObj = $Item->smallImageUrls[0];
    $smallImageUrl = isset($imgObj->imageUrl) ? $imgObj->imageUrl : '';
    $sizes = get_rakuten_image_size($smallImageUrl);
    if ($sizes) {
      $smallImageWidth = $sizes['width'];
      $smallImageHeight = $sizes['height'];
    }
  }

  $mediumImageUrl = '';
  $mediumImageWidth = 128;
  $mediumImageHeight = 128;
  if (isset($Item->mediumImageUrls) && is_array($Item->mediumImageUrls) && isset($Item->mediumImageUrls[0])) {
    $imgObj = $Item->mediumImageUrls[0];
    $mediumImageUrl = isset($imgObj->imageUrl) ? $imgObj->imageUrl : '';
    $sizes = get_rakuten_image_size($mediumImageUrl);
    if ($sizes) {
      $mediumImageWidth = $sizes['width'];
      $mediumImageHeight = $sizes['height'];
    }
  }

  // 画像サイズの決定
  switch ($size) {
    case 's':
      $size_class = 'pis-s';
      if ($smallImageUrl) {
        $ImageUrl = $smallImageUrl;
        $ImageWidth = $smallImageWidth;
        $ImageHeight = $smallImageHeight;
      } else {
        $ImageUrl = defined('NO_IMAGE_150') ? NO_IMAGE_150 : '';
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
        $ImageUrl = defined('NO_IMAGE_150') ? NO_IMAGE_150 : '';
        $ImageWidth = '128';
        $ImageHeight = '128';
      }
      break;
  }

  // タイトルの決定
  if ($customTitle) {
    $Title = $customTitle;
  } else {
    $Title = $itemName;
  }
  $TitleAttr = esc_attr($Title);
  $TitleHtml = esc_html($Title);

  // もしもアフィリエイトの処理（ブロック設定が優先）
  $use_moshimo = isset($settings['useMoshimoAffiliate']) ? (bool)$settings['useMoshimoAffiliate'] : is_moshimo_affiliate_link_enable();
  $moshimo_rakuten_impression_tag = null;
  if ($moshimo_rakuten_id && $use_moshimo) {
    // アフィリエイトURLから商品ページURLを正規表現で抽出
    $decoded_affiliateUrl = urldecode($affiliateUrl);
    $decoded_affiliateUrl = str_replace('&amp;', '&', $decoded_affiliateUrl);
    if (preg_match_all('{\\?pc=(.+?)&m=}i', urldecode($decoded_affiliateUrl), $m)) {
      if (isset($m[1][0]) && $m[1][0]) {
        $rakuten_product_page_url = $m[1][0];
        $affiliateUrl = 'https://af.moshimo.com/af/c/click?a_id='.$moshimo_rakuten_id.'&p_id=54&pc_id=54&pl_id=616&url='.urlencode($rakuten_product_page_url);
        // インプレッションタグ
        $moshimo_rakuten_impression_tag = get_moshimo_rakuten_impression_tag();
      }
    }
  }

  // 画像のみモードの処理
  if ($displayMode === 'image_only') {
    $image_link_tag = '<a href="'.esc_url($affiliateUrl).'" class="rakuten-item-thumb-link product-item-thumb-link image-thumb rakuten-item-image-only product-item-image-only no-icon" target="_blank" title="'.$TitleAttr.'" rel="nofollow noopener">'.
      '<img src="'.esc_url($ImageUrl).'" alt="'.$TitleAttr.'" width="'.esc_attr($ImageWidth).'" height="'.esc_attr($ImageHeight).'" class="rakuten-item-thumb-image product-item-thumb-image">'.
      $moshimo_rakuten_impression_tag.
    '</a>';
    return $image_link_tag;
  }

  // テキストのみモードの処理
  if ($displayMode === 'text_only') {
    $text_link_tag = '<a href="'.esc_url($affiliateUrl).'" class="rakuten-item-title-link product-item-title-link rakuten-item-text-only product-item-text-only" target="_blank" title="'.$TitleAttr.'" rel="nofollow noopener">'.
      $TitleHtml.
      $moshimo_rakuten_impression_tag.
    '</a>';
    return $text_link_tag;
  }

  // 画像リンクタグの生成
  $image_link_tag = '<a href="'.esc_url($affiliateUrl).'" class="rakuten-item-thumb-link product-item-thumb-link image-thumb" target="_blank" title="'.$TitleAttr.'" rel="nofollow noopener">'.
    '<img src="'.esc_url($ImageUrl).'" alt="'.$TitleAttr.'" width="'.esc_attr($ImageWidth).'" height="'.esc_attr($ImageHeight).'" class="rakuten-item-thumb-image product-item-thumb-image">'.
    $moshimo_rakuten_impression_tag.
  '</a>';

  // 画像ブロック
  $image_figure_tag = '<figure class="rakuten-item-thumb product-item-thumb">'.$image_link_tag.'</figure>';

  // テキストリンク
  $text_link_tag = '<a href="'.esc_url($affiliateUrl).'" class="rakuten-item-title-link product-item-title-link" target="_blank" title="'.$TitleAttr.'" rel="nofollow noopener">'.
    $TitleHtml.
    $moshimo_rakuten_impression_tag.
  '</a>';

  // 価格表示タグ
  $item_price_tag = null;
  // JSから渡された取得時刻があればそれを使用。なければ現在時刻
  $priceFetchedAt = isset($settings['priceFetchedAt']) ? $settings['priceFetchedAt'] : '';
  if ($priceFetchedAt) {
    // ISO文字列（UTC）→ UnixタイムスタンプをWordPressのタイムゾーンでフォーマット
    $ts = strtotime($priceFetchedAt);
    $acquired_date = $ts ? wp_date(__( 'Y/m/d H:i', THEME_NAME ), $ts) : date_i18n(__( 'Y/m/d H:i', THEME_NAME ));
  } else {
    $acquired_date = date_i18n(__( 'Y/m/d H:i', THEME_NAME ));
  }
  if ($showPrice && $itemPrice) {
    $FormattedPrice = '￥ '.number_format($itemPrice);
    $item_price_tag = get_item_price_tag($FormattedPrice, $acquired_date);
  }

  // 説明文の処理（showDescriptionが無効なときはカスタム説明文の入力があっても表示しない）
  $description = '';
  if ($showDescription) {
    if ($customDescription) {
      $description = $customDescription;
    } elseif (isset($Item->itemCaption)) {
      // 商品説明文を先頭200文字に制限
      $description = mb_substr(strip_tags($Item->itemCaption), 0, 200);
    }
  }
  $description_tag = get_item_description_tag($description);

  // 検索ボタンの生成
  if (!$keyword) {
    $keyword = $Title;
  }
  $args = array(
    'keyword'              => $keyword,
    'associate_tracking_id' => $associate_tracking_id,
    'rakuten_affiliate_id' => $rakuten_affiliate_id,
    'sid'                  => $sid,
    'pid'                  => $pid,
    'mercari_affiliate_id' => $mercari_affiliate_id,
    'dmm_affiliate_id'     => $dmm_affiliate_id,
    'moshimo_amazon_id'    => $moshimo_amazon_id,
    'moshimo_rakuten_id'   => $moshimo_rakuten_id,
    'moshimo_yahoo_id'     => $moshimo_yahoo_id,
    'amazon'               => $settings['showAmazonButton'] ? 1 : 0,
    'rakuten'              => $settings['showRakutenButton'] ? 1 : 0,
    'yahoo'                => $settings['showYahooButton'] ? 1 : 0,
    'mercari'              => $settings['showMercariButton'] ? 1 : 0,
    'dmm'                  => 0,
    'amazon_page_url'      => null,
    'rakuten_page_url'     => $affiliateUrl,
    'btn1_url'             => $settings['btn1Url'],
    'btn1_text'            => $settings['btn1Text'],
    'btn1_tag'             => $settings['btn1Tag'],
    'btn2_url'             => $settings['btn2Url'],
    'btn2_text'            => $settings['btn2Text'],
    'btn2_tag'             => $settings['btn2Tag'],
    'btn3_url'             => $settings['btn3Url'],
    'btn3_text'            => $settings['btn3Text'],
    'btn3_tag'             => $settings['btn3Tag'],
    // ブロックからの呼び出し時はCocoon設定のボタン表示チェックをスキップ
    'skip_visibility_check' => true,
    // ブロック設定のもしもアフィリエイト制御
    'use_moshimo' => $use_moshimo,
  );
  $buttons_tag = get_search_buttons_tag($args);

  // 枠線・ロゴの制御
  $border_class = $showBorder ? '' : ' no-border';
  $logo_class = $showLogo ? '' : ' no-after';

  // 商品リンクタグの生成（既存ショートコードと同じ構造）
  $tag = '<div class="rakuten-item-box product-item-box no-icon '.$size_class.$border_class.$logo_class.' '.esc_attr($itemCode).' cf">'.
    $image_figure_tag.
    '<div class="rakuten-item-content product-item-content cf">'.
      '<div class="rakuten-item-title product-item-title">'.
        $text_link_tag.
      '</div>'.
      '<div class="rakuten-item-snippet product-item-snippet">'.
        '<div class="rakuten-item-maker product-item-maker">'.esc_html($shopName).'</div>'.
        $item_price_tag.
        $description_tag.
      '</div>'.
      $buttons_tag.
    '</div>'.
  '</div>';

  return $tag;
}
endif;
