<?php //カラム設定をデータベースに保存
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

///////////////////////////////////////
// メインカラム
///////////////////////////////////////

//メインカラム幅
update_theme_option(OP_MAIN_COLUMN_CONTENTS_WIDTH);

//メインカラム外側余白
update_theme_option(OP_MAIN_COLUMN_MARGIN);

//メインカラム内側余白
update_theme_option(OP_MAIN_COLUMN_PADDING);

//メインカラム枠線幅
update_theme_option(OP_MAIN_COLUMN_BORDER_WIDTH);

//メインカラム枠線色
update_theme_option(OP_MAIN_COLUMN_BORDER_COLOR);

///////////////////////////////////////
// サイドバー
///////////////////////////////////////

//サイドバー幅
update_theme_option(OP_SIDEBAR_CONTENTS_WIDTH);

//サイドバー外側余白
update_theme_option(OP_SIDEBAR_MARGIN);

//サイドバー内側余白
update_theme_option(OP_SIDEBAR_PADDING);

//サイドバー枠線幅
update_theme_option(OP_SIDEBAR_BORDER_WIDTH);

//サイドバー枠線色
update_theme_option(OP_SIDEBAR_BORDER_COLOR);


//メインカラムとサイドバーの間隔
update_theme_option(OP_MAIN_SIDEBAR_MARGIN);
