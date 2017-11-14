<div class="metabox-holder">

<!-- コメント -->
<div id="page-page" class="postbox">
  <h2 class="hndle"><?php _e( 'コメント設定', THEME_NAME ) ?></h2>
  <div class="inside">

    <p><?php _e( '固定ページのコメント表示設定です。', THEME_NAME ) ?></p>

    <table class="form-table">
      <tbody>
        <!-- 表示 -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_PAGE_COMMENT_VISIBLE, __('表示', THEME_NAME) ); ?>
          </th>
          <td>
            <?php
            generate_checkbox_tag(OP_PAGE_COMMENT_VISIBLE , is_page_comment_visible(), __( 'コメントを表示する', THEME_NAME ));
            generate_tips_tag(__( '固定ページページにコメントを表示するか。', THEME_NAME ));
            ?>
          </td>
        </tr>

      </tbody>
    </table>

  </div>
</div>

<!-- パンくずリスト -->
<div id="page-page" class="postbox">
  <h2 class="hndle"><?php _e( 'パンくずリスト設定', THEME_NAME ) ?></h2>
  <div class="inside">

    <p><?php _e( '固定ページのパンくずリスト設定です。', THEME_NAME ) ?></p>

    <table class="form-table">
      <tbody>
        <!-- 表示 -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_PAGE_BREADCRUMBS_POSITION, __('パンくずリストの配置', THEME_NAME) ); ?>
          </th>
          <td>
            <?php
            $options = array(
              'none' => __( '表示しない', THEME_NAME ),
              'main_before' => __( 'メインカラム手前', THEME_NAME ),
              'main_top' => __( 'メインカラムトップ', THEME_NAME ),
              'main_bottom' => __( 'メインカラムボトム（デフォルト）', THEME_NAME ),
              'footer_before' => __( 'フッター手前', THEME_NAME ),
            );
            generate_radiobox_tag(OP_PAGE_BREADCRUMBS_POSITION, $options, get_page_breadcrumbs_position());
            generate_tips_tag(__( '固定ページのパンくずリスト表示位置を設定します。', THEME_NAME ));
            ?>
          </td>
        </tr>

      </tbody>
    </table>

  </div>
</div>

</div><!-- /.metabox-holder -->