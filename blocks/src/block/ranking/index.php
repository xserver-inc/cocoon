<?php

function render_ranking_list($attributes, $content) {
  $code = $attributes['code'];
  return generate_item_ranking_tag($code);
}

if( function_exists('register_block_type_from_metadata')) {
  add_action( 'init', 'register_block_cocoon_ranking', 99 );
  function register_block_cocoon_ranking() {
    register_block_type_from_metadata(
      __DIR__,
      array(
        'render_callback' => 'render_ranking_list',
      )
    );
  }
}