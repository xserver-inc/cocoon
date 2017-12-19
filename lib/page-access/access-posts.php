<?php //アクセス集計設定保存

//アクセス数を取得するか
update_theme_option(OP_ACCESS_COUNT_ENABLE);

//アクセス数のキャッシュ有効
update_theme_option(OP_ACCESS_COUNT_CACHE_ENABLE);

//アクセス数のキャッシュインターバル（分）
update_theme_option(OP_ACCESS_COUNT_CACHE_INTERVAL);