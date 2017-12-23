<?php //404ページ設定をデータベースに保存

//スキンURLの取得
update_theme_option(OP_SKIN_URL);

//親フォルダのスキンを含める
update_theme_option(OP_INCLUDE_SKIN_TYPE);