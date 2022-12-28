<?php

function render_profile($attributes, $content) {
  $id = $attributes['id'];
  $classes = $attributes['classNames'];
  $label = $attributes['label'];
  $isImageCircle = $attributes['isImageCircle'] ? 1 : 0;

  if ($id) {
    ob_start();
    echo '<div class="'.$classes.'">';
    generate_author_box_tag($id, $label, $isImageCircle);
    echo '</div>';
    return ob_get_clean();
  }
}

if( function_exists('register_block_type')) {
  register_block_type_from_metadata(
    __DIR__,
     array(
      'render_callback' => 'render_profile',
    )
  );
}