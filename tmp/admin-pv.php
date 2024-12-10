<?php //PV表示
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

//PVエリアの表示
?>
  <div class="admin-pv">
    <span class="admin-pv-by">
      <?php if (get_admin_panel_pv_type() == THEME_NAME): ?>
        by <?php echo THEME_NAME_CAMEL; ?>
      <?php else: ?>
        by Jetpack
      <?php endif ?>
    </span>
    <span class="fa fa-bar-chart fa-fw" aria-hidden="true"></span>
    <span class="today-pv">
      <span class="today-pv-label"><?php _e( '本日:', THEME_NAME ) ?></span>
      <span class="today-pv-count"><?php echo get_todays_pv(); ?></span>
    </span>
    <span class="week-pv">
      <span class="week-pv-label"><?php _e( '週:', THEME_NAME ) ?></span>
      <span class="week-pv-count"><?php echo get_last_7days_pv(); ?></span>
    </span>
    <span class="month-pv">
      <span class="month-pv-label"><?php _e( '月:', THEME_NAME ) ?></span>
      <span class="month-pv-count"><?php echo get_last_30days_pv(); ?></span>
    </span>
    <span class="all-pv">
      <span class="all-pv-label"><?php _e( '全体:', THEME_NAME ) ?></span>
      <span class="all-pv-count"><?php echo get_all_pv(); ?></span>
    </span>
    <?php //Jetpackチャート表示
    if ((get_admin_panel_pv_type() == 'jetpack')
        //Jetpackが有効の場合
        && is_jetpack_stats_module_active()
        //投稿・固定ページの場合
        && is_singular()) {
      echo '<span class="jetpack-page"><a href="'.admin_url().'admin.php?page=stats&view=post&post='.get_the_ID().'"title="'.__( 'Jetpackの統計', THEME_NAME ).'" target="_blank" rel="noopener noreferrer"><span class="fa fa-line-chart" aria-hidden="true"></span></a></span>';
    } ?>
  </div>
