<div class="metabox-holder">

<!-- トップへ戻るボタン -->
<div id="toc" class="postbox">
  <h2 class="hndle"><?php _e( 'トップへ戻るボタン設定', THEME_NAME ) ?></h2>
  <div class="inside">

    <p><?php _e( 'ページトップにスクロール移動するかボタンの設定です。。', THEME_NAME ) ?></p>

    <table class="form-table">
      <tbody>
        <!-- プレビュー画面 -->
        <tr>
          <th scope="row">
            <label><?php _e( 'プレビュー', THEME_NAME ) ?></label>
          </th>
          <td>
            <div class="demo go-to-top" style="">
              <?php //get_template_part('tmp/content') ?>
            </div>
            <?php genelate_tips_tag(__( 'デモは動作しません。', THEME_NAME )); ?>
          </td>
        </tr>

        <!-- トップへ戻るボタンの表示 -->
        <tr>
          <th scope="row">
            <?php genelate_label_tag(OP_GO_TO_TOP_BUTTON_VISIBLE, __('トップへ戻るボタンの表示', THEME_NAME) ); ?>
          </th>
          <td>
            <?php
            genelate_checkbox_tag(OP_GO_TO_TOP_BUTTON_VISIBLE , is_go_to_top_button_visible(), __( 'トップへ戻るボタンを表示する', THEME_NAME ));
            genelate_tips_tag(__( 'トップへスクロール移動するボタンを表示するかどうか。', THEME_NAME ));
            ?>
          </td>
        </tr>

        <!-- ボタンのアイコンフォント -->
        <tr>
          <th scope="row">
            <?php genelate_label_tag(OP_GO_TO_TOP_BUTTON_ICON_FONT, __('ボタンのアイコンフォント', THEME_NAME) ); ?>
          </th>
          <td>
            <?php
            $options = array(
              'fa-angle-double-up' => __( 'fa-angle-double-up', THEME_NAME ),
              'fa-angle-up' => __( 'fa-angle-up', THEME_NAME ),
              'fa-arrow-circle-up' => __( 'fa-arrow-circle-up', THEME_NAME ),
              'fa-arrow-up' => __( 'fa-arrow-up', THEME_NAME ),
              'fa-caret-up' => __( 'fa-caret-up', THEME_NAME ),
              'fa-caret-square-o-up' => __( 'fa-caret-square-o-up', THEME_NAME ),
              'fa-chevron-circle-up' => __( 'fa-chevron-circle-up', THEME_NAME ),
              'fa-chevron-up' => __( 'fa-chevron-up', THEME_NAME ),
              'fa-hand-o-up' => __( 'fa-hand-o-up', THEME_NAME ),
              'fa-long-arrow-up' => __( 'fa-long-arrow-up', THEME_NAME ),
              'fa-caret-square-o-up' => __( 'fa-caret-square-o-up', THEME_NAME ),
            );
            genelate_selectbox_tag(OP_GO_TO_TOP_BUTTON_ICON_FONT, $options, get_go_to_top_button_icon_font(), true);
            genelate_tips_tag(__( 'トップへ戻るボタンを示すアイコンフォントを選択します。', THEME_NAME ));
            ?>
          </td>
        </tr>

        <!-- ボタン画像 -->
        <tr>
          <th scope="row">
            <?php genelate_label_tag(OP_GO_TO_TOP_BUTTON_IMAGE_URL, __('ボタン画像', THEME_NAME) ); ?>
          </th>
          <td>
            <?php
            genelate_upload_image_tag(OP_GO_TO_TOP_BUTTON_IMAGE_URL, get_go_to_top_button_image_url());
            genelate_tips_tag(__( 'トップへ戻るボタンのアイコンフォント代わりに表示する画像を選択します。こちらに画像を設定するとアイコンフォントボタンは表示されません。最大横幅は120pxになります。', THEME_NAME ));
            ?>
          </td>
        </tr>

      </tbody>
    </table>

  </div>
</div>

</div><!-- /.metabox-holder -->