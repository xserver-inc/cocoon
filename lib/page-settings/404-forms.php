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

        <!-- 404ページタイトル -->
        <tr>
          <th scope="row">
            <?php genelate_label_tag(OP_404_PAGE_TITLE, __('404ページタイトル', THEME_NAME) ); ?>
          </th>
          <td>
            <?php
            genelate_textbox_tag(OP_404_PAGE_TITLE, get_404_page_title(), __( '404 NOT FOUND', THEME_NAME ));
            genelate_tips_tag(__( '404ページに表示するタイトルを入力します。', THEME_NAME ));
            ?>
          </td>
        </tr>

        <!-- 404ページメッセージ -->
        <tr>
          <th scope="row">
            <?php genelate_label_tag(OP_404_PAGE_MESSAGE, __('404ページメッセージ', THEME_NAME) ); ?>
          </th>
          <td>
            <?php
            genelate_textbox_tag(OP_404_PAGE_MESSAGE, get_404_page_message(), __( 'お探しのページは見つかりませんでした。', THEME_NAME ));
            genelate_tips_tag(__( '404ページに表示するメッセージを入力します。', THEME_NAME ));
            ?>
          </td>
        </tr>


      </tbody>
    </table>

  </div>
</div>

</div><!-- /.metabox-holder -->