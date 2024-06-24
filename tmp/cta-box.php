<?php //CTAボックス
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit; ?>

<div class="cta-box <?php echo $_LAYOUT; ?>">
  <?php if ($_HEADING): ?>
    <div class="cta-heading">
      <?php echo $_HEADING; ?>
    </div>
  <?php endif ?>

  <div class="cta-content">
    <?php if ($_IMAGE_URL): ?>
      <div class="cta-thumb">
        <img class="cta-image" src="<?php echo $_IMAGE_URL; ?>" alt="" />
      </div>
    <?php endif ?>
    <?php if ($_MESSAGE): ?>
      <div class="cta-message">
        <?php echo $_MESSAGE; ?>
      </div>
    <?php endif ?>
  </div>
  <?php //URLが入力されているとき
  if ($_BUTTON_URL): ?>
    <div class="cta-button">
      <a href="<?php echo $_BUTTON_URL; ?>" class="btn <?php echo $_BUTTON_COLOR_CLASS; ?> btn-l"><?php echo $_BUTTON_TEXT; ?></a>
    </div>
  <?php endif ?>

</div>
