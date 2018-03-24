
<!-- OGP -->
<meta property="og:type" content="<?php echo (is_singular() ? 'article' : 'website'); ?>">
<?php
$description = get_meta_description_text();
if (is_singular()){//単一記事ページの場合
  //if(have_posts()): while(have_posts()): the_post();
    echo '<meta property="og:description" content="'.$description.'">';echo "\n";//抜粋を表示
  //endwhile; endif;
  $title = get_the_title();
  if ( is_front_page() ) {
    $title = get_bloginfo('name');
  }
  echo '<meta property="og:title" content="'; echo $title; echo '">';echo "\n";//単一記事タイトルを表示
  echo '<meta property="og:url" content="'; the_permalink(); echo '">';echo "\n";//単一記事URLを表示
} else {//単一記事ページページ以外の場合（アーカイブページやホームなど）
  $title = get_bloginfo('name');
  $url = home_url();

  if ( is_category() ) {//カテゴリ用設定
    $description = get_category_meta_description();
    $title = wp_title(null, false).' | '.get_bloginfo('name');
    $url = generate_canonical_url();
  }

  if ( is_tag() ) {//タグ用設定
    $description = get_tag_meta_description();
    $title = wp_title(null, false).' | '.get_bloginfo('name');
    $url = generate_canonical_url();
  }
  echo '<meta property="og:description" content="'; echo $description; echo '">';echo "\n";//「一般設定」管理画面で指定したブログの説明文を表示
  echo '<meta property="og:title" content="'; echo $title; echo '">';echo "\n";//「一般設定」管理画面で指定したブログのタイトルを表示
  echo '<meta property="og:url" content="'; echo $url; echo '">';echo "\n";//「一般設定」管理画面で指定したブログのURLを表示取る
}
$content = '';
if ( isset( $post->post_content ) ){
  $content = $post->post_content;
}
$searchPattern = '/<img.*?src=(["\'])(.+?)\1.*?>/i';//投稿にイメージがあるか調べる
if (is_singular()){//単一記事ページの場合
  if (has_post_thumbnail()){//投稿にサムネイルがある場合の処理
    $image_id = get_post_thumbnail_id();
    $image = wp_get_attachment_image_src( $image_id, 'full');
    echo '<meta property="og:image" content="'.$image[0].'">';echo "\n";
  } else if ( preg_match( $searchPattern, $content, $imgurl ) && !is_archive()) {//投稿にサムネイルは無いが画像がある場合の処理
    echo '<meta property="og:image" content="'.$imgurl[2].'">';echo "\n";
  } else if ( get_ogp_home_image_url() ){//ホームイメージが設定されている場合
    echo '<meta property="og:image" content="'.get_ogp_home_image_url().'">';echo "\n";
  } else {//投稿にサムネイルも画像も無い場合の処理
    $ogp_image = get_template_directory_uri().'/images/no-image-320.png';
    echo '<meta property="og:image" content="'.$ogp_image.'">';echo "\n";
  }
} else {//単一記事ページページ以外の場合（アーカイブページやホームなど）
  if ( get_ogp_home_image_url() ) {
    $ogp_image = get_ogp_home_image_url();
  } else {
    if ( get_the_site_logo_url() ){//ヘッダーロゴがある場合はロゴを使用
      $ogp_image = get_the_site_logo_url();
    } elseif ( get_header_image() ){//ヘッダーイメージがある場合はそれを使用
      //$ogp_image = get_header_image();
    // } else {//ヘッダーイメージがない場合は、テーマのスクリーンショット
    //   $ogp_image = get_stylesheet_directory_uri().'/screenshot.png';
    }
  }
  if ( !empty($ogp_image) ) {//使えそうな$ogp_imageがある場合
    echo '<meta property="og:image" content="'.$ogp_image.'">';echo "\n";
  }
}
?>
<meta property="og:site_name" content="<?php bloginfo('name'); ?>">
<meta property="og:locale" content="ja_JP">
<?php if ( false ): //fb:adminsの取得?>
<meta property="fb:admins" content="<?php echo get_fb_admins(); ?>">
<?php endif; ?>
<?php if ( get_facebook_app_id() ): //fb:app_idの取得?>
<meta property="fb:app_id" content="<?php echo get_facebook_app_id(); ?>">
<?php endif; ?>
<meta property="article:published_time" content="<?php echo get_seo_post_time(); ?>" />
<?php if ($update_time = get_seo_update_time()): ?>
<meta property="article:modified_time" content="<?php echo $update_time; ?>" />
<?php endif ?>
<?php //カテゴリー
$cats = get_the_category();
if ($cats) {
  foreach($cats as $cat) {
    echo '<meta property="article:section" content="' . $cat->name . '">'.PHP_EOL;
  }
} ?>
<?php //タグ
$tags = get_the_tags();
if ($tags) {
  foreach($tags as $tag) {
    echo '<meta property="article:tag" content="' . $tag->name . '">'.PHP_EOL;
  }
} ?>
<!-- /OGP -->
