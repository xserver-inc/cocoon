<?php //Facebook OGPタグ
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit; ?>

<!-- OGP -->
<meta property="og:type" content="<?php echo (is_singular() ? 'article' : 'website'); ?>">
<?php
$description = get_meta_description_text();
if (is_singular()){//単一記事ページの場合
  //if(have_posts()): while(have_posts()): the_post();
    echo '<meta property="og:description" content="'.esc_attr($description).'">';echo "\n";//抜粋を表示
  //endwhile; endif;
  $title = get_the_title();
  if ( is_front_page() ) {
    $title = get_bloginfo('name');
  }
  echo '<meta property="og:title" content="'; echo esc_attr($title); echo '">';echo "\n";//単一記事タイトルを表示
  echo '<meta property="og:url" content="'; echo esc_url(get_the_permalink()); echo '">';echo "\n";//単一記事URLを表示
} else {//単一記事ページページ以外の場合（アーカイブページやホームなど）
  if (is_front_page()) {
    $url = home_url();
    $title = get_bloginfo('name');
  } else {
    $url = generate_canonical_url();
    $title = wp_get_document_title();
  }
  $description = get_bloginfo('description');

  if ( is_category() ) {//カテゴリ用設定
    $description = get_category_meta_description();
    if ($category_title =  get_category_title(get_query_var('cat'))) {
      $title = $category_title;
    } else {
      $title = wp_title(null, false).' | '.get_bloginfo('name');
    }
    $url = generate_canonical_url();
  }

  if ( is_tag() ) {//タグ用設定
    $description = get_tag_meta_description();
    if ($tag_title =  get_tag_title(get_query_var('tag_id'))) {
      $title = $tag_title;
    } else {
      $title = wp_title(null, false).' | '.get_bloginfo('name');
    }
    $url = generate_canonical_url();
  }

  echo '<meta property="og:description" content="'; echo esc_attr($description); echo '">';echo "\n";//「一般設定」管理画面で指定したブログの説明文を表示
  echo '<meta property="og:title" content="'; echo esc_attr($title); echo '">';echo "\n";//「一般設定」管理画面で指定したブログのタイトルを表示
  echo '<meta property="og:url" content="'; echo esc_url($url); echo '">';echo "\n";//「一般設定」管理画面で指定したブログのURLを表示取る
}
if (is_singular()){//単一記事ページの場合
  if ($ogp_image = get_singular_sns_share_image_url()) {
    echo '<meta property="og:image" content="'.esc_url($ogp_image).'">';echo "\n";
  }
} else {//単一記事ページページ以外の場合（アーカイブページやホームなど）
  if (is_category() && !is_paged() && $eye_catch = get_category_eye_catch_url(get_query_var('cat'))) {
    $ogp_image = $eye_catch;
  } elseif ( get_ogp_home_image_url() ) {
    $ogp_image = get_ogp_home_image_url();
  } else {
    if ( get_the_site_logo_url() ){//ヘッダーロゴがある場合はロゴを使用
      $ogp_image = get_the_site_logo_url();
    }
  }
  if ( !empty($ogp_image) ) {//使えそうな$ogp_imageがある場合
    echo '<meta property="og:image" content="'.esc_url($ogp_image).'">';echo "\n";
  }
}
?>
<meta property="og:site_name" content="<?php echo esc_attr(get_bloginfo('name')); ?>">
<meta property="og:locale" content="ja_JP">
<?php if ( false ): //fb:adminsの取得?>
<meta property="fb:admins" content="<?php echo esc_attr(get_fb_admins()); ?>">
<?php endif; ?>
<?php if ( get_facebook_app_id() ): //fb:app_idの取得?>
<meta property="fb:app_id" content="<?php echo esc_attr(get_facebook_app_id()); ?>">
<?php endif; ?>
<meta property="article:published_time" content="<?php echo esc_attr(get_seo_post_time()); ?>" />
<?php if ($update_time = get_seo_update_time()): ?>
<meta property="article:modified_time" content="<?php echo esc_attr($update_time); ?>" />
<?php endif ?>
<?php //カテゴリー
$cats = get_the_category();
if ($cats) {
  foreach($cats as $cat) {
    echo '<meta property="article:section" content="' . esc_attr($cat->name) . '">'.PHP_EOL;
  }
} ?>
<?php //タグ
$tags = get_the_tags();
if ($tags) {
  foreach($tags as $tag) {
    echo '<meta property="article:tag" content="' . esc_attr($tag->name) . '">'.PHP_EOL;
  }
} ?>
<!-- /OGP -->
