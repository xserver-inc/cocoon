<?php /**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit; ?>

<div class="metabox-holder">

<!-- トップへ戻るボタン -->
<div id="toc" class="postbox">
  <h2 class="hndle"><?php _e( 'トップへ戻るボタン設定', THEME_NAME ) ?></h2>
  <div class="inside">

    <p><?php _e( 'ページトップにスクロール移動するボタンの設定です。', THEME_NAME ) ?></p>

    <table class="form-table">
      <tbody>

      <?php if (DEBUG_ADMIN_DEMO_ENABLE && apply_filters('cocoon_setting_preview_buttons', true)): ?>
        <!-- プレビュー画面 -->
        <tr>
          <th scope="row">
            <label><?php _e( 'プレビュー', THEME_NAME ) ?></label>
          </th>
          <td>
            <div class="demo go-to-top" style="">
              <?php get_sanitize_preview_template_part('tmp/button-go-to-top') ?>
            </div>
            <?php generate_tips_tag(__( 'デモは動作しません。', THEME_NAME )); ?>
          </td>
        </tr>
        <?php endif; ?>

        <!-- トップへ戻るボタンの表示 -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_GO_TO_TOP_BUTTON_VISIBLE, __('トップへ戻るボタンの表示', THEME_NAME) ); ?>
          </th>
          <td>
            <?php
            generate_checkbox_tag(OP_GO_TO_TOP_BUTTON_VISIBLE , is_go_to_top_button_visible(), __( 'トップへ戻るボタンを表示する', THEME_NAME ));
            generate_tips_tag(__( 'トップへスクロール移動するボタンを表示するかどうか。', THEME_NAME ));
            ?>
          </td>
        </tr>

        <!-- ボタンのアイコンフォント -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_GO_TO_TOP_BUTTON_ICON_FONT, __('ボタンのアイコンフォント', THEME_NAME) ); ?>
          </th>
          <td>
            <?php
            $options = array(
              'fa-angle-double-up' => change_fa('<span class="fa fa-angle-double-up" aria-hidden="true"></span>'),
              'fa-angle-up' => change_fa('<span class="fa fa-angle-up" aria-hidden="true"></span>'),
              'fa-arrow-circle-up' => change_fa('<span class="fa fa-arrow-circle-up" aria-hidden="true"></span>'),
              'fa-arrow-up' => change_fa('<span class="fa fa-arrow-up" aria-hidden="true"></span>'),
              'fa-caret-up' => change_fa('<span class="fa fa-caret-up" aria-hidden="true"></span>'),
              'fa-caret-square-o-up' => change_fa('<span class="fa fa-caret-square-o-up" aria-hidden="true"></span>'),
              'fa-chevron-circle-up' => change_fa('<span class="fa fa-chevron-circle-up" aria-hidden="true"></span>'),
              'fa-chevron-up' => change_fa('<span class="fa fa-chevron-up" aria-hidden="true"></span>'),
              'fa-hand-o-up' => change_fa('<span class="fa fa-hand-o-up" aria-hidden="true"></span>'),
              'fa-long-arrow-up' => change_fa('<span class="fa fa-long-arrow-up" aria-hidden="true"></span>'),
              'fa-caret-square-o-up' => change_fa('<span class="fa fa-caret-square-o-up" aria-hidden="true"></span>'),
            );
            generate_radiobox_tag(OP_GO_TO_TOP_BUTTON_ICON_FONT, $options, get_go_to_top_button_icon_font(),__( 'アイコンフォント', THEME_NAME ) , true);
            generate_tips_tag(__( 'トップへ戻るボタンを示すアイコンフォントを選択します。', THEME_NAME ));
            ?>
          </td>
        </tr>

        <!-- 色 -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_GO_TO_TOP_BACKGROUND_COLOR, __('ボタン色', THEME_NAME) ); ?>
            <?php generate_select_color_tip_tag(); ?>
          </th>
          <td>
            <?php
            generate_color_picker_tag(OP_GO_TO_TOP_BACKGROUND_COLOR,  get_go_to_top_background_color(), __( '背景色', THEME_NAME ));
            generate_tips_tag(__( 'ボタンの背景色を設定します。', THEME_NAME ));

            generate_color_picker_tag(OP_GO_TO_TOP_TEXT_COLOR,  get_go_to_top_text_color(), __( '文字色', THEME_NAME ));
            generate_tips_tag(__( 'ボタンの文字色を設定します。', THEME_NAME ));
            ?>
          </td>
        </tr>

        <!-- ボタン画像 -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_GO_TO_TOP_BUTTON_IMAGE_URL, __('ボタン画像', THEME_NAME) ); ?>
          </th>
          <td>
            <?php
            generate_upload_image_tag(OP_GO_TO_TOP_BUTTON_IMAGE_URL, get_go_to_top_button_image_url());
            generate_tips_tag(__( 'トップへ戻るボタンのアイコンフォント代わりに表示する画像を選択します。こちらに画像を設定するとアイコンフォントボタンは表示されません。最大横幅は120pxになります。', THEME_NAME ));
            ?>
          </td>
        </tr>

      </tbody>
    </table>

  </div>
</div>

</div><!-- /.metabox-holder -->
