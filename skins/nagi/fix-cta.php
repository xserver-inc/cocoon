<?php 
$post_id = get_the_ID();
$fix_link = get_post_meta($post_id, 'fix_link', true);
$fix_microcopy = get_post_meta($post_id,'fix_microcopy',true);
$cta_color = get_post_meta($post->ID,'cta_color',true);
$cta_layout = get_post_meta($post->ID,'cta_layout',true);
// 'fix_link'が入力されているかチェック
if (!empty($fix_link)) :?>

  <input id="fixed_close" type="checkbox" name="my_checkbox">
<div class="fixed_contents <?php echo $cta_color," ", $cta_layout;?>">


  <div class="footer_fixed">
  <label class="button" for="fixed_close">
<span class="close">
<span class="fa-solid fa-xmark"></span></span>  </label>

<span class="footer_micro">
	
<?php echo $fix_microcopy ;?>
</span>
<span class="footer_button">
 <?php echo do_shortcode($fix_link);?>
</span>
  </div>
</div>
<?php endif; ?>