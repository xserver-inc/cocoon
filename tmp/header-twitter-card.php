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
$description = get_ogp_description_text();
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

  if ( is_category() ) {//カテゴリ用設定
    $category = get_queried_object();
    if ($category) {
      $title = $category->name;
    } else {
      $title = wp_title(null, false).' | '.get_bloginfo('name');
    }
    $url = generate_canonical_url();
  }

  if ( is_tag() || is_tax() ) {//タグ用設定
    $tag = get_queried_object();
    if ($tag) {
      $title = $tag->name;
    } else {
      $title = wp_title(null, false).' | '.get_bloginfo('name');
    }
    $url = generate_canonical_url();
  }
}

$title = apply_filters('sns_card_title', $title);
$title = apply_filters('ogp_card_title', $title);
$description = apply_filters('ogp_card_description', $description);
echo '<meta property="twitter:description" content="'; echo esc_attr($description); echo '">';echo "\n";//ブログの説明文を表示
echo '<meta property="twitter:title" content="'; echo esc_attr($title); echo '">';echo "\n";//ブログのタイトルを表示
echo '<meta property="twitter:url" content="'; echo esc_url($url); echo '">';echo "\n";//ブログのURLを表示取る

$ogp_home_image = get_ogp_home_image_url();
$ogp_image = $ogp_home_image ? $ogp_home_image : get_no_image_url();
if (is_singular()){//単一記事ページの場合
  $ogp_image = get_singular_sns_share_image_url();
} else {//単一記事ページページ以外の場合（アーカイブページやホームなど）
  if (is_category() && !is_paged() && $eye_catch = get_the_category_eye_catch_url(get_query_var('cat'))) {
    $ogp_image = $eye_catch;
  } elseif ((is_tag() || is_tax()) && !is_paged() && $eye_catch = get_the_tag_eye_catch_url(get_queried_object_id())) {
    $ogp_image = $eye_catch;
  } elseif ( get_ogp_home_image_url() ) {
    $ogp_image = get_ogp_home_image_url();
  } else {
    if ( get_the_site_logo_url() ){//ヘッダーロゴがある場合はロゴを使用
      $ogp_image = get_the_site_logo_url();
    }
  }
}
$ogp_image = apply_filters('ogp_card_ogp_image', $ogp_image);
if ( !empty($ogp_image) ) {//使えそうな$ogp_imageがある場合
  echo '<meta name="twitter:image" content="'.esc_url($ogp_image).'">';echo "\n";
}
?>
<meta name="twitter:domain" content="<?php echo esc_attr(get_the_site_domain()) ?>">
<?php
$twitter_id = get_the_author_twitter_id();//Twitter IDの取得
if ( $twitter_id )://TwitterIDが設定されている場合
 ?>
<meta name="twitter:creator" content="@<?php echo esc_attr($twitter_id); ?>">
<meta name="twitter:site" content="@<?php echo esc_attr($twitter_id); ?>">
<?php endif; ?>
<!-- /Twitter Card -->
