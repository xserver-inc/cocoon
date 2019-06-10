<?php /**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit; ?>

<div class="metabox-holder">

<!-- リスト -->
<div id="index-page" class="postbox">
  <h2 class="hndle"><?php _e( 'リスト設定', THEME_NAME ) ?></h2>
  <div class="inside">

    <p><?php _e( 'リスト表示の設定です。', THEME_NAME ) ?></p>

    <table class="form-table">
      <tbody>

        <?php if (DEBUG_ADMIN_DEMO_ENABLE && apply_filters('cocoon_setting_preview_index', true)): ?>
        <!-- プレビュー画面 -->
        <tr>
          <th scope="row">
            <label><?php _e( 'プレビュー', THEME_NAME ) ?></label>
          </th>
          <td>
            <div class="demo" style="height: 300px;overflow: auto;">
              <div <?php body_class(); ?>>
              <?php query_posts('no_found_rows=1&posts_per_page=10'); ?>
              <?php get_template_part('tmp/list'); ?>
              <?php wp_reset_query(); ?>
              </div>
            </div>
            <!-- <p><?php _e( '※タイル表示はうまくプレビューできないかも。今のところ原因不明。', THEME_NAME ) ?></p> -->
          </td>
        </tr>
        <?php endif; ?>

        <!-- 並び順 -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_INDEX_SORT_ORDERBY, __('並び順', THEME_NAME) ); ?>
          </th>
          <td>
            <?php
            $options = array(
              '' => __( '投稿日（降順）', THEME_NAME ),
              'modified' => __( '更新日（降順）', THEME_NAME ),
            );
            generate_radiobox_tag(OP_INDEX_SORT_ORDERBY, $options, get_index_sort_orderby());
            generate_tips_tag(__( '一覧リストを表示する順番を設定します。', THEME_NAME ));
            ?>
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
              'big_card_first' => __( '大きなカード（先頭のみ）', THEME_NAME ),
              'big_card' => __( '大きなカード', THEME_NAME ),
              'vertical_card_2' => __( '縦型カード2列', THEME_NAME ),
              'vertical_card_3' => __( '縦型カード3列', THEME_NAME ),
              'tile_card_2' => __( 'タイルカード2列', THEME_NAME ),
              'tile_card_3' => __( 'タイルカード3列', THEME_NAME ),
            );
            generate_radiobox_tag(OP_ENTRY_CARD_TYPE, $options, get_entry_card_type());
            generate_tips_tag(__( '一覧リストのカード表示を変更します。カード表示数を変更するには、「設定→1ページに表示する最大投稿数」から変更してください。', THEME_NAME ).get_help_page_tag('https://wp-cocoon.com/index-entry-card-type/'));
            generate_tips_tag(__( '縦型カード・タイルカードに設定した場合はサムネイルの再生成を行ってください。', THEME_NAME ).get_help_page_tag('https://wp-cocoon.com/regenerate-thumbnails/'));

            //echo '<div'.get_not_allowed_form_class(!is_entry_card_type_entry_card()).'>';
            echo '<div>';
            generate_checkbox_tag(OP_SMARTPHONE_ENTRY_CARD_1_COLUMN , is_smartphone_entry_card_1_column(), __( 'スマホ端末で縦型＆タイル型のエントリーカードを1カラムにする', THEME_NAME ));
            generate_tips_tag(__( 'スマホ（480px以下）で表示した際に、カードタイプを「縦型カード」「タイルカード」にしている場合は、1カラムで表示します。※デフォルトは2カラム', THEME_NAME ));
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
            <?php generate_label_tag(OP_ENTRY_CARD_EXCERPT_MAX_LENGTH, __('自動生成抜粋文字数', THEME_NAME) ); ?>
          </th>
          <td>
            <?php
            generate_number_tag(OP_ENTRY_CARD_EXCERPT_MAX_LENGTH,  get_entry_card_excerpt_max_length(), 120, 0, 500);
            generate_tips_tag(__( '「エントリーカード」で、「本文から自動生成される抜粋文」を表示する場合の最大文字数を
              設定します。※投稿編集画面の抜粋文ではありません。（最小：0、最大：500）', THEME_NAME ));
            ?>
          </td>
        </tr>

        <!-- 省略文字列 -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_ENTRY_CARD_EXCERPT_MORE, __('省略文字列', THEME_NAME) ); ?>
          </th>
          <td>
            <?php
            generate_textbox_tag(OP_ENTRY_CARD_EXCERPT_MORE, get_entry_card_excerpt_more(), __( __( '...', THEME_NAME ), THEME_NAME ));
            generate_tips_tag(__( '「自動生成抜粋文字数」を自動抜粋文が超えたときに表示する省略を表す文字を入力してください。', THEME_NAME ));
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
            echo '<br>';

            generate_checkbox_tag(OP_ENTRY_CARD_POST_COMMENT_COUNT_VISIBLE , is_entry_card_post_comment_count_visible(), __( 'コメント数の表示', THEME_NAME ));

            generate_tips_tag(__( 'エントリーカードに投稿関連情報を表示するかどうか。', THEME_NAME ));
            ?>
          </td>
        </tr>

      </tbody>
    </table>

  </div>
</div>

</div><!-- /.metabox-holder -->
