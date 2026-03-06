<?php //Amazon商品リンクブロック 定期自動更新（WP-Cron）
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

///////////////////////////////////////////
// カスタムCronスケジュールの追加
///////////////////////////////////////////
add_filter('cron_schedules', 'cocoon_amazon_block_cron_schedules');
if ( !function_exists( 'cocoon_amazon_block_cron_schedules' ) ):
function cocoon_amazon_block_cron_schedules($schedules){
  // 2週間ごと
  $schedules['cocoon_biweekly'] = array(
    'interval' => 14 * DAY_IN_SECONDS,
    'display'  => __('2週間ごと', THEME_NAME),
  );
  // 1ヶ月ごと（30日）
  $schedules['cocoon_monthly'] = array(
    'interval' => 30 * DAY_IN_SECONDS,
    'display'  => __('1ヶ月ごと', THEME_NAME),
  );
  // 3ヶ月ごと（90日）
  $schedules['cocoon_quarterly'] = array(
    'interval' => 90 * DAY_IN_SECONDS,
    'display'  => __('3ヶ月ごと', THEME_NAME),
  );
  return $schedules;
}
endif;

///////////////////////////////////////////
// Cronスケジュールの管理
///////////////////////////////////////////
add_action('init', 'cocoon_amazon_block_cron_manage');
if ( !function_exists( 'cocoon_amazon_block_cron_manage' ) ):
function cocoon_amazon_block_cron_manage(){
  $event_hook = 'cocoon_amazon_block_update_event';
  // 自動更新が無効な場合はスケジュールを解除
  if (!is_product_block_auto_update_enable()) {
    $timestamp = wp_next_scheduled($event_hook);
    if ($timestamp) {
      wp_unschedule_event($timestamp, $event_hook);
    }
    return;
  }
  // 更新間隔の取得（デフォルト: 1ヶ月）
  $interval = get_product_block_auto_update_interval();
  // まだスケジュールされていない場合は登録
  if (!wp_next_scheduled($event_hook)) {
    wp_schedule_event(time(), $interval, $event_hook);
  }
}
endif;

///////////////////////////////////////////
// Cronイベントハンドラ（バッチ処理）
///////////////////////////////////////////
add_action('cocoon_amazon_block_update_event', 'cocoon_amazon_block_batch_update');
if ( !function_exists( 'cocoon_amazon_block_batch_update' ) ):
function cocoon_amazon_block_batch_update(){
  // 自動更新がOFFなら処理しない
  if (!is_product_block_auto_update_enable()) {
    return;
  }

  // 1回あたりの処理件数（共通設定から取得）
  $batch_size = get_product_block_auto_update_batch_size();
  if ($batch_size < 1) $batch_size = PRODUCT_BLOCK_AUTO_UPDATE_BATCH_SIZE_DEFAULT;

  // 前回の処理位置を取得
  $last_processed_id = (int)get_option('cocoon_amazon_block_last_processed_id', 0);

  // Amazon商品リンクブロックを含む投稿を検索
  global $wpdb;
  $posts = $wpdb->get_results($wpdb->prepare(
    "SELECT ID, post_content, post_modified, post_modified_gmt
     FROM {$wpdb->posts}
     WHERE post_status = 'publish'
       AND post_type IN ('post', 'page')
       AND post_content LIKE %s
       AND ID > %d
     ORDER BY ID ASC
     LIMIT %d",
    '%<!-- wp:cocoon-blocks/amazon-product-link%',
    $last_processed_id,
    $batch_size
  ));

  // 結果が空の場合はリスタート
  if (empty($posts)) {
    update_option('cocoon_amazon_block_last_processed_id', 0);
    amazon_creators_api_debug_log('cron: all posts processed, resetting');
    return;
  }

  // 投稿ごとに処理
  // ループ変数 $post はループ外で参照すると危険なため別変数 $last_id に保持する
  $last_id = 0;
  foreach ($posts as $post) {
    cocoon_amazon_block_update_post_blocks($post);
    // 処理位置を更新
    $last_id = $post->ID;
    update_option('cocoon_amazon_block_last_processed_id', $last_id);
    // 投稿間のスリープ
    sleep(PRODUCT_BLOCK_CRON_POST_SLEEP_SECONDS);
  }

  // ループ変数ではなく明示的な変数でログ出力
  amazon_creators_api_debug_log('cron: batch completed, last_id='.$last_id);
}
endif;

///////////////////////////////////////////
// 投稿内のAmazon商品リンクブロックを更新
///////////////////////////////////////////
if ( !function_exists( 'cocoon_amazon_block_update_post_blocks' ) ):
function cocoon_amazon_block_update_post_blocks($post){
  $content = $post->post_content;
  $blocks = parse_blocks($content);
  // ブロックが見つからない場合は処理しない
  if (empty($blocks)) return;

  $updated = false;
  // ブロックを再帰的に処理
  $blocks = cocoon_amazon_block_update_blocks_recursive($blocks, $updated);

  // 更新があった場合のみ投稿を更新
  if (!$updated) return;

  // 更新後のコンテンツを生成
  $new_content = serialize_blocks($blocks);

  // 更新日時を保存しておく（復元用）
  $post_modified = $post->post_modified;
  $post_modified_gmt = $post->post_modified_gmt;

  // 投稿を更新
  wp_update_post(array(
    'ID'           => $post->ID,
    'post_content' => $new_content,
  ));

  // 更新日時を復元（変更しない）
  global $wpdb;
  $wpdb->update(
    $wpdb->posts,
    array(
      'post_modified'     => $post_modified,
      'post_modified_gmt' => $post_modified_gmt,
    ),
    array('ID' => $post->ID),
    array('%s', '%s'),
    array('%d')
  );

  // キャッシュをクリア
  clean_post_cache($post->ID);

  amazon_creators_api_debug_log('cron: post '.$post->ID.' updated');
}
endif;

///////////////////////////////////////////
// ブロック配列を再帰的に更新
///////////////////////////////////////////
if ( !function_exists( 'cocoon_amazon_block_update_blocks_recursive' ) ):
function cocoon_amazon_block_update_blocks_recursive($blocks, &$updated){
  foreach ($blocks as $key => $block) {
    // Amazon商品リンクブロックの場合
    if ($block['blockName'] === 'cocoon-blocks/amazon-product-link') {
      $attrs = $block['attrs'];
      $asin = isset($attrs['asin']) ? $attrs['asin'] : '';

      if (empty($asin)) continue;

      // Creators APIで商品情報を再取得
      $res = get_amazon_creators_itemlookup_json($asin);
      // APIレート制限対策：1ブロックのAPIリクエストごとに待機
      sleep(PRODUCT_BLOCK_CRON_API_SLEEP_SECONDS);
      if ($res === false) continue;

      $json = is_string($res) ? json_decode($res) : $res;
      if (!$json || !isset($json->ItemsResult) || !isset($json->ItemsResult->Items) || empty($json->ItemsResult->Items[0])) {
        continue;
      }

      $item = $json->ItemsResult->Items[0];
      $itemData = cocoon_amazon_block_extract_item_data($item);

      // カスタムタイトル・カスタム説明文はユーザー設定を維持
      $customTitle = isset($attrs['customTitle']) ? $attrs['customTitle'] : '';
      $customDescription = isset($attrs['customDescription']) ? $attrs['customDescription'] : '';

      // 属性を更新（APIから取得した値で上書き）
      $attrs['title']           = $itemData['title'];
      $attrs['maker']           = $itemData['maker'];
      $attrs['productGroup']    = $itemData['productGroup'];
      $attrs['description']     = $itemData['description'];
      $attrs['detailPageUrl']   = $itemData['detailPageUrl'];
      $attrs['imageSmallUrl']   = $itemData['imageSmallUrl'];
      $attrs['imageSmallWidth'] = $itemData['imageSmallWidth'];
      $attrs['imageSmallHeight'] = $itemData['imageSmallHeight'];
      $attrs['imageUrl']        = $itemData['imageUrl'];
      $attrs['imageWidth']      = $itemData['imageWidth'];
      $attrs['imageHeight']     = $itemData['imageHeight'];
      $attrs['imageLargeUrl']   = $itemData['imageLargeUrl'];
      $attrs['imageLargeWidth'] = $itemData['imageLargeWidth'];
      $attrs['imageLargeHeight'] = $itemData['imageLargeHeight'];
      $attrs['variantImages']   = $itemData['variantImages'];

      // 設定を組み立てて静的HTMLを再生成
      $settings = array(
        'size'              => isset($attrs['size']) ? $attrs['size'] : 'm',
        'displayMode'       => isset($attrs['displayMode']) ? $attrs['displayMode'] : 'normal',
        'showPrice'         => isset($attrs['showPrice']) ? (bool)$attrs['showPrice'] : true,
        'showReview'        => isset($attrs['showReview']) ? (bool)$attrs['showReview'] : false,
        'showDescription'   => isset($attrs['showDescription']) ? (bool)$attrs['showDescription'] : false,
        'showLogo'          => isset($attrs['showLogo']) ? (bool)$attrs['showLogo'] : true,
        'showBorder'        => isset($attrs['showBorder']) ? (bool)$attrs['showBorder'] : true,
        'showCatalogImages' => isset($attrs['showCatalogImages']) ? (bool)$attrs['showCatalogImages'] : true,
        'showAmazonButton'  => isset($attrs['showAmazonButton']) ? (bool)$attrs['showAmazonButton'] : true,
        'showRakutenButton' => isset($attrs['showRakutenButton']) ? (bool)$attrs['showRakutenButton'] : true,
        'showYahooButton'   => isset($attrs['showYahooButton']) ? (bool)$attrs['showYahooButton'] : true,
        'showMercariButton' => isset($attrs['showMercariButton']) ? (bool)$attrs['showMercariButton'] : true,
        'customTitle'       => $customTitle,
        'customDescription' => $customDescription,
        'searchKeyword'     => isset($attrs['searchKeyword']) ? $attrs['searchKeyword'] : '',
        'btn1Url'           => isset($attrs['btn1Url']) ? $attrs['btn1Url'] : '',
        'btn1Text'          => isset($attrs['btn1Text']) ? $attrs['btn1Text'] : '',
        'btn1Tag'           => isset($attrs['btn1Tag']) ? $attrs['btn1Tag'] : '',
        'btn2Url'           => isset($attrs['btn2Url']) ? $attrs['btn2Url'] : '',
        'btn2Text'          => isset($attrs['btn2Text']) ? $attrs['btn2Text'] : '',
        'btn2Tag'           => isset($attrs['btn2Tag']) ? $attrs['btn2Tag'] : '',
        'btn3Url'           => isset($attrs['btn3Url']) ? $attrs['btn3Url'] : '',
        'btn3Text'          => isset($attrs['btn3Text']) ? $attrs['btn3Text'] : '',
        'btn3Tag'           => isset($attrs['btn3Tag']) ? $attrs['btn3Tag'] : '',
        'useMoshimoAffiliate' => isset($attrs['useMoshimoAffiliate']) ? (bool)$attrs['useMoshimoAffiliate'] : false,
      );

      // 静的HTMLを再生成
      $html = cocoon_amazon_block_generate_static_html($item, $asin, $settings);

      // staticHtml属性も更新（エディタ再編集時のプレビュー整合性のため）
      $attrs['staticHtml'] = $html;

      // ブロックのattrsとinnerHTMLを更新
      $blocks[$key]['attrs'] = $attrs;
      $blocks[$key]['innerHTML'] = $html;
      $blocks[$key]['innerContent'] = array($html);
      $updated = true;
    }

    // インナーブロックがある場合は再帰的に処理
    if (!empty($block['innerBlocks'])) {
      $blocks[$key]['innerBlocks'] = cocoon_amazon_block_update_blocks_recursive($block['innerBlocks'], $updated);
    }
  }
  return $blocks;
}
endif;

///////////////////////////////////////////
// テーマ非アクティブ化時にCronを解除
///////////////////////////////////////////
add_action('switch_theme', 'cocoon_amazon_block_cron_deactivate');
if ( !function_exists( 'cocoon_amazon_block_cron_deactivate' ) ):
function cocoon_amazon_block_cron_deactivate(){
  $timestamp = wp_next_scheduled('cocoon_amazon_block_update_event');
  if ($timestamp) {
    wp_unschedule_event($timestamp, 'cocoon_amazon_block_update_event');
  }
}
endif;
