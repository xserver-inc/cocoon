<?php //楽天商品リンクブロック 定期自動更新（WP-Cron）
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

///////////////////////////////////////////
// Cronスケジュールの管理（admin_initで毎リクエストのDB照会を回避）
// ※ カスタムスケジュール（cocoon_every_2_days, cocoon_every_3_days 等）は
//    Amazon版ブロックのCronファイルで既に追加済みのため再定義しない
///////////////////////////////////////////
add_action('admin_init', 'cocoon_rakuten_block_cron_manage');
if ( !function_exists( 'cocoon_rakuten_block_cron_manage' ) ):
function cocoon_rakuten_block_cron_manage(){
  $event_hook = 'cocoon_rakuten_block_update_event';
  // 自動更新が無効な場合はスケジュールを解除
  if (!is_product_block_auto_update_enable()) {
    $timestamp = wp_next_scheduled($event_hook);
    if ($timestamp) {
      wp_unschedule_event($timestamp, $event_hook);
    }
    return;
  }
  // 更新間隔の取得（デフォルト: 3日ごと）
  $interval = get_product_block_auto_update_interval();
  // Amazon版Cronとの同時発火を避けるためバッチサイズに応じたオフセットを設ける
  // 1投稿あたり post_sleep + API_sleep × 平均ブロック数 を余裕込みで約10秒と見積もる
  $batch_size = get_product_block_auto_update_batch_size();
  $schedule_offset = $batch_size * 10;
  // 前回スケジュール時のバッチサイズを保持し、変更を検知する
  $last_batch_size = (int)get_option('cocoon_rakuten_block_cron_batch_size', 0);
  $needs_reschedule = ($last_batch_size !== $batch_size);
  $timestamp = wp_next_scheduled($event_hook);
  if ($timestamp) {
    $current_schedule = wp_get_schedule($event_hook);
    // 更新間隔またはバッチサイズが変わった場合は再スケジュール
    if ($current_schedule !== $interval || $needs_reschedule) {
      wp_unschedule_event($timestamp, $event_hook);
      $scheduled = wp_schedule_event(time() + $schedule_offset, $interval, $event_hook);
      if ($scheduled !== false && !is_wp_error($scheduled)) {
        update_option('cocoon_rakuten_block_cron_batch_size', $batch_size, false);
      } else {
        cocoon_product_block_debug_log('cron: schedule_event failed', 'RakutenCron');
      }
    }
  } else {
    $scheduled = wp_schedule_event(time() + $schedule_offset, $interval, $event_hook);
    if ($scheduled !== false && !is_wp_error($scheduled)) {
      update_option('cocoon_rakuten_block_cron_batch_size', $batch_size, false);
    } else {
      cocoon_product_block_debug_log('cron: schedule_event failed', 'RakutenCron');
    }
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
  if (!is_product_block_auto_update_enable()) {
    return;
  }

  // 多重起動防止ロック（トランジェント／永続キャッシュのいずれか一方のみ使用し二重設定を避ける）
  $lock_key = 'cocoon_rakuten_block_cron_running';
  $use_ext_cache = wp_using_ext_object_cache();
  if ($use_ext_cache) {
    if (wp_cache_get($lock_key)) {
      cocoon_product_block_debug_log('cron: skipped (already running)', 'RakutenCron');
      return;
    }
    $added = wp_cache_add($lock_key, 1, '', HOUR_IN_SECONDS);
    if (!$added) {
      cocoon_product_block_debug_log('cron: skipped (already running)', 'RakutenCron');
      return;
    }
  } else {
    if (get_transient($lock_key)) {
      cocoon_product_block_debug_log('cron: skipped (already running)', 'RakutenCron');
      return;
    }
    set_transient($lock_key, 1, HOUR_IN_SECONDS);
  }

  // Fatal Error時にもロックを解放する
  $lock_released = false;
  register_shutdown_function(function() use ($lock_key, &$lock_released, $use_ext_cache) {
    if ($lock_released) return;
    if ($use_ext_cache) {
      wp_cache_delete($lock_key);
    } else {
      delete_transient($lock_key);
    }
  });

  // Cronバックグラウンド処理のためタイムアウトを無制限化
  @set_time_limit(0);

  // 1回あたりの処理件数（共通設定から取得）
  $batch_size = get_product_block_auto_update_batch_size();
  if ($batch_size < 1) $batch_size = PRODUCT_BLOCK_AUTO_UPDATE_BATCH_SIZE_DEFAULT;

  // 前回の処理位置を取得
  $last_processed_id = (int)get_option('cocoon_rakuten_block_last_processed_id', 0);

  // 楽天商品リンクブロックを含む投稿を検索
  global $wpdb;
  $posts = $wpdb->get_results($wpdb->prepare(
    "SELECT ID, post_content, post_modified, post_modified_gmt
     FROM `{$wpdb->posts}`
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
    cocoon_product_block_debug_log('cron: all posts processed, resetting', 'RakutenCron');
    if ($use_ext_cache) {
      wp_cache_delete($lock_key);
    } else {
      delete_transient($lock_key);
    }
    $lock_released = true;
    return;
  }

  // 投稿ごとに処理（ループ変数 $post はループ外で参照すると危険なため別変数 $last_id に保持する）
  $last_id = 0;
  foreach ($posts as $post) {
    cocoon_rakuten_block_update_post_blocks($post);
    // 処理位置を更新
    $last_id = $post->ID;
    update_option('cocoon_rakuten_block_last_processed_id', $last_id);
    // 投稿間のスリープ
    sleep(PRODUCT_BLOCK_CRON_POST_SLEEP_SECONDS);
  }

  // ループ変数ではなく明示的な変数でログ出力
  cocoon_product_block_debug_log('cron: batch completed, last_id=' . $last_id, 'RakutenCron');

  // ロック解放
  if ($use_ext_cache) {
    wp_cache_delete($lock_key);
  } else {
    delete_transient($lock_key);
  }
  $lock_released = true;
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

  // 投稿を更新（成功時は投稿ID、失敗時は0またはWP_Errorを返す）
  $result = wp_update_post(array(
    'ID'           => $post->ID,
    'post_content' => wp_slash($new_content),
  ));

  // 更新失敗時はログを残して抜ける（更新日時復元・キャッシュクリアは行わない）
  if (!$result || is_wp_error($result)) {
    cocoon_product_block_debug_log('cron: post '.$post->ID.' update failed', 'RakutenCron');
    return;
  }

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
      // APIレート制限対策：1ブロックのAPIリクエストごとに待機
      sleep(PRODUCT_BLOCK_CRON_API_SLEEP_SECONDS);
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
