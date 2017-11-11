<?php //Facebookボックス ?>
<?php if (is_singular()): ?>
  <?php $thumb = get_the_post_thumbnail(get_the_ID(), array(640, 360), array('class' => 'fb-like-thumb-image card-thumb-image', 'alt' => '') ) ?>
  <div class="fb-like-box cf">
    <?php //アイキャッチがある場合
    if ($thumb): ?>
      <figure class="fb-like-thumb">
        <?php echo $thumb; //サムネイルを呼び出す?>
      </figure>
    <?php endif ?>
    <div class="fb-like-content">
      <div class="fb-like-message"><?php _e( $_MESSAGE, THEME_NAME ) ?></div>
      <div class="fb-like-buttons">
        <?php if ($_FACEBOOK_URL): ?>
          <div class="fb-like-facebook">
            <div class="fb-like" data-href="<?php echo $_FACEBOOK_URL; ?>" data-layout="button_count" data-action="like" data-size="large" data-show-faces="true" data-share="false"></div>
            <div id="fb-root"></div>
            <script>(function(d, s, id) {
              var js, fjs = d.getElementsByTagName(s)[0];
              if (d.getElementById(id)) return;
              js = d.createElement(s); js.id = id;
              js.src = 'https://connect.facebook.net/ja_JP/sdk.js#xfbml=1&version=v2.11&appId=569002053185714';
              fjs.parentNode.insertBefore(js, fjs);
            }(document, 'script', 'facebook-jssdk'));</script>
          </div>
        <?php endif ?>
        <?php if ($_TWITTER_ID): ?>
          <div class="fb-like-twitter">
            <a href="https://twitter.com/<?php echo $_TWITTER_ID; ?>?ref_src=twsrc%5Etfw" class="twitter-follow-button" data-show-count="false">Follow @<?php echo $_TWITTER_ID; ?></a><script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>
          </div>
        <?php endif ?>
        <?php if ($_LINE_ID): ?>
          <div class="fb-like-line">
            <div class="line-it-button" style="display: none;" data-lang="ja" data-type="friend" data-lineid="@<?php echo $_LINE_ID; ?>"></div>
  <script src="https://d.line-scdn.net/r/web/social-plugin/js/thirdparty/loader.min.js" async="async" defer="defer"></script>
          </div>
        <?php endif ?>

      </div>
      <div class="fb-like-sub-message"><?php _e( $_SUB_MESSAGE, THEME_NAME ) ?></div>
    </div>
  </div>
<?php endif ?>