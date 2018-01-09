<?php if (!is_amp()): ?>
<form method="get" class="search-box" action="<?php echo home_url('/'); ?>">
  <input type="text" placeholder="<?php _e( 'ブログ内を検索', THEME_NAME ) ?>" name="s" class="search-edit">
  <button type="submit" class="search-submit"></button>
</form>
<?php endif ?>