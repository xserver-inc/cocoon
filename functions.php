<?php
//require_once 'lib/debug.php'; //edump用のデバッグコード
require_once 'lib/_defins.php'; //定数を定義
require_once 'lib/admin.php'; //管理者機能（functions.phpで呼ばないと動作しないので）
require_once 'lib/admin-tinymce-qtag.php'; //管理者用編集ボタン機能（functions.phpで呼ばないと動作しないので）


//本文部分の冒頭を綺麗に抜粋する
if ( !function_exists( 'get_content_excerpt' ) ):
function get_content_excerpt($content, $length = 70){
  $content =  preg_replace('/<!--more-->.+/is', '', $content); //moreタグ以降削除
  $content =  strip_shortcodes($content);//ショートコード削除
  $content =  strip_tags($content);//タグの除去
  $content =  str_replace('&nbsp;', '', $content);//特殊文字の削除（今回はスペースのみ）
  $content =  preg_replace('/\[.+?\]/i', '', $content); //ショートコードを取り除く
  $content =  preg_replace(URL_REG, '', $content); //URLを取り除く
  // $content =  preg_replace('/\s/iu',"",$content); //余分な空白を削除
  $over    =  intval(mb_strlen($content)) > intval($length);
  $content =  mb_substr($content, 0, $length);//文字列を指定した長さで切り取る

  return $content;
}
endif;

//WP_Queryの引数を取得
if ( !function_exists( 'get_related_wp_query_args' ) ):
function get_related_wp_query_args(){
  global $post;
  if (!$post) {
    $post = get_random_posts(1);
  }
  //var_dump($post);
  if ( true ) {
  //if ( is_related_entry_association_category() ) {
    //カテゴリ情報から関連記事をランダムに呼び出す
    $categories = get_the_category($post->ID);
    $category_IDs = array();
    foreach($categories as $category):
      array_push( $category_IDs, $category->cat_ID);
    endforeach ;
    if ( empty($category_IDs) ) return;
    return $args = array(
      'post__not_in' => array($post->ID),
      'posts_per_page'=> intval(get_related_entry_count()),
      'category__in' => $category_IDs,
      'orderby' => 'rand',
    );
  } else {
    //タグ情報から関連記事をランダムに呼び出す
    $tags = wp_get_post_tags($post->ID);
    $tag_IDs = array();
    foreach($tags as $tag):
      array_push( $tag_IDs, $tag->term_id);
    endforeach ;
    if ( empty($tag_IDs) ) return;
    return $args = array(
      'post__not_in' => array($post -> ID),
      'posts_per_page'=> intval(10),
      //'posts_per_page'=> intval(get_related_entry_count()),
      'tag__in' => $tag_IDs,
      'orderby' => 'rand',
    );
  }
}
endif;

//images/no-image.pngを使用するimgタグに出力するサイズ関係の属性
if ( !function_exists( 'get_noimage_sizes_attr' ) ):
function get_noimage_sizes_attr($image = null){
  if (!$image) {
    $image = get_template_directory_uri().'/images/no-image-160.png';
  }
  $sizes = ' srcset="'.$image.' 160w" width="160" height="90" sizes="(max-width: 160px) 160vw, 90px"';
  return $sizes;
}
endif;

//投稿ナビのサムネイルタグを取得する
if ( !function_exists( 'get_post_navi_thumbnail_tag' ) ):
function get_post_navi_thumbnail_tag($id, $width = 120, $height = 67){
  $thumb = get_the_post_thumbnail( $id, array($width, $height), array('alt' => '') );
  if ( !$thumb ) {
    $image = get_template_directory_uri().'/images/no-image-%s.png';
    //表示タイプ＝デフォルト
    if ($width == 120) {
      $w = '160';
      $image = sprintf($image, $w);
      $wh_attr = ' srcset="'.$image.' 120w" width="120" height="67" sizes="(max-width: 120px) 120vw, 67px"';
    } else {//表示タイプ＝スクエア
      $w = '150';
      $image = sprintf($image, $w);
      $wh_attr = ' srcset="'.$image.' 120w" width="120" height="120" sizes="(max-width: 120px) 120vw, 120px"';
    }
    $thumb = '<img src="'.$image.'" alt="NO IMAGE" class="no-image post-navi-no-image"'.$wh_attr.' />';
  }
  return $thumb;
}
endif;

///////////////////////////////////////
// グローバルナビに説明文を加えるウォーカークラス
///////////////////////////////////////
class menu_description_walker extends Walker_Nav_Menu {
  function start_el(&$output, $item, $depth = 0, $args = array(), $id = 0) {
    global $wp_query;
    $indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

    $class_names = $value = '';

    $classes = empty( $item->classes ) ? array() : (array) $item->classes;
    //$classes[] = 'fa';
    if ($item->description) {
      $classes[] = 'menu-item-has-description';
    }
    //var_dump($classes);

    $class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item ) );
    $class_names = ' class="'. esc_attr( $class_names ) . '"';
    $output .= $indent . '<li id="menu-item-'. $item->ID . '"' . $value . $class_names .'>';

    $attributes  = ! empty( $item->attr_title ) ? ' title="'  . esc_attr( $item->attr_title ) .'"' : '';
    $attributes .= ! empty( $item->target )     ? ' target="' . esc_attr( $item->target     ) .'"' : '';
    $attributes .= ! empty( $item->xfn )        ? ' rel="'    . esc_attr( $item->xfn        ) .'"' : '';
    $attributes .= ! empty( $item->url )        ? ' href="'   . esc_attr( $item->url        ) .'"' : '';

    $prepend = '<div class="item-label">';
    $append = '</div>';
    $description  = ! empty( $item->description ) ? '<div class="item-description sub-caption">'.esc_attr( $item->description ).'</div>' : '';

    // if($depth != 0) {
    //   $description = $append = $prepend = "";
    // }

    $item_output = $args->before;
    $item_output .= '<a'. $attributes .'>';
    $item_output .= '<div class="caption-wrap">';
    $item_output .= $args->link_before .$prepend.apply_filters( 'the_title', $item->title, $item->ID ).$append;
    $item_output .= $description.$args->link_after;
    $item_output .= '</div>';
    $item_output .= '</a>';
    $item_output .= $args->after;

    $output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
  }
}

//アップデートチェックの初期化
require 'theme-update-checker.php'; //ライブラリのパス
$example_update_checker = new ThemeUpdateChecker(
  strtolower(THEME_NAME), //テーマフォルダ名
  'http://example.com/example-theme/update-info.json' //JSONファイルのURL
);

//アーカイブタイトルの取得
if ( !function_exists( 'get_archive_chapter_title' ) ):
function get_archive_chapter_title(){
  $chapter_title = null;
  if( is_category() ) {//カテゴリページの場合
    $chapter_title .= single_cat_title( '<span class="fa fa-folder-open"></span>', false );
  } elseif( is_tag() ) {//タグページの場合
    $chapter_title .= single_tag_title( '<span class="fa fa-tags"></span>
', false );
  } elseif( is_tax() ) {//タクソノミページの場合
    $chapter_title .= single_term_title( '', false );
  } elseif (is_day()) {
    //年月日のフォーマットを取得
    $chapter_title .= '<span class="fa fa-calendar"></span>
'.get_the_time('Y-m-n');
  } elseif (is_month()) {
    //年と月のフォーマットを取得
    $chapter_title .= '<span class="fa fa-calendar"></span>
'.get_the_time('Y-m');
  } elseif (is_year()) {
    //年のフォーマットを取得
    $chapter_title .= '<span class="fa fa-calendar"></span>
'.get_the_time('Y');
  } elseif (is_author()) {//著書ページの場合
    $chapter_title .= esc_html(get_queried_object()->display_name);
  } elseif (isset($_GET['paged']) && !empty($_GET['paged'])) {
    $chapter_title .= 'Archives';
  } else {
    $chapter_title .= 'Archives';
  }
  return $chapter_title;
}
endif;

//アーカイブ見出しの取得
if ( !function_exists( 'get_archive_chapter_text' ) ):
function get_archive_chapter_text(){
  $chapter_text = null;
  //アーカイブタイトル前
  //$chapter_text .= '<span class="archive-title-pb">'.__( '"', THEME_NAME ).'</span><span class="archive-title-text">';
  //アーカイブタイトルの取得
  $chapter_text .= get_archive_chapter_title();
  //アーカイブタイトル後
  //$chapter_text .= '</span><span class="archive-title-pa">'.__( '"', THEME_NAME );//.'</span><span class="archive-title-list-text">'.get_theme_text_list().'</span>';
  //返り値として返す
  return $chapter_text;
}
endif;

// add_action('admin_print_styles', 'my_admin_print_styles');
// function my_admin_print_styles() {
//  wp_enqueue_style( 'wp-color-picker' );
// }

//'wp-color-picker'の呼び出し順操作（最初の方に読み込む）
add_action('admin_enqueue_scripts', 'admin_scripts');
function admin_scripts($hook) {
    wp_enqueue_script('colorpicker-script', get_template_directory_uri() . '/js/color-picker.js', array( 'wp-color-picker' ), false, true);
}

//不要なテーマカスタマイザー項目を削除
//https://tenman.info/labo/snip/archives/8682
add_action( "customize_register", "customize_register_custom" );
if ( !function_exists( 'customize_register_custom' ) ):
function customize_register_custom( $wp_customize ) {
  $wp_customize->remove_control("header_image");
  //$wp_customize->remove_panel("widgets");
  $wp_customize->remove_section("colors");
  $wp_customize->remove_section("background_image");
  //$wp_customize->remove_section("static_front_page");
  //$wp_customize->remove_section("title_tagline");
  //$wp_customize->remove_control('nav');
}
endif;


//投稿管理画面のカテゴリリストの階層を保つ
add_filter('wp_terms_checklist_args', 'solecolor_wp_terms_checklist_args', 10, 2);
if ( !function_exists( 'solecolor_wp_terms_checklist_args' ) ):
function solecolor_wp_terms_checklist_args( $args, $post_id ){
 if ( isset($args['checked_ontop']) && ($args['checked_ontop'] !== false )){
    $args['checked_ontop'] = false;
 }
 return $args;
}
endif;


//カスタムフィールドのショートコードをロケーションURIに置換
if ( !function_exists( 'replace_directory_uri' ) ):
function replace_directory_uri($code){
  $code = str_replace('[template_directory_uri]', get_template_directory_uri(), $code);
  $code = str_replace('[stylesheet_directory_uri]', get_stylesheet_directory_uri(), $code);
  $code = str_replace('<?php echo template_directory_uri(); ?>', get_template_directory_uri(), $code);
  $code = str_replace('<?php echo get_stylesheet_directory_uri(); ?>', get_stylesheet_directory_uri(), $code);
  return $code;
}
endif;


//投稿内容をSSL対応する
if ( !function_exists( 'chagne_http_to_https' ) ):
function chagne_site_url_html_to_https($the_content){
  //httpとhttpsURLの取得
  if (strpos(site_url(), 'https://') !== false) {
    $http_url = str_replace('https://', 'http://', site_url());
    $https_url = site_url();
  } else {
    $http_url = site_url();
    $https_url = str_replace('http://', 'https://', site_url());
  }
  //投稿本文の内部リンクを置換
  $the_content = str_replace($http_url, $https_url, $the_content);

  //AmazonアソシエイトのSSL対応
  $search  = 'http://ecx.images-amazon.com';
  $replace = 'https://images-fe.ssl-images-amazon.com';
  $the_content = str_replace($search, $replace, $the_content);
  $search  = 'http://ir-jp.amazon-adsystem.com';
  $replace = 'https://ir-jp.amazon-adsystem.com';
  $the_content = str_replace($search, $replace, $the_content);


  //バリューコマースのSSL対応
  $search  = 'http://ck.jp.ap.valuecommerce.com';
  $replace = 'https://ck.jp.ap.valuecommerce.com';
  $the_content = str_replace($search, $replace, $the_content);
  $search  = 'http://ad.jp.ap.valuecommerce.com';
  $replace = 'https://ad.jp.ap.valuecommerce.com';
  $the_content = str_replace($search, $replace, $the_content);

  //もしもアフィリエイトのSSL対応
  $search  = 'http://c.af.moshimo.com';
  $replace = 'https://af.moshimo.com';
  $the_content = str_replace($search, $replace, $the_content);
  $search  = 'http://i.af.moshimo.com';
  $replace = 'https://i.moshimo.com';
  $the_content = str_replace($search, $replace, $the_content);
  $search  = 'http://image.moshimo.com';
  $replace = 'https://image.moshimo.com';
  $the_content = str_replace($search, $replace, $the_content);

  //A8.netのSSL対応
  $search  = 'http://px.a8.net';
  $replace = 'https://px.a8.net';
  $the_content = str_replace($search, $replace, $the_content);
  // $search  = 'http://www14.a8.net/0.gif';
  // $replace = 'https://www14.a8.net/0.gif';
  // $the_content = str_replace($search, $replace, $the_content);
  // $search  = 'http://www16.a8.net/0.gif';
  // $replace = 'https://www16.a8.net/0.gif';
  // $the_content = str_replace($search, $replace, $the_content);
  $search  = '{http://www(\d+).a8.net/0.gif}';
  $replace = "https://www$1.a8.net/0.gif";
  $the_content = preg_replace($search, $replace, $the_content);

  //アクセストレードのSSL対応
  $search  = 'http://h.accesstrade.net';
  $replace = 'https://h.accesstrade.net';
  $the_content = str_replace($search, $replace, $the_content);

  //はてなブログカードのSSL対応
  $search  = 'http://hatenablog.com/embed?url=';
  $replace = 'https://hatenablog-parts.com/embed?url=';
  $the_content = str_replace($search, $replace, $the_content);

  //はてブ数画像のSSL対応
  $search  = 'http://b.hatena.ne.jp/entry/image/';
  $replace = 'https://b.hatena.ne.jp/entry/image/';
  $the_content = str_replace($search, $replace, $the_content);

  //楽天商品画像のSSL対応
  $search  = 'http://hbb.afl.rakuten.co.jp';
  $replace = 'https://hbb.afl.rakuten.co.jp';
  $the_content = str_replace($search, $replace, $the_content);

  //リンクシェアのSSL対応
  $search  = 'http://ad.linksynergy.com';
  $replace = 'https://ad.linksynergy.com';
  $the_content = str_replace($search, $replace, $the_content);

  //Google検索ボックスのSSL対応
  $search  = 'http://www.google.co.jp/cse';
  $replace = 'https://www.google.co.jp/cse';
  $the_content = str_replace($search, $replace, $the_content);
  $search  = 'http://www.google.co.jp/coop/cse/brand';
  $replace = 'https://www.google.co.jp/coop/cse/brand';
  $the_content = str_replace($search, $replace, $the_content);

  //ここに新しい置換条件を追加していく

  // //のSSL対応
  // $search  = '';
  // $replace = '';
  // $the_content = str_replace($search, $replace, $the_content);

  return $the_content;
}
endif;
if (is_easy_ssl_enable()) {
  add_filter('the_content', 'chagne_site_url_html_to_https', 1);
  add_filter('widget_text', 'chagne_site_url_html_to_https', 1);
  add_filter('widget_text_pc_text', 'chagne_site_url_html_to_https', 1);
  //add_filter('widget_classic_text', 'chagne_site_url_html_to_https', 1);
  add_filter('widget_text_mobile_text', 'chagne_site_url_html_to_https', 1);
  add_filter('comment_text', 'chagne_site_url_html_to_https', 1);
}



/*
add_action('comment_form','google_recaptcha_script');
function google_recaptcha_script(){
  echo '<script src="https://www.google.com/recaptcha/api.js" async defer></script>';
}

add_action('comment_form','display_google_recaptcha');
function display_google_recaptcha() { ?>
  <div class="g-recaptcha" data-sitekey="6LehHTgUAAAAALR98L7a600Cgqlf2aV3zMFhBWzV"></div>
<?php }



add_action('pre_comment_on_post', 'verify_google_recaptcha');
function verify_google_recaptcha($comment_post_ID)
{
  if (isset($_POST['g-recaptcha-response'])) {
    $secret_key = '6LehHTgUAAAAAEkr_HmJ7WkkICDgEMb1Pt92g4H2';
    $response = wp_remote_get("https://www.google.com/recaptcha/api/siteverify?secret=". $secret_key ."&response=". $_POST['g-recaptcha-response']);
    $response = json_decode($response["body"], true);
    if ($response["success"] == true) {
      return;
    } else {
      // $errors->add("reCaptcha Invalid", __("Ошибка Регистрации: Похоже вы не человек.","textdomain"));
      wp_die(__( 'reCaptchaにより投稿が拒否されました。', THEME_NAME ));
    }
  } else {
    //$errors->add("reCaptcha Invalid", __("Ошибка Регистрации: Похоже вы бот. Если у вас отключен JavaScript","textdomain"));
    wp_die(__( 'reCaptchaにより投稿が拒否されました。', THEME_NAME ));
  }
  return;
}
*/

add_filter( 'in_widget_form', 'widget_logic_in_widget_form1', 10, 3 );
function widget_logic_in_widget_form1( $widget, $return, $instance ){
  $info = widget_info_by_id( $widget->id );
  //var_dump($info);
  //値の初期化
  $widget_action_def = 'hide';
  $widget_categories_def = array();
  if ($info) {
    if (isset($info['widget_action'])) {
      $widget_action_def = $info['widget_action'];
    }
    if (isset($info['widget_categories'])) {
      $widget_categories_def = $info['widget_categories'];
    }
  }

  $widget_logic = isset( $instance['widget_logic'] ) ? $instance['widget_logic'] : widget_info_by_id( $widget->id );
  $widget_action = isset( $instance['widget_action'] ) ? $instance['widget_action'] : $widget_action_def;
  $widget_categories = isset( $instance['widget_categories'] ) ? $instance['widget_categories'] : $widget_categories_def;

  ?>
    <p>
      <label for="<?php echo $widget->get_field_id('widget_logic'); ?>">
        <?php esc_html_e('ウィジェットの表示', THEME_NAME) ?>
      </label>
      <?php
        $options = array(
          'hide' => __( 'チェックしたページで非表示', THEME_NAME ),
          'show' => __( 'チェックしたページで表示', THEME_NAME ),
        );
        generate_selectbox_tag($widget->get_field_name('widget_action'), $options, $widget_action);
        //echo get_hierarchical_category_check_list_box(0, $widget->get_field_name('widget_categories'), $widget_categories);
        generate_hierarchical_category_check_list(0, $widget->get_field_name('widget_categories'), $widget_categories);

       ?>
      <textarea class="widefat" name="<?php echo $widget->get_field_name('widget_logic'); ?>" id="<?php echo $widget->get_field_id('widget_logic'); ?>"><?php echo esc_textarea( $widget_logic ) ?></textarea>
      <!-- <textarea class="widefat" name="<?php echo $widget->get_field_name('widget_action'); ?>" id="<?php echo $widget->get_field_id('widget_action'); ?>"><?php echo esc_textarea( $widget_action ) ?></textarea> -->
    </p>
  <?php
  return;
}

add_filter( 'widget_update_callback', 'widget_logic_update_callback1', 10, 4);
function widget_logic_update_callback1( $instance, $new_instance, $old_instance, $this_widget )
{
  if ( isset( $new_instance['widget_logic'] ) )
    $instance['widget_logic'] = $new_instance['widget_logic'];
  if ( isset( $new_instance['widget_action'] ) )
    $instance['widget_action'] = $new_instance['widget_action'];
  if ( isset( $new_instance['widget_categories'] ) )
    $instance['widget_categories'] = $new_instance['widget_categories'];


  return $instance;
}

function is_widget_visible( $info ){
  $widget_action = !empty($info['widget_action']) ? $info['widget_action'] : 'hide';
  $widget_categories = !empty($info['widget_categories']) ? $info['widget_categories'] : array();

  $display = true;
  //ウィジェットを表示する条件
  if ($widget_action == 'show') {
    if (empty($widget_categories)) {
      $display = false;
    } else {
      $display = is_category($widget_categories) || in_category($widget_categories);
    }
  } elseif ($widget_action == 'hide') {
    if (empty($widget_categories)) {
      $display = true;
    } else {
      $display = !is_category($widget_categories) && !in_category($widget_categories);
    }
  }
  // var_dump($widget_action);
  // var_dump($widget_categories);
  // var_dump($display);
  return $display;
}

// CALLED ON 'sidebars_widgets' FILTER
if (!is_admin()) {
  add_filter( 'sidebars_widgets', 'widget_logic_filter_sidebars_widgets', 10);
}
function widget_logic_filter_sidebars_widgets( $sidebars_widgets ){
  global $wp_reset_query_is_done, $wl_options, $wl_in_customizer;

  if ( $wl_in_customizer )
    return $sidebars_widgets;

  // reset any database queries done now that we're about to make decisions based on the context given in the WP query for the page
  if ( !empty( $wl_options['widget_logic-options-wp_reset_query'] ) && empty( $wp_reset_query_is_done ) )
  {
    wp_reset_query();
    $wp_reset_query_is_done = true;
  }

  // loop through every widget in every sidebar (barring 'wp_inactive_widgets') checking WL for each one
  foreach($sidebars_widgets as $widget_area => $widget_list)  {
    if ($widget_area=='wp_inactive_widgets' || empty($widget_list))
      continue;

    foreach($widget_list as $pos => $widget_id)    {
      //$logic = 'a';
      $info = widget_info_by_id( $widget_id );
      //_v($info);

      if ( !is_widget_visible( $info ) )
        unset($sidebars_widgets[$widget_area][$pos]);
    }
  }
  return $sidebars_widgets;
}

function widget_info_by_id( $widget_id ){
  global $wl_options;

  if ( preg_match( '/^(.+)-(\d+)$/', $widget_id, $m ) )  {
    $widget_class = $m[1];
    $widget_i = $m[2];

    $info = get_option( 'widget_'.$widget_class );
    if ( empty( $info[ $widget_i ] ) )
      return '';

    $info = $info[ $widget_i ];
  }
  else {
    $info = (array)get_option( 'widget_'.$widget_id, array() );
  }

  // //var_dump($info);
  // if ( isset( $info['widget_logic'] ) ){
  //   $logic = $info['widget_logic'];
  // }
  // elseif ( isset( $wl_options[ $widget_id ] ) )  {
  //   $logic = stripslashes( $wl_options[ $widget_id ] );
  //   widget_logic_save( $widget_id, $logic );

  //   unset( $wl_options[ $widget_id ] );
  //   update_option( 'widget_logic', $wl_options );
  // }
  // else {
  //   $logic = '';
  // }

  return $info;
}

if ( !function_exists( 'widget_info_by_id' ) ):
function widget_info_by_id( $widget_id ){
  global $wl_options;

  if ( preg_match( '/^(.+)-(\d+)$/', $widget_id, $m ) )  {
    $widget_class = $m[1];
    $widget_i = $m[2];

    $info = get_option( 'widget_'.$widget_class );
    if ( empty( $info[ $widget_i ] ) )
      return '';

    $info = $info[ $widget_i ];
  }
  else {
    $info = (array)get_option( 'widget_'.$widget_id, array() );
  }

  // //var_dump($info);
  // if ( isset( $info['widget_logic'] ) ){
  //   $logic = $info['widget_logic'];
  // }  elseif ( isset( $wl_options[ $widget_id ] ) )  {
  //   // $logic = stripslashes( $wl_options[ $widget_id ] );
  //   // widget_logic_save( $widget_id, $logic );

  //   // unset( $wl_options[ $widget_id ] );
  //   // update_option( 'widget_logic', $wl_options );
  // } else {
  //   $logic = '';
  // }

  return $info;
}
endif;