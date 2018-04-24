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
              <div <?php body_class(); ?>>
              <?php query_posts('no_found_rows=1'); ?>
              <?php get_template_part('tmp/list'); ?>
              <?php wp_reset_query(); ?>
              </div>
            </div>
          </td>
        </tr>

        <!-- カードタイプ -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_ENTRY_CARD_TYPE, __('カードタイプ', THEME_NAME) ); ?>
          </th>
          <td>
            <?php
            $options = array(
              'entry_card' => __( 'エントリーカード（デフォルト）', THEME_NAME ),
              'vertical_card_2' => __( '縦型カード2列（推奨表示数：偶数）', THEME_NAME ),
              'vertical_card_3' => __( '縦型カード3列（推奨表示数：3, 6, 9, 12, ...）', THEME_NAME ),
            );
            generate_radiobox_tag(OP_ENTRY_CARD_TYPE, $options, get_entry_card_type());
            generate_tips_tag(__( '一覧リストのカード表示を変更します。カード表示数を変更するには、「設定→1ページに表示する最大投稿数」から変更してください。', THEME_NAME ).get_help_page_tag('https://wp-cocoon.com/index-entry-card-type/'));

            echo '<div'.get_not_allowed_form_class(!is_entry_card_type_entry_card()).'>';
            generate_checkbox_tag(OP_SMARTPHONE_ENTRY_CARD_1_COLUMN , is_smartphone_entry_card_1_column(), __( 'スマホ端末で縦型のエントリーカードを1カラムにする', THEME_NAME ));
            generate_tips_tag(__( 'スマホ（480px以下）で表示した際に、カードタイプを「縦型カード」にしている場合は、1カラムで表示します。※デフォルトは2カラム', THEME_NAME ));
            echo '</div>';

            ?>
          </td>
        </tr>

        <!-- 枠線の表示 -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_ENTRY_CARD_BORDER_VISIBLE, __('枠線の表示', THEME_NAME) ); ?>
          </th>
          <td>
            <?php
            generate_checkbox_tag(OP_ENTRY_CARD_BORDER_VISIBLE , is_entry_card_border_visible(), __( 'カードの枠線を表示する', THEME_NAME ));
            generate_tips_tag(__( '投稿エントリーカードの枠となる罫線を表示するか。', THEME_NAME ));
            ?>
          </td>
        </tr>

        <!-- 最大抜粋文字数 -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_ENTRY_CARD_EXCERPT_MAX_LENGTH, __('最大自動生成抜粋文字数', THEME_NAME) ); ?>
          </th>
          <td>
            <?php
            generate_number_tag(OP_ENTRY_CARD_EXCERPT_MAX_LENGTH,  get_entry_card_excerpt_max_length(), 120, 0, 500);
            generate_tips_tag(__( '「エントリーカード」で、「本文から自動生成される抜粋文」を表示する場合の最大文字数を
              設定します。※投稿編集画面の抜粋文ではありません。（最小：0、最大：500）', THEME_NAME ));
            ?>
          </td>
        </tr>


        <!-- 投稿関連情報の表示 -->
        <tr>
          <th scope="row">
            <?php generate_label_tag('', __('投稿関連情報の表示', THEME_NAME) ); ?>
          </th>
          <td>
            <?php
            generate_checkbox_tag(OP_ENTRY_CARD_SNIPPET_VISIBLE , is_entry_card_snippet_visible(), __( 'スニペット（抜粋）の表示', THEME_NAME ));


            echo '<div class="indent'.get_not_allowed_form_class(is_entry_card_snippet_visible(), true).'">';
              generate_checkbox_tag(OP_SMARTPHONE_ENTRY_CARD_SNIPPET_VISIBLE , is_smartphone_entry_card_snippet_visible(), __( 'スマホ端末でスニペットを表示（480px以下）', THEME_NAME ));
            echo '</div>';

            generate_checkbox_tag(OP_ENTRY_CARD_POST_DATE_VISIBLE , is_entry_card_post_date_visible(), __( '投稿日の表示', THEME_NAME ));
            //表示しないとき
            $is_not_allowed = is_entry_card_post_date_visible();
            $is_not_allowed = $is_not_allowed || !is_entry_card_post_update_visible();
            echo '<div class="indent'.get_not_allowed_form_class(!$is_not_allowed, true).'">';
              generate_checkbox_tag(OP_ENTRY_CARD_POST_DATE_OR_UPDATE_VISIBLE , is_entry_card_post_date_or_update_visible(), __( '更新日が存在しない場合は投稿日を表示', THEME_NAME ));
            echo '</div>';

            generate_checkbox_tag(OP_ENTRY_CARD_POST_UPDATE_VISIBLE , is_entry_card_post_update_visible(), __( '更新日の表示', THEME_NAME ));
            echo '<br>';

            generate_checkbox_tag(OP_ENTRY_CARD_POST_AUTHOR_VISIBLE , is_entry_card_post_author_visible(), __( '投稿者名の表示', THEME_NAME ));
            generate_tips_tag(__( 'エントリーカードに投稿関連情報を表示するかどうか。', THEME_NAME ));
            ?>
          </td>
        </tr>

      </tbody>
    </table>

  </div>
</div>

</div><!-- /.metabox-holder -->