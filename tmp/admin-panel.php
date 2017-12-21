<?php //投稿・固定ページでのみ管理者パネルを表示する
if (is_admin_panel_visible() && is_singular() && is_user_administrator()):
?>
<div id="admin-panel" class="admin-panel">

  <?php //PVエリアの表示
  if (is_admin_panel_pv_area_visible()): ?>
    <div class="admin-pv">
      <span class="fa fa-signal fa-fw"></span>
      <span class="today-pv">
        <span class="today-pv-label"><?php _e( '本日', THEME_NAME ) ?></span>
        <span class="today-pv-count"><?php echo get_todays_access_count(); ?></span>
      </span>
      <span class="week-pv">
        <span class="week-pv-label"><?php _e( '週', THEME_NAME ) ?></span>
        <span class="week-pv-count"><?php echo get_last_7days_access_count(); ?></span>
      </span>
      <span class="month-pv">
        <span class="month-pv-label"><?php _e( '月', THEME_NAME ) ?></span>
        <span class="month-pv-count"><?php echo get_last_30days_access_count(); ?></span>
      </span>
      <span class="all-pv">
        <span class="all-pv-label"><?php _e( '全体', THEME_NAME ) ?></span>
        <span class="all-pv-count"><?php echo get_all_access_count(); ?></span>
      </span>
    </div>
  <?php endif ?>

  <?php //編集エリアの表示
  if (is_admin_panel_edit_area_visible()): ?>
    <div class="admin-edit">
      <span class="fa fa-edit fa-fw"></span>
      <span class="post-edit"><?php edit_post_link(__( '編集', THEME_NAME )); ?></span>
      <span class="post-wlw-edit"><?php wlw_edit_post_link(__( 'WLWで編集', THEME_NAME )); ?></span>
    </div>
  <?php endif ?>


  <div class="admin-amp">
    <span class="fa fa-bolt fa-fw"></span>
    AMP
  </div>
</div>
<?php endif //is_singular ?>