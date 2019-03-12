<?php /**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit; ?>

<div class="metabox-holder">

<!-- 通知 -->
<div id="notice-area-page" class="postbox">
  <h2 class="hndle"><?php _e( '通知設定', THEME_NAME ) ?></h2>
  <div class="inside">

    <p>
      <?php _e( 'サイト上部ベルト状に表示される通知メッセージの設定です。', THEME_NAME );
      echo get_help_page_tag('https://wp-cocoon.com/notice-area/');
     ?>
    </p>

    <?php if (DEBUG_ADMIN_DEMO_ENABLE && apply_filters('cocoon_setting_preview_notice', true)): ?>
    <p class="preview-label"><?php _e( 'プレビュー', THEME_NAME ) ?></p>
    <div class="demo notice-area-demo">
      <?php //通知エリア
      get_template_part('tmp/notice'); ?>
    </div>
    <?php endif; ?>

    <table class="form-table">
      <tbody>

        <!-- 通知表示 -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_NOTICE_AREA_VISIBLE, __('通知表示', THEME_NAME) ); ?>
          </th>
          <td>
            <?php
            generate_checkbox_tag(OP_NOTICE_AREA_VISIBLE , is_notice_area_visible(), __( '通知エリアの表示', THEME_NAME ));
            generate_tips_tag(__( '通知メッセージを入力して「通知表示」を有効にすればヘッダー下に通知メッセージが表示されます。', THEME_NAME ));
            ?>
          </td>
        </tr>

        <!-- 通知メッセージ -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_NOTICE_AREA_MESSAGE, __('通知メッセージ', THEME_NAME) ); ?>
          </th>
          <td>
            <?php
            generate_textbox_tag(OP_NOTICE_AREA_MESSAGE, get_notice_area_message(), __( 'メッセージを入力してください。', THEME_NAME ));
            generate_tips_tag(__( '通知エリアに表示するメッセージを入力してください。', THEME_NAME ));
            ?>
          </td>
        </tr>

        <!-- 通知URL -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_NOTICE_AREA_URL, __('通知URL', THEME_NAME) ); ?>
          </th>
          <td>
            <?php
            generate_textbox_tag(OP_NOTICE_AREA_URL, get_notice_area_url(), __( 'http://', THEME_NAME ));
            generate_tips_tag(__( '通知エリアにリンクを設定する場合はURLを入力してください。', THEME_NAME ));
            ?>
          </td>
        </tr>

        <!-- リンクの開き方 -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_NOTICE_LINK_TARGET_BLANK, __('リンクの開き方', THEME_NAME) ); ?>
          </th>
          <td>
            <?php
            generate_checkbox_tag(OP_NOTICE_LINK_TARGET_BLANK , is_notice_link_target_blank(), __( '通知リンクを新しいタブで開く', THEME_NAME ));
            generate_tips_tag(__( '通知エリアのリンクをtarget="_blank"で開くかどうか。', THEME_NAME ));
            ?>
          </td>
        </tr>

        <!-- 通知タイプ -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_NOTICE_TYPE, __('通知タイプ', THEME_NAME) ); ?>
          </th>
          <td>
            <?php
            $options = array(
              'notice' => __( '通知（緑色）', THEME_NAME ),
              'warning' => __( '注意（黄色）', THEME_NAME ),
              'danger' => __( '警告（赤色）', THEME_NAME ),
            );
            generate_selectbox_tag(OP_NOTICE_TYPE, $options, get_notice_type());
            generate_tips_tag(__( '通知の種類を選択してください。背景色も変わり、重要度が変わります。', THEME_NAME ));
            ?>
          </td>
        </tr>

        <!-- 背景色 -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_NOTICE_AREA_BACKGROUND_COLOR, __('色', THEME_NAME) ); ?>
          </th>
          <td>
            <?php
            generate_color_picker_tag(OP_NOTICE_AREA_BACKGROUND_COLOR,  get_notice_area_background_color(), '通知エリア背景色');
            generate_tips_tag(__( 'メッセージに対して独自の背景色を設定したい場合は色を選択してください。デフォルト色を変更したい場合は、こちらの色が優先されます。', THEME_NAME ));

            generate_color_picker_tag(OP_NOTICE_AREA_TEXT_COLOR,  get_notice_area_text_color(), '通知エリア文字色');
            generate_tips_tag(__( 'メッセージに対して独自の背テキスト色を設定したい場合は色を選択してください。デフォルト色を変更したい場合は、こちらの色が優先されます。', THEME_NAME ));
            ?>
          </td>
        </tr>

      </tbody>
    </table>

  </div>
</div>

</div><!-- /.metabox-holder -->
