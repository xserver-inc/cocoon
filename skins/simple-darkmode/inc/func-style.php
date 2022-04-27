<?php
/**
 * 動的スタイルシート
 */
add_action('get_template_part_tmp/css-custom', function() {
  $color = get_site_key_color() ?: '#757575';
  $text_color = get_site_key_text_color() ?: '#ffffff';

  echo '
  .slick-dots li.slick-active button:before,
  .slick-dots li button:before,
  .shortcut{
    color: '.$color.';
  }
  .header{
    background-color: '.$color.';
    color: '.$text_color.';
  }
  .site-name-text{
    color: '.$text_color.';
  }
  .eye-catch .cat-label{
    color: '.$text_color.';
    background-color: '.$color.';
    left: auto;
    top: .5em;
    bottom: auto;
    right: .5em;
    padding: 4px 9px 1.9px;
    opacity: .7;
    border: 1.3px solid '.$text_color.';
    border-radius: 7px;
  }
  .page-numbers,
  .tagcloud a,
  .author-box,
  .ranking-item,
  .pagination-next-link,
  .comment-reply-link,
  .toc {
    border: 2px solid '.$color.';
    border-radius: 6px;
  }
  .pagination .current,
  .search-submit {
    background: '.$color.';
  }
  .search-submit{
    border: solid 2px '.$color.';
  }
  .blogcard-label::after,
  .fas .fa-folder,
  .search-submit{
    color: '.$text_color.';
  }
  .blogcard-label, .cat-link, .cat-label,
  .tag-link, .comment-reply-link,
  .mobile-menu-buttons,
  .mobile-menu-buttons .menu-button > a,
  .navi-menu-content,
  .navi-menu-content a,
  .go-to-top-button,
  #submit{
    background-color: '.$color.';
    color: '.$text_color.';
  }
  .box-menu{
    box-shadow: inset 1px 1px 0 0 '.$color.', 1px 1px 0 0 '.$color.', 1px 0 0 0 '.$color.';
  }
  .search-form>div {
    border: 2px solid '.$color.';
  }
  .search-form div.sbtn {
    background-color: '.$color.';
    color: '.$text_color.';
    text-align: center;
    width: 140px;
    position: relative;
  }
  .search-form div.sbtn:hover{
    border: 2px solid '.$color.';
  }
  .article h2 {
    border-bottom: 3px solid '.$color.';
  }
  .article h3,
  .sidebar h2,
  .sidebar h3,
  .under-entry-content h2{
    padding: 1rem 2rem;
    border-left: 5px solid '.$color.';
    border-radius: 4px 0px 0px 4px;
  }
  .article h4 {
    padding: 1rem 2rem;
    border-left: 4px dashed '.$color.';
  }
  .article h5 {
    padding: 1rem 2rem;
    border-left: 4px dotted '.$color.';
  }
  ';

  global $_THEME_OPTIONS;
  $_THEME_OPTIONS['site_key_color'] = $_THEME_OPTIONS['site_key_text_color'] = '';
});

add_action('cocoon_settings_after_save', function() {
  global $_THEME_OPTIONS;
  unset($_THEME_OPTIONS['site_key_color'], $_THEME_OPTIONS['site_key_text_color']);
});

add_filter('get_editor_key_color', function($color) {
  return get_theme_mod('site_key_color') ?: '#757575';
});
