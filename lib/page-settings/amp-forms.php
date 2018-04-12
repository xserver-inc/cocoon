<!-- AMP設定 -->
<div id="amp-page" class="postbox">
  <h2 class="hndle"><?php _e( 'AMP設定', THEME_NAME ) ?></h2>
  <div class="inside">

    <p><?php _e( 'AMP（Accelerated Mobile Pages）に関する設定です。投稿ページをモバイル上で高速表示させるための仕組みです。', THEME_NAME );
    echo get_help_page_tag('https://wp-cocoon.com/amp/'); ?></p>

    <table class="form-table">
      <tbody>

        <!-- AMPの有効化 -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_AMP_ENABLE, __( 'AMPの有効化', THEME_NAME )); ?>
          </th>
          <td>
            <?php
            generate_checkbox_tag(OP_AMP_ENABLE, is_amp_enable(), __("AMP機能を有効化する",THEME_NAME ));
            generate_tips_tag(__( '有効化することで、AMP機能が有効化され高速表示されます。※AMP対応するページは投稿・固定ページのみです。', THEME_NAME ));
            ?>
          </td>
        </tr>

        <!-- AMPロゴ -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_AMP_LOGO_IMAGE_URL, __('AMPロゴ', THEME_NAME) ); ?>
          </th>
          <td>
            <?php
            generate_upload_image_tag(OP_AMP_LOGO_IMAGE_URL, get_amp_logo_image_url());
            generate_tips_tag(__( 'Google検索結果に表示されるAMP用のロゴ画像を設定します。ロゴのサイズは幅600px、高さ60px以下にしてください。構造化データのArticle > publisher > logoでも利用されます。', THEME_NAME ));
            ?>
          </td>
        </tr>

        <!-- AMPバリデーター -->
        <!--
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_AMP_VALIDATOR, __('AMPバリデーター', THEME_NAME) ); ?>
          </th>
          <td>
            <?php
            $options = array(
              AVT_AMP_TEST => __( 'Google AMPテスト', THEME_NAME ),
              AVT_THE_AMP_VALIDATOR => __( 'The AMP Validator', THEME_NAME ),
              AVT_THE_AMP_BENCH => __( '  AMPBench', THEME_NAME ),
            );
            generate_radiobox_tag(OP_AMP_VALIDATOR, $options, get_amp_validator());
            generate_tips_tag(__( '「AMPテスト」を行うテストツール（バリデーター）を選択します。', THEME_NAME ));
            ?>

          </td>
        </tr> -->

        <!-- AMP除外カテゴリーID -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_AMP_EXCLUDE_CATEGORY_IDS, __( 'AMP除外カテゴリー', THEME_NAME )); ?>
          </th>
          <td>
            <?php
            generate_hierarchical_category_check_list( 0, OP_AMP_EXCLUDE_CATEGORY_IDS, get_amp_exclude_category_ids(), 300 );
            generate_tips_tag(__( 'AMPページを生成しないカテゴリを選択してください。', THEME_NAME ));
            ?>
          </td>
        </tr>


      </tbody>
    </table>

  </div>
</div>
