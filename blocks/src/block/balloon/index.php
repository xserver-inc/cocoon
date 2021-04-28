<?php

if( function_exists('register_block_type_from_metadata')) {
  add_action( 'init', 'register_block_cocoon_balloon', 99 );
  function register_block_cocoon_balloon() {
    register_block_type_from_metadata(
      __DIR__,
      array()
    );
  }
}
