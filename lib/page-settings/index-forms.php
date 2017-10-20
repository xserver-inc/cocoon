<div class="metabox-holder">

<!-- リスト -->
<div id="single-page" class="postbox">
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
              <?php get_template_part('tmp/list') ?>
            </div>
          </td>
        </tr>

        <!-- カードタイプ -->
        <tr>
          <th scope="row">
            <?php genelate_label_tag(OP_ENTRY_CARD_TYPE, __('カードタイプ', THEME_NAME) ); ?>
          </th>
          <td>
            <?php
            $options = array(
              'entry_card' => __( 'エントリーカード（デフォルト）', THEME_NAME ),
              'vertical_card_2' => __( '縦型カード2列（推奨：表示数偶数）', THEME_NAME ),
              'vertical_card_3' => __( '縦型カード3列（推奨：3, 6, 9, 12, ...）', THEME_NAME ),
            );
            genelate_radiobox_tag(OP_ENTRY_CARD_TYPE, $options, get_entry_card_type());
            genelate_tips_tag(__( '一覧リストのカード表示を変更します。カード表示数を変更するには、「設定→1ページに表示する最大投稿数」から変更してください。', THEME_NAME ));
            ?>
          </td>
        </tr>


      </tbody>
    </table>

  </div>
</div>

</div><!-- /.metabox-holder -->