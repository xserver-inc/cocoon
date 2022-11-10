<?php

function render_ranking_list($attributes, $content) {
  $id = $attributes['id'];

  //返り値がないechoとかのHTML出力結果を取得する
  ob_start();
  generate_item_ranking_tag($id);
  $ranking_html = ob_get_clean();

  return $ranking_html;
}

if( function_exists('register_block_type')) {
  register_block_type( 'cocoon-blocks/ranking', array(
    'attributes' => array(
      'id' => array (
        'type' => 'string',
        'default' => '-1',
      )
    ),
    'render_callback' => 'render_ranking_list',
  ) );
}