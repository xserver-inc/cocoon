<?php /**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit; ?>

<div class="metabox-holder">

<!-- カルーセル -->
<div id="carousel-area" class="postbox">
  <h2 class="hndle"><?php _e( 'カルーセル設定', THEME_NAME ) ?></h2>
  <div class="inside">

    <p><?php _e( 'ヘッダー下でカルーセル表示させたい投稿の設定を行います。', THEME_NAME );
             echo get_help_page_tag('https://wp-cocoon.com/carousel-setting/') ?></p>

    <?php if (DEBUG_ADMIN_DEMO_ENABLE && apply_filters('cocoon_setting_preview_carousel', true)): ?>
    <p class="preview-label"><?php _e( 'プレビュー', THEME_NAME ) ?></p>
    <div class="demo carousel-area-demo" style="">
      <?php get_sanitize_preview_template_part('tmp/carousel'); ?>
    </div>
    <?php generate_tips_tag(__( '設定が反映されない場合はリロードしてみてください。', THEME_NAME )); ?>
    <?php endif; ?>

    <table class="form-table">
      <tbody>

        <!-- カルーセルの表示 -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_CAROUSEL_DISPLAY_TYPE, __('カルーセルの表示', THEME_NAME) ); ?>
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
            generate_selectbox_tag(OP_CAROUSEL_DISPLAY_TYPE, $options, get_carousel_display_type());
            generate_tips_tag(__( 'カルーセルを表示するページを設定します。', THEME_NAME ));

            generate_checkbox_tag(OP_CAROUSEL_SMARTPHONE_VISIBLE , is_carousel_smartphone_visible(), __( 'スマートフォンで表示（480px以下）', THEME_NAME ));
            ?>
          </td>
        </tr>

        <!-- カルーセルカテゴリーID -->
        <tr>
          <th scope="row">
            <?php generate_label_tag('', __( '表示内容', THEME_NAME )); ?>
          </th>
          <td>
            <?php
            echo __( '人気記事', THEME_NAME ).'<br>';
            generate_checkbox_tag( OP_CAROUSEL_POPULAR_POSTS_ENABLE, is_carousel_popular_posts_enable(), '');

            $options = array(
              '1' => __( '本日', THEME_NAME ),
              '7' => __( '7日間', THEME_NAME ),
              '30' => __( '30日間', THEME_NAME ),
              '365' => __( '1年間', THEME_NAME ),
              'all' => __( '全期間', THEME_NAME ),
            );
            generate_selectbox_tag(OP_CAROUSEL_POPULAR_POSTS_COUNT_DAYS, $options,  get_carousel_popular_posts_count_days());
            generate_label_tag(OP_CAROUSEL_POPULAR_POSTS_COUNT_DAYS, __('で集計した人気記事を含める', THEME_NAME) );
            echo '<br>';
            echo '<br>';

            echo __( 'カテゴリー', THEME_NAME ).'<br>';
            generate_hierarchical_category_check_list( 0, OP_CAROUSEL_CATEGORY_IDS, get_carousel_category_ids(), 300 );
            echo '<br>';

            echo __( 'タグ', THEME_NAME ).'<br>';
            generate_tagcloud_check_list(OP_CAROUSEL_TAG_IDS, get_carousel_tag_ids());
            generate_tips_tag(__( 'カルーセルと関連付けるカテゴリもしくはタグを選択してください。人気記事を有効にしカテゴリーのタグを選択した場合は、すべて合算しランダムで表示されます。', THEME_NAME ));
            ?>
          </td>
        </tr>

        <!-- カルーセルの並び替え -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_CAROUSEL_ORDERBY, __('カルーセルの並び替え', THEME_NAME) ); ?>
          </th>
          <td>
            <?php
            $options = array(
              'rand' => __( 'ランダム', THEME_NAME ),
              'post_date' => __( '投稿日（降順）', THEME_NAME ),
              'post_modified' => __( '更新日（降順）', THEME_NAME ),
            );
            generate_selectbox_tag(OP_CAROUSEL_ORDERBY, $options, get_carousel_orderby());
            generate_tips_tag(__( 'カルーセルを表示する順番を変更します。', THEME_NAME ));
            ?>
          </td>
        </tr>

        <!-- 最大表示数 -->
        <tr>
          <th scope="row">
            <?php
            generate_label_tag(OP_CAROUSEL_MAX_COUNT, __('最大表示数', THEME_NAME) );
            ?>
          </th>
          <td>
          <?php
            generate_number_tag(OP_CAROUSEL_MAX_COUNT,  get_carousel_max_count(), '', 12, 120);
            generate_tips_tag(__( 'カルーセルに表示するアイテムの最大表示数を設定します。（デフォルト：18、最小：12、最大：120）', THEME_NAME ));
            ?>
          </td>
        </tr>

        <!-- 枠線の表示 -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_CAROUSEL_CARD_BORDER_VISIBLE, __('枠線の表示', THEME_NAME) ); ?>
          </th>
          <td>
            <?php
            generate_checkbox_tag(OP_CAROUSEL_CARD_BORDER_VISIBLE , is_carousel_card_border_visible(), __( 'カードの枠線を表示する', THEME_NAME ));
            generate_tips_tag(__( 'カルーセルのカードの枠となる罫線を表示するか。', THEME_NAME ));
            ?>
          </td>
        </tr>

        <!-- オートプレイ-->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_CAROUSEL_AUTOPLAY_ENABLE, __( 'オートプレイ', THEME_NAME )); ?>
          </th>
          <td>
            <?php
            generate_checkbox_tag( OP_CAROUSEL_AUTOPLAY_ENABLE, is_carousel_autoplay_enable(), __( 'オートプレイを実行', THEME_NAME ));
            generate_tips_tag(__( 'カルーセルが自動的に送られます。', THEME_NAME ));
            ?>
          </td>
        </tr>

        <!-- オートプレイインターバル -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_CAROUSEL_AUTOPLAY_INTERVAL, __('オートプレイインターバル', THEME_NAME) ); ?>
          </th>
          <td>
            <?php
            $options = array();
            for ($i=3; $i <= 30; $i++) {
              $options[$i] = $i.__( '秒', THEME_NAME );
            }
            generate_selectbox_tag(OP_CAROUSEL_AUTOPLAY_INTERVAL, $options, get_carousel_autoplay_interval());
            generate_tips_tag(__( 'カルーセルの自動送り間隔です。オートプレイが有効な時のみ設定した秒数ごとに入れ替わります。', THEME_NAME ));
            ?>
          </td>
        </tr>

      </tbody>
    </table>

  </div>
</div>

</div><!-- /.metabox-holder -->
