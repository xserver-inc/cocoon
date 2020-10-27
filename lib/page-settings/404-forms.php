<?php /**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit; ?>

<div class="metabox-holder">

<!-- 404ページ -->
<div id="page-404" class="postbox">
  <h2 class="hndle"><?php _e( '404ページ設定', THEME_NAME ) ?></h2>
  <div class="inside">

    <p><?php _e( 'ページが見つからなかった場合の404ページの表示設定です。', THEME_NAME ) ?></p>

    <?php if (DEBUG_ADMIN_DEMO_ENABLE && apply_filters('cocoon_setting_preview_404', true)): ?>
      <p class="preview-label"><?php _e( 'プレビュー', THEME_NAME ) ?></p>
      <div class="demo iframe-standard-demo page-404-demo">
        <?php
            //iframeから404ページを呼び出すと以下のPHP警告が出る
            //unlink(/app/public/wp-content/temp-write-test-1512636307): Text file busy
            //原因はよくわからないけど警告なので様子見
        ?>
        <iframe id="page-404-demo" class="iframe-demo" src="<?php echo get_home_url().'/404/not/found/'; ?>" width="1000" height="400"></iframe>
      </div>
    <?php endif; ?>


    <table class="form-table">
      <tbody>

        <!-- 404ページ画像 -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_404_IMAGE_URL, __('404ページ画像', THEME_NAME) ); ?>
          </th>
          <td>
            <?php
            generate_upload_image_tag(OP_404_IMAGE_URL, get_404_image_url());
            generate_tips_tag(__( '404ページで表示する画像を選択してください。', THEME_NAME ));
            ?>
          </td>
        </tr>

        <!-- 404ページタイトル -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_404_PAGE_TITLE, __('404ページタイトル', THEME_NAME) ); ?>
          </th>
          <td>
            <?php
            generate_textbox_tag(OP_404_PAGE_TITLE, get_404_page_title(), __( '404 NOT FOUND', THEME_NAME ));
            generate_tips_tag(__( '404ページに表示するタイトルを入力します。', THEME_NAME ));
            ?>
          </td>
        </tr>

        <!-- 404ページメッセージ -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_404_PAGE_MESSAGE, __('404ページメッセージ', THEME_NAME) ); ?>
          </th>
          <td>
            <?php
            generate_textarea_tag(OP_404_PAGE_MESSAGE, get_404_page_message(), __( 'お探しのページは見つかりませんでした。', THEME_NAME ));
            generate_tips_tag(__( '404ページに表示するメッセージを入力します。タグ入力可能です。入力されたテキストには自動的に段落が付加されます。', THEME_NAME ));
            ?>
          </td>
        </tr>


      </tbody>
    </table>

  </div>
</div>

</div><!-- /.metabox-holder -->
