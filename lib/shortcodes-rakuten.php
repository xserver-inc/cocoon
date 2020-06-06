<?php //楽天商品リンク
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

//楽天商品リンク作成
if (!shortcode_exists('rakuten')) {
  add_shortcode('rakuten', 'rakuten_product_link_shortcode');
}
if ( !function_exists( 'rakuten_product_link_shortcode' ) ):
function rakuten_product_link_shortcode($atts){
  extract( shortcode_atts( array(
    'id' => null,
    'no' => null,
    'search' => null,
    'shop' => null,
    'kw' => null,
    'title' => null,
    'desc' => null,
    'size' => 'm',
    'price' => null,
    'amazon' => 1,
    'rakuten' => 1,
    'yahoo' => 1,
    'dmm' => 1,
    'border' => 1,
    'logo' => null,
    'sort' => null,
    'image_only' => 0,
    'text_only' => 0,
    'btn1_url' => null,
    'btn1_text' => __( '詳細ページ', THEME_NAME ),
    'btn1_tag' => null,
    'btn2_url' => null,
    'btn2_text' => __( '詳細ページ', THEME_NAME ),
    'btn2_tag' => null,
    'btn3_url' => null,
    'btn3_text' => __( '詳細ページ', THEME_NAME ),
    'btn3_tag' => null,
  ), $atts, 'rakuten' ) );

  $id = sanitize_shortcode_value($id);

  if ($no) {
    $search = $no;
  }
  $search = sanitize_shortcode_value($search);

  //キーワード
  $keyword = sanitize_shortcode_value($kw);
  //全角スペースを半角に置換
  $keyword = str_replace('　', ' ', $keyword);
  //連続した半角スペースを1つに置換
  $keyword = preg_replace('/\s{2,}/', ' ', $keyword);
  //全角のハイフンを半角に置換
  $keyword = str_replace(' －', ' -', $keyword);
  //全角のダッシュを半角に置換
  $keyword = str_replace(' ―', ' -', $keyword);

  $description = $desc;

  $shop = sanitize_shortcode_value($shop);
  $sort = sanitize_shortcode_value($sort);


  //楽天アプリケーションID
  $rakuten_application_id = trim(get_rakuten_application_id());
  //楽天アフィリエイトID
  $rakuten_affiliate_id = trim(get_rakuten_affiliate_id());
  //アソシエイトタグ
  $associate_tracking_id = trim(get_amazon_associate_tracking_id());
  //Yahoo!バリューコマースSID
  $sid = trim(get_yahoo_valuecommerce_sid());
  //Yahoo!バリューコマースPID
  $pid = trim(get_yahoo_valuecommerce_pid());
  //DMMアフィリエイトID
  $dmm_affiliate_id = trim(get_dmm_affiliate_id());

  //キャッシュ更新間隔
  $days = intval(get_api_cache_retention_period());

  //もしもID
  $moshimo_amazon_id  = trim(get_moshimo_amazon_id());
  $moshimo_rakuten_id = trim(get_moshimo_rakuten_id());
  $moshimo_yahoo_id   = trim(get_moshimo_yahoo_id());



  //楽天アフィリエイトIDがない場合
  if (empty($rakuten_application_id) || empty($rakuten_affiliate_id)) {
    $error_message = __( '「楽天アプリケーションID」もしくは「楽天アフィリエイトID」が設定されていません。「Cocoon設定」の「API」タブから入力してください。', THEME_NAME );
    return wrap_product_item_box($error_message);
  }

  //商品IDがない場合
  if (empty($id) && empty($search)) {
    $error_message = __( 'id, no, searchオプションのいずれかが入力されていません。', THEME_NAME );
    return wrap_product_item_box($error_message);
  }

  if ($id) {
    $search_id = $id;
  } else {
    $search_id = $search;
  }
  $default_rakuten_link_tag = get_default_rakuten_link_tag($rakuten_affiliate_id, $search_id, $keyword);

  if ($id) {
    $cache_id = $id;
  } else {
    $cache_id = $search.$shop;
  }


  //キャッシュの取得
  $transient_id = get_rakuten_api_transient_id($cache_id);
  $transient_bk_id = get_rakuten_api_transient_bk_id($cache_id);
  $json_cache = get_transient( $transient_id );
  //キャッシュ更新間隔（randで次回の同時読み込みを防ぐ）
  $cache_expiration = DAY_IN_SECONDS * $days + (rand(0, 60) * 60);

  //キャッシュがある場合はキャッシュを利用する
  if ($json_cache) {
    // _v('cahce');
    $json = $json_cache;
  } else {
    // _v('api');
    $itemCode = null;
    if ($id) {
      $itemCode = '&itemCode='.$id;
    }


    $sortQuery = '&sort='.get_rakuten_api_sort();
    if ($sort && !$id) {
      $sortQuery = '&sort='.$sort;
    }
    $sortQuery = str_replace('+', '%2B', $sortQuery);

    $shopCode = null;
    if ($shop && !$id) {
      $shopCode = '&shopCode='.$shop;
    }
    $searchkw = null;
    if ($search && !$id) {
      $searchkw = '&keyword='.$search;
    }
    $request_url = 'https://app.rakuten.co.jp/services/api/IchibaItem/Search/20170706?applicationId='.$rakuten_application_id.'&affiliateId='.$rakuten_affiliate_id.'&imageFlag=1'.$sortQuery.$shopCode.'&hits=1'.$searchkw.$itemCode;
    //_v($request_url);
    $args = array( 'sslverify' => true );
    $args = apply_filters('wp_remote_get_rakuten_args', $args);
    $json = wp_remote_get( $request_url, $args );

    //ジェイソンのリクエスト結果チェック
    $is_request_success = !is_wp_error( $json ) && $json['response']['code'] === 200;
    //JSON取得に失敗した場合はバックアップキャッシュを取得
    if (!$is_request_success) {
      $json_cache = get_transient( $transient_bk_id );
      if ($json_cache) {
        $json = $json_cache;
        // _v('bk');
        // _v($json);
      }
    }
  }


  if ($json) {
    //ジェイソンのリクエスト結果チェック
    $is_request_success = !is_wp_error( $json ) && $json['response']['code'] === 200;
    ///////////////////////////////////////////
    // キャッシュ削除リンク
    ///////////////////////////////////////////
    $cache_delete_tag = get_cache_delete_tag('rakuten', $cache_id);
    //リクエストが成功した時タグを作成する
    if ($is_request_success) {
      $acquired_date = date_i18n(__( 'Y/m/d H:i', THEME_NAME ));

      //キャッシュの保存
      if (!$json_cache) {
        $jb = $json['body'];
        if ($jb) {
          $jb = preg_replace('/{/', '{"date":"'.$acquired_date.'",', $jb, 1);
          $json['body'] = $jb;
        }
        //楽天APIキャッシュの保存
        set_transient($transient_id, $json, $cache_expiration);
        //楽天APIバックアップキャッシュの保存
        set_transient($transient_bk_id, $json, $cache_expiration * 2);
      }


      $body = $json["body"];
      //ジェイソンの配列化
      $body = json_decode( $body );
      //IDの商品が見つからなかった場合
      if (intval($body->{'count'}) > 0) {

        $Item = $body->{'Items'}['0']->{'Item'};
        if ($Item) {

          $itemName = $Item->{'itemName'};
          $itemCode = $Item->{'itemCode'};
          $itemPrice = $Item->{'itemPrice'};
          $itemCaption = esc_html($Item->{'itemCaption'});
          $itemUrl = esc_attr($Item->{'itemUrl'});//affiliateUrlと同じ
          $shopUrl = esc_attr($Item->{'shopUrl'});//shopAffiliateUrlと同じ
          $affiliateUrl = esc_attr($Item->{'affiliateUrl'});//itemUrlと同じ
          $shopAffiliateUrl = esc_attr($Item->{'shopAffiliateUrl'});//shopUrlと同じ
          $shopName = esc_html($Item->{'shopName'});
          $shopCode = $Item->{'shopCode'};
          $affiliateRate = $Item->{'affiliateRate'};


          //小さな画像
          $smallImageUrls = $Item->{'smallImageUrls'};
          $smallImageUrl = $smallImageUrls['0']->{'imageUrl'};
          //画像サイズの取得
          $sizes = get_rakuten_image_size($smallImageUrl);
          if ($sizes) {
            $smallImageWidth = $sizes['width'];
            $smallImageHeight = $sizes['height'];
          } else {
            $smallImageUrl = null;
            $smallImageWidth = null;
            $smallImageHeight = null;
          }

          //標準画像
          $mediumImageUrls = $Item->{'mediumImageUrls'};
          $mediumImageUrl = $mediumImageUrls['0']->{'imageUrl'};
          //画像サイズの取得
          $sizes = get_rakuten_image_size($mediumImageUrl);
          if ($sizes) {
            $mediumImageWidth = $sizes['width'];
            $mediumImageHeight = $sizes['height'];
          } else {
            $mediumImageUrl = null;
            $mediumImageWidth = null;
            $mediumImageHeight = null;
          }

          //サイズ設定
          $size = strtolower($size);
          switch ($size) {
            case 's':
              $size_class = 'pis-s';
              if ($smallImageUrl) {
                $ImageUrl = $smallImageUrl;
                $ImageWidth = $smallImageWidth;
                $ImageHeight = $smallImageHeight;
              } else {
                $ImageUrl = NO_IMAGE_150;
                $ImageWidth = '64';
                $ImageHeight = '64';
              }
              break;
            default:
              $size_class = 'pis-m';
              if ($mediumImageUrl) {
                $ImageUrl = $mediumImageUrl;
                $ImageWidth = $mediumImageWidth;
                $ImageHeight = $mediumImageHeight;
              } else {
                $ImageUrl = NO_IMAGE_150;
                $ImageWidth = '128';
                $ImageHeight = '128';
              }
              break;
            }


          ///////////////////////////////////////////
          // 商品リンク出力用の変数設定
          ///////////////////////////////////////////
          if ($title) {
            $Title = $title;
          } else {
            $Title = $itemName;
          }

          $TitleAttr = $Title;
          $TitleHtml = $Title;


          ///////////////////////////////////////////
          // 値段表記
          ///////////////////////////////////////////
          $item_price_tag = null;
          if (isset($body->{'date'})) {
            $acquired_date = $body->{'date'};
          }

          if ((is_rakuten_item_price_visible() || $price === '1')
                && $itemPrice
                && $price !== '0'
              ) {
            $FormattedPrice = '￥ '.number_format($itemPrice);;
            $item_price_tag = get_item_price_tag($FormattedPrice, $acquired_date);
          }

          ///////////////////////////////////////////
          // 説明文タグ
          ///////////////////////////////////////////
          $description_tag = get_item_description_tag($description);

          ///////////////////////////////////////////
          // もしも楽天URL
          ///////////////////////////////////////////
          $moshimo_rakuten_url = null;
          $moshimo_rakuten_impression_tag = null;
          if ($moshimo_rakuten_id && is_moshimo_affiliate_link_enable()) {
            $decoded_affiliateUrl = urldecode($affiliateUrl);
            $decoded_affiliateUrl = str_replace('&amp;', '&', $decoded_affiliateUrl);
            //_v(urldecode($decoded_affiliateUrl));
            if (preg_match_all('{\?pc=(.+?)&m=}i', urldecode($decoded_affiliateUrl), $m)) {
              if ($m[1][0]) {
                $rakuten_product_page_url = $m[1][0];
                $moshimo_rakuten_url = 'https://af.moshimo.com/af/c/click?a_id='.$moshimo_rakuten_id.'&p_id=54&pc_id=54&pl_id=616&url='.urlencode($rakuten_product_page_url);
                $affiliateUrl = $moshimo_rakuten_url;
                //インプレッションタグ
                $moshimo_rakuten_impression_tag = get_moshimo_rakuten_impression_tag();
              }
            }
          }

          ///////////////////////////////////////////
          // 検索ボタンの作成
          ///////////////////////////////////////////
          $args = array(
            'keyword' => $keyword,
            'associate_tracking_id' => $associate_tracking_id,
            'rakuten_affiliate_id' => $rakuten_affiliate_id,
            'sid' => $sid,
            'pid' => $pid,
            'dmm_affiliate_id' => $dmm_affiliate_id,
            'moshimo_amazon_id' => $moshimo_amazon_id,
            'moshimo_rakuten_id' => $moshimo_rakuten_id,
            'moshimo_yahoo_id' => $moshimo_yahoo_id,
            'amazon' => $amazon,
            'rakuten' => $rakuten,
            'yahoo' => $yahoo,
            'dmm' => $dmm,
            'amazon_page_url' => null,
            'rakuten_page_url' => $affiliateUrl,
            'btn1_url' => $btn1_url,
            'btn1_text' => $btn1_text,
            'btn1_tag' => $btn1_tag,
            'btn2_url' => $btn2_url,
            'btn2_text' => $btn2_text,
            'btn2_tag' => $btn2_tag,
            'btn3_url' => $btn3_url,
            'btn3_text' => $btn3_text,
            'btn3_tag' => $btn3_tag,
          );
          $buttons_tag = get_search_buttons_tag($args);


          //枠線非表示
          $border_class = null;
          if (!$border) {
            $border_class = ' no-border';
          }

          //ロゴ非表示
          $logo_class = null;
          if ((!is_rakuten_item_logo_visible() && is_null($logo)) || (!$logo && !is_null($logo) )) {
            $logo_class = ' no-after';
          }

          // ///////////////////////////////////////////
          // // キャッシュ削除リンク
          // ///////////////////////////////////////////
          // $cache_delete_tag = get_cache_delete_tag('rakuten', $cache_id);

          ///////////////////////////////////////////
          // アフィリエイト料率タグ
          ///////////////////////////////////////////
          $affiliate_rate_tag = null;
          if (is_user_administrator()) {
            $affiliate_rate_tag = '<span class="product-affiliate-rate">'.__('料率：', THEME_NAME).$affiliateRate.'%</span>';
          }

          ///////////////////////////////////////////
          // 管理者情報タグ
          ///////////////////////////////////////////
          $product_item_admin_tag = get_product_item_admin_tag($cache_delete_tag, $affiliate_rate_tag);

          ///////////////////////////////////////////
          // イメージリンクタグ
          ///////////////////////////////////////////
          $image_only_class = null;
          if ($image_only) {
            $image_only_class = ' rakuten-item-image-only product-item-image-only no-icon';
          }
          $image_link_tag = '<a href="'.esc_url($affiliateUrl).'" class="rakuten-item-thumb-link product-item-thumb-link'.esc_attr($image_only_class).'" target="_blank" title="'.esc_attr($TitleAttr).'" rel="nofollow noopener">'.
                  '<img src="'.esc_url($ImageUrl).'" alt="'.esc_attr($TitleAttr).'" width="'.esc_attr($ImageWidth).'" height="'.esc_attr($ImageHeight).'" class="rakuten-item-thumb-image product-item-thumb-image">'.
                  $moshimo_rakuten_impression_tag.
                '</a>';
          //画像のみ出力する場合
          if ($image_only) {
            return apply_filters('rakuten_product_image_link_tag', $image_link_tag);
          }

          ///////////////////////////////////////////
          // 楽天テキストリンク
          ///////////////////////////////////////////
          $text_only_class = null;
          if ($text_only) {
            $text_only_class = ' rakuten-item-text-only product-item-text-only';
          }
          $text_link_tag =
          '<a href="'.esc_url($affiliateUrl).'" class="rakuten-item-title-link product-item-title-link'.esc_attr($text_only_class).'" target="_blank" title="'.esc_attr($TitleAttr).'" rel="nofollow noopener">'.
            esc_html($TitleHtml).
            $moshimo_rakuten_impression_tag.
          '</a>';
          if ($text_only) {
            return apply_filters('rakuten_product_text_link_tag', $text_link_tag);
          }

          ///////////////////////////////////////////
          // 商品リンクタグの生成
          ///////////////////////////////////////////
          $tag =
            '<div class="rakuten-item-box product-item-box no-icon '.$size_class.$border_class.$logo_class.' '.$id.' cf">'.
              '<figure class="rakuten-item-thumb product-item-thumb">'.
              $image_link_tag.
              '</figure>'.
              '<div class="rakuten-item-content product-item-content cf">'.
                '<div class="rakuten-item-title product-item-title">'.
                  $text_link_tag.
                '</div>'.
                '<div class="rakuten-item-snippet product-item-snippet">'.
                  '<div class="rakuten-item-maker product-item-maker">'.
                    $shopName.
                  '</div>'.
                  $item_price_tag.
                  $description_tag.
                '</div>'.
                $buttons_tag.
              '</div>'.
              $product_item_admin_tag.
            '</div>';

          //_v($tag);
          return apply_filters('rakuten_product_link_tag', $tag);
        }
      } else {
        $error_message = __( '商品が見つかりませんでした。', THEME_NAME );
        //楽天商品取得エラーの出力
        if (!$json_cache) {
          error_log_to_rakuten_product($id, $search, $error_message, $keyword);
        }
        return get_rakuten_error_message_tag($default_rakuten_link_tag, $error_message, $cache_delete_tag);
      }

    } else {
      if (is_array($json) && isset($json['body'])) {
        $ebody = json_decode( $json['body'] );
        $error = $ebody->{'error'};
        $error_description = $ebody->{'error_description'};
        switch ($error) {
          case 'wrong_parameter':
          $error_message = $error_description.':'.__( 'ショートコードの値が正しく記入されていない可能性があります。', THEME_NAME );
          //楽天商品取得エラーの出力
          if (!$json_cache) {
            error_log_to_rakuten_product($id, $search, $error_message, $keyword);
          }
          //楽天APIキャッシュの保存
          set_transient($transient_id, $json, $cache_expiration);
          return get_rakuten_error_message_tag($default_rakuten_link_tag, $error_message, $cache_delete_tag);
            break;
          default:
          $error_message = $error_description.':'.__( 'Bad Requestが返されました。リクエスト制限を受けた可能性があります。しばらく時間を置いた後、リロードすると商品リンクが表示される可能性があります。', THEME_NAME );
            break;
        }
        return get_rakuten_error_message_tag($default_rakuten_link_tag, $error_message);
      }
    }
  } else {
    $error_message = __( 'JSONを取得できませんでした。接続環境に問題がある可能性があります。', THEME_NAME );
    return get_rakuten_error_message_tag($default_rakuten_link_tag, $error_message);
  }

}
endif;

//楽天APIで商品情報を取得できなかった場合のエラーログ
if ( !function_exists( 'error_log_to_rakuten_product' ) ):
  function error_log_to_rakuten_product($id, $no, $message = '', $keyword = ''){
    //エラーログに出力
    $msg = date_i18n("Y-m-d H:i:s").','.
           $id.','.
           $no.','.
           get_the_permalink().
           PHP_EOL;
   $rakuten_affiliate_id = trim(get_rakuten_affiliate_id());
   if ($keyword) {
     $rakuten_url = get_rakuten_affiliate_search_url($keyword, $rakuten_affiliate_id);
   } else {
    $rakuten_url = 'https://a.r10.to/hllTWS';
   }
  error_log($msg, 3, get_theme_rakuten_product_error_log_file());

    //メールで送信
    if (is_api_error_mail_enable()) {
      $subject = __( '楽天商品取得エラー', THEME_NAME );
      $mail_msg =
        __( '楽天商品リンクが取得できませんでした。', THEME_NAME ).PHP_EOL.
        PHP_EOL.
        'ID:'.$id.PHP_EOL.
        'No.(Search):'.$no.PHP_EOL.
        'URL:'.get_the_permalink().PHP_EOL.
        'Message:'.$message.PHP_EOL.
        THEME_MAIL_RAKUTEN_PR.PHP_EOL.
        $rakuten_url.
        THEME_MAIL_CREDIT;
      wp_mail( get_wordpress_admin_email(), $subject, $mail_msg );
    }
  }
  endif;
