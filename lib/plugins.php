<?php //プラグイン関連する関数
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

if ( !function_exists( 'is_jetpack_exist' ) ):
function is_jetpack_exist(){
  return class_exists('Jetpack');
}
endif;

//JetpackのAMP計測関数
if ( !function_exists( 'jetpack_amp_build_stats_pixel_url' ) ):
function jetpack_amp_build_stats_pixel_url() {
  if (!is_jetpack_exist()) {
    return ;
  }
  global $wp_the_query;
  if ( function_exists( 'stats_build_view_data' ) ) { // added in https://github.com/Automattic/jetpack/pull/3445
    $data = stats_build_view_data();
  } else {
    $blog = Jetpack_Options::get_option( 'id' );
    $tz = get_option( 'gmt_offset' );
    $v = 'ext';
    $blog_url = parse_url( site_url() );
    $srv = $blog_url['host'];
    $j = sprintf( '%s:%s', JETPACK__API_VERSION, JETPACK__VERSION );
    $post = $wp_the_query->get_queried_object_id();
    $data = compact( 'v', 'j', 'blog', 'post', 'tz', 'srv' );
  }
  $data['host'] = isset( $_SERVER['HTTP_HOST'] ) ? rawurlencode( $_SERVER['HTTP_HOST'] ) : '';
  $data['rand'] = 'RANDOM'; // amp placeholder
  $data['ref'] = 'DOCUMENT_REFERRER'; // amp placeholder
  $data = array_map( 'rawurlencode' , $data );
  return add_query_arg( $data, 'https://pixel.wp.com/g.gif' );
}
endif;
