<?php //投稿・固定ページでのみ管理者パネルを表示する
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

if (is_user_administrator()
  && is_admin_panel_visible()
  && !is_amp()
  && !isset($_GET['demo'])
  && (is_admin_panel_singular_page_visible() || is_admin_panel_not_singular_page_visible())
):
?>
<div id="admin-panel" class="admin-panel<?php echo get_additional_admin_panel_area_classes(); ?>">

  <?php //PVエリアの表示
  if (is_admin_panel_pv_area_visible() && is_singular()): ?>
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
        echo '<span class="jetpack-page"><a href="'.admin_url().'admin.php?page=stats&view=post&post='.get_the_ID().'"title="'.__( 'Jetpackの統計', THEME_NAME ).'" target="_blank"><span class="fa fa-line-chart"></span></a></span>';
      } ?>
    </div>
  <?php endif ?>

  <?php //編集エリアの表示
  if (is_admin_panel_edit_area_visible() && is_singular()): ?>
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
  if (is_admin_panel_amp_area_visible() && is_singular() && has_amp_page()): ?>
    <div class="admin-amp">
      <span class="icon-amp-logo2"></span>
      <a href="<?php echo get_amp_permalink(); ?> "><?php _e( 'AMPページへ', THEME_NAME ) ?></a>
      <?php
        $encoded_url = get_encoded_url(get_amp_permalink());
      ?>
      <?php if (is_admin_google_amp_test_visible()): ?>
        <a href="https://search.google.com/test/amp?url=<?php echo $encoded_url; ?> " target="_blank"><?php _e( 'Google AMPテスト', THEME_NAME ) ?></a>
      <?php endif ?>
      <?php if (is_admin_the_amp_validator_visible()): ?>
        <a href="https://validator.ampproject.org/#url=<?php echo $encoded_url; ?> " target="_blank"><?php _e( 'The AMP Validator', THEME_NAME ) ?></a>
      <?php endif ?>
      <?php if (is_admin_ampbench_visible()): ?>
        <a href="https://ampbench.appspot.com/validate?url=<?php echo $encoded_url; ?> " target="_blank"><?php _e( 'AMPBench', THEME_NAME ) ?></a>
      <?php endif ?>
    </div>
  <?php endif ?>

  <?php if (is_admin_panel_check_tools_area_visible()): ?>
    <div class="admin-checks">
      <span class="fa fa-check"></span>
      <?php
        $encoded_url = get_encoded_url(get_requested_url());
      ?>
      <?php if (is_admin_pagespeed_insights_visible()): ?>
        <a href="https://developers.google.com/speed/pagespeed/insights/?filter_third_party_resources=true&hl=<?php _e( 'ja', THEME_NAME ) ?>&url=<?php echo $encoded_url; ?> " target="_blank" class="pagespeed"><?php _e( 'ページスピード', THEME_NAME ) ?></a>
      <?php endif ?>
      <?php if (is_admin_gtmetrix_visible()): ?>
        <a href="https://gtmetrix.com/?url=<?php echo $encoded_url; ?> " target="_blank" class="gtmetrix"><?php _e( 'GTmetrix', THEME_NAME ) ?></a>
      <?php endif ?>
      <?php if (is_admin_mobile_friendly_test_visible()): ?>
        <a href="https://search.google.com/test/mobile-friendly?url=<?php echo $encoded_url; ?> " target="_blank"><?php _e( 'モバイルフレンドリー', THEME_NAME ) ?></a>
      <?php endif ?>
      <?php if (is_admin_structured_data_visible()): ?>
        <a href="https://search.google.com/structured-data/testing-tool/?hl=<?php _e( 'ja', THEME_NAME ) ?>&url=<?php echo $encoded_url; ?> " target="_blank"><?php _e( '構造化データー', THEME_NAME ) ?></a>
      <?php endif ?>
      <?php if (is_admin_nu_html_checker_visible()): ?>
        <a href="https://validator.w3.org/nu/?showsource=yes&showoutline=yes&showimagereport=yes&doc=<?php echo $encoded_url; ?> " target="_blank" class="validator-w3"><?php _e( 'HTML5', THEME_NAME ) ?></a>
      <?php endif ?>
      <?php if (is_admin_html5_outliner_visible()): ?>
        <a href="https://gsnedders.html5.org/outliner/process.py?url=<?php echo $encoded_url; ?> " target="_blank" class="outliner"><?php _e( 'アウトライン', THEME_NAME ) ?></a>
      <?php endif ?>
      <?php if (is_admin_seocheki_visible()): ?>
        <a href="http://seocheki.net/site-check.php?u=<?php echo $encoded_url; ?> " target="_blank" class="seocheki"><?php _e( 'SEOチェキ', THEME_NAME ) ?></a>
      <?php endif ?>
      <?php if (is_admin_tweet_check_visible()): ?>
        <a href="https://twitter.com/search?f=tweets&q=<?php echo $encoded_url; ?> " target="_blank" class="tweets"><?php _e( 'ツイート検索', THEME_NAME ) ?></a>
      <?php endif ?>
    </div>
  <?php endif ?>

  <?php if (is_admin_panel_responsive_tools_area_visible()): ?>
    <div class="admin-cresponsive">
      <span class="fa fa-tablet"></span>
      <?php
        $encoded_url = get_encoded_url(get_requested_url());
      ?>
      <?php if (is_admin_responsinator_visible()): ?>
        <a href="https://www.responsinator.com/?url=<?php echo $encoded_url; ?> " target="_blank"><?php _e( 'レスポンシブテスト', THEME_NAME ) ?></a>
      <?php endif ?>
      <?php if (is_admin_sizzy_visible()): ?>
        <a href="https://sizzy.co/?url=<?php echo $encoded_url; ?> " target="_blank"><?php _e( 'Sizzy', THEME_NAME ) ?></a>
      <?php endif ?>
      <?php
      if (is_admin_multi_screen_resolution_test_visible()): ?>
         <a href="http://whatismyscreenresolution.net/multi-screen-test?site-url=<?php echo $encoded_url; ?>&w=414&h=736" target="_blank"><?php _e( 'Resolution Test', THEME_NAME ) ?></a>
      <?php endif ?>

    </div>
  <?php endif ?>

</div>
<?php endif //is_singular ?>
