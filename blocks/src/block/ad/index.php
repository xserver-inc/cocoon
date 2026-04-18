<?php

function render_ad($attributes, $content) {
  $classes = $attributes['classNames'];
  $ad_content = ad_shortcode($attributes);

  ob_start();
  echo '<div class="'.$classes.'">';
  echo $ad_content;
  echo '</div>';
  $html = ob_get_clean();

  if (is_rest()) {
    $html = add_editor_no_link_click_class($html);
  }

  return $html;
}

if( function_exists('register_block_type')) {
  register_block_type(
    __DIR__,
     array(
      'render_callback' => 'render_ad',
    )
  );
}

