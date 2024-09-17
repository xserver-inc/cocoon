<?php //Punycode関数
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

///////////////////////////////////////
//http_build_url関数の代わり
//http://php.net/manual/ja/function.http-build-url.php
///////////////////////////////////////
//参考：http://mio-koduki.blogspot.jp/2012/05/php-httpbuildurl.html
//PECLのhttp_build_urlがある可能性があるためチェックする
if(!function_exists('puny_http_build_url')){
  //フラグの定数を設定
  define('COCOON_HTTP_URL_REPLACE',1);
  define('COCOON_HTTP_URL_JOIN_PATH',2);
  define('COCOON_HTTP_URL_JOIN_QUERY',4);
  define('COCOON_HTTP_URL_STRIP_USER',8);
  define('COCOON_HTTP_URL_STRIP_PASS',16);
  define('COCOON_HTTP_URL_STRIP_AUTH',24);
  define('COCOON_HTTP_URL_STRIP_PORT',32);
  define('COCOON_HTTP_URL_STRIP_PATH',64);
  define('COCOON_HTTP_URL_STRIP_QUERY',128);
  define('COCOON_HTTP_URL_STRIP_FRAGMENT',256);
  define('COCOON_HTTP_URL_STRIP_ALL',504);
  function puny_http_build_url($url,$parts=array(),$flags=COCOON_HTTP_URL_REPLACE,&$new_url=array())  {
    if (empty($url)) return;
    //置き換えるキー
    $key=array('user','pass','port','path','query','fragment');
    //urlをパースする
    $new_url=parse_url($url);
    //スキーマとホストが設定されていれば置き換える
    if(isset($parts['scheme'])) {
      $new_url['scheme']=$parts['scheme'];
    } if(isset($parts['host'])) {
      $new_url['host']=$parts['host'];
    }
    //フラグにCOCOON_HTTP_URL_REPLACEがあれば置き換える
    if($flags&COCOON_HTTP_URL_REPLACE) {
      foreach($key as $v) {
        if(isset($parts[$v])) {
          $new_url[$v]=$parts[$v];
        }
      }
    } else {
      //フラグにCOCOON_HTTP_URL_JOIN_PATHがあり新しいパスがあれば新しいパスをつなげる
      if(isset($parts['path'])&&$flags&COCOON_HTTP_URL_JOIN_PATH) {
        if(isset($new_url['path'])) {
          $new_url['path']=rtrim(preg_replace('#'.preg_quote(basename($new_url['path']),'#').'$#','',$new_url['path']),'/').'/'.ltrim($parts['path'],'/');
        } else {
          $new_url['path']=$parts['path'];
        }
      }
      //フラグにCOCOON_HTTP_URL_JOIN_QUERYがあり新しいクエリがあれば新しいクエリをつなげる
      if(isset($parts['query'])&&$flags&COCOON_HTTP_URL_JOIN_QUERY) {
        if(isset($new_url['query'])) {
          $new_url['query'].='&'.$parts['query'];
        } else {
          $new_url['query']=$parts['query'];
        }
      }
    }
    //ストリップフラグの判定をし、設定されていれば消す
    foreach($key as $v) {
      if($flags&constant('COCOON_HTTP_URL_STRIP_'.strtoupper($v))) {
          unset($new_url[$v]);
      }
    }
    //パーツを繋げて返す
    return (isset($new_url['scheme'])?$new_url['scheme'].'://':'').(isset($new_url['user'])?$new_url['user'].(isset($new_url['pass'])?':'.$new_url['pass']:'').'@':'').(isset($new_url['host'])?$new_url['host']:'').(isset($new_url['port'])?':'.$new_url['port']:'').(isset($new_url['path'])?$new_url['path']:'').(isset($new_url['query'])?'?'.$new_url['query']:'').(isset($new_url['fragment'])?'#'.$new_url['fragment']:'');
  }
}

//Punycode変換ライブラリを読み込む
include 'punycode-obj.php';
///////////////////////////////////////
//Punycode変換関数
///////////////////////////////////////
function convert_punycode($url, $is_encode = true){
  if (empty($url)) return;
  $url_parts = parse_url($url);
  $Punycode = new Punycode();
  $url_host = null;
  if (isset($url_parts['host'])) {
    $url_host = $url_parts['host'];
  }
  if ( $is_encode ) {
    $host = $Punycode->encode($url_host);
  } else {
    $host = $Punycode->decode($url_host);
  }
  $url_parts['host'] = $host;
  return puny_http_build_url($url, $url_parts);
}

///////////////////////////////////////
//Punycodeへの変換（エンコード）
///////////////////////////////////////
function punycode_encode($url){
  return convert_punycode($url, true);
}

///////////////////////////////////////
//通常のURLへ戻す（PreCode）
///////////////////////////////////////
function punycode_decode($url){
  return convert_punycode($url, false);
}
