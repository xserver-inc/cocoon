<?php //バックアップフォーム
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit; ?>
<div class="metabox-holder">

<!-- バックアップ・リストア -->
<div id="backup" class="postbox">
  <h2 class="hndle"><?php _e( 'バックアップ・リストア', THEME_NAME ) ?></h2>
  <div class="inside">

    <p><?php _e( '設定情報のバックアップやリストアを行います。', THEME_NAME ) ?></p>

    <table class="form-table">
      <tbody>

        <!-- バックアップ  -->
        <tr>
          <th scope="row">
            <?php generate_label_tag('', __( 'バックアップ', THEME_NAME ) ); ?>
          </th>
          <td>
            <a href="<?php echo get_template_directory_uri().'/lib/page-backup/backup-download.php'; ?>" class="button"><?php _e( 'バックアップファイルの取得', THEME_NAME ) ?></a>
            <?php
              generate_tips_tag(__( 'テーマ設定のバックアップをする際はボタンを押してください。', THEME_NAME ).get_help_page_tag('https://wp-cocoon.com/how-to-theme-settings-backup/'));
            ?>
          </td>
        </tr>

        <!-- リストア  -->
        <tr>
          <th scope="row">
            <?php generate_label_tag('', __( 'リストア', THEME_NAME ) ); ?>
          </th>
          <td>
            <form enctype="multipart/form-data" action="" method="POST">
                <input type="hidden" name="MAX_FILE_SIZE" value="300000" />
                <?php _e( 'このファイルをアップロード: ', THEME_NAME ) ?>
                <input name="settings" type="file" /><br>
                <input type="submit" class="button" value="<?php _e( '設定の復元', THEME_NAME ) ?>" />
                <input type="hidden" name="<?php echo HIDDEN_FIELD_NAME; ?>" value="<?php echo wp_create_nonce('backup');?>">
                <?php
                generate_tips_tag(__( '参照ボタンでテーマ設定ファイルを選択し、「設定の復元」ボタンを押してください。', THEME_NAME ).get_help_page_tag('https://wp-cocoon.com/how-to-theme-settings-restore/'));
                 ?>
            </form>
          </td>
        </tr>

      </tbody>
    </table>

  </div>
</div>

</div><!-- /.metabox-holder -->
