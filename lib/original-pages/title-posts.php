<?php //タイトル設定をデータベースに保存

//タイトルセパレーター
update_theme_option(OP_TITLE_SEPARATOR);
//フロントページのタイトル
update_theme_option(OP_FRONT_PAGE_TITLE_FORMAT);
//フロントページのメタディスクリプション
update_theme_option(OP_FRONT_PAGE_META_DESCRIPTION);
//フロントページのメタディスクリプション
update_theme_option(OP_FRONT_PAGE_META_KEYWORDS);
//投稿・固定ページのタイトル
update_theme_option(OP_SINGULAR_PAGE_TITLE_FORMAT);
//投稿・固定ページにメタディスクリプションを含める
update_theme_option(OP_META_DESCRIPTION_TO_SINGULAR);
//投稿・固定ページにメタキーワードを含める
update_theme_option(OP_META_KEYWORDS_TO_SINGULAR);
//カテゴリページのタイトル
update_theme_option(OP_CATEGORY_PAGE_TITLE_FORMAT);
//カテゴリページにメタディスクリプションを含める
update_theme_option(OP_META_DESCRIPTION_TO_CATEGORY);
//カテゴリページにメタキーワードを含める
update_theme_option(OP_META_KEYWORDS_TO_CATEGORY);
//検索エンジンに知らせる日付
update_theme_option(OP_SEO_DATE_TYPE);
