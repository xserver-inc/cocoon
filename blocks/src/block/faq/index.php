<?php

if( function_exists('register_block_type')) {
  add_action( 'init', 'register_block_cocoon_faq', 99 );
  function register_block_cocoon_faq() {
    register_block_type(
      __DIR__,
      array()
    );
  }
}
