<?php

//リンクのないカテゴリーの取得（複数）
function get_the_nolink_categories(){
  $categories = null;
  foreach((get_the_category()) as $category){
    $categories .= '<span class="entry-category">'.$category->cat_name.'</span>';
  }
  return $categories;
}

//リンクのないカテゴリーの出力（複数）
function the_nolink_categories(){
  echo get_the_nolink_categories();
}

//カテゴリリンクの取得
function get_the_category_links(){
  $categories = null;
  foreach((get_the_category()) as $category){
    $categories .= '<a class="catlink" href="'.get_category_link( $category->cat_ID ).'">'.$category->cat_name.'</a>';
  }
  return $categories;
}

//カテゴリリンクの出力
function the_category_links(){
  echo get_the_category_links();
}

//リンクのないカテゴリーの取得
function get_the_nolink_category(){
  $categories = get_the_category();
  //var_dump($categories);
  if ( isset($categories[0]) ) {
    $category = $categories[0];
    return '<span class="category-label">'.$category->cat_name.'</span>';
  }
}

//リンクのないカテゴリーの出力
function the_nolink_category(){
  echo get_the_nolink_category();
}


//タグリンクの取得
function get_the_tag_links(){
  $tags = null;
  $posttags = get_the_tags();
  if ( $posttags ) {
    foreach(get_the_tags() as $tag){
      $tags .= '<a class="taglink" href="'.get_tag_link( $tag->term_id ).'">'.$tag->name.'</a>';
    }
  }
  return $tags;
}

//タグリンクの出力
function the_tag_links(){
  echo get_the_tag_links();
}

//コメントが許可されているか
function is_comment_allow(){
  global $post;
  if ( isset($post->comment_status) ) {
    return $post->comment_status == 'open';
  }
  return false;
}

//現在のカテゴリをカンマ区切りテキストで取得する
if ( !function_exists( 'get_category_ids' ) ):
function get_category_ids(){
  if ( is_single() ) {//投稿ページでは全カテゴリー取得
    $categories = get_the_category();
    $category_IDs = array();
    foreach($categories as $category):
      array_push( $category_IDs, $category -> cat_ID);
    endforeach ;
    return $category_IDs;
  } elseif ( is_category() ) {//カテゴリページではトップカテゴリーのみ取得
    $categories = get_the_category();
    $cat_now = $categories[0];
    return array( $cat_now->cat_ID );
  }
  return null;
}
endif;

//AdSense用のフォーマットに変換
if ( !function_exists( 'to_adsense_format' ) ):
function to_adsense_format($format){
  switch ($format) {
    case DATA_AD_FORMAT_AUTO:
      $format = DATA_AD_FORMAT_AUTO;
      break;
    case DATA_AD_FORMAT_RECTANGLE:
      $format = DATA_AD_FORMAT_RECTANGLE;
      break;
    case DATA_AD_FORMAT_HORIZONTAL:
      $format = DATA_AD_FORMAT_HORIZONTAL;
      break;
    case DATA_AD_FORMAT_VERTICAL:
      $format = DATA_AD_FORMAT_VERTICAL;
      break;
    default:
      $format = DATA_AD_FORMAT_RECTANGLE;
      break;
  }
  return $format;
}
endif;

//フォーマットを指定して広告テンプレートファイル呼び出す
if ( !function_exists( 'get_template_part_with_ad_format' ) ):
function get_template_part_with_ad_format($format = DATA_AD_FORMAT_AUTO, $wrap_class = null){
  // if ($wrap_class) {
  //   echo '<div class="'.$wrap_class.'">';
  // }
  if (isset($wrap_class)) {
    $wrap_class = ' '.trim($wrap_class).' ad-'.$format;


  }
  //var_dump($format);
  //$format変数をテンプレートファイルに渡す
  set_query_var('format', $format);
  //$format変数をテンプレートファイルに渡す
  set_query_var('wrap_class', $wrap_class);
  //広告テンプレートの呼び出し
  get_template_part('tmp/ad');
  // if ($wrap_class) {
  //   echo '</div>';
  // }
}
endif;

//オプション付きのテンプレート呼び出し
if ( !function_exists( 'get_template_part_with_option' ) ):
function get_template_part_with_option($slug, $option = null){
  //$option変数をテンプレートファイルに渡す
  set_query_var('option', $option);
  //広告テンプレートの呼び出し
  get_template_part($slug);
}
endif;


//著者セレクトボックスを手軽に作成する
if ( !function_exists( 'get_author_list_selectbox_tag' ) ):
function get_author_list_selectbox_tag($name, $value){
  $users = get_users( array('orderby'=>'ID','order'=>'ASC') );
  $html = '<select id="'.$name.'" name="'.$name.'">'.PHP_EOL;
  foreach($users as $user) {
    $uid = $user->ID;
    if ($uid == intval($value)) {
      $selected = " selected";
    } else {
      $selected = null;
    }
    $html .= '  <option value="'.$uid.'"'.$selected.'>'.$user->display_name.'</option>'.PHP_EOL;
  } //foreach
  $html .= '</select>'.PHP_EOL;
  return $html;
}
endif;

//オプションの値をデータベースに保存する
if ( !function_exists( 'update_theme_option' ) ):
function update_theme_option($option_name){
  $opt_val = isset($_POST[$option_name]) ? $_POST[$option_name] : null;
  update_option($option_name, $opt_val);
}
endif;

//チェックボックスのチェックを付けるか
if ( !function_exists( 'the_checkbox_checked' ) ):
function the_checkbox_checked($val1, $val2 = 1){
  if ( $val1 == $val2 ) {
    echo ' checked="checked"';
  }
}
endif;

//セレクトボックスのチェックを付けるか
if ( !function_exists( 'the_option_selected' ) ):
function the_option_selected($val1, $val2){
  if ($val1 == $val2) {
    echo ' selected="selected"';
  }
}
endif;

//セレクトボックスの生成
if ( !function_exists( 'genelate_selectbox_tag' ) ):
function genelate_selectbox_tag($name, $options, $now_value){?>
<select name="<?php echo $name; ?>">
  <?php foreach ($options as $value => $caption) { ?>
  <option value="<?php echo $value; ?>"<?php the_option_selected($value, $now_value) ?>><?php echo $caption; ?></option>
  <?php } ?>
</select>
<?php
}
endif;

//チェックボックスの生成
if ( !function_exists( 'genelate_checkbox_tag' ) ):
function genelate_checkbox_tag($name, $now_value, $label){?>
  <input type="checkbox" name="<?php echo $name; ?>" value="1"<?php the_checkbox_checked($now_value); ?>><?php echo $label; ?>
  <?php
}
endif;

//ラジオボックスの生成
if ( !function_exists( 'genelate_radiobox_tag' ) ):
function genelate_radiobox_tag($name, $options, $now_value){?>
<ul>
  <?php foreach ($options as $value => $caption) { ?>
  <li><input type="radio" name="<?php echo $name; ?>" value="<?php echo $value; ?>"<?php the_checkbox_checked($value, $now_value) ?>><?php echo $caption; ?></li>
  <?php } ?>
</ul>
  <?php
}
endif;

//ラベルの生成
if ( !function_exists( 'genelate_label_tag' ) ):
function genelate_label_tag($name, $caption){?>
  <label for="<?php echo $name; ?>"><?php echo $caption; ?></label>
  <?php
}
endif;

//説明文の生成
if ( !function_exists( 'genelate_tips_tag' ) ):
function genelate_tips_tag($caption){?>
  <p class="tips"><?php echo $caption; ?></p>
  <?php
}
endif;

//テキストボックスの生成
if ( !function_exists( 'genelate_textbox_tag' ) ):
function genelate_textbox_tag($name, $value, $placeholder, $cols = DEFAULT_INPUT_COLS){?>
  <input type="text" name="<?php echo $name; ?>" size="<?php echo $cols; ?>" value="<?php echo $value; ?>" placeholder="<?php echo $placeholder; ?>">
  <?php
}
endif;

//highlight-jsのCSS URLを取得
if ( !function_exists( 'get_highlight_js_css_url' ) ):
function get_highlight_js_css_url(){
  return get_template_directory_uri() . '/plugins/highlight-js/styles/'.get_code_highlight_style().'.css';
}
endif;

//ソースコードのハイライト表示に必要なリソースの読み込み
if ( !function_exists( 'wp_enqueue_highlight_js' ) ):
function wp_enqueue_highlight_js(){
  global $pagenow;
  if ( is_code_highlight_enable() || (is_admin() && $pagenow == 'admin.php') ) {
    // if (is_admin()) {
    //   echo '<link rel="stylesheet" type="text/css" href="'. get_highlight_js_css_url().'">'.PHP_EOL;
    // } else {
    //   wp_enqueue_style( 'code-highlight-style',  get_highlight_js_css_url() );
    // }

    //ソースコードハイライト表示用のスタイル
    wp_enqueue_style( 'code-highlight-style',  get_highlight_js_css_url() );
    wp_enqueue_script( 'code-highlight-js', get_template_directory_uri() . '/plugins/highlight-js/highlight.min.js', array( 'jquery' ), false, true );
    $data = '
      (function($){
       $("'.get_code_highlight_css_selector().'").each(function(i, block) {
        hljs.highlightBlock(block);
       });
      })(jQuery);
    ';
    wp_add_inline_script( 'code-highlight-js', $data, 'after' ) ;
  }
}
endif;

if ( !function_exists( 'get_random_1_post' ) ):
function get_random_1_post(){
  $posts = get_posts('numberposts=1&orderby=rand');
  foreach( $posts as $post ) {
    return $post;
  }
}
endif;
