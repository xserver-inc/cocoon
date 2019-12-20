<?php /**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit; ?>

<div class="metabox-holder">

<!-- フォローボタン -->
<div id="sns-follow" class="postbox">
  <h2 class="hndle"><?php _e( 'フォローボタン', THEME_NAME ) ?></h2>
  <div class="inside">
    <p><?php _e( 'フォローボタンの表示に関する設定です。', THEME_NAME ) ?></p>
    <table class="form-table">
      <tbody>

        <?php if (DEBUG_ADMIN_DEMO_ENABLE && apply_filters('cocoon_setting_preview_sns_follow', true)): ?>
        <!-- プレビュー画面 -->
        <tr>
          <th scope="row">
            <label><?php _e( 'プレビュー', THEME_NAME ) ?></label>
          </th>
          <td>
            <div class="demo">
            <?php //テンプレートの読み込み
              if (is_sns_follow_buttons_visible())
                get_template_part('/tmp/sns-follow-buttons'); ?>
            </div>
          </td>
        </tr>
        <?php endif; ?>

        <!-- フォローボタンの表示 -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_SNS_FOLLOW_BUTTONS_VISIBLE, __( 'フォローボタンの表示', THEME_NAME )); ?>
          </th>
          <td>
            <?php
            generate_checkbox_tag( OP_SNS_FOLLOW_BUTTONS_VISIBLE, is_sns_follow_buttons_visible(), __( '本文下のフォローボタンを表示する', THEME_NAME ));
            generate_tips_tag(__( '投稿・固定ページの本文下にあるフォローボタンの表示を切り替えます。', THEME_NAME ));
            ?>
          </td>
        </tr>

        <!-- フォローメッセージ -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_SNS_FOLLOW_MESSAGE, __( 'フォローメッセージ', THEME_NAME )); ?>
          </th>
          <td>
            <?php
            //var_dump(get_sns_follow_message());
            generate_textbox_tag(OP_SNS_FOLLOW_MESSAGE, get_sns_follow_message(), __( 'フォローメッセージの入力', THEME_NAME ));
            generate_tips_tag(__( '訪問者にフォローを促すメッセージを入力してください。', THEME_NAME ).REP_AUTHOR.__( 'には、投稿者のそれぞれの表示名が入ります。', THEME_NAME ));
            ?>
          </td>
        </tr>

        <!-- SNSサービスのURL -->
        <tr>
          <th scope="row">
            <label><?php _e( 'SNSサービスのURL', THEME_NAME ) ?></label>
          </th>
          <td>
            <p><?php echo THEME_NAME_CAMEL; ?><?php _e( 'は、ログインユーザーごとにフォローページを設定できます。', THEME_NAME ) ?></p>
            <p><?php _e( '以下のアカウントURLを設定する場合は、プロフィールページから設定してください。', THEME_NAME ) ?>(<a href="profile.php"><?php _e( 'あなたのプロフィール', THEME_NAME ) ?></a>)</p>
            <ul class="list-style-disc">
              <li><?php _e( 'ウェブサイト', THEME_NAME ) ?></li>
              <li><?php _e( 'Twitter', THEME_NAME ) ?></li>
              <li><?php _e( 'Facebook', THEME_NAME ) ?></li>
              <!-- <li><?php _e( 'Google+', THEME_NAME ) ?></li> -->
              <li><?php _e( 'はてなブックマーク', THEME_NAME ) ?></li>
              <li><?php _e( 'Instagram', THEME_NAME ) ?></li>
              <li><?php _e( 'Pinterest', THEME_NAME ) ?></li>
              <li><?php _e( 'YouTube', THEME_NAME ) ?></li>
              <li><?php _e( 'LinkedIn', THEME_NAME ) ?></li>
              <li><?php _e( 'note', THEME_NAME ) ?></li>
              <li><?php _e( 'Flickr', THEME_NAME ) ?></li>
              <li><?php _e( 'Amazon欲しい物リスト', THEME_NAME ) ?></li>
              <li><?php _e( '楽天ROOM', THEME_NAME ) ?></li>
              <li><?php _e( 'Slack', THEME_NAME ) ?></li>
              <li><?php _e( 'GitHub', THEME_NAME ) ?></li>
              <li><?php _e( 'CodePen', THEME_NAME ) ?></li>
            </ul>
            <p><a href="profile.php"><?php _e( 'あなたのプロフィール', THEME_NAME ) ?></a>から設定</p>
            <p class="tips"><?php _e( '現ログインユーザーのSNSフォローページを設定します。', THEME_NAME ) ?></p>
          </td>
        </tr>


        <!-- 表示ページ -->
        <tr>
          <th scope="row">
            <?php generate_label_tag('', __( '表示ページ', THEME_NAME )); ?>
          </th>
          <td>
            <p><?php _e( 'フォローボタンを表示するページの切り替え。', THEME_NAME ) ?></p>
            <ul>
              <li>
                <?php generate_checkbox_tag(OP_SNS_FRONT_PAGE_FOLLOW_BUTTONS_VISIBLE, is_sns_front_page_follow_buttons_visible(), __( 'フロントページ', THEME_NAME )); ?>
              </li>
              <li>
                <?php generate_checkbox_tag(OP_SNS_SINGLE_FOLLOW_BUTTONS_VISIBLE, is_sns_single_follow_buttons_visible(), __( '投稿', THEME_NAME )); ?>
              </li>
              <li>
                <?php generate_checkbox_tag(OP_SNS_PAGE_FOLLOW_BUTTONS_VISIBLE, is_sns_page_follow_buttons_visible(), __( '固定ページ', THEME_NAME )); ?>
              </li>
              <li>
                <?php generate_checkbox_tag(OP_SNS_CATEGORY_FOLLOW_BUTTONS_VISIBLE, is_sns_category_follow_buttons_visible(), __( 'カテゴリー', THEME_NAME )); ?>
              </li>
              <li>
                <?php generate_checkbox_tag(OP_SNS_TAG_FOLLOW_BUTTONS_VISIBLE, is_sns_tag_follow_buttons_visible(), __( 'タグ', THEME_NAME )); ?>
              </li>
            <p><?php _e( 'フォローボタンを表示するページを選択してください。', THEME_NAME );echo get_help_page_tag('https://wp-cocoon.com/sns-button-display-switching/'); ?></p>
          </td>
        </tr>

        <!-- feedlyの表示 -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_FEEDLY_FOLLOW_BUTTON_VISIBLE, __( 'feedlyの表示', THEME_NAME )); ?>
          </th>
          <td>
            <?php
            generate_checkbox_tag( OP_FEEDLY_FOLLOW_BUTTON_VISIBLE, is_feedly_follow_button_visible(), __( 'feedlyフォローボタンを表示する', THEME_NAME ));
            generate_tips_tag(__( 'feedlyフォローボタンを表示します。', THEME_NAME ));
            ?>
          </td>
        </tr>

        <!-- RSSの表示 -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_RSS_FOLLOW_BUTTON_VISIBLE, __( 'RSSの表示', THEME_NAME )); ?>
          </th>
          <td>
            <?php
            generate_checkbox_tag( OP_RSS_FOLLOW_BUTTON_VISIBLE, is_rss_follow_button_visible(), __( 'RSS購読ボタンを表示する', THEME_NAME ));
            generate_tips_tag(__( 'RSS購読用のボタンを表示します。', THEME_NAME ));
            ?>
          </td>
        </tr>


        <!-- ボタンカラー -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_SNS_FOLLOW_BUTTON_COLOR, __( 'ボタンカラー', THEME_NAME )); ?>
          </th>
          <td>
            <?php
            $options = array(
              'monochrome' => __( 'モノクロ', THEME_NAME ),
              'brand_color' => __( 'ブランドカラー', THEME_NAME ),
              'brand_color_white' => __( 'ブランドカラー（白抜き）', THEME_NAME ),
            );
            generate_selectbox_tag(OP_SNS_FOLLOW_BUTTON_COLOR, $options, get_sns_follow_button_color());
            generate_tips_tag(__( 'シェアボタンの配色を選択してください。', THEME_NAME ));
            ?>
          </td>
        </tr>

        <!-- デフォルトユーザー -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_SNS_DEFAULT_FOLLOW_USER, __( 'デフォルトユーザー', THEME_NAME )); ?>
          </th>
          <td>
            <?php
            generate_author_list_selectbox_tag(OP_SNS_DEFAULT_FOLLOW_USER, get_sns_default_follow_user());
            generate_tips_tag(__( '投稿・固定ページ以外でフォローボタンを表示するユーザーを選択してください。', THEME_NAME ));
             ?>
          </td>
        </tr>

        <!-- フォロー数の表示 -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_SNS_FOLLOW_BUTTONS_COUNT_VISIBLE, __( 'フォロー数の表示', THEME_NAME )); ?>
          </th>
          <td>
            <?php
            generate_checkbox_tag( OP_SNS_FOLLOW_BUTTONS_COUNT_VISIBLE, is_sns_follow_buttons_count_visible(), __( 'フォロー数を表示する', THEME_NAME ));
            generate_tips_tag(__( '本文下やウィジェットなどのフォロー数表示を切り替えます。', THEME_NAME ));
            ?>
          </td>
        </tr>

        <!-- feedly購読者数 -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_SNS_FEEDLY_FOLLOW_COUNT, __('feedly購読者数', THEME_NAME) ); ?>
          </th>
          <td>
            <?php
            generate_number_tag(OP_SNS_FEEDLY_FOLLOW_COUNT,  get_sns_feedly_follow_count(), 0, 0, 9999999999);
            generate_tips_tag(__( 'feedlyは購読者情報がエラーで取得できない場合があります。その際には、こちらで設定された購読者数が表示されます。', THEME_NAME ));
            ?>
          </td>
        </tr>

      </tbody>
    </table>

  </div>
</div>


<!-- キャッシュ設定 -->
<div id="sns-follow-cache" class="postbox">
  <h2 class="hndle"><?php _e( 'キャッシュ設定', THEME_NAME ) ?></h2>
  <div class="inside">
    <p><?php _e( 'フォロー数取得時のキャッシュ利用設定です。キャッシュを利用するとページ表示スピードを多少なりともあげることができます。', THEME_NAME ) ?></p>
    <table class="form-table">
      <tbody>

        <!-- キャシュの有効化 -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_SNS_FOLLOW_COUNT_CACHE_ENABLE, __( 'キャシュの有効化', THEME_NAME )); ?>
          </th>
          <td>
            <?php
            generate_checkbox_tag( OP_SNS_FOLLOW_COUNT_CACHE_ENABLE, is_sns_follow_count_cache_enable(), __( 'キャッシュを有効にする', THEME_NAME ));
            generate_tips_tag(__( 'SNSシェア数をキャッシュ化することでページ表示スピードの短縮化を図ります。', THEME_NAME ));
            ?>
          </td>
        </tr>

        <!-- キャッシュ間隔 -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_SNS_FOLLOW_COUNT_CACHE_INTERVAL, __('キャッシュ間隔', THEME_NAME) ); ?>
          </th>
          <td>
            <?php
            $options = array(
              '8' => __( '6時間', THEME_NAME ),
              '12' => __( '12時間', THEME_NAME ),
              '18' => __( '18時間', THEME_NAME ),
              '24' => __( '24時間', THEME_NAME ),
              '48' => __( '2日間', THEME_NAME ),
              '36' => __( '3日間', THEME_NAME ),
            );
            generate_selectbox_tag(OP_SNS_FOLLOW_COUNT_CACHE_INTERVAL, $options, get_sns_follow_count_cache_interval());
            generate_tips_tag(__( 'キャッシュの取得間隔を設定します。間隔が狭いほど更新は増えますがサーバー負担は増えます（主に相手サーバー）。', THEME_NAME ));
            ?>
          </td>
        </tr>

        <!-- 別スキームのSNSシェア数 -->
        <tr>
          <th scope="row">
            <?php
            generate_label_tag(OP_ANOTHER_SCHEME_SNS_FOLLOW_COUNT, __('別スキームフォロー数', THEME_NAME) );
            ?>
          </th>
          <td>
          <?php
            generate_checkbox_tag(OP_ANOTHER_SCHEME_SNS_FOLLOW_COUNT , is_another_scheme_sns_follow_count(), __( '別スキームのSNSフォロー数をキャッシュする', THEME_NAME ));
            generate_tips_tag(__( 'httpsサイトであれば、httpサイトの頃のシェア数を取得するかどうか（httpの場合はhttps）。相手サーバーへ倍の負荷をかけるのと取得に時間がかかるので、キャッシュが有効でないと利用できない仕様です。', THEME_NAME ).get_help_page_tag('https://wp-cocoon.com/another-scheme-sns-follow-count/'));
            ?>
          </td>
        </tr>
      </tbody>
    </table>

  </div>
</div>




</div><!-- /.metabox-holder -->
