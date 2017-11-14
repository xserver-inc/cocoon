<div class="metabox-holder">


<!-- コメント -->
<div id="comment8" class="postbox">
  <h2 class="hndle"><?php _e( 'コメント設定', THEME_NAME ) ?></h2>
  <div class="inside">

    <p><?php _e( 'コメント一覧や入力欄のみタグに関する設定です。', THEME_NAME ) ?></p>

    <table class="form-table">
      <tbody>
        <?php if (0): ?>
        <!-- プレビュー画面 -->
        <tr>
          <th scope="row">
            <label><?php _e( 'プレビュー', THEME_NAME ) ?></label>
          </th>
          <td>
            <div class="demo">
              <?php get_template_part('comments'); ?>
            </div>
            <?php generate_tips_tag(__( 'デモはランダム表示です。', THEME_NAME )); ?>
          </td>
        </tr>
        <?php endif ?>


        <!-- コメント一覧見出し -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_COMMENT_HEADING, __('コメント一覧見出し', THEME_NAME) ); ?>
          </th>
          <td>
            <?php
            generate_textbox_tag(OP_COMMENT_HEADING, get_comment_heading(), __( '見出し', THEME_NAME ));
            generate_tips_tag(__( 'コメント一覧の見出しを入力してください。', THEME_NAME ));
            generate_textbox_tag(OP_COMMENT_SUB_HEADING, get_comment_sub_heading(), __( 'サブ見出し', THEME_NAME ));
            generate_tips_tag(__( 'コメント一覧の補助となる見出しを入力してください。', THEME_NAME ));
            ?>
          </td>
        </tr>

        <!-- コメント入力欄見出し -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_COMMENT_FORM_HEADING, __('コメント入力欄見出し', THEME_NAME) ); ?>
          </th>
          <td>
            <?php
            generate_textbox_tag(OP_COMMENT_FORM_HEADING, get_comment_form_heading(), __( '見出し', THEME_NAME ));
            generate_tips_tag(__( 'コメント入力欄の見出しを入力してください。', THEME_NAME ));
            ?>
          </td>
        </tr>

        <!-- ウェブサイト入力欄の表示 -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_COMMENT_WEBSITE_VISIBLE, __('ウェブサイトの表示', THEME_NAME) ); ?>
          </th>
          <td>
            <?php
            generate_checkbox_tag(OP_COMMENT_WEBSITE_VISIBLE , is_comment_website_visible(), __( 'ウェブサイト入力ボックスを表示する', THEME_NAME ));
            generate_tips_tag(__( 'ウェブサイト入力欄を表示するか。', THEME_NAME ));
            ?>
          </td>
        </tr>

        <!-- コメント送信ボタンラベル -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_COMMENT_SUBMIT_LABEL, __('送信ボタンラベル', THEME_NAME) ); ?>
          </th>
          <td>
            <?php
            generate_textbox_tag(OP_COMMENT_SUBMIT_LABEL, get_comment_submit_label(), __( 'コメントを送信', THEME_NAME ));
            generate_tips_tag(__( 'コメントの送信ボタンのラベルテキストを入力しているなさい。', THEME_NAME ));
            ?>
          </td>
        </tr>

      </tbody>
    </table>

  </div>
</div>


</div><!-- /.metabox-holder -->