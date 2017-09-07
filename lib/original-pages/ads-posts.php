<?php //広告設定をデータベースに保存

//広告表示設定
$opt_val = isset($_POST[AD_ALL_ADS_VISIBLE_NAME]) ? $_POST[AD_ALL_ADS_VISIBLE_NAME] : null;
update_option(AD_ALL_ADS_VISIBLE_NAME, $opt_val);

//広告コード
$opt_val = $_POST[AD_AD_CODE_NAME];
update_option(AD_AD_CODE_NAME, $opt_val);