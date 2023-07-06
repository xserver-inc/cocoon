<?php //フッターで呼び出すスクリプトをまとめたもの
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;
ob_start();
?>

<?php //フッターで読み込むJavaScript用テンプレート
cocoon_template_part('tmp/footer-javascript');?>

<?php //カスタムフィールドの挿入（カスタムフィールド名：footer_custom）
cocoon_template_part('tmp/footer-custom-field');?>

<?php //アクセス解析フッタータグの取得
cocoon_template_part('tmp/footer-analytics'); ?>

<?php //フッター挿入用のユーザー用テンプレート
if (is_amp()) {
  //AMP用のフッターアクションフック
  do_action( 'wp_amp_footer_insert_open' );
  //親テーマのAMPフッター用
  cocoon_template_part('tmp/amp-footer-insert');
  //子テーマのAMPフッター用
  cocoon_template_part('tmp-user/amp-footer-insert');
} else {
  //フッター用のアクションフック
  do_action( 'wp_footer_insert_open' );
  //フッター用のテンプレート
  cocoon_template_part('tmp-user/footer-insert');
}
$buffer = ob_get_clean();
//JS縮小化
if (is_js_minify_enable()) {
  $buffer = tag_code_to_minify_js($buffer);
}
echo $buffer;
?>
