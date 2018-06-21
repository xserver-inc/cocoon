<?php //フッター設定をデータベースに保存
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */

//フッターの表示タイプ
update_theme_option(OP_FOOTER_DISPLAY_TYPE);

//サイト開始日
update_theme_option(OP_SITE_INITIATION_YEAR);

//クレジット表記
update_theme_option(OP_CREDIT_NOTATION);

//ユーザークレジット表記
update_theme_option(OP_USER_CREDIT_NOTATION);

//フッターメニュー幅
update_theme_option(OP_FOOTER_NAVI_MENU_WIDTH);

//フッターメニュー幅をテキストの幅にする
update_theme_option(OP_FOOTER_NAVI_MENU_TEXT_WIDTH_ENABLE);