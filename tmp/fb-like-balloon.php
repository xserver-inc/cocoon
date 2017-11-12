<?php //フェイスブックページのURLを設定↓
$facebook_page_url = 'https://www.facebook.com/' . get_facebook_follow_id(); ?>
<div class="article-like">
  <div class="article-like-thumb">
    <?php if ( has_post_thumbnail() ): // サムネイルを持っているとき ?>
      <?php the_post_thumbnail( 'thumb100', array('class' => 'article-like-entry-thumnail', 'alt' => '') ); ?>
    <?php else: // サムネイルを持っていない ?>
      <img src="<?php echo get_template_directory_uri();//get_stylesheet_directory_uri();子テーマに画像を置いた場合 ?>/images/no-image.png" alt="NO IMAGE" class="article-like-entry-thumnail no-image" />
    <?php endif; ?>
  </div>
  <div class="article-like-arrow-box">
    <div class="article-like-arrow-box-in">
      <div class="article-like-button">
        <div class="fb-like fb-like-pc" data-href="<?php echo $facebook_page_url; ?>" data-layout="box_count" data-action="like" data-show-faces="false" data-share="false"></div>

        <div class="fb-like fb-like-mobile" data-href="<?php echo $facebook_page_url; ?>" data-layout="button_count" data-action="like" data-show-faces="false" data-share="false"></div>

      </div>
      <div class="article-like-body">
        <?php //メッセージの呼び出し
          echo $_FACEBOOK_PAGE_LIKE_TEXT; ?>
      </div>
    </div>
  </div>
  <div class="clear"></div>
</div>