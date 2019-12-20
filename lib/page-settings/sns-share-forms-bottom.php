<?php /**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit; ?>

<!-- ボトムシェアボタン -->
<div id="sns-share-bottom" class="postbox">
  <h2 class="hndle"><?php _e( 'ボトムシェアボタン', THEME_NAME ) ?></h2>
  <div class="inside">
    <p><?php _e( 'ボトムシェアボタンの表示に関する設定です。', THEME_NAME ) ?></p>
    <table class="form-table">
      <tbody>

        <?php if (DEBUG_ADMIN_DEMO_ENABLE && apply_filters('cocoon_setting_preview_sns_share_bottom', true)): ?>
        <!-- プレビュー画面 -->
        <tr>
          <th scope="row">
            <label><?php _e( 'プレビュー', THEME_NAME ) ?></label>
          </th>
          <td>
            <div class="demo">
            <?php //テンプレートの読み込み
              if (is_sns_bottom_share_buttons_visible())
                get_template_part_with_option('tmp/sns-share-buttons', SS_BOTTOM); ?>
            </div>
          </td>
        </tr>
        <?php endif; ?>

        <!-- ボトムシェアボタンの表示 -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_SNS_BOTTOM_SHARE_BUTTONS_VISIBLE, __( 'ボトムシェアボタンの表示', THEME_NAME )); ?>
          </th>
          <td>
            <?php
            generate_checkbox_tag( OP_SNS_BOTTOM_SHARE_BUTTONS_VISIBLE, is_sns_bottom_share_buttons_visible(), __( 'メインカラムボトムシェアボタンを表示', THEME_NAME ));
            generate_tips_tag(__( 'ボトムシェアボタンの表示を切り替えます。', THEME_NAME ));
            ?>
          </td>
        </tr>

        <!-- シェアメッセージ -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_SNS_BOTTOM_SHARE_MESSAGE, __( 'シェアメッセージ', THEME_NAME )); ?>
          </th>
          <td>
            <?php
            generate_textbox_tag(OP_SNS_BOTTOM_SHARE_MESSAGE, get_sns_bottom_share_message(), __( 'シェアメッセージの入力', THEME_NAME ));
            generate_tips_tag(__( '訪問者にシェアを促すメッセージを入力してください。', THEME_NAME ));
            ?>
          </td>
        </tr>

        <!-- 表示切替 -->
        <tr>
          <th scope="row">
            <?php generate_label_tag('', __( '表示切替', THEME_NAME )); ?>
          </th>
          <td>
            <p><?php _e( '個々のシェアボタンの表示切り替え。', THEME_NAME ) ?></p>
            <ul>
              <li>
                <?php generate_checkbox_tag(OP_BOTTOM_TWITTER_SHARE_BUTTON_VISIBLE, is_bottom_twitter_share_button_visible(), __( 'Twitter', THEME_NAME )); ?>
              </li>
              <li>
                <?php generate_checkbox_tag(OP_BOTTOM_FACEBOOK_SHARE_BUTTON_VISIBLE, is_bottom_facebook_share_button_visible(), __( 'Facebook', THEME_NAME )); ?>
              </li>
              <li>
                <?php generate_checkbox_tag(OP_BOTTOM_HATEBU_SHARE_BUTTON_VISIBLE, is_bottom_hatebu_share_button_visible(), __( 'はてなブックマーク', THEME_NAME )); ?>
              </li>
              <!-- <li>
                <?php generate_checkbox_tag(OP_BOTTOM_GOOGLE_PLUS_SHARE_BUTTON_VISIBLE, is_bottom_google_plus_share_button_visible(), __( 'Google', THEME_NAME )); ?>
              </li> -->
              <li>
                <?php generate_checkbox_tag(OP_BOTTOM_POCKET_SHARE_BUTTON_VISIBLE, is_bottom_pocket_share_button_visible(), __( 'Pocket', THEME_NAME )); ?>
              </li>
              <li>
                <?php generate_checkbox_tag(OP_BOTTOM_LINE_AT_SHARE_BUTTON_VISIBLE, is_bottom_line_at_share_button_visible(), __( 'LINE@', THEME_NAME )); ?>
              </li>
              <li>
                <?php generate_checkbox_tag(OP_BOTTOM_PINTEREST_SHARE_BUTTON_VISIBLE, is_bottom_pinterest_share_button_visible(), __( 'Pinterest', THEME_NAME )); ?>
              </li>
              <li>
                <?php generate_checkbox_tag(OP_BOTTOM_LINKEDIN_SHARE_BUTTON_VISIBLE, is_bottom_linkedin_share_button_visible(), __( 'LinkedIn', THEME_NAME )); ?>
              </li>
              <li>
                <?php generate_checkbox_tag(OP_BOTTOM_COPY_SHARE_BUTTON_VISIBLE, is_bottom_copy_share_button_visible(), __( 'タイトルとURLをコピー', THEME_NAME )); ?>
              </li>
            </ul>
            <p><?php _e( 'シェアボタンを選択してください。', THEME_NAME ) ?></p>
          </td>
        </tr>


        <!-- 表示ページ -->
        <tr>
          <th scope="row">
            <?php generate_label_tag('', __( '表示ページ', THEME_NAME )); ?>
          </th>
          <td>
            <p><?php _e( 'シェアボタンを表示するページの切り替え。', THEME_NAME ) ?></p>
            <ul>
              <li>
                <?php generate_checkbox_tag(OP_SNS_FRONT_PAGE_BOTTOM_SHARE_BUTTONS_VISIBLE, is_sns_front_page_bottom_share_buttons_visible(), __( 'フロントページ', THEME_NAME )); ?>
              </li>
              <li>
                <?php generate_checkbox_tag(OP_SNS_SINGLE_BOTTOM_SHARE_BUTTONS_VISIBLE, is_sns_single_bottom_share_buttons_visible(), __( '投稿', THEME_NAME )); ?>
              </li>
              <li>
                <?php generate_checkbox_tag(OP_SNS_PAGE_BOTTOM_SHARE_BUTTONS_VISIBLE, is_sns_page_bottom_share_buttons_visible(), __( '固定ページ', THEME_NAME )); ?>
              </li>
              <li>
                <?php generate_checkbox_tag(OP_SNS_CATEGORY_BOTTOM_SHARE_BUTTONS_VISIBLE, is_sns_category_bottom_share_buttons_visible(), __( 'カテゴリー', THEME_NAME )); ?>
              </li>
              <li>
                <?php generate_checkbox_tag(OP_SNS_TAG_BOTTOM_SHARE_BUTTONS_VISIBLE, is_sns_tag_bottom_share_buttons_visible(), __( 'タグ', THEME_NAME )); ?>
              </li>
            <p><?php _e( 'シェアボタンを表示するページを選択してください。', THEME_NAME );echo get_help_page_tag('https://wp-cocoon.com/sns-button-display-switching/'); ?></p>
          </td>
        </tr>


        <!-- ボタンカラー -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_SNS_BOTTOM_SHARE_BUTTON_COLOR, __( 'ボタンカラー', THEME_NAME )); ?>
          </th>
          <td>
            <?php
            $options = array(
              'monochrome' => __( 'モノクロ', THEME_NAME ),
              'brand_color' => __( 'ブランドカラー', THEME_NAME ),
              'brand_color_white' => __( 'ブランドカラー（白抜き）', THEME_NAME ),
            );
            generate_selectbox_tag(OP_SNS_BOTTOM_SHARE_BUTTON_COLOR, $options, get_sns_bottom_share_button_color());
            generate_tips_tag(__( 'ボトムシェアボタンの配色を選択してください。', THEME_NAME ));
            ?>
          </td>
        </tr>

        <!-- カラム数 -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_SNS_BOTTOM_SHARE_COLUMN_COUNT, __( 'カラム数', THEME_NAME )); ?>
          </th>
          <td>
            <?php
            $options = array(
              '1' => __( '1列', THEME_NAME ),
              '2' => __( '2列', THEME_NAME ),
              '3' => __( '3列', THEME_NAME ),
              '4' => __( '4列', THEME_NAME ),
              '5' => __( '5列', THEME_NAME ),
              '6' => __( '6列', THEME_NAME ),
            );
            generate_selectbox_tag(OP_SNS_BOTTOM_SHARE_COLUMN_COUNT, $options, get_sns_bottom_share_column_count());
            generate_tips_tag(__( 'ボトムシェアボタンのカラム数を選択してください。', THEME_NAME ));
            ?>
          </td>
        </tr>

        <!-- ロゴ・キャプション配置 -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_SNS_BOTTOM_SHARE_LOGO_CAPTION_POSITION, __( 'ロゴ・キャプション配置', THEME_NAME )); ?>
          </th>
          <td>
            <?php
            $options = array(
              'left_and_right' => __( 'ロゴ・キャプション 左右', THEME_NAME ),
              'high_and_low_lc' => __('ロゴ・キャプション 上下' , THEME_NAME ),
              'high_and_low_cl' => __( 'キャプション・ロゴ 上下', THEME_NAME ),
            );
            generate_selectbox_tag(OP_SNS_BOTTOM_SHARE_LOGO_CAPTION_POSITION, $options, get_sns_bottom_share_logo_caption_position());
            generate_tips_tag(__( 'ボトムシェアボタンのロゴとキャプションの配置を選択してください。', THEME_NAME ));
            ?>
          </td>
        </tr>

        <!-- シェア数の表示 -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_SNS_BOTTOM_SHARE_BUTTONS_COUNT_VISIBLE, __( 'シェア数の表示', THEME_NAME )); ?>
          </th>
          <td>
            <?php
            generate_checkbox_tag( OP_SNS_BOTTOM_SHARE_BUTTONS_COUNT_VISIBLE, is_sns_bottom_share_buttons_count_visible(), __( 'シェア数を表示', THEME_NAME ));
            generate_tips_tag(__( 'ボトムシェアボタンのシェア数表示を切り替えます。', THEME_NAME ));
            ?>
          </td>
        </tr>

      </tbody>
    </table>

  </div>
</div>
