<?php //投稿・固定ページでのみ管理者パネルを表示する
if (is_singular() && is_user_logged_in()):
  //アクセス数のカウント
  if (is_access_count_enable()) {
    count_this_page_access();

    $time_start = microtime(true);

    get_access_ranking_records();

    $time = microtime(true) - $time_start;
    _v($time);
    // var_dump(get_todays_access_count());
    // var_dump(get_last_7days_access_count());
    // var_dump(get_last_30days_access_count());
    // var_dump(get_all_access_count());
  }
?>
<div class="admin-panel">
  <div class="admin-pv">
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
</div>
<?php endif //is_singular ?>