<div class="metabox-holder">

<!-- バックアップ・レストア -->
<div id="backup" class="postbox">
  <h2 class="hndle"><?php _e( 'バックアップ・レストア', THEME_NAME ) ?></h2>
  <div class="inside">

    <p><?php _e( '設定情報のバックアップやレストアを行います。', THEME_NAME ) ?></p>

    <table class="form-table">
      <tbody>

        <!-- バックアップ  -->
        <tr>
          <th scope="row">
            <?php genelate_label_tag('', __( 'バックアップ', THEME_NAME ) ); ?>
          </th>
          <td>
            <a href="<?php echo get_template_directory_uri().'/lib/page-backup/backup-download.php'; ?>" class="button"><?php _e( 'バックアップファイルの取得', THEME_NAME ) ?></a>
          </td>
        </tr>

        <!-- レストア  -->
        <tr>
          <th scope="row">
            <?php genelate_label_tag('', __( 'レストア', THEME_NAME ) ); ?>
          </th>
          <td>
            <form enctype="multipart/form-data" action="" method="POST">
                <!-- MAX_FILE_SIZE は、必ず "file" input フィールドより前になければなりません -->
                <input type="hidden" name="MAX_FILE_SIZE" value="30000" />
                <!-- input 要素の name 属性の値が、$_FILES 配列のキーになります -->
                このファイルをアップロード: <input name="settings" type="file" /><br>
                <input type="submit" class="button" value="ファイルを送信" />
                <input type="hidden" name="<?php echo HIDDEN_FIELD_NAME; ?>" value="Y">
            </form>
          </td>
        </tr>

      </tbody>
    </table>

  </div>
</div>

</div><!-- /.metabox-holder -->