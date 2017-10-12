<div class="metabox-holder">

<!-- 関連記事 -->
<div id="single-page" class="postbox">
  <h2 class="hndle"><?php _e( '関連記事設定', THEME_NAME ) ?></h2>
  <div class="inside">

    <p><?php _e( '関連記事の表示に関する設定です。', THEME_NAME ) ?></p>

    <table class="form-table">
      <tbody>
        <!-- プレビュー画面 -->
        <tr>
          <th scope="row">
            <label><?php _e( 'プレビュー', THEME_NAME ) ?></label>
          </th>
          <td>
            <div class="demo" style="height: 300px;overflow: auto;">
              <?php get_template_part('tmp/related-entries') ?>
            </div>
            <?php genelate_tips_tag(__( 'デモの関連記事はランダムです。', THEME_NAME )); ?>
          </td>
        </tr>

        <!-- 表示 -->
        <tr>
          <th scope="row">
            <?php genelate_label_tag(OP_RELATED_ENTRIES_VISIBLE, __('表示', THEME_NAME) ); ?>
          </th>
          <td>
            <?php
            genelate_checkbox_tag(OP_RELATED_ENTRIES_VISIBLE , is_related_entries_visible(), __( '関連記事を表示する', THEME_NAME ));
            genelate_tips_tag(__( '投稿ページの関連記事を表示するか。', THEME_NAME ));
            ?>
          </td>
        </tr>

        <!-- 表示タイプ -->
        <tr>
          <th scope="row">
            <?php genelate_label_tag(OP_RELATED_ENTRY_TYPE, __('表示タイプ', THEME_NAME) ); ?>
          </th>
          <td>
            <?php
            $options = array(
              'entry_card' => __( 'エントリーカード', THEME_NAME ),
              'mini_card' => __( 'ミニカード', THEME_NAME ),
              'vartical_card' => __( '縦型カード', THEME_NAME ),
            );
            genelate_radiobox_tag(OP_RELATED_ENTRY_TYPE, $options, get_related_entry_type());
            genelate_tips_tag(__( '関連記事の表示タイプを選択します。', THEME_NAME ));
            ?>
          </td>
        </tr>

        <!-- 表示数 -->
        <tr>
          <th scope="row">
            <?php genelate_label_tag(OP_RELATED_ENTRY_COUNT, __('表示数', THEME_NAME) ); ?>
          </th>
          <td>
            <?php
            genelate_number_tag(OP_RELATED_ENTRY_COUNT,  get_related_entry_count(), 2, 30);
            genelate_tips_tag(__( '関連記事で表示する投稿数の設定です。（最小：2、最大：30）', THEME_NAME ));
            ?>
          </td>
        </tr>

      </tbody>
    </table>

  </div>
</div>

</div><!-- /.metabox-holder -->