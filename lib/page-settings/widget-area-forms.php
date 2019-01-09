<?php /**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit; ?>

<!-- ウィジェット設定 -->
<div id="widget-area-page" class="postbox">
  <h2 class="hndle"><?php _e( 'ウィジェットエリア表示', THEME_NAME ) ?></h2>
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
              //_v($widget_areas);
            ?>
            <ul>
              <?php
              foreach ($widget_areas as $id => $widget_area) {
                $checked = null;
                //_v($widget->widget_options);
                if (in_array($id, get_exclude_widget_area_ids())) {
                  $checked = ' checked="checked"';
                }
                echo '<li><input type="checkbox" name="'.OP_EXCLUDE_WIDGET_AREA_IDS.'[]" value="'.$id.'"'.$checked.'><b>' . $widget_area['name'].'</b>：'.$widget_area['description'].'</li>';
              }
              ?>
            </ul>
          </td>
        </tr>


      </tbody>
    </table>

  </div>
</div>
