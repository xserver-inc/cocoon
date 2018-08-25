<?php


//Amazon必須バッジ
if ( !function_exists( 'generate_amazon_badge_tag' ) ):
  function generate_amazon_badge_tag($caption){?>
  <span class="amazon-badge api-badge"><?php echo $caption; ?></span><?php
  }
  endif;
  
  //楽天必須バッジ
  if ( !function_exists( 'generate_rakuten_badge_tag' ) ):
  function generate_rakuten_badge_tag($caption){?>
  <span class="rakuten-badge api-badge"><?php echo $caption; ?></span><?php
  }
  endif;
  
  //もしくはツリー必須バッジ
  if ( !function_exists( 'generate_moshimo_badge_tag' ) ):
  function generate_moshimo_badge_tag($caption){?>
  <span class="moshimo-badge api-badge"><?php echo $caption; ?></span><?php
  }
  endif;