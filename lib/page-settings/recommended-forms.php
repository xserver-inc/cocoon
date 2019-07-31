<?php /**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit; ?>

<div class="metabox-holder">

<!-- おすすめカード -->
<div id="recommended-cards" class="postbox">
  <h2 class="hndle"><?php _e( 'おすすめカード設定', THEME_NAME ) ?></h2>
  <div class="inside">

    <p><?php _e( 'おすすめしたい記事やカテゴリーなどをヘッダー明日の目立つ部分に表示させます。', THEME_NAME ) ?></p>
    <?php if (DEBUG_ADMIN_DEMO_ENABLE && apply_filters('cocoon_setting_preview_recommended_cards', true)): ?>
      <p class="preview-label"><?php _e( 'プレビュー', THEME_NAME ) ?></p>
      <div class="demo recommended-cards-demo" style="">
        <?php get_sanitize_preview_template_part('tmp/recommended-cards') ?>
      </div>
      <?php
      generate_tips_tag(__( 'デモの表示は実際の表示と多少変わる可能性があります。', THEME_NAME ));
      ?>
    <?php endif; ?>

    <table class="form-table">
      <tbody>

        <!-- おすすめカードの表示 -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_RECOMMENDED_CARDS_DISPLAY_TYPE, __('おすすめカードの表示', THEME_NAME) ); ?>
          </th>
          <td>
            <?php
            $options = array(
              'none' => __( '表示しない', THEME_NAME ),
              'all_page' => __( '全ページで表示', THEME_NAME ),
              'front_page_only' => __( 'フロントページのみで表示', THEME_NAME ),
              'not_singular' => __( '投稿・固定ページ以外で表示', THEME_NAME ),
              'singular_only' => __( '投稿・固定ページのみで表示', THEME_NAME ),
              'single_only' => __( '投稿ページのみで表示', THEME_NAME ),
              'page_only' => __( '固定ページのみで表示', THEME_NAME ),
            );
            generate_selectbox_tag(OP_RECOMMENDED_CARDS_DISPLAY_TYPE, $options, get_recommended_cards_display_type());
            generate_tips_tag(__( 'おすすめカードを表示するページを設定します。', THEME_NAME ));
            ?>
          </td>
        </tr>

        <!-- メニュー選択 -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_RECOMMENDED_CARDS_MENU_NAME, __('メニュー選択', THEME_NAME) ); ?>
          </th>
          <td>
            <?php
            $options = array();
            $menus = wp_get_nav_menus();
            //_v($menus);
            foreach ($menus as $menu) {
              $menu_name = $menu->name;
              $options[$menu_name] = $menu_name;
            }
            generate_selectbox_tag(OP_RECOMMENDED_CARDS_MENU_NAME, $options, get_recommended_cards_menu_name());
            ?>
          </td>
        </tr>

        <!-- エリア画像 -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_RECOMMENDED_CARDS_TITLE_VISIBLE, __('タイトル表示', THEME_NAME) ); ?>
          </th>
          <td>
            <?php
            //ヘッダー背景画像の固定
            generate_checkbox_tag(OP_RECOMMENDED_CARDS_TITLE_VISIBLE, is_recommended_cards_title_visible(), __( 'おすすめカードタイトルを表示する', THEME_NAME ));
            generate_tips_tag(__( 'おすすめカードのタイトルに設定した文字列を表示するかどうか。', THEME_NAME ));
            ?>
          </td>
        </tr>

      </tbody>
    </table>

  </div>
</div>

</div><!-- /.metabox-holder -->
