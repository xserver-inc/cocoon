<?php

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
    if (has_specific_blocks($post_content, array('cocoon-blocks/radar')) || is_admin()) {
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

  // 各ブロックをチェック感じましたか詳しく撮ったりいたします。
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