<?php //SNS関係の関数

if ( !function_exists( 'generate_facebook_sdk_code' ) ):
function generate_facebook_sdk_code(){?>
  <!-- <div id="fb-root"></div> -->
  <script><?php echo minify_js("(function(d, s, id) {
      var js, fjs = d.getElementsByTagName(s)[0];
      if (d.getElementById(id)) return;
      js = d.createElement(s); js.id = id;
      js.src = 'https://connect.facebook.net/".__( 'ja_JP', THEME_NAME )."/sdk.js#xfbml=1&version=v2.11';
      fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));"); ?></script>
<?php
}
endif;