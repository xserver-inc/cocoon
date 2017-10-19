<div class="metabox-holder">

<!-- バックアップ・レストア -->
<div id="backup" class="postbox">
  <h2 class="hndle"><?php _e( 'バックアップ・レストア', THEME_NAME ) ?></h2>
  <div class="inside">

    <p><?php _e( '設定情報のバックアップやレストアを行います。', THEME_NAME ) ?></p>

    <table class="form-table">
      <tbody>

        <!-- リセット  -->
        <tr>
          <th scope="row">
            <?php genelate_label_tag('', __( 'バックアップ', THEME_NAME ) ); ?>
          </th>
          <td>
            <a href="<?php echo get_template_directory_uri().'/lib/original-pages/download-settings.php'; ?>" class="button"><?php _e( 'バックアップファイルの取得', THEME_NAME ) ?></a>
          </td>
        </tr>

      </tbody>
    </table>

  </div>
</div>

</div><!-- /.metabox-holder -->