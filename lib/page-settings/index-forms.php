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

        <?php if (DEBUG_ADMIN_DEMO_ENABLE && apply_filters('cocoon_setting_preview_index', false)): ?>
        <!-- プレビュー画面 -->
        <tr>
          <th scope="row">
            <label><?php _e( 'プレビュー', THEME_NAME ) ?></label>
          </th>
          <td>
            <div class="demo" style="height: 300px;overflow: auto;">
              <div <?php body_class(); ?>>
              <?php query_posts('no_found_rows=1&posts_per_page=10'); ?>
              <?php get_sanitize_preview_template_part('tmp/list'); ?>
              <?php wp_reset_query(); ?>
              </div>
            </div>
            <!-- <p><?php _e( '※タイル表示はうまくプレビューできないかも。今のところ原因不明。', THEME_NAME ) ?></p> -->
          </td>
        </tr>
        <?php endif; ?>


        <!-- フロントページ -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_FRONT_PAGE_TYPE, __( 'フロントページタイプ', THEME_NAME )); ?>
          </th>
          <td>
            <?php
            generate_label_tag(OP_FRONT_PAGE_TYPE, __( '表示形式', THEME_NAME ));
            $options = array(
              'index' => get_image_preview_tag('https://im-cocoon.net/wp-content/uploads/fpt-index.png', '', 400).__( '一覧', THEME_NAME ).__( '（デフォルト）', THEME_NAME ),
              'tab_index' => get_image_preview_tag('https://im-cocoon.net/wp-content/uploads/fpt-tab-min.gif', '', 400).__( 'タブ一覧', THEME_NAME ),
              'category' => get_image_preview_tag('https://im-cocoon.net/wp-content/uploads/fpt-categories-min.gif', '', 400).__( 'カテゴリーごと', THEME_NAME ),
              'category_2_columns' => get_image_preview_tag('https://im-cocoon.net/wp-content/uploads/fpt-categoly-columnn-2-min.gif', '', 400).__( 'カテゴリーごと', THEME_NAME ).__( '（2カラム）', THEME_NAME ),
              'category_3_columns' => get_image_preview_tag('https://im-cocoon.net/wp-content/uploads/fpt-categoly-columnn-3-min.gif', '', 400).__( 'カテゴリーごと', THEME_NAME ).__( '（3カラム）', THEME_NAME ).__( '※サイドバーを表示しないレイアウト向け。', THEME_NAME ).__( '表示数は3の倍数推奨。', THEME_NAME ),
            );
            generate_radiobox_tag(OP_FRONT_PAGE_TYPE, $options, get_front_page_type());
            generate_tips_tag(__( 'フロントページの表示形式を選択します。', THEME_NAME ));

            //フロントページに表示するカテゴリー
            generate_label_tag(OP_INDEX_CATEGORY_IDS, __( '表示カテゴリー', THEME_NAME ));
            generate_hierarchical_category_check_list( 0, OP_INDEX_CATEGORY_IDS, get_index_category_ids(), 300 );
            generate_tips_tag(__( '通常の記事インデックスの他に、カテゴリーの記事をタブ化して表示します。', THEME_NAME ).__( '「タブ一覧」の際は、3つまで有効。4つ目以降は無視されます。', THEME_NAME ));

            //カテゴリーIDのカンマテキスト
            $comma_text = get_index_category_ids_comma_text();
            ob_start();
            generate_label_tag(OP_INDEX_CATEGORY_IDS_COMMA_TEXT, __( 'カテゴリー順の変更', THEME_NAME ));
            echo '<br>';
            generate_textbox_tag(OP_INDEX_CATEGORY_IDS_COMMA_TEXT, $comma_text, __( 'カテゴリーIDのカンマテキストを入力', THEME_NAME )) ;
            generate_tips_tag(__( '意図通りの順番でカテゴリーを表示する場合は、カテゴリーIDをカンマ区切りで入力してください。', THEME_NAME ).__( 'こちらの入力がある場合は、チェックボックスのものよりこちらの設定が優先されます。', THEME_NAME ));
            $form = ob_get_clean();
            generate_toggle_area(__( 'カテゴリー表示の順番を並び替える場合はこちら', THEME_NAME ), $form);
            //入力チェック
            generate_toggle_entered($comma_text);
            echo '<br>';

            //エントリーカード数の設定用オプション
            $options = array(
              '1'  => __( '1個', THEME_NAME ),
              '2'  => __( '2個', THEME_NAME ),
              '3'  => __( '3個', THEME_NAME ),
              '4'  => __( '4個', THEME_NAME ).__( '（デフォルト）', THEME_NAME ),
              '5'  => __( '5個', THEME_NAME ),
              '6'  => __( '6個', THEME_NAME ),
              '7'  => __( '7個', THEME_NAME ),
              '8'  => __( '8個', THEME_NAME ),
              '9'  => __( '9個', THEME_NAME ),
              '10' => __( '10個', THEME_NAME ),
              '11' => __( '11個', THEME_NAME ),
              '12' => __( '12個', THEME_NAME ),
            );

            echo '<br>';
            ob_start();
            //新着エントリーカード表示数
            echo '<br>';
            generate_label_tag(OP_INDEX_NEW_ENTRY_CARD_COUNT, __( '新着エントリーカード表示数', THEME_NAME ));
            echo '<br>';
            generate_selectbox_tag(OP_INDEX_NEW_ENTRY_CARD_COUNT, $options, get_index_new_entry_card_count());
            generate_tips_tag(__( 'フロントページタイプを「カテゴリーごと」にした際に表示される新着エントリーカード数を設定します。', THEME_NAME ));

            //カテゴリーエントリーカード表示数
            generate_label_tag(OP_INDEX_CATEGORY_ENTRY_CARD_COUNT, __( 'カテゴリーエントリーカード表示数', THEME_NAME ));
            echo '<br>';
            generate_selectbox_tag(OP_INDEX_CATEGORY_ENTRY_CARD_COUNT, $options, get_index_category_entry_card_count());
            generate_tips_tag(__( 'フロントページのインデックスでカテゴリーごとに表示するエントリーカード数を設定します。', THEME_NAME ));
            $form = ob_get_clean();
            generate_toggle_area(__( '「カテゴリーごと」表示でカード表示数を変更する場合はこちら', THEME_NAME ), $form);
            ?>
          </td>
        </tr>

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
              'rand' => __( 'ランダム', THEME_NAME ),
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
              'entry_card' => get_image_preview_tag('https://im-cocoon.net/wp-content/uploads/ect-entry-card.png').__( 'エントリーカード（デフォルト）', THEME_NAME ),
              'big_card_first' => get_image_preview_tag('https://im-cocoon.net/wp-content/uploads/ect-big-card-first.png').__( '大きなカード（先頭のみ）', THEME_NAME ),
              'big_card' => get_image_preview_tag('https://im-cocoon.net/wp-content/uploads/ect-big-card.png').__( '大きなカード', THEME_NAME ),
              'vertical_card_2' => get_image_preview_tag('https://im-cocoon.net/wp-content/uploads/ect-vertical-card-2.png').__( '縦型カード2列', THEME_NAME ),
              'vertical_card_3' => get_image_preview_tag('https://im-cocoon.net/wp-content/uploads/ect-vertical-card-3.png').__( '縦型カード3列', THEME_NAME ),
              'tile_card_2' => get_image_preview_tag('https://im-cocoon.net/wp-content/uploads/ect-tile-card-2.png').__( 'タイルカード2列', THEME_NAME ),
              'tile_card_3' => get_image_preview_tag('https://im-cocoon.net/wp-content/uploads/ect-tile-card-3.png').__( 'タイルカード3列', THEME_NAME ),
            );
            generate_radiobox_tag(OP_ENTRY_CARD_TYPE, $options, get_entry_card_type());
            generate_tips_tag(__( '一覧リストのカード表示を変更します。カード表示数を変更するには、「設定 → 表示設定 → 1ページに表示する最大投稿数」から変更してください。', THEME_NAME ).get_help_page_tag('https://wp-cocoon.com/index-entry-card-type/'));
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
            generate_tips_tag(__( '「エントリーカード」で、「本文から自動生成される抜粋文」を表示する場合の最大文字数を設定します。※投稿編集画面の抜粋文ではありません。（最小：0、最大：500）', THEME_NAME ));
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
            echo '<br>';

            // //表示しないとき
            // $is_not_allowed = is_entry_card_post_date_visible();
            // $is_not_allowed = $is_not_allowed || !is_entry_card_post_update_visible();
            // echo '<div class="indent'.get_not_allowed_form_class(!$is_not_allowed, true).'">';
            //   generate_checkbox_tag(OP_ENTRY_CARD_POST_DATE_OR_UPDATE_VISIBLE , is_entry_card_post_date_or_update_visible(), __( '更新日が存在しない場合は投稿日を表示', THEME_NAME ));
            // echo '</div>';

            generate_checkbox_tag(OP_ENTRY_CARD_POST_UPDATE_VISIBLE , is_entry_card_post_update_visible(), __( '更新日の表示', THEME_NAME ));
            echo '<br>';

            generate_checkbox_tag(OP_ENTRY_CARD_POST_AUTHOR_VISIBLE , is_entry_card_post_author_visible(), __( '投稿者名の表示', THEME_NAME ));
            echo '<br>';

            generate_checkbox_tag(OP_ENTRY_CARD_POST_COMMENT_COUNT_VISIBLE , is_entry_card_post_comment_count_visible(), __( 'コメント数の表示', THEME_NAME ));

            generate_tips_tag(__( 'インデックスページのエントリーカードに投稿関連情報を表示するかどうか。', THEME_NAME ));
            ?>
          </td>
        </tr>

        <!-- 除外カテゴリー -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_ARCHIVE_EXCLUDE_CATEGORY_IDS, __( '除外カテゴリー', THEME_NAME )); ?>
          </th>
          <td>
            <?php
            generate_hierarchical_category_check_list( 0, OP_ARCHIVE_EXCLUDE_CATEGORY_IDS, get_archive_exclude_category_ids(), 300 );
            generate_tips_tag(__( 'アーカイブ（インデックスリスト・新着関連記事ウィジェット等）に表示させたくないカテゴリーを選択してください。', THEME_NAME ).get_help_page_tag('https://wp-cocoon.com/do-not-output-posts-that-belong-to-archives/'));
            ?>
          </td>
        </tr>


      </tbody>
    </table>

  </div>
</div>

</div><!-- /.metabox-holder -->
