<?php

function render_cta($attributes, $content) {
  $classes = $attributes['classNames'];
  $message = $attributes['message'];

  $atts = [
    'heading' => $attributes['header'],
    'image_url' => $attributes['image'],
    'layout' => $attributes['layout'],
    'filter' => $attributes['autoParagraph'] ? 1 : 0,
    'button_text' => $attributes['buttonText'],
    'button_url' => $attributes['buttonURL'],
    'button_color' => $attributes['buttonColor'],
  ];

  $html = get_cta_tag($atts, $message);

  if (is_rest()) {
    $html = add_editor_no_link_click_class($html);
  }

  return $html;
}

if( function_exists('register_block_type')) {
  register_block_type(
    __DIR__,
     array(
      'render_callback' => 'render_cta',
    )
  );
}

