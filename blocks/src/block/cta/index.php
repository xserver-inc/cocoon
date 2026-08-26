<?php

function render_cta($attributes, $content) {
  $attributes = shortcode_atts(
    array(
      'header' => '',
      'image' => '',
      'layout' => 'cta-left-and-right',
      'message' => '',
      'autoParagraph' => true,
      'buttonText' => '',
      'buttonURL' => '',
      'buttonColor' => 'btn-red',
    ),
    is_array($attributes) ? $attributes : array(),
    'cocoon-blocks/cta'
  );

  $message = is_scalar($attributes['message']) ? (string)$attributes['message'] : '';
  $layout = is_scalar($attributes['layout']) ? trim((string)$attributes['layout']) : 'cta-left-and-right';
  $button_color = is_scalar($attributes['buttonColor']) ? trim((string)$attributes['buttonColor']) : 'btn-red';

  // ブロックで選べるデザインだけを受け入れるための許可リスト検証
  if ( !in_array($layout, get_cta_allowed_layout_classes(), true) ) {
    $layout = 'cta-left-and-right';
  }
  if ( !in_array($button_color, get_cta_allowed_button_color_classes(), true) ) {
    $button_color = 'btn-red';
  }

  $atts = [
    'heading' => $attributes['header'],
    'image_url' => $attributes['image'],
    'layout' => $layout,
    'filter' => $attributes['autoParagraph'] ? 1 : 0,
    'button_text' => $attributes['buttonText'],
    'button_url' => $attributes['buttonURL'],
    'button_color' => $button_color,
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

