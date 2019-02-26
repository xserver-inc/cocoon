<?php

register_block_type( 'cocoon-blocks/blank-box-demo', array(
  'attributes' => array(
    'content' => array (
      'type' => 'string'
    )
  ),
  'render_callback' => function($attributes){return 'blank-box-demo';},
) );



register_block_type( 'cocoon-blocks/blank-box-demo-editor', array(
  'attributes' => array(
    'content' => array (
      'type' => 'string'
    )
  ),
  'render_callback' => function($attributes){return 'latest-post-editor';},
) );





