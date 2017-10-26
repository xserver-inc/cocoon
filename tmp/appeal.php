<?php //アピールエリアテンプレート ?>
<?php //アピールエリアを表示するか
if (is_appeal_area_visible()): ?>
<div id="appeal" class="appeal<?php echo get_additional_appeal_area_classes(); ?>">
  <div id="appeal-in" class="appeal-in wrap">
    <div class="appeal-content">
      <div class="appeal-message">

      </div>
      <div class="appeal-button">

      </div>
    </div>
  </div>
</div>
<?php endif ?>