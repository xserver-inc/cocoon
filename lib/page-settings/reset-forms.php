<?php /**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit; ?>

<div class="metabox-holder">

<!-- 設定のリセット -->
<div id="reset" class="postbox">
  <h2 class="hndle"><?php _e( 'テーマ設定のリセット', THEME_NAME ) ?></h2>
  <div class="inside">

    <p><?php _e( '全テーマ設定の内容をリセットします。リセットを行う前にバックアップ機能を用いて設定の保存をしておくことをおすすめします。そうすれば、リストアも可能です。', THEME_NAME );
    echo get_help_page_tag('https://wp-cocoon.com/how-to-theme-settings-reset/'); ?></p>

    <table class="form-table">
      <tbody>

        <!-- リセット  -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_RESET_ALL_SETTINGS, __( 'リセット', THEME_NAME ) ); ?>
          </th>
          <td>
            <!-- <input name="reset_all_settings111" value="1" type="checkbox"> -->
            <?php
            generate_checkbox_tag(OP_RESET_ALL_SETTINGS , 0, __( '全ての設定をリセットする', THEME_NAME ));
            generate_tips_tag(__( 'テーマ設定をデフォルト状態に戻します。チェックをつけて設定の保存を行ってください。', THEME_NAME ));
            generate_checkbox_tag(OP_CONFIRM_RESET_ALL_SETTINGS , 0, __( 'リセット動作の確認', THEME_NAME ));
            generate_tips_tag(__( '誤って設定を削除するのを防ぐために確認のためもう一度チェックしてください。', THEME_NAME ));
            ?>
          </td>
        </tr>

      </tbody>
    </table>

  </div>
</div>

</div><!-- /.metabox-holder -->
