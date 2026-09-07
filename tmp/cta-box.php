<?php //CTA
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit; ?>

<?php
$cta_heading = isset($_HEADING) && is_scalar($_HEADING) ? (string)$_HEADING : '';
$cta_layout = isset($_LAYOUT) && is_scalar($_LAYOUT) ? trim((string)$_LAYOUT) : '';
$cta_image_url = isset($_IMAGE_URL) && is_scalar($_IMAGE_URL) ? (string)$_IMAGE_URL : '';
$cta_message = isset($_MESSAGE) && is_scalar($_MESSAGE) ? (string)$_MESSAGE : '';
$cta_button_text = isset($_BUTTON_TEXT) && is_scalar($_BUTTON_TEXT) ? (string)$_BUTTON_TEXT : '';
$cta_button_url = isset($_BUTTON_URL) && is_scalar($_BUTTON_URL) ? (string)$_BUTTON_URL : '';
$cta_button_color_class = isset($_BUTTON_COLOR_CLASS) && is_scalar($_BUTTON_COLOR_CLASS) ? trim((string)$_BUTTON_COLOR_CLASS) : '';

// テンプレート単体利用時にも不正なクラスを出力しないための再検証
if ( !in_array($cta_layout, get_cta_allowed_layout_classes(), true) ) {
  $cta_layout = '';
}
if ( !in_array($cta_button_color_class, get_cta_allowed_button_color_classes(), true) ) {
  $cta_button_color_class = '';
}
?>

<div class="cta-box <?php echo esc_attr($cta_layout); ?>">
  <?php if ($cta_heading): ?>
    <div class="cta-heading">
      <?php echo esc_html($cta_heading); ?>
    </div>
  <?php endif ?>

  <div class="cta-content">
    <?php if ($cta_image_url): ?>
      <div class="cta-thumb">
        <img class="cta-image" src="<?php echo esc_url($cta_image_url); ?>" alt="" />
      </div>
    <?php endif ?>
    <?php if ($cta_message): ?>
      <div class="cta-message">
        <?php echo wp_kses_post($cta_message); ?>
      </div>
    <?php endif ?>
  </div>
  <?php //URLが入力されているとき
  if ($cta_button_url): ?>
    <div class="cta-button">
      <a href="<?php echo esc_url($cta_button_url); ?>" class="btn <?php echo esc_attr($cta_button_color_class); ?> btn-l"><?php echo esc_html($cta_button_text); ?></a>
    </div>
  <?php endif ?>

</div>
