<?php //楽天商品リンクブロック 定期自動更新（WP-Cron）
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

///////////////////////////////////////////
// Cronスケジュールの管理
// ※ カスタムスケジュール（cocoon_biweekly, cocoon_monthly, cocoon_quarterly）は
//    Amazon版ブロックのCronファイルで既に追加済みのため再定義しない
///////////////////////////////////////////
add_action('init', 'cocoon_rakuten_block_cron_manage');
if ( !function_exists( 'cocoon_rakuten_block_cron_manage' ) ):
function cocoon_rakuten_block_cron_manage(){
  $event_hook = 'cocoon_rakuten_block_update_event';
  // 自動更新が無効な場合はスケジュールを解除
  if (!get_theme_option('rakuten_block_auto_update_enable', false)) {
    $timestamp = wp_next_scheduled($event_hook);
    if ($timestamp) {
      wp_unschedule_event($timestamp, $event_hook);
    }
    return;
  }
  // 更新間隔の取得（デフォルト: 1ヶ月）
  $interval = get_theme_option('rakuten_block_auto_update_interval', 'cocoon_monthly');
  // まだスケジュールされていない場合は登録
  if (!wp_next_scheduled($event_hook)) {
    wp_schedule_event(time(), $interval, $event_hook);
  }
}
endif;

///////////////////////////////////////////
// Cronイベントハンドラ（バッチ処理）
///////////////////////////////////////////
add_action('cocoon_rakuten_block_update_event', 'cocoon_rakuten_block_batch_update');
if ( !function_exists( 'cocoon_rakuten_block_batch_update' ) ):
function cocoon_rakuten_block_batch_update(){
  // 自動更新がOFFなら処理しない
  if (!get_theme_option('rakuten_block_auto_update_enable', false)) {
    return;
  }

  // 1回あたりの処理件数（デフォルト: 5投稿）
  $batch_size = (int)get_theme_option('rakuten_block_auto_update_batch_size', 5);
  if ($batch_size < 1) $batch_size = 5;

  // 前回の処理位置を取得
  $last_processed_id = (int)get_option('cocoon_rakuten_block_last_processed_id', 0);

  // 楽天商品リンクブロックを含む投稿を検索
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
    '%<!-- wp:cocoon-blocks/rakuten-product-link%',
    $last_processed_id,
    $batch_size
  ));

  // 結果が空の場合はリスタート
  if (empty($posts)) {
    update_option('cocoon_rakuten_block_last_processed_id', 0);
    return;
  }

  // 投稿ごとに処理
  foreach ($posts as $post) {
    cocoon_rakuten_block_update_post_blocks($post);
    // 処理位置を更新
    update_option('cocoon_rakuten_block_last_processed_id', $post->ID);
    // APIレート制限対策のスリープ
    sleep(1);
  }
}
endif;

///////////////////////////////////////////
// 投稿内の楽天商品リンクブロックを更新
///////////////////////////////////////////
if ( !function_exists( 'cocoon_rakuten_block_update_post_blocks' ) ):
function cocoon_rakuten_block_update_post_blocks($post){
  $content = $post->post_content;
  $blocks = parse_blocks($content);
  // ブロックが見つからない場合は処理しない
  if (empty($blocks)) return;

  $updated = false;
  // ブロックを再帰的に処理
  $blocks = cocoon_rakuten_block_update_blocks_recursive($blocks, $updated);

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
}
endif;

///////////////////////////////////////////
// ブロック配列を再帰的に更新
///////////////////////////////////////////
if ( !function_exists( 'cocoon_rakuten_block_update_blocks_recursive' ) ):
function cocoon_rakuten_block_update_blocks_recursive($blocks, &$updated){
  foreach ($blocks as $key => $block) {
    // 楽天商品リンクブロックの場合
    if ($block['blockName'] === 'cocoon-blocks/rakuten-product-link') {
      $attrs = $block['attrs'];
      $itemCode = isset($attrs['itemCode']) ? $attrs['itemCode'] : '';

      if (empty($itemCode)) continue;

      // 楽天APIで商品情報を再取得
      $Item = cocoon_rakuten_block_fetch_item($itemCode);
      if (is_wp_error($Item)) continue;

      $itemData = cocoon_rakuten_block_extract_item_data($Item);

      // カスタムタイトル・カスタム説明文はユーザー設定を維持
      $customTitle = isset($attrs['customTitle']) ? $attrs['customTitle'] : '';
      $customDescription = isset($attrs['customDescription']) ? $attrs['customDescription'] : '';

      // 属性を更新（APIから取得した値で上書き）
      $attrs['title']            = $itemData['title'];
      $attrs['shopName']         = $itemData['shopName'];
      $attrs['shopCode']         = $itemData['shopCode'];
      $attrs['itemPrice']        = $itemData['itemPrice'];
      $attrs['itemCaption']      = $itemData['itemCaption'];
      $attrs['affiliateUrl']     = $itemData['affiliateUrl'];
      $attrs['affiliateRate']    = $itemData['affiliateRate'];
      $attrs['imageSmallUrl']    = $itemData['imageSmallUrl'];
      $attrs['imageSmallWidth']  = $itemData['imageSmallWidth'];
      $attrs['imageSmallHeight'] = $itemData['imageSmallHeight'];
      $attrs['imageUrl']         = $itemData['imageUrl'];
      $attrs['imageWidth']       = $itemData['imageWidth'];
      $attrs['imageHeight']      = $itemData['imageHeight'];

      // 設定を組み立てて静的HTMLを再生成
      $settings = array(
        'size'              => isset($attrs['size']) ? $attrs['size'] : 'm',
        'displayMode'       => isset($attrs['displayMode']) ? $attrs['displayMode'] : 'normal',
        'showPrice'         => isset($attrs['showPrice']) ? (bool)$attrs['showPrice'] : true,
        'showDescription'   => isset($attrs['showDescription']) ? (bool)$attrs['showDescription'] : false,
        'showLogo'          => isset($attrs['showLogo']) ? (bool)$attrs['showLogo'] : true,
        'showBorder'        => isset($attrs['showBorder']) ? (bool)$attrs['showBorder'] : true,
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
      $html = cocoon_rakuten_block_generate_static_html($Item, $itemCode, $settings);

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
      $blocks[$key]['innerBlocks'] = cocoon_rakuten_block_update_blocks_recursive($block['innerBlocks'], $updated);
    }
  }
  return $blocks;
}
endif;

///////////////////////////////////////////
// テーマ非アクティブ化時にCronを解除
///////////////////////////////////////////
add_action('switch_theme', 'cocoon_rakuten_block_cron_deactivate');
if ( !function_exists( 'cocoon_rakuten_block_cron_deactivate' ) ):
function cocoon_rakuten_block_cron_deactivate(){
  $timestamp = wp_next_scheduled('cocoon_rakuten_block_update_event');
  if ($timestamp) {
    wp_unschedule_event($timestamp, 'cocoon_rakuten_block_update_event');
  }
}
endif;
