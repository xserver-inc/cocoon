<?php //アピールエリアテンプレート ?>
<?php //アピールエリアを表示するか
if (is_appeal_area_visible()): ?>
<div id="appeal" class="appeal<?php echo get_additional_appeal_area_classes(); ?>">
  <div id="appeal-in" class="appeal-in wrap">
    <div class="appeal-content">
      <?php //メッセージが存在するか
      if (get_appeal_area_message()): ?>
      <div class="appeal-message">
        <?php echo get_appeal_area_message(); ?>
      </div>
      <?php endif ?>
      <?php if (get_appeal_area_button_message() && get_appeal_area_button_url()): ?>
      <a href="<?php echo get_appeal_area_button_url(); ?>" class="appeal-button">
        <?php echo get_appeal_area_button_message(); ?>
      </a>
      <?php endif ?>
    </div>
  </div>
</div>
<?php endif ?>