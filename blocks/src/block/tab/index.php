<?php
if( !function_exists('wp_enqueue_tab_frontend_script')) :
function wp_enqueue_tab_frontend_script() {
  wp_enqueue_script(
    'tab-frontend',
    get_template_directory_uri() . '/blocks/src/block/tab/tab-frontend.js',
    array('wp-blocks'),
    null,
    false
  );
}
endif;

if( function_exists('register_block_type')) {
  add_action( 'init', 'register_block_cocoon_tab', 99 );
  function register_block_cocoon_tab() {
    register_block_type(
      __DIR__, array(
        'render_callback' => function( $attributes, $content) {
          if (!is_admin()) {
            wp_enqueue_tab_frontend_script();

            return $content;
          }
        }
      )
    );
  }
}