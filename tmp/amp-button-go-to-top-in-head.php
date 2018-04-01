<?php
//////////////////////////////////
// トップへ戻るボタンのAMP用script呼び出し
//////////////////////////////////
if ( is_go_to_top_button_visible() || is_mobile_button_layout_type_slide_in() ): //トップへ戻るボタンを表示するか?>
  <script async custom-element="amp-animation" src="https://cdn.ampproject.org/v0/amp-animation-0.1.js"></script>
  <script async custom-element="amp-position-observer" src="https://cdn.ampproject.org/v0/amp-position-observer-0.1.js"></script>
<?php endif; ?>