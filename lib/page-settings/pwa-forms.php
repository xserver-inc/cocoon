<?php /**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit; ?>

<div class="metabox-holder">

<!-- PWA -->
<div id="page-pwa" class="postbox">
  <h2 class="hndle"><?php _e( 'PWA設定', THEME_NAME ) ?></h2>
  <div class="inside">

    <p><?php _e( 'PWA（Progressive Web Apps）とは、モバイル向けWebサイトをスマートフォン向けアプリのように使える仕組みです。', THEME_NAME ) ?></p>



    <table class="form-table">
      <tbody>

        <!-- PWAの有効化 -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_PWA_ENABLE, __( 'PWAの有効化', THEME_NAME )); ?>
          </th>
          <td>
            <?php
            generate_checkbox_tag(OP_PWA_ENABLE, is_pwa_enable(), __("PWA機能を有効化する",THEME_NAME ));
            generate_tips_tag(__( '有効化することで、PWA機能が有効化されスマートフォンからサイトがアプリのように利用できます。', THEME_NAME ));
            ?>
          </td>
        </tr>

      </tbody>
    </table>

  </div>
</div>

</div><!-- /.metabox-holder -->
