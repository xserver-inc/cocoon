<?php //Pinterestシェア用のスクリプト
if (is_pinterest_share_button_visible() && is_singular()): ?>
<script async defer data-pin-height="28" data-pin-hover="true" src="//assets.pinterest.com/js/pinit.js"></script>
<?php endif ?>
