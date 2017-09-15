<?php //SNS関係の関数

//はてブURL
if ( !function_exists( 'get_hatebu_share_url' ) ):
function get_hatebu_share_url(){
  if (is_singular()) {
    $url = get_permalink();
  } else {
    $url = home_url();
  }

  if (strpos($url, 'https://') === 0) {
    $u = preg_replace('/https:\/\//', 's/', $url);
  } else {
    $u = preg_replace('/http:\/\//', '', $url);
  }
  return '//b.hatena.ne.jp/entry/'.$u;
}
endif;

//Twitter IDを含めるURLパラメータを取得
function get_twitter_via_param(){
  if ( get_the_author_twitter_id() && is_twitter_id_include() ) {
    return '&amp;via='.get_the_author_twitter_id();
  }
}

//ツイート後にフォローを促すパラメータを取得
function get_twitter_related_param(){
  if ( get_the_author_twitter_id() && is_twitter_related_follow_enable() ) {
    return '&amp;related='.get_the_author_twitter_id();//.':フォロー用の説明文';
  }
}

//feedlyの購読者数取得
if ( !function_exists( 'fetch_feedly_count' ) ):
function fetch_feedly_count(){
  //DBキャッシュからカウントの取得
  $subscribers = get_transient( 'feedly_subscribers' );
  if ( $subscribers ) {
    return $subscribers;
  }
  $feed_url = rawurlencode( get_bloginfo( 'rss2_url' ) );
  $res = 0;
  $args = array( 'sslverify' => is_ssl_verification_enable() );
  $subscribers = wp_remote_get( "http://cloud.feedly.com/v3/feeds/feed%2F$feed_url", $args );
  if (!is_wp_error( $subscribers ) && $subscribers["response"]["code"] === 200) {
    $subscribers = json_decode( $subscribers['body'] );
    if ( $subscribers ) {
      $subscribers = $subscribers->subscribers;
      set_transient( 'feedly_subscribers', $subscribers, 60 * 60 * 12 );
      $res = ($subscribers ? $subscribers : 0);
    }
  }
  return $res;
}
endif;

// //SSLの検証をするか
// if ( !function_exists( 'fetch_push7_info' ) ):
// function ssl_verification(){
//   return true;
// }
// endif;

//Push7情報取得
if ( !function_exists( 'fetch_push7_info' ) ):
function fetch_push7_info(){
  //DBキャッシュからカウントの取得
  $info = get_transient( 'push7_info' );
  if ( $info ) {
    return $info;
  }
  $res = null;
  $app_no = get_push7_follow_app_no();
  if ( $app_no ) {
    $url = 'https://api.push7.jp/api/v1/'.$app_no.'/head';//要https:
    $args = array( 'sslverify' => is_ssl_verification_enable() );
    //$args = array('sslverify' => false);
    $info = wp_remote_get( $url, $args );
    if (!is_wp_error( $info ) && $info["response"]["code"] === 200) {
      $info = json_decode( $info['body'] );
      if ( $info ) {
        //Push7情報をキャッシュに保存
        set_transient( 'push7_info', $info, 60 * 60 * 12 );

        $res = $info;
      }
    }
  }
  return $res;
}
endif;

//不具合対策用のfeedlyの購読者数取得の別名
if ( !function_exists( 'get_feedly_count' ) ):
function get_feedly_count(){
  return fetch_feedly_count();
}
endif;


//Google＋カウントの取得
if ( !function_exists( 'fetch_google_plus_count' ) ):
function fetch_google_plus_count($url) {
  $query = 'https://apis.google.com/_/+1/fastbutton?url=' . urlencode( $url );
  //URL（クエリ）先の情報を取得
  $args = array( 'sslverify' => is_ssl_verification_enable() );
  $result = wp_remote_get($query, $args);
  // 正規表現でカウント数のところだけを抽出
  preg_match( '/\[2,([0-9.]+),\[/', $result["body"], $count );
  // 共有数を表示
  return isset($count[1]) ? intval($count[1]) : 0;
}
endif;

//Pocketカウントの取得
if ( !function_exists( 'fetch_pocket_count' ) ):
function fetch_pocket_count($url) {
  if ( WP_Filesystem() ) {//WP_Filesystemの初期化
    global $wp_filesystem;//$wp_filesystemオブジェクトの呼び出し
    //$query = 'http://widgets.getpocket.com/v1/button?v=1&count=horizontal&url=' . $url;
    $url = urlencode($url);
    $query = 'https://widgets.getpocket.com/v1/button?label=pocket&count=horizontal&v=1&url='.$url.'&src=' . $url;
    //URL（クエリ）先の情報を取得
    $args = array( 'sslverify' => is_ssl_verification_enable() );
    $result = wp_remote_get($query, $args);
    //var_dump($result["body"]);
    // 正規表現でカウント数のところだけを抽出
    preg_match( '/<em id="cnt">([0-9.]+)<\/em>/i', $result["body"], $count );
    // 共有数を表示
    return isset($count[1]) ? intval($count[1]) : 0;
  }
  return 0;
}
endif;

//count.jsoonからTwitterのツイート数を取得
if ( !function_exists( 'fetch_twitter_count' ) ):
function fetch_twitter_count($url){
  $url = rawurlencode( $url );
  $args = array( 'sslverify' => is_ssl_verification_enable() );
  $subscribers = wp_remote_get( "https://jsoon.digitiminimi.com/twitter/count.json?url=$url", $args );
  $res = '0';
  if (!is_wp_error( $subscribers ) && $subscribers["response"]["code"] === 200) {
       $body = $subscribers['body'];
    $json = json_decode( $body );
    $res = ($json->{"count"} ? $json->{"count"} : '0');
  }
  return $res;
}
endif;

//Facebookシェア数を取得する
if ( !function_exists( 'fetch_facebook_count' ) ):
function fetch_facebook_count($url) {
  //URLをURLエンコード
  $encoded_url = rawurlencode( $url );
  //オプションの設定
  $args = array( 'sslverify' => is_ssl_verification_enable() );
  //Facebookにリクエストを送る
  $response = wp_remote_get( 'http://graph.facebook.com/?id='.$encoded_url, $args );
  $res = 0;

  //取得に成功した場合
  if (!is_wp_error( $response ) && $response["response"]["code"] === 200) {
    $body = $response['body'];
    $json = json_decode( $body ); //ジェイソンオブジェクトに変換する
    $res = ($json->{'share'}->{'share_count'} ? $json->{'share'}->{'share_count'} : 0);
  }
  return $res;
}
endif;

//SNS Count Cacheプラグインはインストールされているか
function scc_exists(){
  return function_exists('scc_get_share_twitter');
}

//ツイート数取得関数が存在しているか
function scc_twitter_exists(){
  return function_exists('scc_get_share_twitter');
}

//Facebookシェア数取得関数が存在しているか
function scc_facebook_exists(){
  return function_exists('scc_get_share_facebook');
}

//Google＋シェア数取得関数が存在しているか
function scc_gplus_exists(){
  return function_exists('scc_get_share_gplus');
}

//はてブ数取得関数が存在しているか
function scc_hatebu_exists(){
  return function_exists('scc_get_share_hatebu');
}

//Pocketストック数取得関数が存在しているか
function scc_pocket_exists(){
  return function_exists('scc_get_share_pocket');
}

//トータルシェア数取得関数が存在しているか
function scc_total_exists(){
  return function_exists('scc_get_share_total');
}

//feedly購読者数取得関数が存在しているか
function scc_feedly_exists(){
  return function_exists('scc_get_follow_feedly');
}

//Push7購読者数取得関数が存在しているか
function scc_push7_exists(){
  return function_exists('scc_get_follow_push7');
}


//LINEのシェアURLを取得
if ( !function_exists( 'get_line_share_url' ) ):
function get_line_share_url(){
  // if (wp_is_mobile()) {
  //   return '//line.me/R/msg/text/?'.get_the_title().'%0D%0A'.get_permalink();
  // } else {
  //   return '//lineit.line.me/share/ui?url='.get_the_permalink();
  // }
  return '//timeline.line.me/social-plugin/share?url='.urlencode(get_the_permalink());
}
endif;

//TwitterのシェアURLを取得
if ( !function_exists( 'get_twitter_share_url' ) ):
function get_twitter_share_url(){
  if ( is_singular() ) {
    $url = get_the_permalink();
  } else {
    $url = home_url();
  }

  return 'https://twitter.com/intent/tweet?text='.urlencode( get_the_title() ).'&amp;url='.
  urlencode( $url ).
  get_twitter_via_param(). //ツイートにメンションを含める
  get_twitter_related_param();//ツイート後にフォローを促す
}
endif;