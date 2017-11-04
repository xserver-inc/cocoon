<div class="metabox-holder">

<!-- 目次 -->
<div id="external-link" class="postbox">
  <h2 class="hndle"><?php _e( '外部リンク設定', THEME_NAME ) ?></h2>
  <div class="inside">

    <p><?php _e( '外部リンク動作の設定です。', THEME_NAME ) ?></p>

    <table class="form-table">
      <tbody>

        <!-- 外部リンクの開き方 -->
        <tr>
          <th scope="row">
            <?php genelate_label_tag(OP_EXTERNAL_LINK_OPEN_TYPE, __('外部リンクの開き方', THEME_NAME) ); ?>
          </th>
          <td>
            <?php
            $options = array(
              'default' => __( '変更しない', THEME_NAME ),
              'blank' => __( '新しいタブで開く（_blank）', THEME_NAME ),
              'self' => __( '同じタブで開く（_self）', THEME_NAME ),
            );
            genelate_selectbox_tag(OP_EXTERNAL_LINK_OPEN_TYPE, $options, get_external_link_open_type());
            genelate_tips_tag(__( '本文内の外部リンクをどのように開くか。', THEME_NAME ));
            ?>
          </td>
        </tr>

        <!-- フォロータイプ -->
        <tr>
          <th scope="row">
            <?php genelate_label_tag(OP_EXTERNAL_LINK_FOLLOW_TYPE, __('フォロータイプ', THEME_NAME) ); ?>
          </th>
          <td>
            <?php
            $options = array(
              'default' => __( '変更しない', THEME_NAME ),
              'nofollow' => __( 'フォローしない（nofollow）', THEME_NAME ),
              'follow' => __( 'フォローする（follow）', THEME_NAME ),
            );
            genelate_selectbox_tag(OP_EXTERNAL_LINK_FOLLOW_TYPE, $options, get_external_link_follow_type());
            genelate_tips_tag(__( '本文内の外部リンクのフォロー状態を設定します。', THEME_NAME ));
            ?>
          </td>
        </tr>


        <!-- アイコン表示 -->
        <tr>
          <th scope="row">
            <?php genelate_label_tag(OP_EXTERNAL_LINK_ICON_VISIBLE, __('アイコン表示', THEME_NAME) ); ?>
          </th>
          <td>
            <?php
            genelate_checkbox_tag(OP_EXTERNAL_LINK_ICON_VISIBLE , is_external_link_icon_visible(), __( 'アイコンの表示', THEME_NAME ));
            genelate_tips_tag(__( '外部リンクの右部にFont Awesomeアイコンを表示するか。', THEME_NAME ));
            ?>
          </td>
        </tr>

        <!-- アイコン -->
        <tr>
          <th scope="row">
            <?php genelate_label_tag(OP_EXTERNAL_LINK_ICON, __('アイコン', THEME_NAME) ); ?>
          </th>
          <td>
            <?php
            $options = array(
              'fa-link' => __( '&#xf0c1', THEME_NAME ),
              'fa-level-up' => __( '&#xf148', THEME_NAME ),
              'fa-share' => __( '&#xf064', THEME_NAME ),
              'fa-share-square-o' => __( '&#xf045', THEME_NAME ),
              'fa-share-square' => __( '&#xf14d', THEME_NAME ),
              'fa-sign-out' => __( '&#xf08b', THEME_NAME ),
              'fa-plane' => __( '&#xf072', THEME_NAME ),
              'fa-rocket' => __( '&#xf135', THEME_NAME ),
            );

            genelate_selectbox_tag(OP_EXTERNAL_LINK_ICON, $options, get_external_link_icon(), true);
            genelate_tips_tag(__( '外部リンクの右部に表示するFont Awesomeアイコンを設定します。', THEME_NAME ));
            ?>
          </td>
        </tr>

      </tbody>
    </table>

  </div>
</div>

</div><!-- /.metabox-holder -->