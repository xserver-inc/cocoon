<?php //その他設定をデータベースに保存

//簡単SSL対応
update_theme_option(OP_EASY_SSL_ENABLE);

//ファイルシステム認証を有効にする
update_theme_option(OP_REQUEST_FILESYSTEM_CREDENTIALS_ENABLE);

//Simplicityから設定情報の移行
update_theme_option(OP_MIGRATE_FROM_SIMPLICITY);