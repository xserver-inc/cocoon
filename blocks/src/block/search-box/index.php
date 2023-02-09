<?php

if( function_exists('register_block_type')) {
  add_action( 'init', 'register_block_cocoon_search_box', 99 );
  function register_block_cocoon_search_box() {
    register_block_type(
      __DIR__,
      array()
    );
  }
}