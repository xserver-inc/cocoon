<div class="metabox-holder">

<!-- 全体設定 -->
<div id="all" class="postbox">
  <h2 class="hndle"><?php _e( '全体設定', THEME_NAME ) ?></h2>
  <div class="inside">

    <p><?php _e( 'ページ全体の表示に関する設定です。', THEME_NAME ) ?></p>

    <table class="form-table">
      <tbody>

        <!-- サイトフォント  -->
        <tr>
          <th scope="row">
            <?php genelate_label_tag(OP_SITE_FONT_FAMILY, __('サイトフォント', THEME_NAM) ); ?>
          </th>
          <td>
            <div class="col-2">
              <div style="min-width: 270px;">
                <?php
                $options = array(
                  'yu_gothic' => __( '游ゴシック体, ヒラギノ角ゴ', THEME_NAME ),
                  'meiryo' => __( 'メイリオ, ヒラギノ角ゴ', THEME_NAME ),
                  'ms_pgothic' => __( 'ＭＳ Ｐゴシック, ヒラギノ角ゴ', THEME_NAME ),
                  'noto_sans_jp' => __( '源ノ角ゴシック（WEBフォント）', THEME_NAME ),
                  'mplus_1p' => __( 'Mplus 1p（WEBフォント）', THEME_NAME ),
                  'rounded_mplus_1c' => __( 'Rounded Mplus 1c（WEBフォント）', THEME_NAME ),
                  'hannari' => __( 'はんなり明朝（WEBフォント）', THEME_NAME ),
                  'kokoro' => __( 'こころ明朝（WEBフォント）', THEME_NAME ),
                  'sawarabi_gothic' => __( 'さわらびゴシック（WEBフォント）', THEME_NAME ),
                  'sawarabi_mincho' => __( 'さわらび明朝（WEBフォント）', THEME_NAME ),
                );
                genelate_selectbox_tag(OP_SITE_FONT_FAMILY, $options, get_site_font_family());
                genelate_tips_tag(__( 'サイト全体適用されるフォントを選択します。', THEME_NAME ));

                $options = array(
                  '12px' => __( '12px', THEME_NAME ),
                  '13px' => __( '13px', THEME_NAME ),
                  '14px' => __( '14px', THEME_NAME ),
                  '15px' => __( '15px', THEME_NAME ),
                  '16px' => __( '16px', THEME_NAME ),
                  '17px' => __( '17px', THEME_NAME ),
                  '18px' => __( '18px', THEME_NAME ),
                  '19px' => __( '19px', THEME_NAME ),
                  '20px' => __( '20px', THEME_NAME ),
                  '21px' => __( '21px', THEME_NAME ),
                  '22px' => __( '22px', THEME_NAME ),
                );
                genelate_selectbox_tag(OP_SITE_FONT_SIZE, $options, get_site_font_size());
                genelate_tips_tag(__( 'サイト全体のフォントサイズを変更します。', THEME_NAME ));

                ?>

              </div>
              <div style="width: auto">
                <p class="preview-label">プレビュー</p>
                <div class="demo" style="width: 100%">
                  <p>1234567890</p>
                  <p>abcdefghijklmnopqrstuvwxyz</p>
                  <p>ABCDEFGHIJKLMNOPQRSTUVWXYZ</p>
                  <p>吾輩は猫である。名前はまだ無い。どこで生れたかとんと見当がつかぬ。何でも薄暗いじめじめした所でニャーニャー泣いていた事だけは記憶している。吾輩はここで始めて人間というものを見た。</p>
                </div>
              </div>
            </div>
          </td>
        </tr>

        <!-- サイト幅を揃える  -->
        <tr>
          <th scope="row">
            <?php genelate_label_tag(OP_ALIGN_SITE_WIDTH, __('サイト幅', THEME_NAM) ); ?>
          </th>
          <td>
            <?php
            genelate_checkbox_tag(OP_ALIGN_SITE_WIDTH, is_align_site_width(), __( 'サイト幅を揃える', THEME_NAME ));
            genelate_tips_tag(__('サイト全体の幅をコンテンツ幅で統一します。', THEME_NAME));
            ?>

          </td>
        </tr>

        <!-- サイドバーの位置  -->
        <tr>
          <th scope="row">
            <?php genelate_label_tag(OP_SIDEBAR_POSITION, __( 'サイドバーの位置', THEME_NAME ) ); ?>
          </th>
          <td>
            <?php
            $options = array(
              'sidebar_right' => __( 'サイドバー右', THEME_NAME ),
              'sidebar_left' => __( 'サイドバー左', THEME_NAME ),
            );
            //アドミンバーに独自管理メニューを表示
            genelate_radiobox_tag(OP_SIDEBAR_POSITION, $options, get_sidebar_position());
            genelate_tips_tag(__( 'サイドバーの表示位置の設定です。', THEME_NAME ));
            ?>
          </td>
        </tr>

        <!-- サイドバーの表示状態  -->
        <tr>
          <th scope="row">
            <?php genelate_label_tag(OP_SIDEBAR_DISPLAY_TYPE, __( 'サイドバーの表示状態', THEME_NAME ) ); ?>
          </th>
          <td>
            <?php
            $options = array(
              'display_all' => __( '全てのページで表示', THEME_NAME ),
              'no_display_all' => __( '全てのページで非表示', THEME_NAME ),
              'no_display_front_page' => __( 'フロントページで非表示（固定ページがトップページの場合）', THEME_NAME ),
              'no_display_index_pages' => __( 'インデックスページで非表示', THEME_NAME ),
              'no_display_pages' => __( '固定ページで非表示', THEME_NAME ),
              'no_display_singles' => __( '投稿ページで非表示', THEME_NAME ),
            );
            //アドミンバーに独自管理メニューを表示
            genelate_radiobox_tag(OP_SIDEBAR_DISPLAY_TYPE, $options, get_sidebar_display_type());
            genelate_tips_tag(__( 'サイドバーを表示するページの設定です。', THEME_NAME ));
            ?>
          </td>
        </tr>


      </tbody>
    </table>

  </div>
</div>



</div><!-- /.metabox-holder -->