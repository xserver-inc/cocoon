<?php /**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit; ?>

<div class="metabox-holder">

<!-- カテゴリー・タグ表示 -->
<div id="cat-tag-page" class="postbox">
  <h2 class="hndle"><?php _e( 'カテゴリー・タグ表示設定', THEME_NAME ) ?></h2>
  <div class="inside">

    <p><?php _e( '投稿本文下のカテゴリーとタグの表示を設定します。', THEME_NAME ) ?></p>

    <table class="form-table">
      <tbody>

        <?php if (DEBUG_ADMIN_DEMO_ENABLE && apply_filters('cocoon_setting_preview_singular_categories_tags', true)): ?>
        <!-- プレビュー画面 -->
        <tr>
          <th scope="row">
            <label><?php _e( 'プレビュー', THEME_NAME ) ?></label>
          </th>
          <td>
            <div class="demo" style="overflow: auto;">
              <?php get_sanitize_preview_template_part('tmp/categories-tags'); ?>
            </div>
            <?php generate_tips_tag(__( 'デモはランダムです。', THEME_NAME )); ?>
          </td>
        </tr>
        <?php endif; ?>

        <!-- カテゴリー・タグ表示 -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_CATEGORY_TAG_DISPLAY_TYPE, __('カテゴリー・タグ表示', THEME_NAME) ); ?>
          </th>
          <td>
            <?php
            $options = array(
              'two_rows' => __( 'カテゴリー・タグ2列', THEME_NAME ),
              'one_row' => __( 'カテゴリー・タグ1列', THEME_NAME ),
              'category_only' => __( 'カテゴリーのみ', THEME_NAME ),
              'tag_only' => __( 'タグのみ', THEME_NAME ),
              'none' => __( 'カテゴリーもタグも表示しない', THEME_NAME ),
            );
            generate_selectbox_tag(OP_CATEGORY_TAG_DISPLAY_TYPE, $options, get_category_tag_display_type());
            generate_tips_tag(__( 'カテゴリーとタグの表示を制御します。', THEME_NAME ));
            ?>
          </td>
        </tr>

        <!-- カテゴリー・タグ表示位置 -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_CATEGORY_TAG_DISPLAY_POSITION, __('カテゴリー・タグ表示位置', THEME_NAME) ); ?>
          </th>
          <td>
            <?php
            $options = array(
              'title_top' => __( 'タイトル上', THEME_NAME ),
              'content_top' => __( '本文上', THEME_NAME ),
              'content_bottom' => __( '本文下（デフォルト）', THEME_NAME ),
            );
            generate_selectbox_tag(OP_CATEGORY_TAG_DISPLAY_POSITION, $options, get_category_tag_display_position());
            generate_tips_tag(__( 'カテゴリーとタグの表示位置を設定します。', THEME_NAME ));
            ?>
          </td>
        </tr>

      </tbody>
    </table>

  </div>
</div>


<!-- 関連記事 -->
<div id="single-relation" class="postbox">
  <h2 class="hndle"><?php _e( '関連記事設定', THEME_NAME ) ?></h2>
  <div class="inside">

    <p><?php _e( '関連記事の表示に関する設定です。', THEME_NAME ) ?></p>

    <table class="form-table">
      <tbody>

        <?php if (DEBUG_ADMIN_DEMO_ENABLE && apply_filters('cocoon_setting_preview_singular_related_entries', true)): ?>
        <!-- プレビュー画面 -->
        <tr>
          <th scope="row">
            <label><?php _e( 'プレビュー', THEME_NAME ) ?></label>
          </th>
          <td>
            <div class="demo" style="height: 300px;overflow: auto;">
              <div <?php body_class(); ?>>
              <?php get_sanitize_preview_template_part('tmp/related-entries') ?>
              </div>
            </div>
            <?php generate_tips_tag(__( 'デモの関連記事はランダムです。', THEME_NAME )); ?>
          </td>
        </tr>
        <?php endif; ?>

        <!-- 表示 -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_RELATED_ENTRIES_VISIBLE, __('表示', THEME_NAME) ); ?>
          </th>
          <td>
            <?php
            generate_checkbox_tag(OP_RELATED_ENTRIES_VISIBLE , is_related_entries_visible(), __( '関連記事を表示する', THEME_NAME ));
            generate_tips_tag(__( '投稿ページの関連記事を表示するか。', THEME_NAME ));
            ?>
          </td>
        </tr>

        <!-- 関連性 -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_RELATED_ASSOCIATION_TYPE, __('関連性', THEME_NAME) ); ?>
          </th>
          <td>
            <?php
            $options = array(
              'category' => __( 'カテゴリー', THEME_NAME ),
              'tag' => __( 'タグ', THEME_NAME ),
            );
            generate_radiobox_tag(OP_RELATED_ASSOCIATION_TYPE, $options, get_related_association_type());
            generate_tips_tag(__( '関連記事にカテゴリーを関連づけるかタグを関連づけるか。タグに関連付けて、タグがない場合はカテゴリー関連記事が表示されます。逆もしかり。', THEME_NAME ));
            ?>
          </td>
        </tr>

        <!-- 関連記事見出し -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_RELATED_ENTRY_HEADING, __('関連記事見出し', THEME_NAME) ); ?>
          </th>
          <td>
            <?php
            generate_textbox_tag(OP_RELATED_ENTRY_HEADING, get_related_entry_heading(), __( '見出し', THEME_NAME ));
            generate_tips_tag(__( '関連記事の見出しを入力してください。', THEME_NAME ));
            generate_textbox_tag(OP_RELATED_ENTRY_SUB_HEADING, get_related_entry_sub_heading(), __( 'サブ見出し', THEME_NAME ));
            generate_tips_tag(__( '関連記事の補助となる見出しを入力してください。', THEME_NAME ));
            ?>
          </td>
        </tr>

        <!-- 表示タイプ -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_RELATED_ENTRY_TYPE, __('表示タイプ', THEME_NAME) ); ?>
          </th>
          <td>
            <?php
            $options = array(
              'entry_card' => __( 'エントリーカード（デフォルト）', THEME_NAME ),
              'mini_card' => __( 'ミニカード（推奨表示数：偶数）', THEME_NAME ),
              'vertical_card_3' => __( '縦型カード3列（推奨表示数：6, 12, 18...）', THEME_NAME ),
              'vertical_card_4' => __( '縦型カード4列（推奨表示数：4, 8, 12...）', THEME_NAME ),
            );
            generate_radiobox_tag(OP_RELATED_ENTRY_TYPE, $options, get_related_entry_type());
            generate_tips_tag(__( '関連記事の表示タイプを選択します。', THEME_NAME ));
            ?>
          </td>
        </tr>

        <!-- 表示数 -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_RELATED_ENTRY_COUNT, __('表示数', THEME_NAME) ); ?>
          </th>
          <td>
            <?php
            generate_number_tag(OP_RELATED_ENTRY_COUNT,  get_related_entry_count(), 6, 2, 30);
            generate_tips_tag(__( '関連記事で表示する投稿数の設定です。（最小：2、最大：30）', THEME_NAME ));
            ?>
          </td>
        </tr>

        <!-- 取得期間 -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_RELATED_ENTRY_PERIOD, __('取得期間', THEME_NAME) ); ?>
          </th>
          <td>
            <?php
            $options = array(
              '' => __( '全期間', THEME_NAME ),
              '-1 week' => __( '1週間', THEME_NAME ),
              '-2 week' => __( '2週間', THEME_NAME ),
              '-3 week' => __( '3週間', THEME_NAME ),
              '-1 month' => __( '1ヶ月', THEME_NAME ),
              '-2 month' => __( '2ヶ月', THEME_NAME ),
              '-3 month' => __( '3ヶ月', THEME_NAME ),
              '-6 month' => __( '6ヶ月', THEME_NAME ),
              '-1 year' => __( '1年', THEME_NAME ),
              '-2 year' => __( '2年', THEME_NAME ),
              '-3 year' => __( '3年', THEME_NAME ),
            );
            generate_selectbox_tag(OP_RELATED_ENTRY_PERIOD, $options, get_related_entry_period());
            generate_tips_tag(__( '関連記事を取得する期間を選択することで新鮮な記事を表示しやすくします。ニュースサイト等で、新しい記事が並んで欲しい時に設定します。頻回にサイトを更新していない場合は利用しないことをおすすめします。', THEME_NAME ));
            ?>
          </td>
        </tr>

        <!-- 枠線の表示 -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_RELATED_ENTRY_BORDER_VISIBLE, __('枠線の表示', THEME_NAME) ); ?>
          </th>
          <td>
            <?php
            generate_checkbox_tag(OP_RELATED_ENTRY_BORDER_VISIBLE , is_related_entry_border_visible(), __( 'カードの枠線を表示する', THEME_NAME ));
            generate_tips_tag(__( '投稿エントリーカードの枠となる罫線を表示するか。', THEME_NAME ));
            ?>
          </td>
        </tr>

        <!-- 最大自動生成抜粋文字数 -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_RELATED_EXCERPT_MAX_LENGTH, __('最大自動生成抜粋文字数', THEME_NAME) ); ?>
          </th>
          <td>
            <?php
            generate_number_tag(OP_RELATED_EXCERPT_MAX_LENGTH,  get_related_excerpt_max_length(), 120, 1, 500);
            generate_tips_tag(__( '「関連記事エントリーカード」で、「本文から自動生成される抜粋文」を表示する場合の最大文字数を設定します。※投稿編集画面の抜粋文ではありません。（最小：1、最大：500）', THEME_NAME ));
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
            generate_checkbox_tag(OP_RELATED_ENTRY_CARD_SNIPPET_VISIBLE , is_related_entry_card_snippet_visible(), __( 'スニペット（抜粋）を表示する', THEME_NAME ));


            echo '<div class="indent'.get_not_allowed_form_class(is_related_entry_card_snippet_visible(), true).'">';
              generate_checkbox_tag(OP_SMARTPHONE_RELATED_ENTRY_CARD_SNIPPET_VISIBLE , is_smartphone_related_entry_card_snippet_visible(), __( 'スマートフォンで表示する（480px以下）', THEME_NAME ));
            echo '</div>';

            generate_checkbox_tag(OP_RELATED_ENTRY_CARD_POST_DATE_VISIBLE , is_related_entry_card_post_date_visible(), __( '投稿日を表示する', THEME_NAME ));
            echo '<br>';

            generate_checkbox_tag(OP_RELATED_ENTRY_CARD_POST_UPDATE_VISIBLE , is_related_entry_card_post_update_visible(), __( '更新日を表示する', THEME_NAME ));
            echo '<br>';

            generate_checkbox_tag(OP_RELATED_ENTRY_CARD_POST_AUTHOR_VISIBLE , is_related_entry_card_post_author_visible(), __( '投稿者名を表示する', THEME_NAME ));
            generate_tips_tag(__( '投稿の関連記事のエントリーカードに投稿関連情報を表示するかどうか。', THEME_NAME ));
            ?>
          </td>
        </tr>

      </tbody>
    </table>

  </div>
</div>

<!-- ページ送りナビ -->
<div id="single-nav" class="postbox">
  <h2 class="hndle"><?php _e( 'ページ送りナビ設定', THEME_NAME ) ?></h2>
  <div class="inside">

    <p><?php _e( '[前ページ][次ページ]へと送るナビゲーションの設定です。', THEME_NAME ) ?></p>

    <table class="form-table">
      <tbody>


        <?php if (DEBUG_ADMIN_DEMO_ENABLE && apply_filters('cocoon_setting_preview_singular_pager_post_navi', true)): ?>
        <!-- プレビュー画面 -->
        <tr>
          <th scope="row">
            <label><?php _e( 'プレビュー', THEME_NAME ) ?></label>
          </th>
          <td>
            <div class="demo">
              <div <?php body_class(); ?>>
              <?php get_sanitize_preview_template_part('tmp/pager-post-navi') ?>
              </div>
            </div>
            <?php generate_tips_tag(__( 'デモはランダム表示です。', THEME_NAME )); ?>
          </td>
        </tr>
        <?php endif; ?>

        <!-- 表示 -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_POST_NAVI_VISIBLE, __('表示', THEME_NAME) ); ?>
          </th>
          <td>
            <?php
            generate_checkbox_tag(OP_POST_NAVI_VISIBLE , is_post_navi_visible(), __( 'ページ送りナビを表示する', THEME_NAME ));
            generate_tips_tag(__( '[前ページ][次ページ]ナビを表示するか。', THEME_NAME ));
            ?>
          </td>
        </tr>

        <!-- 表示タイプ -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_POST_NAVI_TYPE, __('表示タイプ', THEME_NAME) ); ?>
          </th>
          <td>
            <?php
            $options = array(
              'default' => __( 'デフォルト', THEME_NAME ),
              'square' => __( 'サムネイル正方形', THEME_NAME ),
            );
            generate_radiobox_tag(OP_POST_NAVI_TYPE, $options, get_post_navi_type());
            generate_tips_tag(__( 'ページ送りナビの見た目を変更します。', THEME_NAME ));
            ?>
          </td>
        </tr>

        <!-- 表示位置 -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_POST_NAVI_POSITION, __('表示位置', THEME_NAME) ); ?>
          </th>
          <td>
            <?php
            $options = array(
              'under_content' => __( '本文下', THEME_NAME ),
              'over_related' => __( '関連記事上', THEME_NAME ),
              'under_related' => __( '関連記事下（デフォルト）', THEME_NAME ),
              'under_comment' => __( 'コメント下', THEME_NAME ),
            );
            generate_radiobox_tag(OP_POST_NAVI_POSITION, $options, get_post_navi_position());
            generate_tips_tag(__( 'ページ送りナビを表示する位置を変更します。', THEME_NAME ).get_help_page_tag('https://wp-cocoon.com/post-navi-position/'));
            ?>
          </td>
        </tr>

        <!-- カテゴリー -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_POST_NAVI_SAME_CATEGORY_ENABLE, __('カテゴリー', THEME_NAME) ); ?>
          </th>
          <td>
            <?php
            generate_checkbox_tag(OP_POST_NAVI_SAME_CATEGORY_ENABLE , is_post_navi_same_category_enable(), __( '同一カテゴリーのものを表示する', THEME_NAME ));
            generate_tips_tag(__( '投稿と同一カテゴリーのページ送りナビを表示するかどうか。複数カテゴリーが設定してある場合は、複数に属するものが表示されます。', THEME_NAME ).get_help_page_tag('https://wp-cocoon.com/in-same-categories-post-navi/'));

            //除外カテゴリー
            generate_label_tag(OP_POST_NAVI_EXCLUDE_CATEGORY_IDS, __('除外カテゴリー', THEME_NAME) );
            generate_hierarchical_category_check_list( 0, OP_POST_NAVI_EXCLUDE_CATEGORY_IDS, get_post_navi_exclude_category_ids(), 300 );
            generate_tips_tag(__( 'ページ送りナビに表示させないカテゴリーを選択してください。', THEME_NAME ).get_help_page_tag('https://wp-cocoon.com/excluded-categories-post-navi/'));
            ?>
          </td>
        </tr>

        <!-- 枠線表示 -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_POST_NAVI_BORDER_VISIBLE, __('枠線表示', THEME_NAME) ); ?>
          </th>
          <td>
            <?php
            generate_checkbox_tag(OP_POST_NAVI_BORDER_VISIBLE , is_post_navi_border_visible(), __( 'ページ送りナビの枠線を表示する', THEME_NAME ));
            generate_tips_tag(__( 'ページ送りナビを囲む枠線を表示するか。', THEME_NAME ));
            ?>
          </td>
        </tr>

      </tbody>
    </table>

  </div>
</div>

<!-- コメント -->
<div id="single-comment" class="postbox">
  <h2 class="hndle"><?php _e( 'コメント設定', THEME_NAME ) ?></h2>
  <div class="inside">

    <p><?php _e( '投稿のコメント表示設定です。', THEME_NAME ) ?></p>

    <table class="form-table">
      <tbody>
        <!-- 表示 -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_SINGLE_COMMENT_VISIBLE, __('表示', THEME_NAME) ); ?>
          </th>
          <td>
            <?php
            generate_checkbox_tag(OP_SINGLE_COMMENT_VISIBLE , is_single_comment_visible(), __( 'コメントを表示する', THEME_NAME ));
            generate_tips_tag(__( '投稿ページにコメントを表示するか。', THEME_NAME ));
            ?>
          </td>
        </tr>

      </tbody>
    </table>

  </div>
</div>

<!-- パンくずリスト -->
<div id="single-bread" class="postbox">
  <h2 class="hndle"><?php _e( 'パンくずリスト設定', THEME_NAME ) ?></h2>
  <div class="inside">

    <p><?php _e( '投稿のパンくずリスト設定です。', THEME_NAME ) ?></p>

    <table class="form-table">
      <tbody>
        <!-- 表示 -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_SINGLE_BREADCRUMBS_POSITION, __('パンくずリストの配置', THEME_NAME) ); ?>
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
            generate_radiobox_tag(OP_SINGLE_BREADCRUMBS_POSITION, $options, get_single_breadcrumbs_position());
            generate_tips_tag(__( '投稿・カテゴリーページのパンくずリスト表示位置を設定します。', THEME_NAME ));
            ?>
          </td>
        </tr>

        <!-- 記事タイトル -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_SINGLE_BREADCRUMBS_INCLUDE_POST, __('記事タイトル', THEME_NAME) ); ?>
          </th>
          <td>
            <?php
            generate_checkbox_tag(OP_SINGLE_BREADCRUMBS_INCLUDE_POST , is_single_breadcrumbs_include_post(), __( 'パンくずリストに記事タイトルを含める', THEME_NAME ));
            generate_tips_tag(__( '投稿ページのパンくずリストに対して、表示されているページのタイトルを含めるか切り替えます。', THEME_NAME ));
            ?>
          </td>
        </tr>

      </tbody>
    </table>

  </div>
</div>

</div><!-- /.metabox-holder -->
