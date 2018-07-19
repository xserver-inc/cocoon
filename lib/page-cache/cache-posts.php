<?php //リセットの実行
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */

//管理者権限を持っているログインユーザーかどうか
if (is_user_administrator()) {
  $delete_option = isset($_GET['cache']) ? $_GET['cache'] : null;
  switch ($delete_option) {
    case 'all_theme_caches':
      delete_all_theme_caches();
      break;
    case 'sns_count_caches':
      delete_sns_count_caches();
      break;
    case 'popular_entries_caches':
      delete_popular_entries_caches();
      break;
    case 'blogcard_caches':
      delete_blogcard_caches();
      break;
    case 'amazon_api_caches':
      delete_amazon_api_caches();
      break;
  }
}