<?php /**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit; ?>

<div class="metabox-holder">

<!-- エディター共通 -->
<div id="editor" class="postbox">
  <h2 class="hndle"><?php _e( 'エディター共通設定', THEME_NAME ) ?></h2>
  <div class="inside">

    <p><?php _e( '投稿・固定ページ管理画面の設定です。', THEME_NAME ) ?></p>

    <table class="form-table">
      <tbody>

        <?php if (!is_classicpress()): ?>
        <!-- Gutenberg -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_GUTENBERG_EDITOR_ENABLE, __('Gutenberg', THEME_NAME) ); ?>
          </th>
          <td>
            <?php
            generate_checkbox_tag(OP_GUTENBERG_EDITOR_ENABLE , is_gutenberg_editor_enable(), __( 'Gutenbergエディターを有効にする', THEME_NAME ));
            generate_tips_tag(__( '無効化することで旧ビジュアルエディター形式で投稿画面が表示されます。', THEME_NAME ));
            ?>
          </td>
        <?php endif; ?>

        </tr>

        <!-- エディタースタイル -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_VISUAL_EDITOR_STYLE_ENABLE, __('エディタースタイル', THEME_NAME) ); ?>
          </th>
          <td>
            <?php
            generate_checkbox_tag(OP_VISUAL_EDITOR_STYLE_ENABLE , is_visual_editor_style_enable(), __( 'エディターにテーマスタイルを反映させる', THEME_NAME ));
            generate_tips_tag(__( '無効にするとWordPressデフォルトのエディターになります。', THEME_NAME ));
            ?>
          </td>
        </tr>

        <!-- 文字カウンター -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_ADMIN_EDITOR_COUNTER_VISIBLE, __('文字カウンター', THEME_NAME) ); ?>
          </th>
          <td>
            <?php
            generate_checkbox_tag(OP_ADMIN_EDITOR_COUNTER_VISIBLE , is_admin_editor_counter_visible(), __( 'タイトル等の文字数カウンターを表示する', THEME_NAME ));
            generate_tips_tag(__( 'タイトルや、SEOタイトル、メタディスクリプションの文字数を表示します。', THEME_NAME ));
            ?>
          </td>
        </tr>

        <!-- エディター色 -->
        <tr>
          <th scope="row">
            <?php generate_label_tag('', __('エディター色', THEME_NAME) ); ?>
          </th>
          <td>
            <?php
            generate_color_picker_tag(OP_EDITOR_BACKGROUND_COLOR,  get_editor_background_color(), __( '背景色', THEME_NAME ));

            generate_tips_tag(__( 'エディターの背景色を指定します。', THEME_NAME ));

            generate_color_picker_tag(OP_EDITOR_TEXT_COLOR,  get_editor_text_color(), __( '文字色', THEME_NAME ));
            generate_tips_tag(__( 'エディターのテキスト色を指定します。', THEME_NAME ));
            ?>
          </td>
        </tr>

        <!-- タグ -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_EDITOR_TAG_CHECK_LIST_ENABLE, __('タグ', THEME_NAME) ); ?>
          </th>
          <td>
            <?php
            generate_checkbox_tag(OP_EDITOR_TAG_CHECK_LIST_ENABLE , is_editor_tag_check_list_enable(), __( 'タグ選択をチェックリスト形式にする', THEME_NAME ));
            generate_image_preview_tag('https://im-cocoon.net/wp-content/uploads/tag-check-box.png');
            generate_tips_tag(__( 'エディターでチェックリストを選択する形でタグを選択する様に変更します。', THEME_NAME ).__( '※タグ編集画面やエディターのタグオプション上で階層構造形式になります。', THEME_NAME ));
            ?>
          </td>
        </tr>

        <!-- アイキャッチ -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_FEATURED_IMAGE_FROM_TITLE, __('アイキャッチ', THEME_NAME) ); ?>
          </th>
          <td>
            <?php
            generate_checkbox_tag(OP_FEATURED_IMAGE_FROM_TITLE , is_featured_image_from_title_enable(), __( '「タイトルからアイキャッチを生成する」機能を有効にする', THEME_NAME ).__( '（β版）', THEME_NAME ));
            generate_image_preview_tag('https://wp-cocoon.com/wp-content/uploads/2025/01/featured-image-from-title.png');
            generate_tips_tag(__( 'エディターのアイキャッチ設定エリア内に、「タイトルからアイキャッチを生成する」オプションを追加します。', THEME_NAME ).__( '「公開（保存）」ボタンが押された時だけ画像が生成されます。', THEME_NAME ).__( 'ベータ版機能ですので、仕様が変わる可能性があるのでご了承ください。', THEME_NAME ).get_help_page_tag('https://wp-cocoon.com/automatic-generation-of-featured-image-from-title/'));
            ?>
          </td>
          <td>
            <?php
            generate_checkbox_tag(OP_FEATURED_IMAGE_FROM_TITLE_DEFAULT , is_featured_image_from_title_default_enable(), __( '「タイトルからアイキャッチを生成する」機能をデフォルトで有効にする', THEME_NAME ).__( '（β版）', THEME_NAME ));
            generate_tips_tag(__( 'アイキャッチ生成機能のチェックボックスの選択をデフォルトで有効にします。', THEME_NAME ));
            ?>
          </td>
        </tr>

        </tbody>
    </table>

  </div>
</div>


<!-- ブロックエディター -->
<div id="block-editor" class="postbox">
  <h2 class="hndle"><?php _e( 'ブロックエディター設定', THEME_NAME ) ?></h2>
  <div class="inside">

  <table class="form-table">
    <tbody>
    <p><?php _e( 'ブロックエディターのみに適用される設定です。', THEME_NAME ) ?></p>

        <!-- ボタン -->
        <tr>
          <th scope="row">
            <?php generate_label_tag('', __('ボタン', THEME_NAME) ); ?>
          </th>
          <td>
            <?php //ルビ
            generate_checkbox_tag(OP_BLOCK_EDITOR_RUBY_BUTTON_VISIBLE , is_block_editor_ruby_button_visible(), __( '「ふりがな（ルビ）」ボタンを表示する', THEME_NAME ));
            generate_tips_tag(__( 'Cocoonの「ルビ」ボタン表示を切り替えます。プラグインのルビ機能を利用していてエラーが出る場合は無効にしてください。', THEME_NAME ));
            ?>

            <?php //組織のクリア
            generate_checkbox_tag(OP_BLOCK_EDITOR_CLEAR_FORMAT_BUTTON_VISIBLE , is_block_editor_clear_format_button_visible(), __( '「書式のクリア」ボタンを表示する', THEME_NAME ));
            generate_tips_tag(__( 'Cocoonの「書式のクリア」表示を切り替えます。プラグインのルビ機能を利用していてエラーが出る場合は無効にしてください。', THEME_NAME ));
            ?>
          </td>
        </tr>

        <!-- スタイルドロップダウン -->
        <tr>
          <th scope="row">
            <?php generate_label_tag('', __('スタイルドロップダウン', THEME_NAME) ); ?>
          </th>
          <td>
            <?php
            generate_checkbox_tag(OP_BLOCK_EDITOR_LETTER_STYLE_DROPDOWN_VISIBLE , is_block_editor_letter_style_dropdown_visible(), __( '「文字スタイル」を表示する', THEME_NAME ));
            generate_tips_tag(__( 'ツールバーの文字スタイルドロップダウンを表示するか。', THEME_NAME ));

            generate_checkbox_tag(OP_BLOCK_EDITOR_MARKER_STYLE_DROPDOWN_VISIBLE , is_block_editor_marker_style_dropdown_visible(), __( '「マーカー」を表示する', THEME_NAME ));
            generate_tips_tag(__( 'ツールバーのマーカースタイルドロップダウンを表示するか。', THEME_NAME ));

            generate_checkbox_tag(OP_BLOCK_EDITOR_BADGE_STYLE_DROPDOWN_VISIBLE , is_block_editor_badge_style_dropdown_visible(), __( '「バッジ」を表示する', THEME_NAME ));
            generate_tips_tag(__( 'ツールバーのバッジスタイルドロップダウンを表示するか。', THEME_NAME ));

            generate_checkbox_tag(OP_BLOCK_EDITOR_FONT_SIZE_STYLE_DROPDOWN_VISIBLE , is_block_editor_font_size_style_dropdown_visible(), __( '「文字サイズ」を表示する', THEME_NAME ));
            generate_tips_tag(__( 'ツールバーの文字サイズスタイルドロップダウンを表示するか。', THEME_NAME ));
            ?>
          </td>
        </tr>

        <!-- ショートコードドロップダウン -->
        <tr>
          <th scope="row">
            <?php generate_label_tag('', __('ショートコードドロップダウン', THEME_NAME) ); ?>
          </th>
          <td>
            <?php
            generate_checkbox_tag(OP_BLOCK_EDITOR_GENERAL_SHORTCODE_DROPDOWN_VISIBLE , is_block_editor_general_shortcode_dropdown_visible(), __( '「汎用ショートコード」を表示する', THEME_NAME ));
            generate_tips_tag(__( 'ツールバーの汎用ショートコードドロップダウンを表示するか。', THEME_NAME ));

            generate_checkbox_tag(OP_BLOCK_EDITOR_TEMPLATE_SHORTCODE_DROPDOWN_VISIBLE , is_block_editor_template_shortcode_dropdown_visible(), __( '「テンプレート」表示する', THEME_NAME ));
            generate_tips_tag(__( 'ツールバーのテンプレートショートコードドロップダウンを表示するか。', THEME_NAME ));

            generate_checkbox_tag(OP_BLOCK_EDITOR_AFFILIATE_SHORTCODE_DROPDOWN_VISIBLE , is_block_editor_affiliate_shortcode_dropdown_visible(), __( '「アフィリエイト」を表示する', THEME_NAME ));
            generate_tips_tag(__( 'ツールバーのアフィリエイトショートコードドロップダウンを表示するか。', THEME_NAME ));

            generate_checkbox_tag(OP_BLOCK_EDITOR_RANKING_SHORTCODE_DROPDOWN_VISIBLE , is_block_editor_ranking_shortcode_dropdown_visible(), __( '「ランキング」を表示する', THEME_NAME ));
            generate_tips_tag(__( 'ツールバーのランキングショートコードドロップダウンを表示するか。', THEME_NAME ));
            ?>
          </td>
        </tr>


        <!-- ブロックオプション -->
        <tr>
          <th scope="row">
            <?php generate_label_tag('', __('ブロックオプション', THEME_NAME) ); ?>
          </th>
          <td>
            <?php
            generate_checkbox_tag(OP_BLOCK_EDITOR_STYLE_BLOCK_OPTION_VISIBLE , is_block_editor_style_block_option_visible(), __( '「スタイル」オプションを表示する', THEME_NAME ));
            generate_tips_tag(__( 'ブロックエディターの「スタイル」オプションを表示するか。', THEME_NAME ));
            ?>
          </td>
        </tr>

        <!-- 拡張カラーパレット色 -->
        <tr>
          <th scope="row">
            <?php generate_label_tag('', __('拡張カラーパレット色', THEME_NAME) ); ?>
          </th>
          <td>
            <?php
            generate_color_picker_tag(OP_BLOCK_EDITOR_EXTENDED_PALETTE_COLOR_A,  get_block_editor_extended_palette_color_a(), __( '拡張色A', THEME_NAME ));
            generate_color_picker_tag(OP_BLOCK_EDITOR_EXTENDED_PALETTE_COLOR_B,  get_block_editor_extended_palette_color_b(), __( '拡張色B', THEME_NAME ));
            generate_color_picker_tag(OP_BLOCK_EDITOR_EXTENDED_PALETTE_COLOR_C,  get_block_editor_extended_palette_color_c(), __( '拡張色C', THEME_NAME ));
            generate_color_picker_tag(OP_BLOCK_EDITOR_EXTENDED_PALETTE_COLOR_D,  get_block_editor_extended_palette_color_d(), __( '拡張色D', THEME_NAME ));
            generate_color_picker_tag(OP_BLOCK_EDITOR_EXTENDED_PALETTE_COLOR_E,  get_block_editor_extended_palette_color_e(), __( '拡張色E', THEME_NAME ));
            generate_color_picker_tag(OP_BLOCK_EDITOR_EXTENDED_PALETTE_COLOR_F,  get_block_editor_extended_palette_color_f(), __( '拡張色F', THEME_NAME ));

            generate_tips_tag(__( '任意のカラーパレット色を設定できます。', THEME_NAME ).get_help_page_tag('https://wp-cocoon.com/gutenberg-color-palette/'));
            ?>
          </td>
        </tr>


      </tbody>
    </table>

  </div>
</div>




<!-- 旧エディター -->
<div id="old-editor" class="postbox">
  <h2 class="hndle"><?php _e( '旧エディター設定', THEME_NAME ) ?></h2>
  <div class="inside">

  <table class="form-table">
    <tbody>
    <p><?php _e( 'ビジュアルエディター用の設定です。', THEME_NAME ) ?></p>

        <!-- 確認ダイアログ -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_CONFIRMATION_BEFORE_PUBLISH, __('確認ダイアログ', THEME_NAME) ); ?>
          </th>
          <td>
            <?php
            generate_checkbox_tag(OP_CONFIRMATION_BEFORE_PUBLISH , is_confirmation_before_publish(), __( 'ページ公開前に確認アラートを出す', THEME_NAME ));
            generate_tips_tag(__( '記事を投稿する前に確認ダイアログを表示します。※旧エディター用の設定', THEME_NAME ));
            ?>
          </td>
        </tr>


      </tbody>
    </table>

  </div>
</div>

</div><!-- /.metabox-holder -->
