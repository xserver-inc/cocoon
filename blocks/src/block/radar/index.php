<?php

// 同期パターンにブロックが組まれているか
if ( !function_exists( 'has_radar_block_in_patterns' ) ):
function has_block_in_patterns($post_content, $block) {
  // `wp:block {"ref":ID}` のパターンを取得
  preg_match_all('/wp:block\s*\{\s*"ref"\s*:\s*(\d+)\s*\}/', $post_content, $matches);

  if (!empty($matches[1])) {
      foreach ($matches[1] as $pattern_id) {
          // パターンの投稿データを取得
          $pattern_post = get_post($pattern_id);
          if ($pattern_post && isset($pattern_post->post_content) && includes_string($pattern_post->post_content, $block) !== false) {
              return true; // `cocoon-blocks/radar` を含むパターンが見つかった
          }
      }
  }

  return false; // `cocoon-blocks/radar` を含むパターンが見つからなかった
}
endif;

// Chart.jsファイルを読み込む
add_action('enqueue_block_editor_assets', 'enqueue_block_editor_assets_radar');
add_action('wp_enqueue_scripts', 'enqueue_block_editor_assets_radar');
if ( !function_exists( 'enqueue_block_editor_assets_radar' ) ):
function enqueue_block_editor_assets_radar() {
  // 使用例：段落ブロックがあるかどうかをチェック
  $post_id = get_the_ID();
  if ($post_id) {
    $post_content = get_post_field('post_content', $post_id);

    // チャートブロックを利用する公開ページと管理画面だけchart.jsを読み込む
    if (
      // 本文内にレーダーチャートブロックが明確に使われているとき読み込む
      has_specific_blocks($post_content, array('cocoon-blocks/radar'))
      // 管理ページ読み込む
      || is_admin()
      // 同期パターンにも含まれていないか確認する
      || has_block_in_patterns($post_content, 'cocoon-blocks/radar')
    ) {
      // chart.jsをフッターで読み込む
      wp_enqueue_script(
          'chart-js',
          'https://cdn.jsdelivr.net/npm/chart.js',
          array(), // 依存関係
          null,
          true // フッターに追加
      );
    }
  }
}
endif;

// ブロック名は一致するか
if ( !function_exists( 'has_block_type_name' ) ):
function has_block_type_name($block_name, $block_type_names){
  if (is_array($block_type_names)) {
    foreach ($block_type_names as $block_type_name) {
      if ($block_name === $block_type_name) {
        return true;
      }
    }
  } elseif (is_string($block_type_names)) {
    return $block_name === $block_type_names;
  }
  return false;
}
endif;


// 本文中に特定のブロックが使われているか
if ( !function_exists( 'has_specific_blocks' ) ):
function has_specific_blocks($post_content, $block_type_names) {
  // 投稿内容をブロックごとに解析
  $blocks = parse_blocks($post_content);

  // 各ブロックをチェック
  foreach ($blocks as $block) {
    if (isset($block['blockName']) && has_block_type_name($block['blockName'], $block_type_names)) {
      return true; // 特定のブロックが見つかった場合
    } else {
      // innerBlocksのチェック
      if (isset($block['innerBlocks'])) {
        $innerBlocks = $block['innerBlocks'];
        // innerBlocksをチェック
        foreach ($innerBlocks as $block) {
          if (has_block_type_name($block['blockName'], $block_type_names)) {
            return true; // 特定のブロックが見つかった場合
          }
        }
      }
    }
  }
  return false; // 特定のブロックが見つからなかった場合
}
endif;