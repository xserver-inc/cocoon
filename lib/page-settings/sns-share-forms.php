<?php /**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit; ?>

<div class="metabox-holder">

<?php

//本文上ボタン用フォーム
require_once abspath(__FILE__).'sns-share-forms-top.php';
//本文下ボタン用フォーム
require_once abspath(__FILE__).'sns-share-forms-bottom.php';
?>

<!-- ツイート設定 -->
<div id="sns-share-twitter" class="postbox">
  <h2 class="hndle"><?php _e( 'ツイート設定', THEME_NAME ) ?></h2>
  <div class="inside">
    <p><?php _e( 'Twitter上でのツイート動作の設定です。', THEME_NAME ) ?></p>
    <table class="form-table">
      <tbody>

        <!-- メンション -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_TWITTER_ID_INCLUDE, __( 'メンション', THEME_NAME )); ?>
          </th>
          <td>
            <?php
            generate_checkbox_tag( OP_TWITTER_ID_INCLUDE, is_twitter_id_include(), __( 'ツイートにメンションを含める', THEME_NAME ));
            generate_tips_tag(__( 'シェアされたツイートに著者のTwitter IDを含める。', THEME_NAME ));
            ?>
          </td>
        </tr>

        <!-- プロモーション -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_TWITTER_RELATED_FOLLOW_ENABLE, __( 'プロモーション', THEME_NAME )); ?>
          </th>
          <td>
            <?php
            generate_checkbox_tag( OP_TWITTER_RELATED_FOLLOW_ENABLE, is_twitter_related_follow_enable(), __( 'ツイート後にフォローを促す', THEME_NAME ));
            generate_tips_tag(__( 'ツイート後に著者のフォローボタンを表示します。', THEME_NAME ));
            ?>
          </td>
        </tr>


        <!-- ハッシュタグ -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_TWITTER_HASH_TAG, __( 'ハッシュタグ', THEME_NAME )); ?>
          </th>
          <td>
            <?php
            generate_textbox_tag(OP_TWITTER_HASH_TAG, get_twitter_hash_tag(), '#wpcocoon '.__( '#ハッシュタグ', THEME_NAME ));
            generate_tips_tag(__( 'ツイート時に含めるハッシュタグを入力してください。半角スペースで区切って複数入力も可能です。URLやタイトルを含めて140文字を超える場合は正常動作しない可能性もあります。', THEME_NAME ));

            ?>
          </td>
        </tr>
      </tbody>
    </table>

  </div>
</div>





<!-- Facebook設定 -->
<div id="sns-share-facebook" class="postbox">
  <h2 class="hndle"><?php _e( 'Facebook設定', THEME_NAME ) ?></h2>
  <div class="inside">
    <p><?php _e( 'Facebookのシェア数に関する設定です。', THEME_NAME ) ?></p>
    <table class="form-table">
      <tbody>

        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_FACEBOOK_ACCESS_TOKEN, __( 'アクセストークン', THEME_NAME )); ?>
          </th>
          <td>
          <td>
            <?php
            generate_textbox_tag(OP_FACEBOOK_ACCESS_TOKEN, get_facebook_access_token(), __( 'access_tokenを入力', THEME_NAME ));
            generate_tips_tag(__( 'Facebookのシェア数を取得するのに必要なアクセストークンを入力します。当テーマではリアクションカウントをシェア数として採用しています。', THEME_NAME ).get_help_page_tag('https://wp-cocoon.com/facebook-share-count/'));

            ?>
          </td>
          </td>
        </tr>

      </tbody>
    </table>

  </div>
</div>





<!-- Pinterest設定 -->
<div id="sns-share-pinterest" class="postbox">
  <h2 class="hndle"><?php _e( 'Pinterest設定', THEME_NAME ) ?></h2>
  <div class="inside">
    <p><?php _e( 'Pinterestの「保存」ボタンに関する設定です。', THEME_NAME ) ?></p>
    <table class="form-table">
      <tbody>

        <!-- メンション -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_PINTEREST_SHARE_PIN_VISIBLE, __( 'Pinterest共有', THEME_NAME )); ?>
          </th>
          <td>
            <?php
            generate_checkbox_tag( OP_PINTEREST_SHARE_PIN_VISIBLE, is_pinterest_share_pin_visible(), __( 'Pinterestで画像をシェアする', THEME_NAME ));
            generate_tips_tag(__( 'この機能を有効にすると、投稿・固定ページ内の画像にマウスホバーすると「ピン」ボタンが表示されます。', THEME_NAME ));
            ?>
          </td>
        </tr>

      </tbody>
    </table>

  </div>
</div>





<!-- キャッシュ設定 -->
<div id="sns-share-cache" class="postbox">
  <h2 class="hndle"><?php _e( 'キャッシュ設定', THEME_NAME ) ?></h2>
  <div class="inside">
    <p><?php _e( 'シェア数取得時のキャッシュ利用設定です。キャッシュを利用するとページ表示スピードを多少なりともあげることができます。', THEME_NAME ) ?></p>
    <table class="form-table">
      <tbody>

        <!-- キャッシュの有効化 -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_SNS_SHARE_COUNT_CACHE_ENABLE, __( 'キャッシュの有効化', THEME_NAME )); ?>
          </th>
          <td>
            <?php
            generate_checkbox_tag( OP_SNS_SHARE_COUNT_CACHE_ENABLE, is_sns_share_count_cache_enable(), __( 'キャッシュを有効にする', THEME_NAME ));
            generate_tips_tag(__( 'SNSシェア数をキャッシュ化することでページ表示スピードの短縮化を図ります。', THEME_NAME ));
            ?>
          </td>
        </tr>

        <!-- キャッシュ間隔 -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_SNS_SHARE_COUNT_CACHE_INTERVAL, __('キャッシュ間隔', THEME_NAME) ); ?>
          </th>
          <td>
            <?php
            $options = array(
              '1' => __( '1時間', THEME_NAME ),
              '2' => __( '2時間', THEME_NAME ),
              '3' => __( '3時間', THEME_NAME ),
              '4' => __( '4時間', THEME_NAME ),
              '5' => __( '5時間', THEME_NAME ),
              '6' => __( '6時間', THEME_NAME ),
              '8' => __( '8時間', THEME_NAME ),
              '10' => __( '10時間', THEME_NAME ),
              '12' => __( '12時間', THEME_NAME ),
              '16' => __( '16時間', THEME_NAME ),
              '20' => __( '20時間', THEME_NAME ),
              '24' => __( '24時間', THEME_NAME ),
              '48' => __( '2日間', THEME_NAME ),
              '36' => __( '3日間', THEME_NAME ),
            );
            generate_selectbox_tag(OP_SNS_SHARE_COUNT_CACHE_INTERVAL, $options, get_sns_share_count_cache_interval());
            generate_tips_tag(__( 'キャッシュの取得間隔を設定します。間隔が狭いほど更新は増えますがサーバー負担は増えます（主に相手サーバー）。', THEME_NAME ));
            ?>
          </td>
        </tr>

        <!-- 別スキームのSNSシェア数 -->
        <tr>
          <th scope="row">
            <?php
            generate_label_tag(OP_ANOTHER_SCHEME_SNS_SHARE_COUNT, __('別スキームシェア数', THEME_NAME) );
            //generate_preview_tooltip_tag('URL', __( 'description', THEME_NAME ));
            ?>
          </th>
          <td>
          <?php
            generate_checkbox_tag(OP_ANOTHER_SCHEME_SNS_SHARE_COUNT , is_another_scheme_sns_share_count(), __( '別スキームのSNSシェア数をキャッシュする', THEME_NAME ));
            generate_tips_tag(__( 'httpsサイトであれば、httpサイトの頃のシェア数を取得するかどうか（httpの場合はhttps）。SNSサーバーへ倍の負荷をかけるのと取得に時間がかかるので、キャッシュが有効でないと利用できない仕様です。', THEME_NAME ).get_help_page_tag('https://wp-cocoon.com/another-scheme-sns-share-count/'));
            ?>
          </td>
        </tr>

      </tbody>
    </table>

  </div>
</div>




</div><!-- /.metabox-holder -->
