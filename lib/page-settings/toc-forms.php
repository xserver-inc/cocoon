<div class="metabox-holder">

<!-- 目次 -->
<div id="toc" class="postbox">
  <h2 class="hndle"><?php _e( 'リスト設定', THEME_NAME ) ?></h2>
  <div class="inside">

    <p><?php _e( 'リスト表示の設定です。', THEME_NAME ) ?></p>

    <table class="form-table">
      <tbody>
        <!-- プレビュー画面 -->
        <tr>
          <th scope="row">
            <label><?php _e( 'プレビュー', THEME_NAME ) ?></label>
          </th>
          <td>
            <div class="demo" style="height: 300px;overflow: auto;">
              <?php query_posts(''); ?>
              <?php //echo add_toc_before_1st_h2(get_latest_posts(1)->post_content); ?>
            </div>
          </td>
        </tr>

        <!-- 目次の表示 -->
        <tr>
          <th scope="row">
            <?php genelate_label_tag(OP_TOC_VISIBLE, __('目次の表示', THEME_NAME) ); ?>
          </th>
          <td>
            <?php
            genelate_checkbox_tag(OP_TOC_VISIBLE , is_toc_visible(), __( '目次を表示する', THEME_NAME ));
            genelate_tips_tag(__( '投稿・固定ページの内容から目次を自動付加します。', THEME_NAME ));
            ?>
          </td>
        </tr>


      </tbody>
    </table>

  </div>
</div>

</div><!-- /.metabox-holder -->