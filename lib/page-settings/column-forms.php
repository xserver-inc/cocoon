<div class="metabox-holder">

<!-- カラム -->
<div id="column" class="postbox">
  <h2 class="hndle"><?php _e( 'カラム設定', THEME_NAME ) ?></h2>
  <div class="inside">

    <p><?php _e( 'メインカラムやサイドバー幅の設定です。', THEME_NAME ) ?></p>

    <p class="preview-label"><?php _e( 'プレビュー', THEME_NAME ) ?></p>
    <div class="demo iframe-standard-demo column-demo">
      <?php
          //iframeからページを呼び出すと以下のPHP警告が出る
          //unlink(/app/public/wp-content/temp-write-test-1512636307): Text file busy
          //原因はよくわからないけど警告なので様子見
       ?>
      <iframe id="column-demo" class="iframe-demo" src="<?php echo home_url(); ?>" width="1000" height="400"></iframe>
    </div>

    <table class="form-table">
      <tbody>

        <!-- コンテンツ幅 -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_MAIN_COLUMN_WIDTH, __('コンテンツ幅', THEME_NAME) ); ?>
          </th>
          <td>
            <?php
            generate_number_tag(OP_MAIN_COLUMN_WIDTH,  get_main_column_width(), 500, 1200, 10);
            generate_tips_tag(__( 'メインカラムのコンテンツ部分の幅を設定します。未入力でデフォルトの800pxになります。', THEME_NAME ));
            ?>
          </td>
        </tr>

        <!-- コンテンツ余白幅 -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_MAIN_COLUMN_PADDING, __('コンテンツ余白幅', THEME_NAME) ); ?>
          </th>
          <td>
            <?php
            generate_number_tag(OP_MAIN_COLUMN_PADDING,  get_main_column_padding(), 10, 100);
            generate_tips_tag(__( 'メインカラムのコンテンツ両側の余白幅を設定します。未入力でデフォルトの29pxになります。', THEME_NAME ));
            ?>
          </td>
        </tr>

      </tbody>
    </table>

  </div>
</div>

</div><!-- /.metabox-holder -->