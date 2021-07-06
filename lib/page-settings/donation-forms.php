<?php /**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit; ?>

<div class="metabox-holder">

<!-- 寄付 -->
<div id="donation" class="postbox">
  <h2 class="hndle"><?php _e( '寄付', THEME_NAME ) ?></h2>
  <div class="inside">

    <p><?php _e('Cocoonは無料テーマのため開発支援をいただければ幸いです。寄付の詳細は<a href="https://wp-cocoon.com/donation-privilege/" target="_blank" rel="noopener noreferrer">こちら</a>。', THEME_NAME); ?></p>

    <table class="form-table">
      <tbody>

      <!--  メタディスクリプション -->
      <tr>
        <th scope="row">
          <?php generate_label_tag(OP_PRIVILEGE_ACTIVATION_CODE, __( '寄付特典', THEME_NAME ) ); ?>
          <?php if (is_privilege_activation_code_available()): ?>
            <span class="moshimo-badge api-badge"><?php _e('有効'); ?></span>
          <?php endif; ?>
        </th>
        <td>
          <?php
          generate_textbox_tag(OP_PRIVILEGE_ACTIVATION_CODE, get_privilege_activation_code(), '');
          generate_tips_tag(__( '取得した「<a href="https://wp-cocoon.com/donation-privilege/" target="_blank" rel="noopener noreferrer">寄付コード</a>」を入力してください。', THEME_NAME ));
          ?>
          <br>
          <p><?php _e('現在は以下の特典機能が利用できます。', THEME_NAME); ?><?php _e('詳細は<a href="https://wp-cocoon.com/cocoon-donation-privileges/" target="_blank" rel="noopener noreferrer">こちら</a>をご参照ください。', THEME_NAME); ?></p>

          <ul style="list-style-type: disc;padding-left: 30px;margin: 2em 0;">
            <li><?php _e('FAQブロック（リッチスニペット対応）', THEME_NAME); ?><?php _e('（<a href="https://wp-cocoon.com/faq-block/" target="_blank" rel="noopener noreferrer">詳細</a>）', THEME_NAME); ?></li>
            <li><?php _e('HTML挿入ボタン（※ショートコード）', THEME_NAME); ?><?php _e('（<a href="https://wp-cocoon.com/html-insert-tool-button/" target="_blank" rel="noopener noreferrer">詳細</a>）', THEME_NAME); ?></li>
            <li><?php _e('更新日表示ショートコード', THEME_NAME); ?><?php _e('（<a href="https://wp-cocoon.com/updated-shortcode" target="_blank" rel="noopener noreferrer">詳細</a>）', THEME_NAME); ?></li>
          </ul>

          <p><?php _e('特典機能を入手する場合は<a href="https://wp-cocoon.com/donation-privilege/" target="_blank" rel="noopener noreferrer">こちら</a>。', THEME_NAME); ?></p>
        </td>
      </tr>

      </tbody>
    </table>

  </div>
</div>

</div><!-- /.metabox-holder -->
