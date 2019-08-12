<?php /**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit; ?>

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
            <?php generate_label_tag(OP_FACEBOOK_OGP_ENABLE, __( 'OGPの有効化', THEME_NAME ) ); ?>
          </th>
          <td>
            <?php
            //OGPタグ
            generate_checkbox_tag(OP_FACEBOOK_OGP_ENABLE, is_facebook_ogp_enable(), __( 'OGPタグの挿入', THEME_NAME ));
            generate_tips_tag(__( 'headタグ内にFacebookや外部サイトなどに、ページの概要情報伝えるタグを挿入します。', THEME_NAME ));
            ?>
          </td>
        </tr>

        <!-- Facebook APP ID -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_FACEBOOK_APP_ID, __( 'Facebook APP ID', THEME_NAME ) ); ?>
          </th>
          <td>
            <?php
            generate_textbox_tag(OP_FACEBOOK_APP_ID, get_facebook_app_id(), __( 'fb:app_idを入力', THEME_NAME ));
            generate_tips_tag(__( 'fb:appidは、FacebookにOGPを表示させるためには必須の設定になります。', THEME_NAME ));
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
            <?php generate_label_tag(OP_TWITTER_CARD_ENABLE, __( 'Twitterカードの有効化', THEME_NAME ) ); ?>
          </th>
          <td>
            <?php

            //Twitterカードタグ
            generate_checkbox_tag(OP_TWITTER_CARD_ENABLE, is_twitter_card_enable(), __( 'Twitterカードタグの挿入', THEME_NAME ));
            generate_tips_tag(__( 'headタグ内にTwitterに対して、ページの概要情報伝えるタグを挿入します。', THEME_NAME ));

            ?>
          </td>
        </tr>

        <!-- Twitterカードタイプ -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_TWITTER_CARD_TYPE, __( 'Twitterカードタイプ', THEME_NAME ) ); ?>
          </th>
          <td>

            <?php
            $options = array(
              'summary' => __( 'サマリー（summary）', THEME_NAME ),
              'summary_large_image' => __( '大きな画像のサマリー（summary_large_image）', THEME_NAME ),
            );
            generate_radiobox_tag(OP_TWITTER_CARD_TYPE, $options, get_twitter_card_type());
            generate_tips_tag(__( 'Twitterカードの表示形式の設定です。', THEME_NAME ).'<a href="https://dev.twitter.com/web/sign-inhttps://dev.twitter.com/ja/cards/overview" target="_blank" rel="noopener">'.__( 'カードタイプ', THEME_NAME ).'</a>');
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
            <?php generate_label_tag(OP_OGP_HOME_IMAGE_URL, __( '画像のアップロード', THEME_NAME ) ); ?>
          </th>
          <td>

          <?php
            generate_upload_image_tag(OP_OGP_HOME_IMAGE_URL, get_ogp_home_image_url());
           ?>

          </td>
        </tr>

      </tbody>
    </table>

  </div>
</div>



</div><!-- /.metabox-holder -->
