<?php //Custom JS Widget
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

add_action( 'admin_menu', 'add_custom_js_custom_box' );
if ( !function_exists( 'add_custom_js_custom_box' ) ):
function add_custom_js_custom_box() {
  add_meta_box( 'custom_js', __( 'カスタムJavaScript', THEME_NAME ), 'view_custom_js_custom_box', 'post', 'normal', 'low' );
  add_meta_box( 'custom_js', __( 'カスタムJavaScript', THEME_NAME ), 'view_custom_js_custom_box', 'page', 'normal', 'low' );
  //カスタム投稿タイプに登録
  add_meta_box_custom_post_types( 'custom_js', __( 'カスタムJavaScript', THEME_NAME ), 'view_custom_js_custom_box', 'custum_post', 'normal', 'low' );
}
endif;

if ( !function_exists( 'view_custom_js_custom_box' ) ):
function view_custom_js_custom_box() {
  global $post;
  echo '<input type="hidden" name="custom_js_noncename" id="custom_js_noncename" value="'.wp_create_nonce('custom-js').'" />';
  echo '<textarea name="custom_js" id="custom_js" rows="5" cols="30" style="width:100%;" placeholder="'.__( '当ページ変更用のJavaScriptコードのみを入力してください。scriptタグは不要です。', THEME_NAME ).'">'.get_post_meta($post->ID,'_custom_js',true).'</textarea>';
}
endif;

add_action( 'save_post', 'custom_js_custom_box_save_data' );
if ( !function_exists( 'custom_js_custom_box_save_data' ) ):
function custom_js_custom_box_save_data($post_id) {
  $custom_js_noncename = isset($_POST['custom_js_noncename']) ? $_POST['custom_js_noncename'] : null;
  if (!wp_verify_nonce($custom_js_noncename, 'custom-js')) return $post_id;
  if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return $post_id;
  $custom_js = $_POST['custom_js'];
  update_post_meta($post_id, '_custom_js', $custom_js);
}
endif;

add_action( 'wp_footer','insert_custom_js', 9999 );
if ( !function_exists( 'insert_custom_js' ) ):
function insert_custom_js() {
  if ( is_singular() ) {
    $custom_js = get_post_meta(get_the_ID(), '_custom_js', true);
    if ($custom_js) {
      echo '<!-- '.THEME_NAME.' Custom JS -->'.PHP_EOL;
      echo '<script type="text/javascript">' . $custom_js . '</script>'.PHP_EOL;
    }
  }
}
endif;

