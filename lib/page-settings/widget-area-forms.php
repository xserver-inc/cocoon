<?php /**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit; ?>

<!-- ウィジェットエリア設定 -->
<div id="widget-area-page" class="postbox">
  <h2 class="hndle"><?php _e( 'ウィジェットエリア設定', THEME_NAME ) ?></h2>
  <div class="inside">

    <p><?php _e( '使用しないウィジェットエリアを表示しないようにする設定です。', THEME_NAME ); ?><?php echo get_help_page_tag('https://wp-cocoon.com/unregister-sidebar/'); ?></p>

    <table class="form-table">
      <tbody>

        <!-- 除外ウィジェットエリア -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_EXCLUDE_WIDGET_AREA_IDS, __( '除外ウィジェットエリア', THEME_NAME )); ?>
          </th>
          <td>
            <?php
              $widget_areas = $GLOBALS['wp_registered_sidebars'];
            ?>
            <ul>
              <?php
              foreach ($widget_areas as $id => $widget_area) {
                // $checked = null;
                // if (in_array($id, get_exclude_widget_area_ids())) {
                //   $checked = ' checked="checked"';
                // }
                // echo '<li><input type="checkbox" name="'.OP_EXCLUDE_WIDGET_AREA_IDS.'[]" value="'.$id.'"'.$checked.'><b>' . $widget_area['name'].'</b>：'.$widget_area['description'].'</li>';

                echo '<li>';
                generate_checkbox_tag(OP_EXCLUDE_WIDGET_AREA_IDS.'[]', get_exclude_widget_area_ids(), '<b>'.$widget_area['name'].'</b>:'.$widget_area['description'], $id);
                echo '</li>';


              }
              ?>
            </ul>
          </td>
        </tr>


      </tbody>
    </table>

  </div>
</div>
