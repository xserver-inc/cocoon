<?php //アピールエリア
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit; ?>

<?php //アピールエリアを表示するか
if (is_appeal_area_visible() && !is_amp() && apply_filters('appeal_area_visible', true)): ?>
<div id="appeal" class="appeal<?php echo get_additional_appeal_area_classes(); ?>">
  <div id="appeal-in" class="appeal-in wrap">

    <?php //テキストメッセージエリアを表示するか
    if (is_appeal_area_content_visible()): ?>
    <div class="appeal-content">
      <?php //タイトルが存在するか
      if (get_appeal_area_title()): ?>
      <div class="appeal-title">
        <?php echo get_appeal_area_title(); ?>
      </div>
      <?php endif ?>
      <?php //メッセージが存在するか
      if ($message = get_appeal_area_message()):
        $message = apply_filters('appeal_area_message', $message);
       ?>
      <div class="appeal-message">
        <?php echo $message; ?>
      </div>
      <?php endif ?>
      <?php if (get_appeal_area_button_message() && get_appeal_area_button_url()): ?>
      <a href="<?php echo get_appeal_area_button_url(); ?>" class="appeal-button" target="<?php echo get_appeal_area_button_target(); ?>">
        <?php echo get_appeal_area_button_message(); ?>
      </a>
      <?php endif ?>
    </div>
    <?php endif; ?>

  </div>
</div>
<?php endif ?>
