<?php
////////////////////////////
//メインカラム追従領域
////////////////////////////
if ( is_active_sidebar( 'main-scroll' ) ) : ?>
<div id="main-scroll" class="main-scroll">
  <?php dynamic_sidebar( 'main-scroll' ); ?>
</div>
<?php endif; ?>