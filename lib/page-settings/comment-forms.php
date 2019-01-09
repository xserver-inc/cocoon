<?php /**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit; ?>

<div class="metabox-holder">

<!-- コメント -->
<div id="comment8" class="postbox">
  <h2 class="hndle"><?php _e( 'コメント設定', THEME_NAME ) ?></h2>
  <div class="inside">

    <p><?php _e( 'コメント一覧や入力欄のみタグに関する設定です。', THEME_NAME ) ?></p>

    <table class="form-table">
      <tbody>
        <?php if (0): ?>
        <!-- プレビュー画面 -->
        <tr>
          <th scope="row">
            <label><?php _e( 'プレビュー', THEME_NAME ) ?></label>
          </th>
          <td>
            <div class="demo">
              <?php get_template_part('comments'); ?>
            </div>
            <?php generate_tips_tag(__( 'デモはランダム表示です。', THEME_NAME )); ?>
          </td>
        </tr>
        <?php endif ?>

        <!-- コメントタイプ -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_COMMENT_DISPLAY_TYPE, __('コメントタイプ', THEME_NAME) ); ?>
          </th>
          <td>
            <?php
            $options = array(
              'default' => __( 'デフォルト', THEME_NAME ),
              'simple_thread' => __( 'シンプルスレッド', THEME_NAME ),
            );
            generate_radiobox_tag(OP_COMMENT_DISPLAY_TYPE, $options, get_comment_display_type());
            generate_tips_tag(__( 'コメントの表示形式を変更します。', THEME_NAME ));
            ?>
          </td>
        </tr>


        <!-- コメント一覧見出し -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_COMMENT_HEADING, __('コメント一覧見出し', THEME_NAME) ); ?>
          </th>
          <td>
            <?php
            generate_textbox_tag(OP_COMMENT_HEADING, get_comment_heading(), __( '見出し', THEME_NAME ));
            generate_tips_tag(__( 'コメント一覧の見出しを入力してください。', THEME_NAME ));
            generate_textbox_tag(OP_COMMENT_SUB_HEADING, get_comment_sub_heading(), __( 'サブ見出し', THEME_NAME ));
            generate_tips_tag(__( 'コメント一覧の補助となる見出しを入力してください。', THEME_NAME ));
            ?>
          </td>
        </tr>

        <!-- コメント入力欄表示 -->
        <tr>
          <th scope="row">
            <?php
            generate_label_tag(OP_COMMENT_FORM_DISPLAY_TYPE, __('コメント入力欄表示', THEME_NAME) );
            generate_preview_tooltip_tag('https://im-cocoon.net/wp-content/uploads/comment-toggle.gif', __( 'ボタン切り換え動作。', THEME_NAME ), 400);
             ?>
          </th>
          <td>
            <?php
            $options = array(
              'always' => __( '常に表示', THEME_NAME ),
              'toggle_button' => __( 'ボタンで表示切り替え', THEME_NAME ),
            );
            generate_radiobox_tag(OP_COMMENT_FORM_DISPLAY_TYPE, $options, get_comment_form_display_type());
            generate_tips_tag(__( 'コメント入力欄の表示状態を設定します。', THEME_NAME ).get_help_page_tag('https://wp-cocoon.com/toggle-comment-button/'));
            ?>
          </td>
        </tr>

        <!-- コメント入力欄見出し -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_COMMENT_FORM_HEADING, __('コメント入力欄見出し', THEME_NAME) ); ?>
          </th>
          <td>
            <?php
            generate_textbox_tag(OP_COMMENT_FORM_HEADING, get_comment_form_heading(), __( '見出し', THEME_NAME ));
            generate_tips_tag(__( 'コメント入力欄の見出しを入力してください。', THEME_NAME ));
            ?>
          </td>
        </tr>

        <!-- コメント案内メッセージ -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_COMMENT_INFORMATION_MESSAGE, __('コメント入力案内メッセージ', THEME_NAME) ); ?>
          </th>
          <td>
            <?php
            generate_visuel_editor_tag(OP_COMMENT_INFORMATION_MESSAGE, get_comment_information_message(), $editor_id = 'comment-information-message');
            generate_tips_tag(__( 'コメント入力フォームの上に表示する案内メッセージを入力してください。', THEME_NAME ).get_help_page_tag('https://wp-cocoon.com/comment-info/'));
            ?>
          </td>
        </tr>

        <!-- ウェブサイト入力欄の表示 -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_COMMENT_WEBSITE_VISIBLE, __('ウェブサイトの表示', THEME_NAME) ); ?>
          </th>
          <td>
            <?php
            generate_checkbox_tag(OP_COMMENT_WEBSITE_VISIBLE , is_comment_website_visible(), __( 'ウェブサイト入力ボックスを表示する', THEME_NAME ));
            generate_tips_tag(__( 'ウェブサイト入力欄を表示するか。', THEME_NAME ));
            ?>
          </td>
        </tr>

        <!-- コメント送信ボタンラベル -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_COMMENT_SUBMIT_LABEL, __('送信ボタンラベル', THEME_NAME) ); ?>
          </th>
          <td>
            <?php
            generate_textbox_tag(OP_COMMENT_SUBMIT_LABEL, get_comment_submit_label(), __( 'コメントを送信', THEME_NAME ));
            generate_tips_tag(__( 'コメントの送信ボタンのラベルテキストを入力してください。', THEME_NAME ));
            ?>
          </td>
        </tr>

      </tbody>
    </table>

  </div>
</div>


</div><!-- /.metabox-holder -->
