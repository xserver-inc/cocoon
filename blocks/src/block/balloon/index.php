<?php

if( function_exists('register_block_type')) {
  add_action( 'init', 'register_block_cocoon_balloon', 99 );
  function register_block_cocoon_balloon() {
    register_block_type(
      __DIR__,
      array()
    );
  }
}
