<?php //フォントプレビュー
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */

$demo_style = null;
if ($site_text_color = get_site_text_color()) {
  $demo_style = 'color: '.$site_text_color.';';
}
?>
<?php if (!is_site_font_family_local()): ?>
  <link rel="stylesheet" href="<?php echo get_site_font_source_url(); ?>">
<?php endif ?>
<p class="preview-label"><?php _e( 'フォントプレビュー', THEME_NAME ) ?></p>
<div class="demo" style="width: 100%">
  <div class="entry-content <?php echo get_site_font_family_class(); ?> <?php echo get_site_font_size_class(); ?> <?php echo get_site_font_weight_class(); ?>" style="<?php echo $demo_style; ?>">
  <p>1234567890</p>
  <p>abcdefghijklmnopqrstuvwxyz</p>
  <p>ABCDEFGHIJKLMNOPQRSTUVWXYZ</p>
  <p><?php _e( '吾輩は猫である。名前はまだ無い。どこで生れたかとんと見当がつかぬ。何でも薄暗いじめじめした所でニャーニャー泣いていた事だけは記憶している。吾輩はここで初めて人間というものを見た。', THEME_NAME ) ?></p>
  </div>
</div>
