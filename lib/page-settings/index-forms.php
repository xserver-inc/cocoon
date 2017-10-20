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

        <!-- 表示タイプ -->
        <tr>
          <th scope="row">
            <?php genelate_label_tag(OP_RELATED_ENTRY_TYPE, __('表示タイプ', THEME_NAME) ); ?>
          </th>
          <td>
            <?php
            $options = array(
              'entry_card' => __( 'エントリーカード（デフォルト）', THEME_NAME ),
              'mini_card' => __( 'ミニカード（推奨：表示数偶数）', THEME_NAME ),
              'vartical_card_3' => __( '縦型カード3列（推奨：表示数 6, 12, 18...）', THEME_NAME ),
              'vartical_card_4' => __( '縦型カード4列（推奨：表示数 4, 8, 12...）', THEME_NAME ),
            );
            genelate_radiobox_tag(OP_RELATED_ENTRY_TYPE, $options, get_related_entry_type());
            genelate_tips_tag(__( '関連記事の表示タイプを選択します。', THEME_NAME ));
            ?>
          </td>
        </tr>


      </tbody>
    </table>

  </div>
</div>

</div><!-- /.metabox-holder -->