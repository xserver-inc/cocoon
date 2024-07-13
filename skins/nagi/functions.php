<?php //スキンから親テーマの定義済み関数等をオーバーライドして設定の書き換えが可能
if ( !defined( 'ABSPATH' ) ) exit;

get_template_part('skins/nagi/nagi-css-custom');
get_template_part('skins/nagi/nagi-customizer');

add_filter( 'body_class', 'nagi_body_class_names' );
function nagi_body_class_names( $classes ) {
	$classes[] = 'class_nagi nagi_custom';
	return $classes;
}

add_action('wp_head', 'nagi_g_font');
function nagi_g_font() {
echo '<link href="https://fonts.googleapis.com/css2?family=Alice&family=Comfortaa:wght@300..700&family=Fugaz+One&family=Quicksand:wght@300..700&family=Roboto:wght@300;400;700&family=Saira:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">';
}

function my_custom_editor_styles() {
  wp_enqueue_style('my-custom-editor-style', get_theme_file_uri('/skins/nagi/editor-style.css'), false, '1.0', 'all');
}
add_action('enqueue_block_editor_assets', 'my_custom_editor_styles');

function create_custom_fields()
{
  add_meta_box(
    'custom_field_1', 
    'フッター固定CTA', 
    'insert_custom_fields', 
    'post', 
    'normal', 
    'default'  
  );
}
add_action('admin_menu', 'create_custom_fields');



function insert_custom_fields($post)
{
  wp_nonce_field('custom_field_save_meta_box_data', 'custom_field_meta_box_nonce');

  $fix_link = get_post_meta($post->ID, 'fix_link', true);
  $fix_microcopy = get_post_meta($post->ID, 'fix_microcopy', true);
  $cta_color = get_post_meta($post->ID,'cta_color',true);
  $cta_layout = get_post_meta($post->ID,'cta_layout',true);
  if (empty($cta_color)) {
    $cta_color = 'cta_red'; 
  }
  if (empty($cta_layout)) {
    $cta_layout = 'cta_v'; 
  }
?>
  <label for="fix_link">アフィリエイトタグのショートコード（こちらが入力されていないとフッター固定CTAは表示されません）</label>
  <input id="fix_link" type="text" name="fix_link" value="<?php echo $fix_link; ?>"placeholder="アフィリエイトタグのショートコードをここへ入力してください">
  <br>
  <label for="fix_microcopy">マイクロコピー</label>
  <input id="fix_microcopy" type="text" name="fix_microcopy" value="<?php echo $fix_microcopy; ?>"placeholder="マイクロコピーをここへ入力してください">
  <br><br>
  <p>CTAボタンの色</p>
  <label for="cta_color1">赤系</label>
  <input id="cta_color1" type="radio" name="cta_color" value="cta_red" <?php if ($cta_color == "cta_red") echo 'checked'; ?>>
  <label for="cta_color2">青系</label>
  <input id="cta_color2" type="radio" name="cta_color" value="cta_blue" <?php if ($cta_color == "cta_blue") echo 'checked'; ?>>
  <label for="cta_color3">緑系</label>
  <input id="cta_color3" type="radio" name="cta_color" value="cta_green" <?php if ($cta_color == "cta_green") echo 'checked'; ?>>
  <label for="cta_color4">キーカラー</label>
  <input id="cta_color4" type="radio" name="cta_color" value="cta_key" <?php if ($cta_color == "cta_key") echo 'checked'; ?>>
  <br><br>
  <p>マイクロコピーとボタンのレイアウト</p>
  <label for="cta_layout_v">縦並び</label>
  <input id="cta_layout_v" type="radio" name="cta_layout" value="cta_v" <?php if ($cta_layout == "cta_v") echo 'checked'; ?>>
  <label for="cta_layout_s">横並び</label>
  <input id="cta_layout_s" type="radio" name="cta_layout" value="cta_s" <?php if ($cta_layout == "cta_s") echo 'checked'; ?>>
<?php
}


function save_custom_fields($post_id)
{
  if (!isset($_POST['custom_field_meta_box_nonce'])) {
    return;
  }

  if (!wp_verify_nonce($_POST['custom_field_meta_box_nonce'], 'custom_field_save_meta_box_data')) {
    return;
  }



  if (isset($_POST['fix_link'])) {
    $data_without_quotes = str_replace('"', '', $_POST['fix_link']);
    
    $sanitized_data = sanitize_textarea_field($data_without_quotes);
    
    update_post_meta($post_id, 'fix_link', $sanitized_data);
}


  if (isset($_POST['fix_microcopy'])) {
    $data = sanitize_text_field($_POST['fix_microcopy']);
    update_post_meta($post_id, 'fix_microcopy', $data);
  }
  if (isset($_POST['cta_color'])) {
    $data = sanitize_text_field($_POST['cta_color']);
    update_post_meta($post_id, 'cta_color', $data);
  }
  if (isset($_POST['cta_layout'])) {
    $data = sanitize_text_field($_POST['cta_layout']);
    update_post_meta($post_id, 'cta_layout', $data);
  }
}
add_action('save_post', 'save_custom_fields');



//-----------------------------------------
//fix-cta.phpの読み込み
//-----------------------------------------
function load_fix_cta() {
  if (is_single()) {
      $fix_link = get_post_meta(get_the_ID(), 'fix_link', true);

      if ($fix_link) {
          get_template_part('skins/nagi/fix-cta');
      }
  }
}

add_action('wp_footer', 'load_fix_cta');


//-----------------------------------------
//カルーセル
//-----------------------------------------
add_filter('cocoon_part__tmp/carousel', function($content) {
  // クラス名を 'carousel-content' から 'custom-carousel-content' に変更
  $content = str_replace('carousel-content', 'custom-carousel-content', $content);
  return $content;
});
if ( !function_exists( 'wp_enqueue_slick_custom' ) ):
  function wp_enqueue_slick_custom(){
    $show1301over=get_theme_mod('carousel_1301over', '4');
    $show1300=get_theme_mod('carousel_1300', '4');
    $show1083=get_theme_mod('carousel_1083', '3');
    $show894=get_theme_mod('carousel_894', '2');
    $show540=get_theme_mod('carousel_540', '1');

    $slide1301over=get_theme_mod('slide_1301over', '1');
    $slide1300=get_theme_mod('slide_1300', '1');
    $slide1083=get_theme_mod('slide_1083', '1');
    $slide894=get_theme_mod('slide_894', '1');
    $slide540=get_theme_mod('slide_540', '1');

    if (is_carousel_visible()) {
      wp_enqueue_style( 'slick-theme-style', get_template_directory_uri() . '/plugins/slick/slick-theme.css' );
      wp_enqueue_script( 'slick-js', get_template_directory_uri() . '/plugins/slick/slick.min.js', array( 'jquery' ), false, true  );
      $autoplay = null;
      if (is_carousel_autoplay_enable()) {
        $autoplay = 'autoplay: true,';
      }
      $data = '
                (function($){
                  $(".custom-carousel-content").slick({
                    dots: true,'.
                    $autoplay.
                    'autoplaySpeed: '.strval(intval(get_carousel_autoplay_interval())*1000).',
                    infinite: true,
                    slidesToShow: '.$show1301over.', 
                    slidesToScroll: '.$slide1301over.', 
                    respondTo: "slider",
                    responsive: [
                      {
                        breakpoint: 1241,
                        settings: {
                          slidesToShow: '.$show1300.',
                          slidesToScroll: '.$slide1300.'
                        }
                      },
                      {
                        breakpoint: 1024,
                        settings: {
                          slidesToShow: '.$show1083.',
                          slidesToScroll: '.$slide1083.'
                        }
                      },
                      {
                        breakpoint: 835,
                        settings: {
                          slidesToShow: '.$show894.',
                          slidesToScroll: '.$slide894.'
                        }
                      },
                      {
                        breakpoint: 481,
                        settings: {
                          slidesToShow: '.$show540.',
                          slidesToScroll: '.$slide540.'
                        }
                      }
                      ]
                  });

                })(jQuery);
              ';
      wp_add_inline_script( 'slick-js', $data, 'after' ) ;
    }
  }
endif;

add_action( 'wp_enqueue_scripts', 'wp_enqueue_slick_custom' );


add_action('wp_enqueue_scripts', 'nagi_enqueue_accordion_assets');
function nagi_enqueue_accordion_assets() {
    if (get_theme_mod('cat_accordion',true) == true) {
        // JavaScriptファイルを読み込む
        wp_enqueue_script('nagi-accordion-js', get_theme_file_uri('/skins/nagi/accordion.js'), array('jquery'), null, true);

        // インラインCSSを出力
        add_action('wp_head', function() {
            echo '<style>ul.children { display: none; }</style>';
        });
    }
}
