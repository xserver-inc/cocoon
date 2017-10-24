<?php
//////////////////////////////////
// トップへ戻るボタンのテンプレート
//////////////////////////////////
if ( is_go_to_top_button_visible()  ): //トップへ戻るボタンを表示するか?>
<div id="go-to-top" class="go-to-top">
  <?php if ( get_go_to_top_button_image_url() ): //カスタマイザーでトップへ戻る画像が指定されている時 ?>
    <a class="go-to-top-button go-to-top-button-image"><img src="<?php echo get_go_to_top_button_image_url(); ?>" alt="<?php _e( 'トップへ戻る', THEME_NAME ) ?>"></a>
  <?php else: ?>
    <a class="go-to-top-button go-to-top-button-icon-font"><span class="fa <?php echo get_go_to_top_button_icon_font(); //Font Awesomeアイコンフォントの取得 ?>"></span></a>
  <?php endif ?>
</div>
<?php endif; ?>