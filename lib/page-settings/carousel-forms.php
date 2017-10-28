<div class="metabox-holder">

<!-- カルーセル -->
<div id="carousel-area" class="postbox">
  <h2 class="hndle"><?php _e( 'カルーセル設定', THEME_NAME ) ?></h2>
  <div class="inside">

    <p><?php _e( 'ヘッダー下でカルーセル表示させたい投稿の設定を行います。', THEME_NAME ) ?></p>
    <p class="preview-label"><?php _e( 'プレビュー', THEME_NAME ) ?></p>
    <div class="demo carousel-area-demo" style="">
      <?php get_template_part('tmp/carousel'); ?>
    </div>
    <?php genelate_tips_tag(__( '設定が反映されない場合はリロードしてみてください。', THEME_NAME )); ?>

    <table class="form-table">
      <tbody>

        <!-- カルーセルの表示 -->
        <tr>
          <th scope="row">
            <?php genelate_label_tag(OP_CAROUSEL_DISPLAY_TYPE, __('カルーセルの表示', THEME_NAME) ); ?>
          </th>
          <td>
            <?php
            $options = array(
              'none' => __( '表示しない', THEME_NAME ),
              'all_page' => __( '全ページで表示', THEME_NAME ),
              'front_page_only' => __( 'フロントページのみで表示', THEME_NAME ),
              'not_singular' => __( '投稿・固定ページ以外で表示', THEME_NAME ),
            );
            genelate_selectbox_tag(OP_CAROUSEL_DISPLAY_TYPE, $options, get_carousel_display_type());
            genelate_tips_tag(__( 'カルーセルを表示するページを設定します。', THEME_NAME ));
            ?>
          </td>
        </tr>

        <!-- カルーセルカテゴリーID -->
        <tr>
          <th scope="row">
            <?php genelate_label_tag(OP_CAROUSEL_CATEGORY_IDS, __( '表示カテゴリー', THEME_NAME )); ?>
          </th>
          <td>
            <?php
            genelate_hierarchical_category_check_list( 0, OP_CAROUSEL_CATEGORY_IDS, get_carousel_category_ids(), 300 );
            genelate_tips_tag(__( 'カルーセルと関連付けるカテゴリを選択してください。アピールしたいカテゴリを選ぶと良いかもしれません。', THEME_NAME ));
            ?>
          </td>
        </tr>

        <!-- オートプレイ-->
        <tr>
          <th scope="row">
            <?php genelate_label_tag(OP_CAROUSEL_AUTOPLAY_ENABLE, __( 'オートプレイ', THEME_NAME )); ?>
          </th>
          <td>
            <?php
            genelate_checkbox_tag( OP_CAROUSEL_AUTOPLAY_ENABLE, is_carousel_autoplay_enable(), __( 'オートプレイを実行', THEME_NAME ));
            genelate_tips_tag(__( 'カルーセルが自動的に送られます。', THEME_NAME ));
            ?>
          </td>
        </tr>

      </tbody>
    </table>

  </div>
</div>

</div><!-- /.metabox-holder -->