<?php

if( function_exists('register_block_type')) {
    add_action( 'init', 'register_block_cocoon_icon_list', 99 );
    function register_block_cocoon_icon_list() {
        register_block_type(
            __DIR__,
            array()
        );
    }
}