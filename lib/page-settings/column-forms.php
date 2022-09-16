<?php /**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit; ?>

<div class="metabox-holder">

<!-- メインカラム -->
<div id="main-column" class="postbox">
  <h2 class="hndle"><?php _e( 'メインカラム設定', THEME_NAME ) ?></h2>
  <div class="inside">

    <p><?php echo __( 'メインカラムの幅、余白幅、枠線の設定です。', THEME_NAME ).get_help_page_tag('https://wp-cocoon.com/column-settiongs/') ?></p>

    <?php if (DEBUG_ADMIN_DEMO_ENABLE && apply_filters('cocoon_setting_preview_columns', true)): ?>
      <p class="preview-label"><?php _e( 'プレビュー', THEME_NAME ) ?></p>
      <div class="demo iframe-standard-demo column-demo">
        <?php
            //iframeからページを呼び出すと以下のPHP警告が出る
            //unlink(/app/public/wp-content/temp-write-test-1512636307): Text file busy
            //原因はよくわからないけど警告なので様子見
        ?>
        <iframe id="column-demo" class="iframe-demo" src="<?php echo home_url(); ?>" width="1000" height="400" loading="lazy"></iframe>
      </div>
    <?php endif; ?>


    <table class="form-table">
      <tbody>

        <!-- コンテンツ幅 -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_MAIN_COLUMN_CONTENTS_WIDTH, __('コンテンツ幅', THEME_NAME) ); ?>
          </th>
          <td>
            <?php
            generate_number_tag(OP_MAIN_COLUMN_CONTENTS_WIDTH,  get_main_column_contents_width(), 800, 600, 1600, 1);
            generate_tips_tag(__( 'メインカラムのコンテンツ部分の幅を設定します。（最小：600px、最大：1600px）', THEME_NAME ).'<br>'.__( '※カラム幅を変更した場合はサムネイルを再生成することで最適化されます。', THEME_NAME ).get_help_page_tag('https://wp-cocoon.com/regenerate-thumbnails/'));
            ?>
          </td>
        </tr>

        <!-- コンテンツ余白幅 -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_MAIN_COLUMN_PADDING, __('コンテンツ余白幅', THEME_NAME) ); ?>
          </th>
          <td>
            <?php
            generate_number_tag(OP_MAIN_COLUMN_PADDING,  get_main_column_padding(), 29, 10, 80);
            generate_tips_tag(__( 'メインカラムコンテンツ両サイドの余白幅を設定します。（最小：10px、最大：80px）', THEME_NAME ));
            ?>
          </td>
        </tr>

        <!-- コンテンツ枠線幅 -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_MAIN_COLUMN_BORDER_WIDTH, __('コンテンツ枠線幅', THEME_NAME) ); ?>
          </th>
          <td>
            <?php
            generate_number_tag(OP_MAIN_COLUMN_BORDER_WIDTH,  get_main_column_border_width(), 1, 0, 10);
            generate_tips_tag(__( 'メインカラムのボーダー幅を設定します。（最小：0px、最大：10px）', THEME_NAME ));
            ?>
          </td>
        </tr>

        <!-- コンテンツ枠線色 -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_MAIN_COLUMN_BORDER_COLOR, __('コンテンツ枠線色', THEME_NAME) ); ?>
          </th>
          <td>
            <?php
            generate_color_picker_tag(OP_MAIN_COLUMN_BORDER_COLOR,  get_main_column_border_color(), __( 'ボーダー色', THEME_NAME ));
            generate_tips_tag(__( 'メインカラムのボーダー色を設定します。未入力でデフォルトの透過色になります。', THEME_NAME ));
            ?>
          </td>
        </tr>

      </tbody>
    </table>

  </div>
</div>



<!-- サイドバー -->
<div id="sidebar-column" class="postbox">
  <h2 class="hndle"><?php _e( 'サイドバー設定', THEME_NAME ) ?></h2>
  <div class="inside">

    <p><?php echo __( 'サイドバーの幅、余白幅、枠線の設定です。', THEME_NAME ).get_help_page_tag('https://wp-cocoon.com/column-settiongs/') ?></p>

    <table class="form-table">
      <tbody>

        <!-- コンテンツ幅 -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_SIDEBAR_CONTENTS_WIDTH, __('サイドバー幅', THEME_NAME) ); ?>
          </th>
          <td>
            <?php
            generate_number_tag(OP_SIDEBAR_CONTENTS_WIDTH,  get_sidebar_contents_width(), 336, 200, 500, 1);
            generate_tips_tag(__( 'サイドバーコンテンツ部分の幅を設定します。（最小：200px、最大：500px）', THEME_NAME ));
            ?>
          </td>
        </tr>

        <!-- コンテンツ余白幅 -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_SIDEBAR_PADDING, __('サイドバー余白幅', THEME_NAME) ); ?>
          </th>
          <td>
            <?php
            generate_number_tag(OP_SIDEBAR_PADDING,  get_sidebar_padding(), 19, 5, 40);
            generate_tips_tag(__( 'サイドバーコンテンツ両サイドの余白幅を設定します。（最小：5px、最大：40px）', THEME_NAME ));
            ?>
          </td>
        </tr>

        <!-- コンテンツ枠線幅 -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_SIDEBAR_BORDER_WIDTH, __('サイドバー枠線幅', THEME_NAME) ); ?>
          </th>
          <td>
            <?php
            generate_number_tag(OP_SIDEBAR_BORDER_WIDTH,  get_sidebar_border_width(), 1, 0, 10);
            generate_tips_tag(__( 'サイドバーのボーダー幅を設定します。（最小：0px、最大：10px）', THEME_NAME ));
            ?>
          </td>
        </tr>

        <!-- コンテンツ枠線色 -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_SIDEBAR_BORDER_COLOR, __('サイドバー枠線色', THEME_NAME) ); ?>
          </th>
          <td>
            <?php
            generate_color_picker_tag(OP_SIDEBAR_BORDER_COLOR,  get_sidebar_border_color(), __( 'ボーダー色', THEME_NAME ));
            generate_tips_tag(__( 'サイドバーのボーダー色を設定します。未入力でデフォルトの透過色になります。', THEME_NAME ));
            ?>
          </td>
        </tr>

      </tbody>
    </table>

  </div>
</div>


<!-- カラム間余白 -->
<div id="main-sidebar" class="postbox">
  <h2 class="hndle"><?php _e( 'カラム間余白設定', THEME_NAME ) ?></h2>
  <div class="inside">

    <p><?php echo __( 'メインカラムとサイドバーの間隔設定です。', THEME_NAME ).get_help_page_tag('https://wp-cocoon.com/column-settiongs/') ?></p>

    <table class="form-table">
      <tbody>

        <!-- コンテンツ幅 -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_MAIN_SIDEBAR_MARGIN, __('カラム間の幅', THEME_NAME) ); ?>
          </th>
          <td>
            <?php
            generate_number_tag(OP_MAIN_SIDEBAR_MARGIN,  get_main_sidebar_margin(), 20, 0, 60);
            generate_tips_tag(__( 'メインカラムとサイドバーの間の幅を設定します。（最小：0px、最大：60px）', THEME_NAME ));
            ?>
          </td>
        </tr>

      </tbody>
    </table>

  </div>
</div>



</div><!-- /.metabox-holder -->
