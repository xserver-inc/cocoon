<?php /**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

$help_text = __( '取得方法', THEME_NAME );
?>
<div class="metabox-holder">

<!-- API -->
<div id="apis" class="postbox">
  <h2 class="hndle"><?php _e( 'API設定', THEME_NAME ) ?></h2>
  <div class="inside">

    <p><?php
    _e( '各種APIやアフィリエイトIDの設定です。', THEME_NAME );
    generate_help_page_tag('https://wp-cocoon.com/amazon-link/') ?></p>

    <table class="form-table">
      <tbody>

        <!-- Amazon -->
        <tr>
          <th scope="row">
            <?php
            generate_label_tag('', __('Amazon', THEME_NAME) );
            ?>
          </th>
          <td>
            <?php
            generate_label_tag(OP_AMAZON_API_ACCESS_KEY_ID, __( 'アクセスキーID', THEME_NAME ));
            generate_amazon_badge_tag(__( 'Amazon必須', THEME_NAME ));
            generate_moshimo_badge_tag(__( 'もしも必須', THEME_NAME ));
            echo '<br>';
            generate_textbox_tag(OP_AMAZON_API_ACCESS_KEY_ID, get_amazon_api_access_key_id(), '');
            generate_tips_tag(__( 'Amazon APIを使用するためのアクセスキーIDを入力してください。', THEME_NAME ).get_help_page_tag('https://wp-cocoon.com/product-advertising-api/', $help_text));

            generate_label_tag(OP_AMAZON_API_SECRET_KEY, __( 'シークレットキー', THEME_NAME ));
            generate_amazon_badge_tag(__( 'Amazon必須', THEME_NAME ));
            generate_moshimo_badge_tag(__( 'もしも必須', THEME_NAME ));
            echo '<br>';
            generate_textbox_tag(OP_AMAZON_API_SECRET_KEY, get_amazon_api_secret_key(), '');
            generate_tips_tag(__( 'Amazon APIを使用するためのシークレットキーを入力してください。', THEME_NAME ).get_help_page_tag('https://wp-cocoon.com/product-advertising-api/', $help_text));

            generate_label_tag(OP_AMAZON_ASSOCIATE_TRACKING_ID, __( 'トラッキングID', THEME_NAME ));
            generate_amazon_badge_tag(__( 'Amazon必須', THEME_NAME ));
            generate_moshimo_badge_tag(__( 'もしも必須', THEME_NAME ));
            echo '<br>';
            generate_textbox_tag(OP_AMAZON_ASSOCIATE_TRACKING_ID, get_amazon_associate_tracking_id(), __( 'yourid-22', THEME_NAME ));
            generate_tips_tag(__( 'AmazonアソシエイトのトラッキングIDを入力してください。', THEME_NAME ).get_help_page_tag('https://wp-cocoon.com/amazon-tracking-id/', $help_text));


            echo '<div'.get_not_allowed_form_class(get_amazon_api_access_key_id() && get_amazon_api_secret_key() && get_amazon_associate_tracking_id()).'>';

            generate_checkbox_tag(OP_AMAZON_ITEM_CATALOG_IMAGE_VISIBLE , is_amazon_item_catalog_image_visible(), __( 'カタログ写真を表示する', THEME_NAME ));
            generate_tips_tag(__( 'サムネイルとは別に商品に関連付けられている「カタログ写真（サンプル画像）」をボタン形式で全て表示します。ボタン状にマウスを乗せると大きな写真で表示されます。', THEME_NAME ).get_help_page_tag('https://wp-cocoon.com/amazon-shortcode-catalog-option/').get_image_preview_tag('https://im-cocoon.net/wp-content/uploads/catalog.gif'));

            generate_checkbox_tag(OP_AMAZON_ITEM_PRICE_VISIBLE , is_amazon_item_price_visible(), __( '価格を表示する', THEME_NAME ));

            echo '<div class="indent'.get_not_allowed_form_class(is_amazon_item_price_visible(), true).'">';
              generate_checkbox_tag(OP_AMAZON_ITEM_STOCK_PRICE_VISIBLE , is_amazon_item_stock_price_visible(), __( 'Amazon在庫価格を表示する', THEME_NAME ));
            echo '</div>';

            generate_tips_tag(__( 'データー取得時点のAmazon販売ページでの値段を表示します。ショートコードでpriceオプションが設定されている場合は、そちらが優先されます。', THEME_NAME ).get_help_page_tag('https://wp-cocoon.com/amazon-link-price/').get_image_preview_tag('https://im-cocoon.net/wp-content/uploads/amazon-price.png'));

            generate_checkbox_tag(OP_AMAZON_ITEM_CUSTOMER_REVIEWS_VISIBLE , is_amazon_item_customer_reviews_visible(), __( 'レビューを表示する', THEME_NAME ));
            echo '<br>';
            generate_textbox_tag(OP_AMAZON_ITEM_CUSTOMER_REVIEWS_TEXT, get_amazon_item_customer_reviews_text(), __( 'レビューページへのリンクテキストを入力', THEME_NAME ));
            generate_tips_tag(__( 'レビューページへのリンクを表示します。', THEME_NAME ).get_help_page_tag('https://wp-cocoon.com/amazon-review-link/').get_image_preview_tag('https://im-cocoon.net/wp-content/uploads/amazon-review.png'));

            generate_checkbox_tag(OP_AMAZON_ITEM_LOGO_VISIBLE , is_amazon_item_logo_visible(), __( 'ロゴを表示する', THEME_NAME ));
            generate_tips_tag(__( 'Amazon商品リンクのロゴの表示切り替え。', THEME_NAME ).get_image_preview_tag('https://im-cocoon.net/wp-content/uploads/amazon-logo.png'));

            generate_checkbox_tag(OP_AMAZON_SEARCH_BUTTON_VISIBLE , is_amazon_search_button_visible(), __( 'Amazon検索ボタンを表示する', THEME_NAME ));
            generate_tips_tag(__( 'Amazonのキーワード検索ボタンを表示するか。', THEME_NAME ).get_image_preview_tag('https://im-cocoon.net/wp-content/uploads/amazon-buttonns.png'));

            generate_textbox_tag(OP_AMAZON_SEARCH_BUTTON_TEXT, get_amazon_search_button_text(), '');
            generate_tips_tag(__( 'Amazonの検索ボタンに表示するテキストを入力してください。', THEME_NAME ));

            generate_checkbox_tag(OP_AMAZON_BUTTON_SEARCH_TO_DETAIL , is_amazon_button_search_to_detail(), __( '検索ボタンのリンク先を詳細ページにする', THEME_NAME ));
            generate_tips_tag(__( 'Amazon検索ボタンのリンクURLを商品詳細ページにするか。', THEME_NAME ));

            echo '<div>';
            ?>

          </td>
        </tr>

        <!-- 楽天 -->
        <tr>
          <th scope="row">
            <?php
            generate_label_tag('', __('楽天', THEME_NAME) );
            ?>
          </th>
          <td>
            <?php

            generate_label_tag(OP_RAKUTEN_APPLICATION_ID, __( '楽天アプリケーションID', THEME_NAME ));
            generate_rakuten_badge_tag(__( '楽天必須', THEME_NAME ));
            generate_moshimo_badge_tag(__( 'もしも必須', THEME_NAME ));
            echo '<br>';
            generate_textbox_tag(OP_RAKUTEN_APPLICATION_ID, get_rakuten_application_id(), '');
            generate_tips_tag(__( '楽天APIを利用するためのアプリケーションIDを入力してください。', THEME_NAME ).get_help_page_tag('https://wp-cocoon.com/rakuten-application-id/', $help_text));

            generate_label_tag(OP_RAKUTEN_AFFILIATE_ID, __( '楽天アフィリエイトID', THEME_NAME ));
            generate_rakuten_badge_tag(__( '楽天必須', THEME_NAME ));
            generate_moshimo_badge_tag(__( 'もしも必須', THEME_NAME ));
            echo '<br>';
            generate_textbox_tag(OP_RAKUTEN_AFFILIATE_ID, get_rakuten_affiliate_id(), '');
            generate_tips_tag(__( '楽天アフィリエイト用のIDを入力してください。', THEME_NAME ).get_help_page_tag('https://wp-cocoon.com/rakuten-affiliate-id/', $help_text));


            echo '<div'.get_not_allowed_form_class(get_rakuten_affiliate_id()).'>';


            $options = array(
              'standard' => __( '楽天標準ソート順', THEME_NAME ),
              '-affiliateRate' => __( 'アフィリエイト料率順（高い順）', THEME_NAME ),
              '+itemPrice' => __( '価格順（安い順）', THEME_NAME ),
              '-itemPrice' => __( '価格順（高い順）', THEME_NAME ),
            );
            generate_selectbox_tag(OP_GET_RAKUTEN_API_SORT, $options, get_rakuten_api_sort(), __( '商品並び替え優先度', THEME_NAME ));
            generate_tips_tag(__( '同一商品番号の商品が複数あった場合の表示優先度です。', THEME_NAME ));

            generate_checkbox_tag(OP_RAKUTEN_ITEM_PRICE_VISIBLE , is_rakuten_item_price_visible(), __( '価格を表示する', THEME_NAME ));
            generate_tips_tag(__( 'データー取得時点の楽天市場販売ページでの値段を表示します。ショートコードでpriceオプションが設定されている場合は、そちらが優先されます。', THEME_NAME ).get_help_page_tag('https://wp-cocoon.com/rakuten-link-price/').get_image_preview_tag('https://im-cocoon.net/wp-content/uploads/rakuten-price.png'));

            generate_checkbox_tag(OP_RAKUTEN_ITEM_LOGO_VISIBLE , is_rakuten_item_logo_visible(), __( 'ロゴを表示する', THEME_NAME ));
            generate_tips_tag(__( '楽天商品リンクのロゴの表示切り替え。', THEME_NAME ).get_image_preview_tag('https://im-cocoon.net/wp-content/uploads/rakuten-logo.png'));

            generate_checkbox_tag(OP_RAKUTEN_SEARCH_BUTTON_VISIBLE , is_rakuten_search_button_visible(), __( '楽天検索ボタンを表示する', THEME_NAME ));
            generate_tips_tag(__( '楽天のキーワード検索ボタンを表示するか。', THEME_NAME ).get_image_preview_tag('https://im-cocoon.net/wp-content/uploads/rakuten-buttons.png'));

            generate_textbox_tag(OP_RAKUTEN_SEARCH_BUTTON_TEXT, get_rakuten_search_button_text(), '');
            generate_tips_tag(__( '楽天の検索ボタンに表示するテキストを入力してください。', THEME_NAME ));

            generate_checkbox_tag(OP_RAKUTEN_BUTTON_SEARCH_TO_DETAIL , is_rakuten_button_search_to_detail(), __( '検索ボタンのリンク先を詳細ページにする', THEME_NAME ));
            generate_tips_tag(__( '楽天検索ボタンのリンク先URLを商品詳細ページにするか。', THEME_NAME ));

            echo '<div>';
            ?>
          </td>
        </tr>

        <!-- Yahoo!ショッピング -->
        <tr>
          <th scope="row">
            <?php
            generate_label_tag('', __('Yahoo!ショッピング', THEME_NAME) );
            ?>
          </th>
          <td>
            <?php
            generate_label_tag(OP_YAHOO_VALUECOMMERCE_SID, __( 'バリューコマースsid', THEME_NAME ));
            echo '<br>';
            generate_textbox_tag(OP_YAHOO_VALUECOMMERCE_SID, get_yahoo_valuecommerce_sid(), '');
            echo '<br>';

            generate_label_tag(OP_YAHOO_VALUECOMMERCE_PID, __( 'バリューコマースpid', THEME_NAME ));
            echo '<br>';
            generate_textbox_tag(OP_YAHOO_VALUECOMMERCE_PID, get_yahoo_valuecommerce_pid(), '');

            generate_tips_tag(__( 'バリューコマースからYahoo!ショッピングに登録しsidとpidを取得してください。', THEME_NAME ).get_help_page_tag('https://wp-cocoon.com/valuecommerce-yahoo-sid-pid/', $help_text));


            echo '<div'.get_not_allowed_form_class(get_yahoo_valuecommerce_sid() && get_yahoo_valuecommerce_pid()).'>';

            generate_checkbox_tag(OP_YAHOO_SEARCH_BUTTON_VISIBLE , is_yahoo_search_button_visible(), __( 'Yahoo!検索ボタンを表示する', THEME_NAME ));
            generate_tips_tag(__( 'Yahoo!のキーワード検索ボタンを表示するか。', THEME_NAME ).get_image_preview_tag('https://im-cocoon.net/wp-content/uploads/yahoo-button.png'));

            generate_textbox_tag(OP_YAHOO_SEARCH_BUTTON_TEXT, get_yahoo_search_button_text(), '');
            generate_tips_tag(__( 'Yahoo!ショッピングの検索ボタンに表示するテキストを入力してください。', THEME_NAME ));

            echo '<div>';
            ?>
          </td>
        </tr>


        <!-- もしもアフィリエイト -->
        <tr>
          <th scope="row">
            <?php generate_label_tag('', __( 'もしもアフィリエイト', THEME_NAME ) ); ?>
          </th>
          <td>
            <?php
            generate_checkbox_tag(OP_MOSHIMO_AFFILIATE_LINK_ENABLE , is_moshimo_affiliate_link_enable(), __( 'リンクをもしもアフィリエイトを経由にする', THEME_NAME ));
            generate_moshimo_badge_tag(__( 'もしも必須', THEME_NAME ));
            generate_tips_tag(__( 'もしもアフィリエイト経由でAmazonリンクを掲載し報酬を得ます。【重要】2019年1月23日の<a href="https://affiliate.amazon.co.jp/help/topic/t52/ref=amb_link_zYXX0aRKMACI_Qkj9rR6Nw_1?pf_rd_p=c08a6c9b-94fe-481e-ad8b-b2c640121b1f" target="_blank">PA-APIの仕様変更</a>により、APIが生成するリンクから売上が発生しないとAPIが利用できなくなりました。ですので、<span class="red">もしもアフィリエイト経由の場合は、30日でAPIが利用できなくなる可能性があります</span>。AmazonのAPIを利用したい場合は、この機能は有効にしないことをおすすめします。PA-APIの制限がクリアできない場合は、楽天商品リンクをご利用ください。', THEME_NAME ).get_help_page_tag('https://wp-cocoon.com/moshimo-amazon-link/'));

            generate_label_tag(OP_MOSHIMO_AMAZON_ID, __( 'Amazon a_id', THEME_NAME ));
            echo '<br>';
            generate_textbox_tag(OP_MOSHIMO_AMAZON_ID, get_moshimo_amazon_id(), '');
            generate_tips_tag(__( 'もしもアフィリエイトのAmazon IDを入力してください。', THEME_NAME ).__( '未入力の場合はデフォルトのリンクが出力されます。', THEME_NAME ).get_help_page_tag('https://wp-cocoon.com/moshimo-amazon-a_id/', $help_text));

            generate_label_tag(OP_MOSHIMO_RAKUTEN_ID, __( '楽天 a_id', THEME_NAME ));
            echo '<br>';
            generate_textbox_tag(OP_MOSHIMO_RAKUTEN_ID, get_moshimo_rakuten_id(), '');
            generate_tips_tag(__( 'もしもアフィリエイトの楽天IDを入力してください。', THEME_NAME ).__( '未入力の場合はデフォルトのリンクが出力されます。', THEME_NAME ).get_help_page_tag('https://wp-cocoon.com/moshimo-rakuten-a_id/', $help_text));

            generate_label_tag(OP_MOSHIMO_YAHOO_ID, __( 'Yahoo!ショッピング a_id', THEME_NAME ));
            echo '<br>';
            generate_textbox_tag(OP_MOSHIMO_YAHOO_ID, get_moshimo_yahoo_id(), '');
            generate_tips_tag(__( 'もしもアフィリエイトのYahoo!ショッピングIDを入力してください。', THEME_NAME ).__( '未入力の場合はデフォルトのリンクが出力されます。', THEME_NAME ).get_help_page_tag('https://wp-cocoon.com/moshimo-yahoo-a_id/', $help_text));


            ?>
          </td>
        </tr>

        <!-- キャッシュの保存期間 -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_API_CACHE_RETENTION_PERIOD, __( 'キャッシュの保存期間', THEME_NAME ) ); ?>
          </th>
          <td>
            <?php
            generate_number_tag(OP_API_CACHE_RETENTION_PERIOD, get_api_cache_retention_period(), '', 1, 365);
            _e( '日', THEME_NAME );
            generate_tips_tag(__( 'APIキャッシュのリフレッシュ間隔を設定します。1～365日の間隔を選べます。間隔が短いほどAPIのリクエスト制限にかかる可能性が高くなりますのでご注意ください。アクセス数の多いサイトは長めに設定しておくことをおすすめします。', THEME_NAME ).get_help_page_tag('https://wp-cocoon.com/amazon-api-cache/', __( '削除方法', THEME_NAME )));
            ?>
          </td>
        </tr>

      </tbody>
    </table>

  <p style="text-align: right;">
    <a href="<?php _e( 'https://affiliate.amazon.co.jp/help/operating/paapilicenseagreement', THEME_NAME ) ?>" target="_blank"><?php _e( 'Amazon.co.jp Product Advertising API ライセンス契約', THEME_NAME ) ?></a><br>
    <a href="<?php _e( 'https://affiliate.amazon.co.jp/help/topic/t32/', THEME_NAME ) ?>" target="_blank"><?php _e( 'Product Advertising API (PA-API) の利用ガイドライン', THEME_NAME ) ?></a><br>
    <a href="<?php _e( 'https://webservice.rakuten.co.jp/guide/rule', THEME_NAME ) ?>" target="_blank"><?php _e( '楽天ウェブサービス規約', THEME_NAME ) ?></a>
  </p>

  </div>
</div>


<!-- エラー -->
<div id="api-error" class="postbox">
  <h2 class="hndle"><?php _e( 'エラー設定', THEME_NAME ) ?></h2>
  <div class="inside">

    <p><?php _e( 'APIに関するエラー通知の設定です。', THEME_NAME ) ?></p>

    <table class="form-table">
      <tbody>
        <!-- メール通知 -->
        <tr>
          <th scope="row">
            <?php
            generate_label_tag(OP_API_ERROR_MAIL_ENABLE, __('メール通知', THEME_NAME) );
            ?>
          </th>
          <td>
          <?php
            generate_checkbox_tag(OP_API_ERROR_MAIL_ENABLE , is_api_error_mail_enable(), __( '商品リンク切れ情報をメールで送信する', THEME_NAME ));
            generate_tips_tag(__( 'APIで商品情報を取得できなかった際に、WordPressに登録されているメール宛にエラーメッセージを送信します。※メール送信は数分遅れる可能性もあります。', THEME_NAME ));
            ?>
          </td>
        </tr>
      </tbody>
    </table>

  </div>
</div>

</div><!-- /.metabox-holder -->
