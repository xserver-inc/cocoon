<?php //YouTubeの動画など
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

//iframeのレスポンシブ対応
add_filter('the_content','wrap_iframe_in_div');
if ( !function_exists( 'wrap_iframe_in_div' ) ):
function wrap_iframe_in_div($the_content) {
  if ( is_singular() ) {
    //YouTube動画にラッパーを装着
    $the_content = preg_replace('/<iframe[^>]+?(youtube\.com|jwplatform\.com)[^<]+?<\/iframe>/is', '<div class="video-container"><div class="video">${0}</div></div>', $the_content);
    //Instagram動画にラッパーを装着
    $the_content = preg_replace('/<iframe[^>]+?instagram\.com[^<]+?<\/iframe>/is', '<div class="instagram-container"><div class="instagram">${0}</div></div>', $the_content);
  }
  return $the_content;
}
endif;

//pixivの埋め込みの大きさ変換
//add_filter('the_content','pixiv_embed_changer');
if ( !function_exists( 'pixiv_embed_changer' ) ):
function pixiv_embed_changer($the_content){
  if ( strstr($the_content, 'http://source.pixiv.net/source/embed.js') )  {
    $patterns = array();
    $patterns[0] = '/data-size="large"/';
    $patterns[1] = '/data-size="medium"/';
    //$patterns[2] = '/data-border="off"/';
    $replacements = array();
    $replacements[0] = 'data-size="small"';
    $replacements[1] = 'data-size="small"';
    //$replacements[2] = 'data-border="on"';
    $the_content = preg_replace($patterns, $replacements, $the_content);
  }
  elseif ( strstr($the_content, 'http://source.pixiv.net/source/embed.js') )  {
    // $patterns = array();
    // $patterns[0] = '/data-size="small"/';
    // $patterns[1] = '/data-size="medium"/';
    // //$patterns[2] = '/data-border="off"/';
    // $replacements = array();
    // $replacements[0] = 'data-size="large"';
    // $replacements[1] = 'data-size="large"';
    // //$replacements[2] = 'data-border="on"';
    // $the_content = preg_replace($patterns, $replacements, $the_content);
  }
  return $the_content;
}
endif;
