<?php
function my_plugin_render_block_latest_post_balloon( $attributes, $content ) {
    $recent_posts = wp_get_recent_posts( array(
        'numberposts' => 1,
        'post_status' => 'publish',
    ) );
    if ( count( $recent_posts ) === 0 ) {
        return 'No posts';
    }
    $post = $recent_posts[ 0 ];
    $post_id = $post['ID'];
    return sprintf(
        '<a class="wp-block-my-plugin-latest-post" href="%1$s">%2$s</a>',
        esc_url( get_permalink( $post_id ) ),
        esc_html( get_the_title( $post_id ) )
    );
}

register_block_type( 'cocoon-blocks/balloon', array(
    'render_callback' => 'my_plugin_render_block_latest_post_balloon',
) );



register_block_type( 'cocoon-blocks/balloon-editor', array(
    'render_callback' => function(){return 'latest-post-editor';},
) );





