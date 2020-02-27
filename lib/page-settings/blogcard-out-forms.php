<?php /**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit; ?>

<div class="metabox-holder">

<!-- 外部ブログカード設定 -->
<div id="external-blogcard" class="postbox">
  <h2 class="hndle"><?php _e( '外部ブログカード設定', THEME_NAME ) ?></h2>
  <div class="inside">

    <p><?php _e( '外部のURLやURLリンクをブログカード形式で表示するための設定です。', THEME_NAME );
    echo get_help_page_tag('https://wp-cocoon.com/how-to-use-external-blogcard/'); ?></p>

    <table class="form-table">
      <tbody>
        <?php if (DEBUG_ADMIN_DEMO_ENABLE && apply_filters('cocoon_setting_preview_external_blogcard', true)): ?>
        <!-- プレビュー画面 -->
        <tr>
          <th scope="row">
            <label><?php _e( 'プレビュー', THEME_NAME ) ?></label>
          </th>
          <td>
            <div class="demo">
            <?php
            $url = 'https://wp-simplicity.com/';
            if (is_external_blogcard_enable()) {
              echo url_to_external_ogp_blogcard_tag($url);
            } else {
              echo '<a href="'.$url.'">'.$url.'</a>';
            }
            generate_tips_tag(__( '外部リンクのブログカードです。', THEME_NAME ));

            ?>
            </div>
          </td>
        </tr>
        <?php endif; ?>

        <!--  ブログカード表示 -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_EXTERNAL_BLOGCARD_ENABLE, __( 'ブログカード表示', THEME_NAME ) ); ?>
          </th>
          <td>
            <?php
            generate_checkbox_tag(OP_EXTERNAL_BLOGCARD_ENABLE, is_external_blogcard_enable(), __( 'ブログカード表示を有効にする', THEME_NAME ));
            generate_tips_tag(__( '本文中にある外部サイトのURLやURLリンクをブログカード表示します。', THEME_NAME ));

            echo '<div class="indent'.get_not_allowed_form_class(is_external_blogcard_enable(), true).'">';
              generate_checkbox_tag(OP_COMMENT_EXTERNAL_BLOGCARD_ENABLE
              , is_comment_external_blogcard_enable(), __( 'コメント欄のブログカード表示を有効にする', THEME_NAME ));
              generate_tips_tag(__( 'コメント内に書き込まれた独立したURLをブログカード化します。', THEME_NAME ).__( 'コメント内の外部リンクブログカードの場合rel="nofollow"が入ります。', THEME_NAME ).get_help_page_tag('https://wp-cocoon.com/comment-blogcard/'));
            echo '</div>';
            ?>
          </td>
        </tr>


        <!-- サムネイルスタイル  -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_EXTERNAL_BLOGCARD_THUMBNAIL_STYLE, __( 'サムネイルスタイル', THEME_NAME ) ); ?>
          </th>
          <td>
            <?php
            $options = array(
              'left' => __( '左側', THEME_NAME ),
              'right' => __( '右側', THEME_NAME ),
            );
            generate_radiobox_tag(OP_EXTERNAL_BLOGCARD_THUMBNAIL_STYLE, $options, get_external_blogcard_thumbnail_style());
            generate_tips_tag(__( 'サムネイルの表示位置を選択してください。', THEME_NAME ));
            ?>
          </td>
        </tr>


        <!--  リンクの開き方 -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_EXTERNAL_BLOGCARD_TARGET_BLANK, __( 'リンクの開き方', THEME_NAME ) ); ?>
          </th>
          <td>
            <?php
            //generate_alert_tag('この機能は「外部リンク設定」機能に統合されました。この項目は、数バージョン表示の後削除されます。');
            generate_checkbox_tag(OP_EXTERNAL_BLOGCARD_TARGET_BLANK, is_external_blogcard_target_blank(), __( '新しいタブで開く', THEME_NAME ));
            generate_tips_tag(__( 'ブログカードクリック時に新規タブを開きます。「外部リンク」が設定されている場合は、そちらが優先されます。', THEME_NAME ));
            ?>
          </td>
        </tr>


        <!-- キャッシュの保存期間 -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_EXTERNAL_BLOGCARD_CACHE_RETENTION_PERIOD, __( 'キャッシュの保存期間', THEME_NAME ) ); ?>
          </th>
          <td>
            <?php
            generate_number_tag(OP_EXTERNAL_BLOGCARD_CACHE_RETENTION_PERIOD, get_external_blogcard_cache_retention_period(), 30, 1, 365);
            generate_tips_tag(__( 'ブログカードキャッシュのリフレッシュ間隔を設定します。1～365日の間隔を選べます。', THEME_NAME ));
            ?>
          </td>
        </tr>

        <!--  キャッシュの更新 -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_EXTERNAL_BLOGCARD_REFRESH_MODE, __( 'キャッシュの更新', THEME_NAME ) ); ?>
          </th>
          <td>
            <?php
            generate_checkbox_tag(OP_EXTERNAL_BLOGCARD_REFRESH_MODE, is_external_blogcard_refresh_mode(), __( 'キャッシュ更新モードを有効にする', THEME_NAME ));
            generate_tips_tag(__( 'キャッシュ更新モードを有効にした状態でページを開くと、ページ上の外部ブログカードキャッシュを新たに取得します。', THEME_NAME ));
            ?>
          </td>
        </tr>

      </tbody>
    </table>

  </div>
</div>


<?php //ブログカードヘルプ
require_once abspath(__FILE__).'blogcard-help.php';; ?>

</div><!-- /.metabox-holder -->
