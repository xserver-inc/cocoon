<div class="metabox-holder">

<!-- ヘッダー設定 -->
<div id="header" class="postbox">
  <h2 class="hndle"><?php _e( 'ヘッダー設定', THEME_NAME ) ?></h2>
  <div class="inside">

    <p><?php _e( 'ヘッダーの表示設定を行います。', THEME_NAME ) ?></p>
    <p class="preview-label"><?php _e( 'プレビュー', THEME_NAME ) ?></p>
    <div class="demo">
      <?php get_template_part('tmp/header-container'); ?>
    </div>

    <table class="form-table">
      <tbody>

        <!-- ヘッダーロゴ -->
        <tr>
          <th scope="row">
            <?php genelate_label_tag(OP_THE_SITE_LOGO_URL, __( 'ヘッダーロゴ', THEME_NAME ) ); ?>
          </th>
          <td>
            <?php
            genelate_upload_image_tag(OP_THE_SITE_LOGO_URL, get_the_site_logo_url());
            genelate_tips_tag(__( 'ヘッダー部分に表示する画像を設定します。', THEME_NAME ));
            ?>
          </td>
        </tr>

        <!-- ヘッダー背景画像 -->
        <tr>
          <th scope="row">
            <?php genelate_label_tag(OP_HEADER_BACKGROUND_IMAGE_URL, __( 'ヘッダー背景画像', THEME_NAME ) ); ?>
          </th>
          <td>
            <?php
            genelate_upload_image_tag(OP_HEADER_BACKGROUND_IMAGE_URL, get_header_background_image_url());
            genelate_tips_tag(__( 'ヘッダー背景として表示する画像を設定します。', THEME_NAME ));
            ?>
          </td>
        </tr>


      </tbody>
    </table>

  </div>
</div>


</div><!-- /.metabox-holder -->