<?php
register_block_type( 'my-plugin/test-as-shortcode-text', array(
  'attributes' => array(
    'content' => array (
      'type' => 'string'
    )
  ),
  'render_callback' => 'my_textbox_text',
) );

function my_textbox_text($attributes){
    //_v($attributes);
  $html  = '';
  $html .= '<div class="my_textbox">';
  $html .= '<p>' . esc_html($attributes['content']) . '</p>';
  $html .= '</div>';
  return $html;
}



