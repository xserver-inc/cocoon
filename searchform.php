<?php if (!is_amp() || !is_ssl()): ?>
<form class="search-box" method="get" action="<?php echo home_url('/'); ?>">
<?php else: ?>
<form class="amp-form search-box" method="get" action="<?php echo home_url('/'); ?>" target="_top">
<?php endif ?>
  <input type="text" placeholder="<?php _e( 'サイト内を検索', THEME_NAME ) ?>" name="s" class="search-edit">
  <button type="submit" class="search-submit"></button>
</form>