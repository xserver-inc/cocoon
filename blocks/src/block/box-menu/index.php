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
    $html = ob_get_clean();
    if (is_rest()) {
      $html = add_editor_no_link_click_class($html);
    }
    return $html;
  }
}

if( function_exists('register_block_type')) {
  register_block_type(
    __DIR__,
     array(
      'render_callback' => 'render_box_menu_list',
    )
  );
}

