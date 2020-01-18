<?php //Twitterカードタグ
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit; ?>

<!-- Twitter Card -->
<meta name="twitter:card" content="<?php echo esc_attr(get_twitter_card_type());//Twitterのカードタイプを取得 ?>">
<?php
$description = get_meta_description_text();
if (is_singular()){//単一記事ページの場合
  $title = get_the_title();
  if ( is_front_page() ) {
    $title = get_bloginfo('name');
  }
  $url = get_the_permalink();
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
    if ($category_title =  get_the_category_title(get_query_var('cat'))) {
      $title = $category_title;
    } else {
      $title = wp_title(null, false).' | '.get_bloginfo('name');
    }
    $url = generate_canonical_url();
  }

  if ( is_tag() ) {//タグ用設定
    $description = get_tag_meta_description();
    if ($tag_title =  get_the_tag_title(get_query_var('tag_id'))) {
      $title = $tag_title;
    } else {
      $title = wp_title(null, false).' | '.get_bloginfo('name');
    }
    $url = generate_canonical_url();
  }
}

$title = apply_filters('sns_card_title', $title);
$title = apply_filters('ogp_card_title', $title);
echo '<meta property="twitter:description" content="'; echo esc_attr($description); echo '">';echo "\n";//ブログの説明文を表示
echo '<meta property="twitter:title" content="'; echo esc_attr($title); echo '">';echo "\n";//ブログのタイトルを表示
echo '<meta property="twitter:url" content="'; echo esc_url($url); echo '">';echo "\n";//ブログのURLを表示取る

if (is_singular()){//単一記事ページの場合
  if ($ogp_image = get_singular_sns_share_image_url()) {
    echo '<meta name="twitter:image" content="'.esc_url($ogp_image).'">';echo "\n";
  }
} else {//単一記事ページページ以外の場合（アーカイブページやホームなど）
  if (is_category() && !is_paged() && $eye_catch = get_the_category_eye_catch_url(get_query_var('cat'))) {
    $ogp_image = $eye_catch;
  } elseif (is_tag() && !is_paged() && $eye_catch = get_the_tag_eye_catch_url(get_query_var('tag_id'))) {
    $ogp_image = $eye_catch;
  } elseif ( get_ogp_home_image_url() ) {
    $ogp_image = get_ogp_home_image_url();
  } else {
    if ( get_the_site_logo_url() ){//ヘッダーロゴがある場合はロゴを使用
      $ogp_image = get_the_site_logo_url();
    }
  }
  if ( !empty($ogp_image) ) {//使えそうな$ogp_imageがある場合
    echo '<meta name="twitter:image" content="'.esc_url($ogp_image).'">';echo "\n";
  }
}
?>
<meta name="twitter:domain" content="<?php echo esc_attr(get_the_site_domain()) ?>">
<?php if ( get_the_author_twitter_url() )://TwitterIDが設定されている場合 ?>
<meta name="twitter:creator" content="@<?php echo esc_attr( get_the_author_twitter_id() ) ?>">
<meta name="twitter:site" content="@<?php echo esc_attr( get_the_author_twitter_id() ) ?>">
<?php endif; ?>
<!-- /Twitter Card -->
