<?php

if( function_exists('register_block_type')) {
  add_action( 'init', 'register_block_cocoon_timeline', 99 );
  function register_block_cocoon_timeline() {
    register_block_type(
      __DIR__,
      array()
    );
  }
}

