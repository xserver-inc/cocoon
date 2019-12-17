<?php /**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit; ?>

<div class="metabox-holder">

<!-- アピールエリア -->
<div id="appeal-area" class="postbox">
  <h2 class="hndle"><?php _e( 'アピールエリア設定', THEME_NAME ) ?></h2>
  <div class="inside">

    <p><?php _e( 'ヘッダー下でアピールしたい内容を入力します。', THEME_NAME ) ?></p>
    <?php if (DEBUG_ADMIN_DEMO_ENABLE && apply_filters('cocoon_setting_preview_appeal', true)): ?>
      <p class="preview-label"><?php _e( 'プレビュー', THEME_NAME ) ?></p>
      <div class="demo appeal-area-demo" style="">
        <?php get_sanitize_preview_template_part('tmp/appeal') ?>
      </div>
      <?php
      generate_tips_tag(__( 'デモの表示は実際の表示と多少変わる可能性があります。', THEME_NAME ));
      ?>
    <?php endif; ?>

    <table class="form-table">
      <tbody>

        <!-- アピールエリアの表示 -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_APPEAL_AREA_DISPLAY_TYPE, __('アピールエリアの表示', THEME_NAME) ); ?>
          </th>
          <td>
            <?php
            $options = array(
              'none' => __( '表示しない', THEME_NAME ),
              'all_page' => __( '全ページで表示', THEME_NAME ),
              'front_page_only' => __( 'フロントページのみで表示', THEME_NAME ),
              'not_singular' => __( '投稿・固定ページ以外で表示', THEME_NAME ),
              'singular_only' => __( '投稿・固定ページのみで表示', THEME_NAME ),
              'single_only' => __( '投稿ページのみで表示', THEME_NAME ),
              'page_only' => __( '固定ページのみで表示', THEME_NAME ),
            );
            generate_selectbox_tag(OP_APPEAL_AREA_DISPLAY_TYPE, $options, get_appeal_area_display_type());
            generate_tips_tag(__( 'アピールエリアを表示するページを設定します。', THEME_NAME ));
            ?>
          </td>
        </tr>

        <!-- 高さ -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_APPEAL_AREA_HEIGHT, __('高さ', THEME_NAME) ); ?>
          </th>
          <td>
            <?php
            generate_number_tag(OP_APPEAL_AREA_HEIGHT, get_appeal_area_height(), '', 200, 800);
            generate_tips_tag(__( 'アピールエリアの高さをpx数で指定します。モバイル環境では高さは無効になります。（最小：200px、最大：800px）', THEME_NAME ));
            ?>
          </td>
        </tr>

        <!-- エリア画像 -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_APPEAL_AREA_IMAGE_URL, __('エリア画像', THEME_NAME) ); ?>
          </th>
          <td>
            <?php
            generate_upload_image_tag(OP_APPEAL_AREA_IMAGE_URL, get_appeal_area_image_url());
            generate_tips_tag(__( 'アピールエリアの背景に表示する画像を設定します。', THEME_NAME ));

            //ヘッダー背景画像の固定
            generate_checkbox_tag(OP_APPEAL_AREA_BACKGROUND_ATTACHMENT_FIXED, is_appeal_area_background_attachment_fixed(), __( 'アピールエリア背景画像の固定', THEME_NAME ));
            generate_tips_tag(__( 'アピールエリアに設定した背景画像を固定します。上下にスクロールしたときに背景画像が移動しなくなります。', THEME_NAME ));
            ?>
          </td>
        </tr>

        <!-- 背景色 -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_APPEAL_AREA_BACKGROUND_COLOR, __('エリア背景色', THEME_NAME) );
            generate_select_color_tip_tag(); ?>
          </th>
          <td>
            <?php
            generate_color_picker_tag(OP_APPEAL_AREA_BACKGROUND_COLOR,  get_appeal_area_background_color(), '背景色');
            generate_tips_tag(__( 'アピールエリアの背景色を設定してください。背景画像を設定すると隠れるエリアとなります。ただ、画像読み込み中に表示される部分でもあります。', THEME_NAME ));
            ?>
          </td>
        </tr>

        <!-- タイトル -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_APPEAL_AREA_TITLE, __('タイトル', THEME_NAME) ); ?>
          </th>
          <td>
            <?php
            generate_textbox_tag(OP_APPEAL_AREA_TITLE, get_appeal_area_title(), __( 'アピールエリアタイトル', THEME_NAME ));
            generate_tips_tag(__( 'アピールエリアのタイトルを入力してください。入力しない場合は表示されません。', THEME_NAME ));
            ?>
          </td>
        </tr>

        <!-- メッセージ -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_APPEAL_AREA_MESSAGE, __('メッセージ', THEME_NAME) ); ?>
          </th>
          <td>
            <?php
            generate_textarea_tag(OP_APPEAL_AREA_MESSAGE, get_appeal_area_message(), __( '訪問者にアピールしたい内容を入力してください。', THEME_NAME ), 3) ;
            generate_tips_tag(__( 'アピールエリアに表示するメッセージを入力してください。HTMLの入力も可能です。', THEME_NAME ));
            ?>
          </td>
        </tr>

        <!-- ボタンメッセージ -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_APPEAL_AREA_BUTTON_MESSAGE, __('ボタンメッセージ', THEME_NAME) ); ?>
          </th>
          <td>
            <?php
            generate_textbox_tag(OP_APPEAL_AREA_BUTTON_MESSAGE, get_appeal_area_button_message(), __( '例：詳細はこちら', THEME_NAME ));
            generate_tips_tag(__( 'ボタンに表示する文字を入力してください。', THEME_NAME ));
            ?>
          </td>
        </tr>

        <!-- ボタンリンク先 -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_APPEAL_AREA_BUTTON_URL, __('ボタンリンク先', THEME_NAME) ); ?>
          </th>
          <td>
            <?php
            generate_textbox_tag(OP_APPEAL_AREA_BUTTON_URL, get_appeal_area_button_url(), __( 'https://xxxxxxxx.com/xxxxx/', THEME_NAME ));
            generate_tips_tag(__( 'ボタンのリンク先となるURLを入力してください。', THEME_NAME ));
            ?>
          </td>
        </tr>

      <!-- ボタンリンク先 -->
      <tr>
        <th scope="row">
          <?php generate_label_tag(OP_APPEAL_AREA_BUTTON_TARGET, __('ボタンリンクの開き方', THEME_NAME) ); ?>
        </th>
        <td>
          <?php
            $options = array(
              '_self' => __( '同じタブで開く（_self）', THEME_NAME ),
              '_blank' => __( '新しいタブで開く（_blank）', THEME_NAME ),
            );
            generate_radiobox_tag(OP_APPEAL_AREA_BUTTON_TARGET, $options, get_appeal_area_button_target());
            generate_tips_tag(__( 'アピールエリアボタンのリンクの開き方を設定します。', THEME_NAME ));
          ?>
        </td>
      </tr>

        <!-- ボタン色 -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_APPEAL_AREA_BUTTON_BACKGROUND_COLOR, __('ボタン色', THEME_NAME) ); ?>
            <?php generate_select_color_tip_tag(); ?>
          </th>
          <td>
            <?php
            generate_color_picker_tag(OP_APPEAL_AREA_BUTTON_BACKGROUND_COLOR,  get_appeal_area_button_background_color(), __( 'ボタン色', THEME_NAME ));
            generate_tips_tag(__( 'ボタン全体の色を選択してください。文字は白色となるので濃いめの色を設定することをおすすめします。', THEME_NAME ));
            ?>
          </td>
        </tr>


      </tbody>
    </table>

  </div>
</div>

</div><!-- /.metabox-holder -->
