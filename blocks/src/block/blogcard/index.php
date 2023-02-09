<?php

if( function_exists('register_block_type')) {
  add_action( 'init', 'register_block_cocoon_blog_card', 99 );
  function register_block_cocoon_blog_card() {
    register_block_type(
      __DIR__,
      array()
    );
  }
}
