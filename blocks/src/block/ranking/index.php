<?php

function render_ranking_list($attributes, $content) {
  $id = $attributes['id'];
  $classes = $attributes['classNames'];
  if ($id) {
    //返り値がないechoとかのHTML出力結果を取得する
    ob_start();
    echo '<div class="'.$classes.'">';
    generate_item_ranking_tag($id);
    echo '</div>';
    $html = ob_get_clean();
    return change_fa( $html );
  }
}

if( function_exists('register_block_type')) {
  register_block_type(
    __DIR__,
     array(
      'render_callback' => 'render_ranking_list',
    )
  );
}
