<?php //アクセス集計ページ本体（ダッシュボード統合）
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 *
 * アクセス解析ダッシュボードと既存の集計設定を統合。
 * ビュー切替は ?view=dashboard|ranking|posts|terms|authors|lifecycle|export|settings で行う。
 */
if ( !defined( 'ABSPATH' ) ) exit;

// 実際のレンダリングは analytics/dashboard-page.php に委譲する
require_once abspath(__FILE__) . 'analytics/dashboard-page.php';

