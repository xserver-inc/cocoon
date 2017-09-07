<?php //広告設定をデータベースに保存

//広告を全て表示するか
define('AD_ALL_ADS_VISIBLE_NAME', 'all_ads_visible');
function is_all_ads_visible(){
  return get_option(AD_ALL_ADS_VISIBLE_NAME);
}

//広告コード
define('AD_AD_CODE_NAME', 'ad_code');
function get_ad_code(){
  return stripslashes_deep(get_option(AD_AD_CODE_NAME));
}