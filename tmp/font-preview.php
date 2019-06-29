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
<div class="demo" style="width: 100%;overflow: visible;">
  <div class="entry-content <?php echo get_site_font_family_class(); ?> <?php echo get_site_font_size_class(); ?> <?php echo get_site_font_weight_class(); ?>" style="<?php echo $demo_style; ?>">
  <p>1234567890</p>
  <p>abcdefghijklmnopqrstuvwxyz</p>
  <p>ABCDEFGHIJKLMNOPQRSTUVWXYZ</p>
  <p><?php _e( '吾輩は猫である。名前はまだ無い。どこで生れたかとんと見当がつかぬ。何でも薄暗いじめじめした所でニャーニャー泣いていた事だけは記憶している。吾輩はここで初めて人間というものを見た。', THEME_NAME ) ?></p>
  <p><?php
    echo '<b>';
    _e('現在の太さ：', THEME_NAME);
    echo '</b>';
    echo get_site_font_weight();
    $content = '<img src="https://im-cocoon.net/wp-content/uploads/site-font-weight.gif" alt=""><br>'.__( '「文字の太さ」変更でフォントに合った太さに調整しよう。', THEME_NAME );
    generate_tooltip_tag($content);
    echo '<br><b>';
    _e('太さ見本：', THEME_NAME);
    echo '</b>';
    echo get_font_weight_demo_tag(array(100, 200, 300, 400, 500, 600, 700, 800, 900));
  ?>
  <?php if (!is_site_font_family_local()): ?>
  <?php
    echo '<br><b>';
    _e('利用可：', THEME_NAME);
    echo '</b>';
    if ($weight_str = get_site_font_source_weight()) {
      $weight_str = str_replace(':', '', $weight_str);
      $weights = explode(',', $weight_str);
    } else {
      $weights = array(400);
    }
    // $taged_weights = array();
    // foreach ($weights as $weight) {
    //   $taged_weights[] = '<span style="font-weight:'.$weight.';">'.$weight.'</span>';
    // }
    // echo implode(', ', $taged_weights);
    echo get_font_weight_demo_tag($weights);
    _e('（WEBフォント）', THEME_NAME);
  ?>
  <?php endif; ?>
  </p>
  </div>
</div>
