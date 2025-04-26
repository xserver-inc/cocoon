<?php //ダッシュボード上部に表示するメッセージ
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 * @reference: https://github.com/rocket-martue/Hello-Musou
 */
if ( !defined( 'ABSPATH' ) ) exit;

if ( !function_exists( 'dashboard_message_get_text' ) ):
function dashboard_message_get_text() {
	//メッセージファイルを取得
	$filename = get_template_directory() . '/lib/dashboard-message.txt';
	$messages = wp_filesystem_get_contents( $filename );

	//改行で分ける
	$messages = explode( "\n", $messages );

	//一行だけを表示する
	return wptexturize( $messages[ rand( 0, count( $messages ) - 1 ) ] );
}
endif;


//メッセージHTMLの出力
add_action( 'admin_notices', 'generate_dashboard_message' );
if ( !function_exists( 'generate_dashboard_message' ) ):
function generate_dashboard_message() {
	$chosen = dashboard_message_get_text();
	$lang   = '';
	if ( 'en_' !== substr( get_user_locale(), 0, 3 ) ) {
		$lang = ' lang="en"';
	}

	printf(
		'<p id="dashboard-message"><span class="screen-reader-text">%s </span><span dir="ltr"%s>%s</span></p>',
		__( 'Guidance on COVID-19:', THEME_NAME),
		$lang,
		$chosen
	);
}
endif;

//メッセージエリアのCSS出力
add_action( 'admin_head', 'dashboard_message_css' );
if ( !function_exists( 'dashboard_message_css' ) ):
function dashboard_message_css() {
	echo "
	<style type='text/css'>
	#dashboard-message {
		float: right;
		padding: 5px 10px;
		margin: 0;
		font-size: 12px;
		line-height: 1.6666;
	}
	.rtl #dashboard-message {
		float: left;
	}
	.block-editor-page #dashboard-message {
		display: none;
	}
	@media screen and (max-width: 782px) {
		#dashboard-message,
		.rtl #dashboard-message {
			float: none;
			padding-left: 0;
			padding-right: 0;
		}
	}
	</style>
	";
}
endif;


