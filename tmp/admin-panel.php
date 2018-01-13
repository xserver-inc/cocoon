<?php //投稿・固定ページでのみ管理者パネルを表示する
if (is_admin_panel_visible() && is_singular() && is_user_administrator() && !is_amp()):
?>
<div id="admin-panel" class="admin-panel">

  <?php //PVエリアの表示
  if (is_admin_panel_pv_area_visible()): ?>
    <div class="admin-pv">
      <span class="admin-pv-by">
        <?php if (get_admin_panel_pv_type() == THEME_NAME): ?>
          by <?php echo THEME_NAME_CAMEL; ?>
        <?php else: ?>
          by Jetpack
        <?php endif ?>
      </span>
      <span class="fa fa-bar-chart fa-fw"></span>
      <span class="today-pv">
        <span class="today-pv-label"><?php _e( '本日', THEME_NAME ) ?></span>
        <span class="today-pv-count"><?php echo get_todays_pv(); ?></span>
      </span>
      <span class="week-pv">
        <span class="week-pv-label"><?php _e( '週', THEME_NAME ) ?></span>
        <span class="week-pv-count"><?php echo get_last_7days_pv(); ?></span>
      </span>
      <span class="month-pv">
        <span class="month-pv-label"><?php _e( '月', THEME_NAME ) ?></span>
        <span class="month-pv-count"><?php echo get_last_30days_pv(); ?></span>
      </span>
      <span class="all-pv">
        <span class="all-pv-label"><?php _e( '全体', THEME_NAME ) ?></span>
        <span class="all-pv-count"><?php echo get_all_pv(); ?></span>
      </span>
      <?php //Jetpackチャート表示
      if ((get_admin_panel_pv_type() == 'jetpack') &&
          //Jetpackが有効の場合
          is_jetpack_stats_module_active()) {
        echo '<span class="jetpack-page"><a href="'.admin_url().'admin.php?page=stats&view=post&post='.get_the_ID().'"title="'.__( 'Jetpackの統計', 'simplicity2' ).'" target="_blank"><span class="fa fa-line-chart"></span></a></span>';
      } ?>
    </div>
  <?php endif ?>

  <?php //編集エリアの表示
  if (is_admin_panel_edit_area_visible()): ?>
    <div class="admin-edit">
      <span class="fa fa-edit fa-fw"></span>
      <?php //投稿編集リンクの表示
      if (is_admin_panel_wp_edit_visible()): ?>
        <span class="post-edit"><?php edit_post_link(__( '編集', THEME_NAME )); ?></span>
      <?php endif ?>
      <?php //Windows Live Writer編集リンクの表示
      if (is_admin_panel_wlw_edit_visible()): ?>
        <span class="post-wlw-edit"><?php wlw_edit_post_link(__( 'WLWで編集', THEME_NAME )); ?></span>
      <?php endif ?>
    </div>
  <?php endif ?>

  <?php //AMPエリアの表示
  if (is_admin_panel_amp_area_visible()): ?>
    <div class="admin-amp">
      <span class="icon-amp-logo2"></span>
      <a href="<?php echo get_amp_permalink(); ?> "><?php _e( 'AMPページへ', THEME_NAME ) ?></a>
      <?php
        $encoded_url = str_replace('&amp;', '&', get_amp_permalink());
        $encoded_url = urlencode($encoded_url);
      ?>
      <a href="https://search.google.com/test/amp?url=<?php echo $encoded_url; ?> " target="_blank"><?php _e( 'Google AMPテスト', THEME_NAME ) ?></a>
      <a href="https://validator.ampproject.org/#url=<?php echo $encoded_url; ?> " target="_blank"><?php _e( 'The AMP Validator', THEME_NAME ) ?></a>
      <a href="https://ampbench.appspot.com/validate?url=<?php echo $encoded_url; ?> " target="_blank"><?php _e( ' AMPBench', THEME_NAME ) ?></a>
    </div>
  <?php endif ?>

  <?php if (is_admin_panel_check_tools_area_visible()): ?>
    <div class="admin-checks">
      <span class="fa fa-check"></span>
      <?php
        $encoded_url = str_replace('&amp;', '&', get_permalink());
        $encoded_url = urlencode($encoded_url);
      ?>
      <a href="https://developers.google.com/speed/pagespeed/insights/?filter_third_party_resources=true&hl=<?php _e( 'ja', THEME_NAME ) ?>&url=<?php echo $encoded_url; ?> " target="_blank" class="pagespeed"><?php _e( 'ページスピード', THEME_NAME ) ?></a>
      <a href="https://search.google.com/structured-data/testing-tool#url=<?php echo $encoded_url; ?> " target="_blank" class="structured-data"><?php _e( '構造化', THEME_NAME ) ?></a>
      <a href="https://validator.w3.org/nu/?doc=<?php echo $encoded_url; ?> " target="_blank" class="validator-w3"><?php _e( 'HTML5', THEME_NAME ) ?></a>
      <a href="https://gsnedders.html5.org/outliner/process.py?url=<?php echo $encoded_url; ?> " target="_blank" class="outliner"><?php _e( 'アウトライン', THEME_NAME ) ?></a>
      <a href="https://twitter.com/search?f=tweets&q=<?php echo $encoded_url; ?> " target="_blank" class="tweets"><?php _e( 'ツイート検索', THEME_NAME ) ?></a>
<!--
      <a href="<?php echo $encoded_url; ?> " target="_blank" class=""><?php _e( '', THEME_NAME ) ?></a>
 -->
  <?php endif ?>

</div>

</div>
<?php endif //is_singular ?>