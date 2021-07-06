<?php

if( function_exists('register_block_type_from_metadata')) {
    add_action( 'init', 'register_block_cocoon_icon_list', 99 );
    function register_block_cocoon_icon_list() {
        register_block_type_from_metadata(
            __DIR__,
            array()
        );
    }
}