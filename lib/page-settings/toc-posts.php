<?php //目次設定をデータベースに保存
//目次の表示
update_theme_option(OP_TOC_VISIBLE);

//投稿ページで目次の表示
update_theme_option(OP_SINGLE_TOC_VISIBLE);

//固定ページで目次の表示
update_theme_option(OP_PAGE_TOC_VISIBLE);

//目次タイトル
update_theme_option(OP_TOC_TITLE);

//目次表示条件（数）
update_theme_option(OP_TOC_DISPLAY_COUNT);

//目次を表示する深さ
update_theme_option(OP_TOC_DEPTH);

//目次の数字の表示
update_theme_option(OP_TOC_NUMBER_TYPE);

//目次を広告の手前に表示
update_theme_option(OP_TOC_BEFORE_ADS);