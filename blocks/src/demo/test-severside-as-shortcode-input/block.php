<?php
register_block_type( 'my-plugin/test-severside-as-shortcode-input', array(
  'attributes' => array(
    'content' => array (
      'type' => 'string'
    )
  ),
  'render_callback' => 'my_textbox_text2',
) );

function my_textbox_text2($attributes){
  $html  = '';
  $html .= '<div class="my_textbox">';
  $html .= '<p>' . esc_html($attributes['content']) . '</p>';
  $html .= '</div>';
  return $html;
}



