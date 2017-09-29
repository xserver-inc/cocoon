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
            ?>

          </td>
        </tr>


      </tbody>
    </table>

  </div>
</div>


</div><!-- /.metabox-holder -->