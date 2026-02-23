<?php //Amazon商品リンクブロック REST API & 静的HTML生成
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
add_action('rest_api_init', 'cocoon_amazon_block_register_routes');
if ( !function_exists( 'cocoon_amazon_block_register_routes' ) ):
function cocoon_amazon_block_register_routes(){
  // 商品検索エンドポイント
  register_rest_route('cocoon/v1', '/amazon/search', array(
    'methods'  => 'POST',
    'callback' => 'cocoon_amazon_block_search',
    'permission_callback' => function(){ return current_user_can('edit_posts'); },
  ));
  // 商品詳細取得エンドポイント
  register_rest_route('cocoon/v1', '/amazon/get-item', array(
    'methods'  => 'POST',
    'callback' => 'cocoon_amazon_block_get_item',
    'permission_callback' => function(){ return current_user_can('edit_posts'); },
  ));
  // 静的HTMLプレビュー生成エンドポイント
  register_rest_route('cocoon/v1', '/amazon/render-preview', array(
    'methods'  => 'POST',
    'callback' => 'cocoon_amazon_block_render_preview',
    'permission_callback' => function(){ return current_user_can('edit_posts'); },
  ));
}
endif;

///////////////////////////////////////////
// 商品検索エンドポイントのコールバック
///////////////////////////////////////////
if ( !function_exists( 'cocoon_amazon_block_search' ) ):
function cocoon_amazon_block_search($request){
  // リクエストパラメータを取得
  $keyword    = sanitize_text_field($request->get_param('keyword'));
  $item_page  = (int)$request->get_param('page');
  if ($item_page < 1) $item_page = 1;

  // キーワードが空なら400エラー
  if (empty($keyword)) {
    return new WP_Error('missing_keyword', __('キーワードを入力してください。', THEME_NAME), array('status' => 400));
  }

  // Creators APIで検索を実行
  $res = get_amazon_creators_search_json($keyword, null, 10, $item_page);

  // API呼び出し失敗
  if ($res === false) {
    return new WP_Error('api_error', __('Amazon APIに接続できませんでした。', THEME_NAME), array('status' => 500));
  }

  // レスポンスをJSONデコード
  $json = is_string($res) ? json_decode($res) : $res;
  if (!$json) {
    return new WP_Error('parse_error', __('APIレスポンスの解析に失敗しました。', THEME_NAME), array('status' => 500));
  }

  // PA-APIエラー形式のチェック
  if (isset($json->Errors)) {
    $error_msg = '';
    if (is_array($json->Errors) && isset($json->Errors[0])) {
      $error_msg = isset($json->Errors[0]->Message) ? $json->Errors[0]->Message : '';
    }
    return new WP_Error('api_error', $error_msg, array('status' => 400));
  }

  // 検索結果を整形して返し
  $items = array();
  $total = 0;

  // PA-API互換のSearchResult形式
  if (isset($json->SearchResult) && isset($json->SearchResult->Items)) {
    $total = isset($json->SearchResult->TotalResultCount) ? (int)$json->SearchResult->TotalResultCount : 0;
    foreach ($json->SearchResult->Items as $item) {
      $items[] = cocoon_amazon_block_format_search_item($item);
    }
  }

  return rest_ensure_response(array(
    'success'      => true,
    'items'        => $items,
    'totalResults' => $total,
    'page'         => $item_page,
  ));
}
endif;

///////////////////////////////////////////
// 検索結果アイテムのフォーマッタ
///////////////////////////////////////////
if ( !function_exists( 'cocoon_amazon_block_format_search_item' ) ):
function cocoon_amazon_block_format_search_item($item){
  // ASINの取得
  $asin = isset($item->ASIN) ? $item->ASIN : '';

  // タイトルの取得
  $title = '';
  if (isset($item->ItemInfo) && isset($item->ItemInfo->Title) && isset($item->ItemInfo->Title->DisplayValue)) {
    $title = $item->ItemInfo->Title->DisplayValue;
  }

  // 著者/ブランド名の取得
  $maker = cocoon_amazon_block_extract_maker($item);

  // 価格の取得（検索結果での参考表示用のみ）
  $price = '';
  if (isset($item->Offers) && isset($item->Offers->Listings) && is_array($item->Offers->Listings) && isset($item->Offers->Listings[0])) {
    $listing = $item->Offers->Listings[0];
    if (isset($listing->Price) && isset($listing->Price->DisplayAmount)) {
      $price = $listing->Price->DisplayAmount;
    }
  }

  // 画像URLの取得
  $imageUrl = '';
  if (isset($item->Images) && isset($item->Images->Primary) && isset($item->Images->Primary->Medium) && isset($item->Images->Primary->Medium->URL)) {
    $imageUrl = $item->Images->Primary->Medium->URL;
  }

  // 商品ページURLの取得
  $detailPageUrl = isset($item->DetailPageURL) ? $item->DetailPageURL : '';

  return array(
    'asin'          => $asin,
    'title'         => $title,
    'maker'         => $maker,
    'price'         => $price,
    'imageUrl'      => $imageUrl,
    'detailPageUrl' => $detailPageUrl,
  );
}
endif;

///////////////////////////////////////////
// 著者/ブランド名を抽出するヘルパー関数
///////////////////////////////////////////
if ( !function_exists( 'cocoon_amazon_block_extract_maker' ) ):
function cocoon_amazon_block_extract_maker($item){
  $maker = '';
  if (!isset($item->ItemInfo) || !isset($item->ItemInfo->ByLineInfo)) {
    return $maker;
  }
  $info = $item->ItemInfo->ByLineInfo;
  // 優先順位: Author > Artist > Actor > Creator > Director > Publisher > Brand > Manufacturer > Binding
  $fields = array('Author', 'Artist', 'Actor', 'Creator', 'Director', 'Publisher', 'Brand', 'Manufacturer', 'Binding');
  foreach ($fields as $field) {
    if (isset($info->$field) && isset($info->$field->DisplayValue)) {
      return $info->$field->DisplayValue;
    }
  }
  return $maker;
}
endif;

///////////////////////////////////////////
// 商品詳細取得エンドポイントのコールバック
///////////////////////////////////////////
if ( !function_exists( 'cocoon_amazon_block_get_item' ) ):
function cocoon_amazon_block_get_item($request){
  $asin = sanitize_text_field($request->get_param('asin'));
  if (empty($asin)) {
    return new WP_Error('missing_asin', __('ASINを指定してください。', THEME_NAME), array('status' => 400));
  }

  // Creators APIで商品情報を取得
  $res = get_amazon_creators_itemlookup_json($asin);
  if ($res === false) {
    return new WP_Error('api_error', __('Amazon APIに接続できませんでした。', THEME_NAME), array('status' => 500));
  }

  $json = is_string($res) ? json_decode($res) : $res;
  if (!$json) {
    return new WP_Error('parse_error', __('APIレスポンスの解析に失敗しました。', THEME_NAME), array('status' => 500));
  }

  // エラーチェック
  if (isset($json->Errors)) {
    $error_msg = '';
    if (is_array($json->Errors) && isset($json->Errors[0])) {
      $error_msg = isset($json->Errors[0]->Message) ? $json->Errors[0]->Message : '';
    }
    return new WP_Error('api_error', $error_msg, array('status' => 400));
  }

  // アイテム情報の抽出
  if (!isset($json->ItemsResult) || !isset($json->ItemsResult->Items) || empty($json->ItemsResult->Items[0])) {
    return new WP_Error('not_found', __('商品が見つかりませんでした。', THEME_NAME), array('status' => 404));
  }

  $item = $json->ItemsResult->Items[0];
  $itemData = cocoon_amazon_block_extract_item_data($item);
  // PA-API変換でASINが欠落するため、リクエストのASINを強制セット
  $itemData['asin'] = $asin;

  return rest_ensure_response(array(
    'success' => true,
    'item'    => $itemData,
  ));
}
endif;

///////////////////////////////////////////
// アイテム情報を抽出するヘルパー関数
///////////////////////////////////////////
if ( !function_exists( 'cocoon_amazon_block_extract_item_data' ) ):
function cocoon_amazon_block_extract_item_data($item){
  // 画像情報の取得
  $Images = isset($item->Images) ? $item->Images : null;
  $Primary = ($Images && isset($Images->Primary)) ? $Images->Primary : null;
  $Variants = ($Images && isset($Images->Variants) && is_array($Images->Variants)) ? $Images->Variants : array();

  // 各サイズの画像URL
  $smallUrl = ($Primary && isset($Primary->Small) && isset($Primary->Small->URL)) ? $Primary->Small->URL : '';
  $smallW = ($Primary && isset($Primary->Small) && isset($Primary->Small->Width)) ? (int)$Primary->Small->Width : 75;
  $smallH = ($Primary && isset($Primary->Small) && isset($Primary->Small->Height)) ? (int)$Primary->Small->Height : 75;

  $mediumUrl = ($Primary && isset($Primary->Medium) && isset($Primary->Medium->URL)) ? $Primary->Medium->URL : '';
  $mediumW = ($Primary && isset($Primary->Medium) && isset($Primary->Medium->Width)) ? (int)$Primary->Medium->Width : 160;
  $mediumH = ($Primary && isset($Primary->Medium) && isset($Primary->Medium->Height)) ? (int)$Primary->Medium->Height : 160;

  $largeUrl = ($Primary && isset($Primary->Large) && isset($Primary->Large->URL)) ? $Primary->Large->URL : '';
  $largeW = ($Primary && isset($Primary->Large) && isset($Primary->Large->Width)) ? (int)$Primary->Large->Width : 500;
  $largeH = ($Primary && isset($Primary->Large) && isset($Primary->Large->Height)) ? (int)$Primary->Large->Height : 500;

  // タイトルの取得
  $title = '';
  $ItemInfo = isset($item->ItemInfo) ? $item->ItemInfo : null;
  if ($ItemInfo && isset($ItemInfo->Title) && isset($ItemInfo->Title->DisplayValue)) {
    $title = $ItemInfo->Title->DisplayValue;
  }

  // 著者/ブランド名
  $maker = cocoon_amazon_block_extract_maker($item);

  // 商品グループ
  $productGroup = '';
  if ($ItemInfo && isset($ItemInfo->Classifications) && isset($ItemInfo->Classifications->ProductGroup) && isset($ItemInfo->Classifications->ProductGroup->DisplayValue)) {
    $productGroup = $ItemInfo->Classifications->ProductGroup->DisplayValue;
  }

  // 説明文
  $description = '';
  if ($ItemInfo && isset($ItemInfo->Features) && isset($ItemInfo->Features->DisplayValues) && isset($ItemInfo->Features->DisplayValues[0])) {
    $description = $ItemInfo->Features->DisplayValues[0];
  }

  // 商品ページURL
  $detailPageUrl = isset($item->DetailPageURL) ? $item->DetailPageURL : '';

  // バリアント画像の配列構築
  $variantImages = array();
  foreach ($Variants as $variant) {
    if (!is_object($variant)) continue;
    $vi = array();
    if (isset($variant->Small) && isset($variant->Small->URL)) {
      $vi['smallUrl'] = $variant->Small->URL;
      $vi['smallWidth'] = isset($variant->Small->Width) ? (int)$variant->Small->Width : 0;
      $vi['smallHeight'] = isset($variant->Small->Height) ? (int)$variant->Small->Height : 0;
    }
    if (isset($variant->Large) && isset($variant->Large->URL)) {
      $vi['largeUrl'] = $variant->Large->URL;
      $vi['largeWidth'] = isset($variant->Large->Width) ? (int)$variant->Large->Width : 0;
      $vi['largeHeight'] = isset($variant->Large->Height) ? (int)$variant->Large->Height : 0;
    }
    if (!empty($vi)) {
      $variantImages[] = $vi;
    }
  }

  return array(
    'asin'          => isset($item->ASIN) ? $item->ASIN : '',
    'title'         => $title,
    'maker'         => $maker,
    'productGroup'  => $productGroup,
    'description'   => $description,
    'detailPageUrl' => $detailPageUrl,
    'imageSmallUrl' => $smallUrl,
    'imageSmallWidth' => $smallW,
    'imageSmallHeight' => $smallH,
    'imageUrl'      => $mediumUrl,
    'imageWidth'    => $mediumW,
    'imageHeight'   => $mediumH,
    'imageLargeUrl' => $largeUrl,
    'imageLargeWidth' => $largeW,
    'imageLargeHeight' => $largeH,
    'variantImages' => $variantImages,
  );
}
endif;

///////////////////////////////////////////
// 静的HTMLプレビュー生成エンドポイントのコールバック
///////////////////////////////////////////
if ( !function_exists( 'cocoon_amazon_block_render_preview' ) ):
function cocoon_amazon_block_render_preview($request){
  $asin = sanitize_text_field($request->get_param('asin'));
  if (empty($asin)) {
    return new WP_Error('missing_asin', __('ASINを指定してください。', THEME_NAME), array('status' => 400));
  }

  // ブロック設定を取得
  $settings = array(
    'size'               => sanitize_text_field($request->get_param('size') ?: 'm'),
    'displayMode'        => sanitize_text_field($request->get_param('displayMode') ?: 'normal'),
    'showReview'         => (bool)$request->get_param('showReview'),
    'showDescription'    => (bool)$request->get_param('showDescription'),
    'showLogo'           => $request->get_param('showLogo') !== null ? (bool)$request->get_param('showLogo') : true,
    'showBorder'         => $request->get_param('showBorder') !== null ? (bool)$request->get_param('showBorder') : true,
    'showCatalogImages'  => $request->get_param('showCatalogImages') !== null ? (bool)$request->get_param('showCatalogImages') : true,
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
  );

  // Creators APIで商品情報を取得
  $res = get_amazon_creators_itemlookup_json($asin);
  if ($res === false) {
    return new WP_Error('api_error', __('Amazon APIに接続できませんでした。', THEME_NAME), array('status' => 500));
  }

  $json = is_string($res) ? json_decode($res) : $res;
  if (!$json || !isset($json->ItemsResult) || !isset($json->ItemsResult->Items) || empty($json->ItemsResult->Items[0])) {
    return new WP_Error('not_found', __('商品が見つかりませんでした。', THEME_NAME), array('status' => 404));
  }

  $item = $json->ItemsResult->Items[0];

  // 静的HTMLを生成
  $html = cocoon_amazon_block_generate_static_html($item, $asin, $settings);

  // アイテムデータも返す（attributesの保存用）
  $itemData = cocoon_amazon_block_extract_item_data($item);
  // PA-API変換でASINが欠落するため、リクエストのASINを強制セット
  $itemData['asin'] = $asin;

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
if ( !function_exists( 'cocoon_amazon_block_generate_static_html' ) ):
function cocoon_amazon_block_generate_static_html($item, $asin, $settings){
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
  $showReview        = $settings['showReview'];
  $showDescription   = $settings['showDescription'];
  $showLogo          = $settings['showLogo'];
  $showBorder        = $settings['showBorder'];
  $showCatalogImages = $settings['showCatalogImages'];
  $customTitle       = $settings['customTitle'];
  $customDescription = $settings['customDescription'];
  $keyword           = $settings['searchKeyword'];

  // アソシエイトURLの取得
  $associate_url = get_amazon_associate_url($asin, $associate_tracking_id);
  $DetailPageURL = isset($item->DetailPageURL) ? esc_url($item->DetailPageURL) : null;
  if ($DetailPageURL) {
    $associate_url = $DetailPageURL;
  }

  // もしもアフィリエイトの処理（ブロック設定が優先）
  $use_moshimo = isset($settings['useMoshimoAffiliate']) ? (bool)$settings['useMoshimoAffiliate'] : is_moshimo_affiliate_link_enable();
  $moshimo_amazon_impression_tag = null;
  if ($moshimo_amazon_id && $use_moshimo) {
    $moshimo_amazon_base_url = 'https://af.moshimo.com/af/c/click?a_id='.$moshimo_amazon_id.'&p_id=170&pc_id=185&pl_id=4062&url=';
    $associate_url = $moshimo_amazon_base_url.urlencode(get_amazon_associate_url($asin));
    $moshimo_amazon_impression_tag = get_moshimo_amazon_impression_tag();
  }

  // 画像情報の取得
  $Images = isset($item->Images) ? $item->Images : null;
  $ImageItem = ($Images && isset($Images->Primary)) ? $Images->Primary : null;
  $Variants = ($Images && isset($Images->Variants) && is_array($Images->Variants)) ? $Images->Variants : array();

  // 画像サイズの決定
  $SmallImage = ($ImageItem && isset($ImageItem->Small)) ? $ImageItem->Small : null;
  $MediumImage = ($ImageItem && isset($ImageItem->Medium)) ? $ImageItem->Medium : null;
  $LargeImage = ($ImageItem && isset($ImageItem->Large)) ? $ImageItem->Large : null;

  switch ($size) {
    case 's':
      $size_class = 'pis-s';
      $ImageUrl = ($SmallImage && isset($SmallImage->URL)) ? $SmallImage->URL : 'https://images-fe.ssl-images-amazon.com/images/G/09/nav2/dp/no-image-no-ciu._SL75_.gif';
      $ImageWidth = ($SmallImage && isset($SmallImage->Width)) ? $SmallImage->Width : 75;
      $ImageHeight = ($SmallImage && isset($SmallImage->Height)) ? $SmallImage->Height : 75;
      break;
    case 'l':
      $size_class = 'pis-l';
      $ImageUrl = ($LargeImage && isset($LargeImage->URL)) ? $LargeImage->URL : 'https://images-fe.ssl-images-amazon.com/images/G/09/nav2/dp/no-image-no-ciu._SL500_.gif';
      $ImageWidth = ($LargeImage && isset($LargeImage->Width)) ? $LargeImage->Width : 500;
      $ImageHeight = ($LargeImage && isset($LargeImage->Height)) ? $LargeImage->Height : 500;
      break;
    default:
      $size_class = 'pis-m';
      $ImageUrl = ($MediumImage && isset($MediumImage->URL)) ? $MediumImage->URL : 'https://images-fe.ssl-images-amazon.com/images/G/09/nav2/dp/no-image-no-ciu._SL160_.gif';
      $ImageWidth = ($MediumImage && isset($MediumImage->Width)) ? $MediumImage->Width : 160;
      $ImageHeight = ($MediumImage && isset($MediumImage->Height)) ? $MediumImage->Height : 160;
      break;
  }

  // タイトルの取得
  $ItemInfo = isset($item->ItemInfo) ? $item->ItemInfo : null;
  if ($customTitle) {
    $Title = $customTitle;
  } elseif ($ItemInfo && isset($ItemInfo->Title) && isset($ItemInfo->Title->DisplayValue)) {
    $Title = $ItemInfo->Title->DisplayValue;
  } else {
    $Title = '';
  }
  $TitleAttr = esc_attr($Title);
  $TitleHtml = esc_html($Title);

  // 商品グループの取得
  $ProductGroupClass = '';
  if ($ItemInfo && isset($ItemInfo->Classifications) && isset($ItemInfo->Classifications->ProductGroup) && isset($ItemInfo->Classifications->ProductGroup->DisplayValue)) {
    $ProductGroupClass = strtolower(esc_html($ItemInfo->Classifications->ProductGroup->DisplayValue));
    $ProductGroupClass = str_replace(' ', '-', $ProductGroupClass);
  }

  // 著者/ブランド名の取得
  $maker = cocoon_amazon_block_extract_maker($item);

  // 説明文の取得
  $description = '';
  if ($customDescription) {
    $description = $customDescription;
  } elseif ($showDescription && $ItemInfo && isset($ItemInfo->Features) && isset($ItemInfo->Features->DisplayValues) && isset($ItemInfo->Features->DisplayValues[0])) {
    $description = $ItemInfo->Features->DisplayValues[0];
  }

  // 画像のみモードの処理
  if ($displayMode === 'image_only') {
    $image_link_tag = '<a href="'.esc_url($associate_url).'" class="amazon-item-thumb-link product-item-thumb-link image-thumb amazon-item-image-only product-item-image-only no-icon" target="_blank" title="'.esc_attr($TitleAttr).'" rel="nofollow noopener">'.
      '<img src="'.esc_url($ImageUrl).'" alt="'.esc_attr($TitleAttr).'" width="'.esc_attr($ImageWidth).'" height="'.esc_attr($ImageHeight).'" class="amazon-item-thumb-image product-item-thumb-image">'.
      $moshimo_amazon_impression_tag.
    '</a>';
    return $image_link_tag;
  }

  // テキストのみモードの処理
  if ($displayMode === 'text_only') {
    $text_link_tag = '<a href="'.esc_url($associate_url).'" class="amazon-item-title-link product-item-title-link amazon-item-text-only product-item-text-only" target="_blank" title="'.esc_attr($TitleAttr).'" rel="nofollow noopener">'.
      $TitleHtml.
      $moshimo_amazon_impression_tag.
    '</a>';
    return $text_link_tag;
  }

  // カタログ拡大画像の生成
  $LargeImageUrl = ($LargeImage && isset($LargeImage->URL)) ? $LargeImage->URL : null;
  $LargeImageWidth = ($LargeImage && isset($LargeImage->Width)) ? $LargeImage->Width : null;
  $LargeImageHeight = ($LargeImage && isset($LargeImage->Height)) ? $LargeImage->Height : null;

  $image_l_tag = null;
  if ($showCatalogImages && ($size != 'l') && $LargeImageUrl) {
    $image_l_tag = '<div class="amazon-item-thumb-l product-item-thumb-l image-content">'.
      '<img src="'.esc_url($LargeImageUrl).'" alt="" width="'.esc_attr($LargeImageWidth).'" height="'.esc_attr($LargeImageHeight).'">'.
    '</div>';
  }

  // スウォッチ画像の生成
  $swatchimages_tag = null;
  if ($showCatalogImages && is_array($Variants) && !empty($Variants)) {
    $tmp_tag = null;
    $variants_count = count($Variants);
    for ($i = 0; $i < $variants_count - 1; $i++) {
      $display_none_class = null;
      if (($size != 'l') && ($i >= 3)) {
        $display_none_class .= ' sp-display-none';
      }
      if (($size == 's') && ($i >= 3) || ($size == 'm') && ($i >= 5)) {
        $display_none_class .= ' display-none';
      }

      $Variant = isset($Variants[$i]) ? $Variants[$i] : null;
      if (!$Variant || !is_object($Variant)) continue;

      $SwatchImage = isset($Variant->Small) ? $Variant->Small : null;
      $SwatchImageURL = ($SwatchImage && isset($SwatchImage->URL)) ? $SwatchImage->URL : null;
      $SwatchImageWidth = ($SwatchImage && isset($SwatchImage->Width)) ? $SwatchImage->Width : null;
      $SwatchImageHeight = ($SwatchImage && isset($SwatchImage->Height)) ? $SwatchImage->Height : null;

      $VariantLarge = isset($Variant->Large) ? $Variant->Large : null;
      $VLargeURL = ($VariantLarge && isset($VariantLarge->URL)) ? $VariantLarge->URL : null;
      $VLargeW = ($VariantLarge && isset($VariantLarge->Width)) ? $VariantLarge->Width : null;
      $VLargeH = ($VariantLarge && isset($VariantLarge->Height)) ? $VariantLarge->Height : null;

      if (!$SwatchImageURL || !$VLargeURL) continue;

      $tmp_tag .= '<div class="image-thumb swatch-image-thumb si-thumb'.esc_attr($display_none_class).'">'.
        '<img src="'.esc_url($SwatchImageURL).'" alt="" width="'.esc_attr($SwatchImageWidth).'" height="'.esc_attr($SwatchImageHeight).'">'.
        '<div class="image-content">'.
        '<img src="'.esc_url($VLargeURL).'" alt="" width="'.esc_attr($VLargeW).'" height="'.esc_attr($VLargeH).'">'.
        '</div>'.
      '</div>';
    }
    if ($tmp_tag) {
      $swatchimages_tag = '<a href="'.esc_url($associate_url).'" class="swatchimages" target="_blank" rel="nofollow noopener">'.$tmp_tag.'</a>';
    }
  }

  // 画像リンクタグの生成
  $image_link_tag = '<a href="'.esc_url($associate_url).'" class="amazon-item-thumb-link product-item-thumb-link image-thumb" target="_blank" title="'.esc_attr($TitleAttr).'" rel="nofollow noopener">'.
    '<img src="'.esc_url($ImageUrl).'" alt="'.esc_attr($TitleAttr).'" width="'.esc_attr($ImageWidth).'" height="'.esc_attr($ImageHeight).'" class="amazon-item-thumb-image product-item-thumb-image">'.
    $moshimo_amazon_impression_tag.
    $image_l_tag.
  '</a>'.
  $swatchimages_tag;

  // 画像ブロック
  $image_figure_tag = '<figure class="amazon-item-thumb product-item-thumb">'.$image_link_tag.'</figure>';

  // テキストリンク
  $text_link_tag = '<a href="'.esc_url($associate_url).'" class="amazon-item-title-link product-item-title-link" target="_blank" title="'.esc_attr($TitleAttr).'" rel="nofollow noopener">'.
    $TitleHtml.
    $moshimo_amazon_impression_tag.
  '</a>';

  // レビューリンク
  $review_tag = null;
  if ($showReview && $associate_tracking_id) {
    $review_url = get_amazon_review_url($asin, $associate_tracking_id);
    $review_tag = '<div class="amazon-item-review product-item-review item-review">'.
      '<span class="fa fa-comments-o" aria-hidden="true"></span> <a class="amazon-item-review-link product-item-review-link item-review-link" href="'.esc_url($review_url).'" target="_blank" rel="nofollow noopener">'.
        get_amazon_item_customer_reviews_text().
      '</a>'.
    '</div>';
  }

  // 説明文タグ
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
    'amazon_page_url'      => $associate_url,
    'rakuten_page_url'     => null,
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
  $tag = '<div class="amazon-item-box product-item-box no-icon '.$size_class.$border_class.$logo_class.' '.esc_attr($ProductGroupClass).' '.esc_attr($asin).' cf">'.
    $image_figure_tag.
    '<div class="amazon-item-content product-item-content cf">'.
      '<div class="amazon-item-title product-item-title">'.
        $text_link_tag.
      '</div>'.
      '<div class="amazon-item-snippet product-item-snippet">'.
        '<div class="amazon-item-maker product-item-maker">'.esc_html($maker).'</div>'.
        $description_tag.
        $review_tag.
      '</div>'.
      $buttons_tag.
    '</div>'.
  '</div>';

  return $tag;
}
endif;
