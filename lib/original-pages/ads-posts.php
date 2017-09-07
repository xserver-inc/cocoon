<?php //広告設定をデータベースに保存

//広告表示設定
update_theme_option(OP_ALL_ADS_VISIBLE);
//広告コード
update_theme_option(OP_AD_CODE);
//広告ラベル
update_theme_option(OP_AD_LABEL);
//広告除外記事ID
update_theme_option(OP_AD_EXCLUDE_POST_IDS);
//広告除外カテゴリーID
update_theme_option(OP_AD_EXCLUDE_CATEGORY_IDS);