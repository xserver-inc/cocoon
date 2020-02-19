<?php /**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit; ?>

<div class="metabox-holder">

<!-- スキン -->
<div id="skin" class="postbox">
  <h2 class="hndle"><?php _e( 'スキン設定', THEME_NAME ) ?></h2>
  <div class="inside">

    <p><?php _e( 'スキンを変更することで、サイトのデザインを手軽に変更できます。', THEME_NAME ) ?></p>
    <?php if (DEBUG_ADMIN_DEMO_ENABLE && apply_filters('cocoon_setting_preview_skins', true)): ?>
        <p class="preview-label"><?php _e( 'プレビュー', THEME_NAME ) ?></p>
      <div class="demo iframe-standard-demo skin-demo">
        <iframe id="skin-demo" class="iframe-demo" src="<?php echo home_url(); ?>" width="1000" height="400"></iframe>
      </div>
    <?php endif; ?>


    <table class="form-table">
      <tbody>

        <!-- スキン一覧 -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_SKIN_URL, __('スキン一覧', THEME_NAME) ); ?>
          </th>
          <td>
            <?php
            $options = array(
              '' => __( 'なし', THEME_NAME ),
            );
            $skin_infos = get_skin_infos();
            foreach ($skin_infos as $info) {
              $file_url = $info['file_url'];
              $skin_name = $info['skin_name'];
              $skin_page_uri = $info['skin_page_uri'];
              $description = $info['description'];
              $author = $info['author'];
              $author_uri = $info['author_uri'];
              $screenshot_uri = $info['screenshot_uri'];
              $version = $info['version'];
              $visibility = $info['visibility'];
              //表示を無効にしている場合は設定画面に表示しない
              if (!DEBUG_MODE && (!$visibility || is_exclude_skin($file_url))) {
                continue;
              }

              $skin_text = $skin_name.' ';
              if ($skin_page_uri) {
                $skin_text = '<a href="'.$skin_page_uri.'" target="_blank" rel="noopener">'.$skin_name.'</a> ';
              }

              $author_text = $author.' ';
              if ($author_uri) {
                $author_text = '&nbsp;&nbsp;<span style="font-style: italic;font-size: 0.9em;">['.__( '作者', THEME_NAME ).': <a href="'.$author_uri.'" target="_blank" rel="noopener">'.$author.'</a>]</span>';
              }

              $screenshot_text = null;
              if ($screenshot_uri) {
                $screenshot_text = get_skin_preview_tag($screenshot_uri, $description);
              }

              if ($version) {
                $version = 'v.'.strip_tags($version).' ';
              }

              if ($description) {
                $description = '<br><span class="indent">'.strip_tags($description).'</span>';
              }

              $caption = $screenshot_text.$skin_text.$author_text;//.$description;
              //var_dump($caption);
              $file_url = apply_filters('cocoon_skin_file_url', $file_url);
              $options += array($file_url => $caption);
            }
            //var_dump($options);
            generate_radiobox_tag(OP_SKIN_URL, $options, get_skin_url());
            generate_tips_tag(__( 'スキンを選択してください。', THEME_NAME ));

            ?>
          </td>
        </tr>

        <!-- 表示スキン -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_INCLUDE_SKIN_TYPE, __('表示スキン', THEME_NAME) ); ?>
          </th>
          <td>
            <?php
            $options = array(
              'all' => __( '全てのスキンを表示', THEME_NAME ),
              'parent_only' => __( '親テーマのスキンのみ表示', THEME_NAME ),
              'child_only' => __( '子テーマのスキンのみ表示', THEME_NAME ),
            );
            generate_radiobox_tag(OP_INCLUDE_SKIN_TYPE, $options, get_include_skin_type());
            generate_tips_tag(__( 'スキン一覧に含めて表示するスキンを選択してください。', THEME_NAME ));
            ?>
          </td>
        </tr>

        <!-- 同梱スキン募集中 -->
        <tr>
          <th scope="row">
            <?php generate_label_tag('', __('オリジナルスキン', THEME_NAME) ); ?>
          </th>
          <td>
            <p><?php _e( 'もしスキンを作成された際には、是非ご連絡ください。サイトで紹介させていただければと思います。', THEME_NAME ) ?></p>
            <p><?php _e( '詳しくはこちら', THEME_NAME ) ?> <span class="fa fa-arrow-right" aria-hidden="true"></span> <a href="https://wp-cocoon.com/skin-make/" target="_blanl"><?php _e( 'オリジナルスキンのCocoonサイト紹介について', THEME_NAME ) ?></a></p>
          </td>
        </tr>

      </tbody>
    </table>

  </div>
</div>

</div><!-- /.metabox-holder -->
