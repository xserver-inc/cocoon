<?php //スキンから親テーマの定義済み関数等をオーバーライドして設定の書き換えが可能
/*
///////////////////////////////////////////
// 設定操作サンプル
// lib\page-settings\内のXXXXX-funcs.phpに書かれている
// 定義済み関数をオーバーライドして設定を上書きできます。
// 関数をオーバーライドする場合は必ず!function_existsで
// 存在を確認してください。
///////////////////////////////////////////
*/

global $_THEME_OPTIONS;
$_THEME_OPTIONS =
array(
  'entry_card_type' => 'vertical_card_2', //インデックスのカードタイプ
  'entry_card_border_visible' => 0, //インデックスの枠線
  'smartphone_entry_card_1_column' => 0, //スマートフォンで1カラムにするか
  'main_column_contents_width' => 730, //メインカラムの幅
  'entry_card_snippet_visible' => 0, //スニペットを表示するか
  'smartphone_entry_card_snippet_visible' => 0, //スニペットを表示するか(モバイル)
  'entry_card_post_date_visible' => 1, //投稿日を表示するか
  'entry_card_post_update_visible' => 0, //更新日を表示するか
  'toc_depth' => 3, //目次の深さ
  'toc_number_type' => 'none', //目次の数字の表示
  'single_breadcrumbs_position' => 'main_top', //パンくずリストの位置
  'sns_top_share_buttons_count_visible' => 0 , //トップSNSシェア数の表示
  'sns_bottom_share_column_count' => 3, //ボトムSNSシェアボタンのカラム数
  'sns_bottom_share_logo_caption_position' => 'left_and_right', //ボトムSNSシェアボタンのロゴとキャプションの位置
  'sns_bottom_share_buttons_count_visible' => 0, //ボトムSNSシェア数の表示
  'related_entry_card_snippet_visible' => 0, //関連記事のスニペットの表示
);
