<?php

function render_ad($attributes, $content) {
  $classes = $attributes['classNames'];
  $ad_content = get_template_part_with_ad_format(get_ad_shortcode_format(), 'ad-shortcode', is_ad_shortcode_label_visible());

  ob_start();
  echo '<div class="'.$classes.'">';
  echo $ad_content;
  echo '</div>';
  return ob_get_clean();
}

if( function_exists('register_block_type')) {
  register_block_type_from_metadata(
    __DIR__,
     array(
      'render_callback' => 'render_ad',
    )
  );
}