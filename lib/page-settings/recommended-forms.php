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

    <p><?php _e( 'おすすめしたい記事やカテゴリーの画像リンクをヘッダー下の目立つ部分に表示させます。', THEME_NAME );
    echo get_help_page_tag('https://wp-cocoon.com/recommended-cards/'); ?></p>
    <?php if (DEBUG_ADMIN_DEMO_ENABLE && apply_filters('cocoon_setting_preview_recommended_cards', true)): ?>
      <p class="preview-label"><?php _e( 'プレビュー', THEME_NAME ) ?></p>
      <div class="demo recommended-cards-demo" style="">
        <?php get_template_part('tmp/recommended-cards') ?>
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
            $options = array(
              '' => __( '未選択', THEME_NAME )
            );
            $menus = wp_get_nav_menus();
            //_v($menus);
            foreach ($menus as $menu) {
              $menu_name = $menu->name;
              $options[$menu_name] = $menu_name;
            }
            generate_selectbox_tag(OP_RECOMMENDED_CARDS_MENU_NAME, $options, get_recommended_cards_menu_name());
            generate_tips_tag(__( '「外観 → メニュー」で作成した「おすすめカード」用のメニューを選択してください。', THEME_NAME ));
            ?>
          </td>
        </tr>

        <!-- 表示スタイル -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_RECOMMENDED_CARDS_STYLE, __('表示スタイル', THEME_NAME) ); ?>
          </th>
          <td>
            <?php
            $options = get_widget_style_options();
            generate_radiobox_tag(OP_RECOMMENDED_CARDS_STYLE, $options, get_recommended_cards_style());
            generate_tips_tag(__( 'おすすめカードの表示スタイルを変更します。', THEME_NAME ));
            ?>
          </td>
        </tr>

        <!-- カード余白 -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_RECOMMENDED_CARDS_MARGIN_ENABLE, __('カード余白', THEME_NAME) ); ?>
          </th>
          <td>
            <?php
            generate_checkbox_tag(OP_RECOMMENDED_CARDS_MARGIN_ENABLE, is_recommended_cards_margin_enable(), get_image_preview_tag('https://im-cocoon.net/wp-content/uploads/recommended_cards_margin_enable.png').__( 'おすすめカード毎に余白を設ける', THEME_NAME ));
            generate_tips_tag(__( 'デフォルトだとカードはすべてくっついています。この機能を有効にすることにより、カードごとに余白を設けます。', THEME_NAME ));
            ?>
          </td>
        </tr>

        <!-- カードエリア左右余白 -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_RECOMMENDED_CARDS_AREA_BOTH_SIDES_MARGIN_ENABLE, __('カードエリア左右余白', THEME_NAME) ); ?>
          </th>
          <td>
            <?php
            generate_checkbox_tag(OP_RECOMMENDED_CARDS_AREA_BOTH_SIDES_MARGIN_ENABLE, is_recommended_cards_area_both_sides_margin_enable(), get_image_preview_tag('https://im-cocoon.net/wp-content/uploads/recommended_cards_area_both_sides_margin_enable.png').__( 'おすすめカードエリアの左右に余白を設ける', THEME_NAME ));
            generate_tips_tag(__( 'デフォルトだと、カードエリアは画面幅いっぱいになっています。この設定を有効にすることにより、コンテンツ部分と同等に左右余白を設けます。※PC表示のみ。', THEME_NAME ));
            ?>
          </td>
        </tr>

      </tbody>
    </table>

  </div>
</div>

</div><!-- /.metabox-holder -->
