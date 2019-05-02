<?php //YouTube関連

/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 *
 * Cocoon WordPress Theme incorporates code from "Youtube SpeedLoad" WordPress Plugin, Copyright 2017 Alexufo[http://habrahabr.ru/users/alexufo/]
"Youtube SpeedLoad" WordPress Plugin is distributed under the terms of the GNU GPL v2
 */
if ( !defined( 'ABSPATH' ) ) exit;

//Jetpackとの競合対応
remove_action( 'init', 'wpcom_youtube_embed_crazy_url_init' );
//YouTube動画表示の高速化
add_filter('embed_oembed_html', 'youtube_embed_oembed_html', 1, 3);
if ( !function_exists( 'youtube_embed_oembed_html' ) ):
function youtube_embed_oembed_html ($cache, $url, $attr) {
  if (is_amp() || is_feed()) {
    return $cache;
  }

  // data-youtubeチェック
  if (strpos($cache, 'data-youtube')) {
    preg_match( '/(?<=data-youtube=")(.+?)(?=")/', $cache, $match_cache);
    $MATCH_CACHE = $match_cache[0];
  };


  //* YouTubeキャッシュが空のときYouTubeビデオとプレイリストのためにこれらを作成する ( video_id, title, picprefix and etc for schema.org )
  if (empty($MATCH_CACHE)) {

    // YouTubeキャッシュを無視する
    if (!strpos($cache, 'youtube')) {
      return $cache;
    }

    // curlの存在確認
    if (!function_exists('curl_version')) {
      return $cache;
    }

    // 古いデータの除去
    $cache = preg_replace('/data-picprefix=\\"(.+?)\\"/s', "", $cache);
    // プレイリストIDがある場合
    if( preg_match_all( '/videoseries|list=/i', $cache, $m )){
      // プレイリストIDの抽出
      preg_match( '/(?<=list=)(.+?)(?=")/', $cache, $list );
      // ビデオIDの取得
      $json_data = @wp_filesystem_get_contents('https://www.youtube.com/oembed?url=http://www.youtube.com/playlist?list='.$list[1], true);
      $json = json_decode($json_data, true);
      // ビデオIDの抽出
      preg_match( '/(?<=vi\/)(.+?)(?=\/)/', $json['thumbnail_url'], $video_id );
    } else {
      preg_match( '/(?<=embed\/)(.+?)(?=\?)/', $cache, $video_id );
    }

    // もしビデオIDないまだ空ならおそらくYouTubeがオフライン
    if (!$video_id[0]) {
      return $cache;
    }

    $ch = curl_init();
    $headers = array(
      'Accept-language: en',
      'User-Agent: Mozilla/5.0 (iPad; CPU OS 7_0_4 like Mac OS X) AppleWebKit/537.51.1 (KHTML, like Gecko) Version/7.0 Mobile/11B554a Safari',
    );
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_URL, "https://www.youtube.com/oembed?url=http://www.youtube.com/watch?v=" . $video_id[0] . "&format=json");

    $data = curl_exec($ch);

    $info = curl_getinfo($ch);
    curl_close($ch);

    if ($info['http_code'] != 200){
      return $cache;
    }

    // もしYouTubeがJSONを変更したら
    if (empty($data)) {
      return $cache;
    }

    // \U0001f534 breakes json_decode with big U
    $data = str_replace("\\U",'\\u', $data);
    $json =  json_decode($data,JSON_UNESCAPED_SLASHES);


    // ジェイソンが無効な場合
    if (empty($json)) {
      return $cache;
    }

    $youtube_cache  = array();
    $youtube_cache['title'] = htmlentities( $json['title'], ENT_QUOTES, 'UTF-8' );
    $youtube_cache['video_id'] = $video_id[0];


    $youtube_cache = base64_encode(json_encode($youtube_cache));

    //wp core with first parsing inject unknow attr discover. Owerwise md5 is not valid
    if(isset($attr['discover']) && $attr['discover'] == 1){
      unset($attr['discover']);
    }

    $cachekey   = '_oembed_' . md5( $url . serialize( $attr ) );
    // $cache変数のアップデート
    $cache      = str_replace('src', ' data-youtube="'.$youtube_cache.'" src', $cache);

    // 新しいキャッシュを保存
    update_post_meta( get_the_ID(), $cachekey, $cache );

    $MATCH_CACHE = $youtube_cache;
  }

  $json   = json_decode(base64_decode($MATCH_CACHE), true);

  $youtube   = preg_replace("/data-youtube=\"(.+?)\"/", "", $cache);
  if (preg_match( '{src=[\'"](.+?)[\'"]}i', $youtube, $m)) {
    $default_args = array('rel' => 0, 'autoplay' => 1);
    $default_args = apply_filters('youtube_embed_default_args', $default_args);

    //元のURL情報の取得
    $urls = parse_url($url);
    $query = isset($urls['query']) ? $urls['query'] : '';
    parse_str($query, $args);
    //デフォルトパラメータと結合
    $args = array_merge($default_args, $args);

    //動画IDは不要なので削除
    if (isset($args['v'])) {
      $args['v'] = null;
    }
    //srcのURL
    $youtube_old_url = $m[1];
    //デフォルトのパラメータ設定
    $args = apply_filters('youtube_embed_args', $args);
    //クエリを追加
    $youtube_new_url = add_query_arg($args, $youtube_old_url);

    $youtube = str_replace($youtube_old_url, $youtube_new_url, $youtube);
  }

  $youtube   = htmlentities($youtube);

  $thumb_url  = "https://i.ytimg.com/vi/{$json['video_id']}/hqdefault.jpg";
  $wrap_start = '<div class="video-container">';
  $wrap_end   = '</div>';


  $html = $wrap_start . "<div class='video-click video' data-iframe='$youtube' style='position:relative;background: url($thumb_url) no-repeat scroll center center / cover' ><div class='video-title-grad'><div class='video-title-text'>{$json['title']}</div></div><div class='video-play'></div></div>" . $wrap_end;

  return apply_filters('youtube_embed_html', $html);

};
endif;
