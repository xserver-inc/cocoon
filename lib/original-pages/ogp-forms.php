<div class="metabox-holder">

<!-- OGP設定 -->
<div id="ogp" class="postbox">
  <h2 class="hndle"><?php _e( 'OGP設定', THEME_NAME ) ?></h2>
  <div class="inside">

    <p><?php _e( 'OGPとは「Open Graph protocol」の略称です。 FacebookやTwitterなどのSNSでシェアされた際に、そのページのタイトル・URL・概要・アイキャッチ画像（サムネイル）を意図した通りに正しく表示させる仕組みです。', THEME_NAME ) ?></p>

    <table class="form-table">
      <tbody>

        <!-- OGPの有効  -->
        <tr>
          <th scope="row">
            <?php genelate_label_tag(OP_FACEBOOK_OGP_ENABLE, __( 'OGPの有効化', THEME_NAME ) ); ?>
          </th>
          <td>
            <?php

            //OGPタグ
            genelate_checkbox_tag(OP_FACEBOOK_OGP_ENABLE, is_facebook_ogp_enable(), __( 'OGPタグの挿入', THEME_NAME ));
            genelate_tips_tag(__( 'headタグ内にFacebookや外部サイトなどに、ページの概要情報伝えるタグを挿入します。', THEME_NAME ));

            ?>
          </td>
        </tr>

        <!-- Facebook APP ID -->
        <tr>
          <th scope="row">
            <?php genelate_label_tag(OP_FACEBOOK_APP_ID, __( 'Facebook APP ID', THEME_NAME ) ); ?>
          </th>
          <td>
            <?php
            genelate_textbox_tag(OP_FACEBOOK_APP_ID, get_facebook_app_id(), __( 'fb:app_idを入力', THEME_NAME ));
            genelate_tips_tag(__( 'fb:appidは、FacebookにOGPを表示させるためには必須の設定になります。', THEME_NAME ));
            ?>
          </td>
        </tr>

      </tbody>
    </table>

  </div>
</div>




<!-- Twitterカード設定 -->
<div id="Twitter-card" class="postbox">
  <h2 class="hndle"><?php _e( 'Twitterカード設定', THEME_NAME ) ?></h2>
  <div class="inside">

    <p><?php _e( 'Twitter上で利用するOGP情報のようなものです。', THEME_NAME ) ?></p>

    <table class="form-table">
      <tbody>

        <!-- Twitterカードの有効化 -->
        <tr>
          <th scope="row">
            <?php genelate_label_tag(OP_TWITTER_CARD_ENABLE, __( 'Twitterカードの有効化', THEME_NAME ) ); ?>
          </th>
          <td>
            <?php

            //Twitterカードタグ
            genelate_checkbox_tag(OP_TWITTER_CARD_ENABLE, is_twitter_card_enable(), __( 'Twitterカードタグの挿入', THEME_NAME ));
            genelate_tips_tag(__( 'headタグ内にTwitterに対して、ページの概要情報伝えるタグを挿入します。', THEME_NAME ));

            ?>
          </td>
        </tr>

        <!-- Twitterカードタイプ -->
        <tr>
          <th scope="row">
            <?php genelate_label_tag(OP_TWITTER_CARD_TYPE, __( 'Twitterカードタイプ', THEME_NAME ) ); ?>
          </th>
          <td>

            <?php
            $options = array(
              'summary' => __( 'サマリー（summary）', THEME_NAME ),
              'summary_large_image' => __( '大きな画像のサマリー（summary_large_image）', THEME_NAME ),
            );
            genelate_radiobox_tag(OP_TWITTER_CARD_TYPE, $options, get_seo_date_type());
            genelate_tips_tag(__( 'Twitterカードの表示形式の設定です。', THEME_NAME ).'<a href="https://dev.twitter.com/ja/cards/types" target="_blank">'.__( 'カードタイプ', THEME_NAME ).'</a>');
            ?>
          </td>
        </tr>


      </tbody>
    </table>

  </div>
</div>




<!-- ホームイメージ -->
<div id="home-image" class="postbox">
  <h2 class="hndle"><?php _e( 'ホームイメージ', THEME_NAME ) ?></h2>
  <div class="inside">

    <p><?php _e( 'トップページのOGPやTwitter Cardsで表示する画像の設定です。', THEME_NAME ) ?></p>

    <table class="form-table">
      <tbody>

        <!-- Twitterカードの有効化 -->
        <tr>
          <th scope="row">
            <?php genelate_label_tag(OP_TWITTER_CARD_ENABLE, __( '画像のアップロード', THEME_NAME ) ); ?>
          </th>
          <td>


<input name="mediaid" type="text" value="" />
<input type="button" name="media" value="<?php _e( '選択', THEME_NAME ) ?>" />
<input type="button" name="media-clear" value="<?php _e( 'クリア', THEME_NAME ) ?>" />
<div id="media" class="uploded-thumbnail"></div>


<script type="text/javascript">
(function ($) {

    var custom_uploader;

    $("input:button[name=media]").click(function(e) {

        e.preventDefault();

        if (custom_uploader) {

            custom_uploader.open();
            return;

        }

        custom_uploader = wp.media({

            title: "Choose Image",

            /* ライブラリの一覧は画像のみにする */
            library: {
                type: "image"
            },

            button: {
                text: "<?php _e( '画像の選択', THEME_NAME ) ?>"
            },

            /* 選択できる画像は 1 つだけにする */
            multiple: false

        });

        custom_uploader.on("select", function() {

            var images = custom_uploader.state().get("selection");

            /* file の中に選択された画像の各種情報が入っている */
            images.each(function(file){

                /* テキストフォームと表示されたサムネイル画像があればクリア */
                $("input:text[name=mediaid]").val("");
                $("#media").empty();

                /* テキストフォームに画像の URL を表示 */
                $("input:text[name=mediaid]").val(file.attributes.sizes.full.url);

                /* プレビュー用に選択されたサムネイル画像を表示 */
                $("#media").append('<img src="'+file.attributes.sizes.full.url+'" />');

            });
        });

        custom_uploader.open();

    });

    /* クリアボタンを押した時の処理 */
    $("input:button[name=media-clear]").click(function() {

        $("input:text[name=mediaid]").val("");
        $("#media").empty();

    });

})(jQuery);
</script>
          </td>
        </tr>

      </tbody>
    </table>

  </div>
</div>



</div><!-- /.metabox-holder -->