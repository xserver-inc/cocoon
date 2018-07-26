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

if (!is_amp()) {
  add_filter('embed_oembed_html', 'ytsl_oembed_html', 1, 3);
}
if ( !function_exists( 'ytsl_oembed_html' ) ):
function ytsl_oembed_html ($cache, $url, $attr) {
  //_log($cache);
  // check signage data-ytsl
  if (strpos($cache, 'data-ytsl')) {
    preg_match( '/(?<=data-ytsl=")(.+?)(?=")/', $cache, $match_cache);
    $MATCH_CACHE = $match_cache[0];
  };

  //* if ytsl cache is empty we need create it ( video_id, title, picprefix and etc for schema.org ) for youtube videos and playlists
  if (empty($MATCH_CACHE)) {

    // ignor not youtube cache. I dont use other services. Sorry
    if (!strpos($cache, 'youtube')) {
      return $cache;
    }

    // check curl exist
    if (!function_exists('curl_version')) {
      return $cache;
    }

    // remove old data attr older v0.3
    $cache = preg_replace('/data-picprefix=\\"(.+?)\\"/s', "", $cache);
    // if playlist get id.
    if( preg_match_all( '/videoseries|list=/i', $cache, $m )){
      // extract playlist id
      preg_match( '/(?<=list=)(.+?)(?=")/', $cache, $list );
      //get video_id
      $json = json_decode(file_get_contents('https://www.youtube.com/oembed?url=http://www.youtube.com/playlist?list='.$list[1]), true);
      // $video_id extract
      preg_match( '/(?<=vi\/)(.+?)(?=\/)/', $json['thumbnail_url'], $video_id );
    } else {
      preg_match( '/(?<=embed\/)(.+?)(?=\?)/', $cache, $video_id );
    }
    //_log($video_id[0]);
    // if  video_id still empty may be youtube offline :-)))

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

    // if youtube change json
    if (empty($data)) {
      return $cache;
    }

    // \U0001f534 breakes json_decode with big U
    $data = str_replace("\\U",'\\u', $data);
    $json =  json_decode($data,JSON_UNESCAPED_SLASHES);

    //_log($data);
    //_log($json);
    // if json not valid
    if (empty($json)) {
      return $cache;
    }
    //print_r($json);

    $ytsl_cache  = [];
    $ytsl_cache['title'] = htmlentities( $json['title'], ENT_QUOTES, 'UTF-8' );
    $ytsl_cache['video_id'] = $video_id[0];


    $ytsl_cache = base64_encode(json_encode($ytsl_cache));

    //wp core with first parsing inject unknow attr discover. Owerwise md5 is not valid
    if($attr['discover'] == 1){
      unset($attr['discover']);
    }

    $cachekey   = '_oembed_' . md5( $url . serialize( $attr ) );
    // update $cache varable
    $cache      = str_replace('src', ' data-ytsl="'.$ytsl_cache.'" src', $cache);
    //_log($ytsl_cache );
    // save new cache
    update_post_meta( get_the_ID(), $cachekey, $cache );

    $MATCH_CACHE = $ytsl_cache;
  }

  preg_match( '/(?<=height=")(.+?)(?=")/', $cache, $video_height );
  preg_match( '/(?<=width=")(.+?)(?=")/' , $cache, $video_width  );

  $json   = json_decode(base64_decode($MATCH_CACHE), true);
  //_log($json);
  $ytsl   = preg_replace("/data-ytsl=\"(.+?)\"/", "", $cache);
  $ytsl   = htmlentities(str_replace( '=oembed','=oembed&autoplay=1', $ytsl ));

  /**
   * title
   * video_id
   * fixed - responsive or not
   **/

  $thumb_url  = "https://i.ytimg.com/vi/{$json['video_id']}/hqdefault.jpg";

  $fixed      = "height:{$video_height[1]}px;width:{$video_width[1]}px;";
  $wrap_start = null;
  $wrap_end = null;

  if(true) {
    $fixed      = '';
    $wrap_start = '<div class="video-container">';
    $wrap_end   = '</div>';
  }

  $html = $wrap_start . "<div class='video-click video' data-iframe='$ytsl' style='$fixed position:relative;background: url($thumb_url) no-repeat scroll center center / cover' ><div class='video-title-grad'><div class='video-title-text'>{$json['title']}</div></div><div class='video-play'></div></div>" . $wrap_end;

  return $html;

};
endif;
