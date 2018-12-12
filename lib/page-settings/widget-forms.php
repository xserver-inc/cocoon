<?php /**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */ ?>
<!-- ウィジェット設定 -->
<div id="widget-page" class="postbox">
  <h2 class="hndle"><?php _e( 'ウィジェット表示', THEME_NAME ) ?></h2>
  <div class="inside">

    <p><?php _e( '使用しないウィジェットを表示しないようにする設定です。。', THEME_NAME ); ?></p>

    <table class="form-table">
      <tbody>

        <!-- AMP除外カテゴリーID -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_EXCLUDE_WIDGET_CLASSES, __( '除外ウィジェット', THEME_NAME )); ?>
          </th>
          <td>
            <?php

            ?>
          </td>
        </tr>


      </tbody>
    </table>

  </div>
</div>
