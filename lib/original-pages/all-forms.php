<div class="metabox-holder">

<!-- 全体設定 -->
<div id="all" class="postbox">
  <h2 class="hndle"><?php _e( '全体設定', THEME_NAME ) ?></h2>
  <div class="inside">

    <p><?php _e( 'ページ全体の表示に関する設定です。', THEME_NAME ) ?></p>

    <table class="form-table">
      <tbody>

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