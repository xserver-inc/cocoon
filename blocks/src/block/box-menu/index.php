<?php

function render_box_menu_list($attributes, $content) {
  //$id = $attributes['id'];
  $classes = $attributes['classNames'];
  $name = $attributes['boxName'];
  if ($name != '') {
    $atts = array(
      'name' => $name,
    );
    $box_menu_content = get_box_menu_tag($atts);
    ob_start();
    echo '<div class="'.$classes.'">';
    echo $box_menu_content;
    echo '</div>';
    return ob_get_clean();
  }
}

if( function_exists('register_block_type')) {
  register_block_type_from_metadata(
    __DIR__,
     array(
      'render_callback' => 'render_box_menu_list',
    )
  );
}