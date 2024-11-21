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
  $font_family = get_theme_mod('font_radio', 'Quicksand');
  $font_url = generate_font_url($font_family);
  if ($font_family !== 'none') {
    echo '<link href="' . esc_url($font_url) . '" rel="preload" as="style">'."\n";
    echo '<link href="' . esc_url($font_url) . '" rel="stylesheet" media="print" onload="this.media=\'all\'">'."\n";
  }
}

function generate_font_url($font_family) {
  $font_families = array(
    'Fugaz One' => 'Fugaz+One&display=swap',
    'Saira'     => 'Saira:ital,wght@0,100..900;1,100..900&display=swap',
    'Alice'     => 'Alice&display=swap',
    'Comfortaa' => 'Comfortaa:wght@300..700&display=swap',
    'Quicksand' => 'Quicksand:wght@300..700&display=swap',
    'Roboto'    => 'Roboto:wght@300;400;700&display=swap',
    'none'      	=> '',
  );

  if (isset($font_families[$font_family]) && $font_family !== 'none') {
    return 'https://fonts.googleapis.com/css2?family=' . $font_families[$font_family];
  }
  return '';
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
<p>※この機能についての<a target="_blank" href="https://go-blogs.com/cocoon/skin-nagi-fix-cta/">詳しい説明はこちら</a></p>
  <label for="fix_link">アフィリエイトタグのショートコード（こちらが入力されていないとフッター固定CTAは表示されません）

<p class="hogehoge">必ずアフィリエイトショートコードを入力してください。<span>アフィリエイトのタグは＜a＞タグで囲まれたものに限られます。それ以外はボタンになりません。</span></p>


  </label>
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
    $show1241over     = get_theme_mod('carousel_1241over', '4');
    $show1024_1240    = get_theme_mod('carousel_1024_1240', '4');
    $show835_1023     = get_theme_mod('carousel_835_1023', '3');
    $show481_834      = get_theme_mod('carousel_481_834', '2');
    $show_under480    = get_theme_mod('carousel_under480', '1');

    $slide1241over     = get_theme_mod('slide_1241over',true)? 1 : $show1241over;
    $slide1024_1240    = get_theme_mod('slide_1024_1240', true)? 1 : $show1024_1240;
    $slide835_1023     = get_theme_mod('slide_835_1023', true)? 1 : $show835_1023;
    $slide481_834      = get_theme_mod('slide_481_834', true)? 1 : $show481_834;
    $slide_under480    = get_theme_mod('slide_under480', true)? 1 : $show_under480;



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
                    slidesToShow: '.$show1241over.',
                    slidesToScroll: '.$slide1241over.',
                    respondTo: "slider",
                    responsive: [
                      {
                        breakpoint: 1241,
                        settings: {
                          slidesToShow: '.$show1024_1240.',
                          slidesToScroll: '.$slide1024_1240.'
                        }
                      },
                      {
                        breakpoint: 1024,
                        settings: {
                          slidesToShow: '.$show835_1023.',
                          slidesToScroll: '.$slide835_1023.'
                        }
                      },
                      {
                        breakpoint: 835,
                        settings: {
                          slidesToShow: '.$show481_834.',
                          slidesToScroll: '.$slide481_834.'
                        }
                      },
                      {
                        breakpoint: 481,
                        settings: {
                          slidesToShow: '.$show_under480.',
                          slidesToScroll: '.$slide_under480.'
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

//カルーセル
add_filter('cocoon_setting_preview_carousel', '__return_false');