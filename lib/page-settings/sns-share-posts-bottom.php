<?php //SNSシェア設定をデータベースに保存
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

//シェアボタンの表示
update_theme_option(OP_SNS_BOTTOM_SHARE_BUTTONS_VISIBLE);
//SNSシェアメッセージ
update_theme_option(OP_SNS_BOTTOM_SHARE_MESSAGE);
//個々のSNSシェアボタンの表示（定義一覧をもとに一括保存）
foreach ( get_cocoon_sns_share_options() as $sns_option ) {
  if ( !empty($sns_option['bottom_key']) ) {
    update_theme_option($sns_option['bottom_key']);
  }
}
//フロントページシェアボタンの表示
update_theme_option(OP_SNS_FRONT_PAGE_BOTTOM_SHARE_BUTTONS_VISIBLE);
//投稿シェアボタンの表示
update_theme_option(OP_SNS_SINGLE_BOTTOM_SHARE_BUTTONS_VISIBLE);
//固定ページシェアボタンの表示
update_theme_option(OP_SNS_PAGE_BOTTOM_SHARE_BUTTONS_VISIBLE);
//カテゴリーシェアボタンの表示
update_theme_option(OP_SNS_CATEGORY_BOTTOM_SHARE_BUTTONS_VISIBLE);
//タグシェアボタンの表示
update_theme_option(OP_SNS_TAG_BOTTOM_SHARE_BUTTONS_VISIBLE);
//SNSシェアボタンカラー
update_theme_option(OP_SNS_BOTTOM_SHARE_BUTTON_COLOR);
//シェアボタンのカラム数
update_theme_option(OP_SNS_BOTTOM_SHARE_COLUMN_COUNT);
//SNSシェアボタンのロゴとキャプションの位置
update_theme_option(OP_SNS_BOTTOM_SHARE_LOGO_CAPTION_POSITION);
//SNSボトムシェア数の表示
update_theme_option(OP_SNS_BOTTOM_SHARE_BUTTONS_COUNT_VISIBLE);
