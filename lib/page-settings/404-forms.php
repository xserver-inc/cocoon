<div class="metabox-holder">

<!-- 404ページ -->
<div id="page-404" class="postbox">
  <h2 class="hndle"><?php _e( '404ページ設定', THEME_NAME ) ?></h2>
  <div class="inside">

    <p><?php _e( 'ページが見つからなかった場合の404ページの表示設定です。', THEME_NAME ) ?></p>

    <table class="form-table">
      <tbody>

        <!-- 404ページ画像 -->
        <tr>
          <th scope="row">
            <?php genelate_label_tag(OP_404_IMAGE_URL, __('404ページ画像', THEME_NAME) ); ?>
          </th>
          <td>
            <?php
            genelate_upload_image_tag(OP_404_IMAGE_URL, get_404_image_url());
            genelate_tips_tag(__( '404ページで表示する画像を選択してください。', THEME_NAME ));
            ?>
          </td>
        </tr>

      </tbody>
    </table>

  </div>
</div>

</div><!-- /.metabox-holder -->