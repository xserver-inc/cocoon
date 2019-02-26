<?php
function my_plugin_render_block_test_get_id( $attributes, $content ) {
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

register_block_type( 'my-plugin/test-get-id', array(
    'render_callback' => 'my_plugin_render_block_test_get_id',
) );



register_block_type( 'my-plugin/test-get-id-editor', array(
  'render_callback' => function($attributes){
   _v($attributes);
   if ( function_exists( 'get_speech_balloons' ) ){
     $records = get_speech_balloons(null, 'title');
     $ids = array();
     foreach ($records as $record) {
       if ($record->visible) {
        $ids[] = $record->id;
       }
     }
     //_v($ids);
   }
    return implode(',', $ids);
  },
) );





