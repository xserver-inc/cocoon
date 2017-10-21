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
              'vertical_card_2' => __( '縦型カード2列（推奨表示数：偶数）', THEME_NAME ),
              'vertical_card_3' => __( '縦型カード3列（推奨表示数：3, 6, 9, 12, ...）', THEME_NAME ),
            );
            genelate_radiobox_tag(OP_ENTRY_CARD_TYPE, $options, get_entry_card_type());
            genelate_tips_tag(__( '一覧リストのカード表示を変更します。カード表示数を変更するには、「設定→1ページに表示する最大投稿数」から変更してください。', THEME_NAME ));
            ?>
          </td>
        </tr>

        <!-- 枠線の表示 -->
        <tr>
          <th scope="row">
            <?php genelate_label_tag(OP_ENTRY_CARD_BORDER_VISIBLE, __('枠線の表示', THEME_NAME) ); ?>
          </th>
          <td>
            <?php
            genelate_checkbox_tag(OP_ENTRY_CARD_BORDER_VISIBLE , is_entry_card_border_visible(), __( 'カードの枠線を表示する', THEME_NAME ));
            genelate_tips_tag(__( '投稿エントリーカードの枠となる罫線を表示するか。', THEME_NAME ));
            ?>
          </td>
        </tr>

        <!-- 最大抜粋文字数 -->
        <tr>
          <th scope="row">
            <?php genelate_label_tag(OP_ENTRY_CARD_EXCERPT_MAX_LENGTH, __('最大抜粋文字数', THEME_NAME) ); ?>
          </th>
          <td>
            <?php
            genelate_number_tag(OP_ENTRY_CARD_EXCERPT_MAX_LENGTH,  get_entry_card_excerpt_max_length(), 0, 500);
            genelate_tips_tag(__( '「エントリーカード」で、抜粋文を表示する場合の最大文字数を
              設定します。（最小：0、最大：500）', THEME_NAME ));
            ?>
          </td>
        </tr>


      </tbody>
    </table>

  </div>
</div>

</div><!-- /.metabox-holder -->