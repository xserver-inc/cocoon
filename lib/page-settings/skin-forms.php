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
        <iframe id="skin-demo" class="iframe-demo" src="<?php echo get_cocoon_settings_preview_url('skin'); ?>" width="1000" height="400" loading="lazy"></iframe>
      </div>
    <?php endif; ?>


    <!-- スキン一覧 -->
    <div class="cocoon-skin-picker-section">
      <div class="cocoon-skin-picker-heading">
        <?php generate_label_tag(OP_SKIN_URL, __('スキン一覧', THEME_NAME) ); ?>
      </div>
      <?php
      $skin_options = array(
        '' => array('skin_name' => __( 'なし', THEME_NAME ), 'author_label' => ''),
      );
      $option_groups = array();
      $group_authors = array();
      foreach (get_skin_infos() as $info) {
        $file_url = $info['file_url'];
        // 表示対象外のスキンと同じURLの候補の除外
        if (!DEBUG_MODE && (!$info['visibility'] || is_exclude_skin($file_url))) {
          continue;
        }
        $file_url = apply_filters('cocoon_skin_file_url', $file_url);
        if (isset($skin_options[$file_url])) {
          continue;
        }

        $author = (string) $info['author'];
        $author_uri = esc_url($info['author_uri'] ?? '');
        $author_name = esc_html($author);
        if ($author_name && $author_uri) {
          $author_name = '<a href="'.$author_uri.'" target="_blank" rel="noopener">'.$author_name.'</a>';
        }
        $info['author_label'] = $author_name ? esc_html__( '作者', THEME_NAME ).': '.$author_name : '';
        $skin_options[$file_url] = $info;

        $group = $info['skin_group'] ?? '';
        if ($group !== '') {
          $option_groups[$file_url] = $group;
          $group_authors[$group][$author] ??= $info['author_label'];
        }
      }

      // 表示対象が複数あり、全候補に共通する作者名の集約
      $group_captions = array();
      $group_counts = array_count_values($option_groups);
      foreach ($group_authors as $group => $authors) {
        if ($group_counts[$group] > 1 && count($authors) === 1) {
          $group_captions[$group] = reset($authors);
        }
      }

      $options = array();
      $has_preview = false;
      // スキン未適用と画像未設定用のアイコン画像URL
      $skin_none_image_uri = get_cocoon_template_directory_uri().'/images/skin-none.svg';
      $skin_no_image_uri = get_cocoon_template_directory_uri().'/images/skin-no-image.svg';
      $selected_status = '<span class="cocoon-skin-selection-status" aria-hidden="true">✓ '.esc_html__( '選択中', THEME_NAME ).'</span>';
      foreach ($skin_options as $file_url => $info) {
        $group = $option_groups[$file_url] ?? '';
        $author_label = empty($group_captions[$group]) ? $info['author_label'] : '';
        $skin_name = $info['skin_name'];
        $list_label = ($group_counts[$group] ?? 0) > 1 ? ($info['skin_label'] ?? $skin_name) : $skin_name;
        $skin_page_uri = esc_url($info['skin_page_uri'] ?? '');
        $screenshot_uri = esc_url($info['screenshot_uri'] ?? '');
        // 一覧サムネイル用のローカル軽量画像URLと外部URLフォールバック
        $thumbnail_uri = esc_url($info['thumbnail_uri'] ?? $screenshot_uri);
        $swatch = sanitize_hex_color($info['skin_color'] ?? '');

        // 短い表示名と読み上げ・ベーススキン選択用の正式名
        $name_label = esc_html($list_label);
        if ($list_label !== $skin_name) {
          $series_label = '<span class="cocoon-skin-series-name" aria-hidden="true">'.esc_html($group).' ・ </span>';
          $name_label = $series_label.'<span aria-hidden="true">'.$name_label.'</span><span class="screen-reader-text">'.esc_html($skin_name).'</span>';
        }
        $color_label = $swatch
          ? '<span class="cocoon-skin-swatch" style="background-color: '.esc_attr($swatch).'" aria-hidden="true"></span>'
          : '';
        $label = sprintf(
          '<span class="cocoon-skin-option-label" data-skin-name="%1$s" data-skin-label="%2$s" data-skin-color="%3$s"><span class="cocoon-skin-option-name">%4$s</span>%5$s%6$s</span>',
          esc_attr($skin_name),
          esc_attr($list_label),
          esc_attr($swatch),
          $name_label,
          $color_label,
          $selected_status
        );
        $controls = '';
        if ($screenshot_uri) {
          $has_preview = true;
          $controls = sprintf(
            '<span class="cocoon-skin-option-thumbnail" aria-hidden="true"><img src="%1$s" alt="" width="360" height="225" loading="lazy" decoding="async" fetchpriority="low" referrerpolicy="no-referrer"></span>'.
            '<a class="button cocoon-skin-option-action cocoon-skin-preview-trigger" href="%2$s" target="_blank" rel="noopener" aria-label="%3$s" data-skin-intro="%4$s" data-skin-description="%5$s">%6$s</a>',
            $thumbnail_uri,
            $screenshot_uri,
            esc_attr(__( 'プレビュー', THEME_NAME ).' – '.$skin_name),
            $skin_page_uri,
            esc_attr(wp_strip_all_tags($info['description'] ?? '')),
            esc_html__( 'プレビュー', THEME_NAME )
          );
        } else {
          if ('' === $file_url) {
            // スキン未適用項目用のアイコン表示
            $controls = sprintf(
              '<span class="cocoon-skin-option-thumbnail is-no-image is-none" aria-hidden="true"><img src="%1$s" alt="" width="360" height="225" loading="lazy" decoding="async" fetchpriority="low"></span>',
              esc_url($skin_none_image_uri)
            );
          } else {
            // 画像未設定スキン用のNo Image表示
            $controls = sprintf(
              '<span class="cocoon-skin-option-thumbnail is-no-image" aria-hidden="true"><img src="%1$s" alt="" width="360" height="225" loading="lazy" decoding="async" fetchpriority="low"></span>',
              esc_url($skin_no_image_uri)
            );
          }
          if ($skin_page_uri) {
            $controls .= '<a class="button cocoon-skin-option-action" href="'.$skin_page_uri.'" target="_blank" rel="noopener">'.esc_html__( '詳細', THEME_NAME ).'</a>';
          }
        }
        if ($author_label) {
          $controls = '<span class="cocoon-skin-option-author">'.$author_label.'</span>'.$controls;
        }
        $options[$file_url] = array('label' => $label, 'controls' => $controls);
      }
      ?>
      <div class="cocoon-skin-options"
        data-no-image-uri="<?php echo esc_url($skin_no_image_uri); ?>"
        data-show-variants="<?php /* translators: %d: 代表候補を除いたシリーズ内の候補数 */ echo esc_attr__( 'ほかの候補 (%d)', THEME_NAME ); ?>"
        data-hide-variants="<?php esc_attr_e( '候補を閉じる', THEME_NAME ); ?>">
        <?php generate_radiobox_tag(OP_SKIN_URL, $options, get_skin_url(), null, $option_groups, $group_captions); ?>
      </div>
      <?php generate_tips_tag(__( 'スキンを選択してください。', THEME_NAME )); ?>
    </div>

    <table class="form-table">
      <tbody>

        <!-- スキン一覧表示 -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_INCLUDE_SKIN_TYPE, __('スキン一覧表示', THEME_NAME) ); ?>
          </th>
          <td>
            <?php
            $options = array(
              'all' => __( '全てのスキンを表示する', THEME_NAME ),
              'parent_only' => __( '親テーマのスキンのみ表示する', THEME_NAME ),
              'child_only' => __( '子テーマのスキンのみ表示する', THEME_NAME ),
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

    <?php if ($has_preview): ?>
      <dialog id="cocoon-skin-preview" class="cocoon-skin-preview-dialog" aria-labelledby="cocoon-skin-preview-title">
        <div class="cocoon-skin-preview-header">
          <h3 id="cocoon-skin-preview-title"></h3>
          <button type="button" class="button cocoon-skin-preview-close" autofocus><?php esc_html_e( '閉じる', THEME_NAME ); ?></button>
        </div>
        <div class="cocoon-skin-preview-body">
          <div class="cocoon-skin-preview-media"></div>
          <p class="cocoon-skin-preview-error" role="status" hidden><?php esc_html_e( '画像を読み込めませんでした。', THEME_NAME ); ?></p>
          <p class="cocoon-skin-preview-description" hidden></p>
          <a class="cocoon-skin-preview-intro" target="_blank" rel="noopener" hidden><?php esc_html_e( '詳細', THEME_NAME ); ?></a>
        </div>
      </dialog>
    <?php endif; ?>

  </div>
</div>

</div><!-- /.metabox-holder -->
