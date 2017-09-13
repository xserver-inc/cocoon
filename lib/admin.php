<?php //管理画面関係の関数

//メディアを挿入の初期表示を「この投稿へのアップロード」にする
if ( !function_exists( 'customize_initial_view_of_media_uploader' ) ):
function customize_initial_view_of_media_uploader() {
echo "<script type='text/javascript'>
 //<![CDATA[
jQuery(function($) {
        $('#wpcontent').ajaxSuccess(function() {
                $('select.attachment-filters [value=\"uploaded\"]').attr( 'selected', true ).parent().trigger('change');
        });
});
  //]]>
</script>";
}
endif;
add_action( 'admin_footer-post-new.php', 'customize_initial_view_of_media_uploader' );
add_action( 'admin_footer-post.php', 'customize_initial_view_of_media_uploader' );


//投稿記事一覧にアイキャッチ画像を表示
//カラムの挿入
if ( !function_exists( 'customize_admin_manage_posts_columns' ) ):
function customize_admin_manage_posts_columns($columns) {
    $columns['thumbnail'] = __( 'アイキャッチ', THEME_NAME );
    return $columns;
}
endif;
add_filter( 'manage_posts_columns', 'customize_admin_manage_posts_columns' );


//サムネイルの挿入
if ( !function_exists( 'customize_admin_add_column' ) ):
function customize_admin_add_column($column_name, $post_id) {
    if ( 'thumbnail' == $column_name) {
        //テーマで設定されているサムネイルを利用する場合
        $thum = get_the_post_thumbnail($post_id, 'thumb100', array( 'style'=>'width:75px;height:auto;' ));
        //Wordpressで設定されているサムネイル（小）を利用する場合
        //$thum = get_the_post_thumbnail($post_id, 'small', array( 'style'=>'width:75px;height:auto;' ));
    }
    if ( isset($thum) && $thum ) {
        echo $thum;
    }
}
endif;
add_action( 'manage_posts_custom_column', 'customize_admin_add_column', 10, 2 );

//アイキャッチ画像の列の幅をCSSで調整
//投稿一覧のカラムの幅のスタイル調整
if ( !function_exists( 'admin_print_styles_custom' ) ):
function admin_print_styles_custom() {
    wp_enqueue_style( 'admin-style', get_template_directory_uri().'/css/admin.css' );
    wp_enqueue_style( 'font-awesome-style', FONT_AWESOME_CDN_URL );

    global $pagenow;
    //var_dump($pagenow);
    if ($pagenow == 'admin.php') {
      wp_enqueue_script( 'tab-js-jquery', '//code.jquery.com/jquery.min.js', array( 'jquery' ), false, true );
      wp_enqueue_script( 'tab-js', get_template_directory_uri() . '/js/jquery.tabs.js', array( 'tab-js-jquery' ), false, true );
      $select_index = 0;
      if (isset($_POST['select_index'])) {
        $select_index = intval($_POST[SELECT_INDEX_NAME]);
        var_dump($select_index);
      }
      $data = 'jQuery(document).ready( function() {
                 tabify("#tabs").select( '.$select_index.' );
               });';
      wp_add_inline_script( 'tab-js', $data, 'after' ) ;
      wp_enqueue_script( 'admin-javascript', get_template_directory_uri() . '/js/admin-javascript.js', array( ), false, true );
    }

    //echo '<link rel="stylesheet" href="'.get_template_directory_uri().'/css/admin.css" />'.PHP_EOL;
    //echo '<style TYPE="text/css">.column-thumbnail{width:80px;}</style>'.PHP_EOL;
}
endif;
add_action('admin_print_styles', 'admin_print_styles_custom', 21);


//管理ツールバーにメニュー追加
if ( !function_exists( 'customize_admin_bar_menu' ) ):
function customize_admin_bar_menu($wp_admin_bar){
  //バーにメニューを追加
  $title = sprintf(
      '<span class="ab-label">%s</span>',
      __( '管理メニュー', THEME_NAME )//親メニューラベル
  );
  $wp_admin_bar->add_menu(array(
      'id'    => 'dashboard_menu',
      'meta'  => array(),
      'title' => $title
  ));
  //サブメニューを追加
  $wp_admin_bar->add_menu(array(
      'parent' => 'dashboard_menu', // 親メニューID
      'id'     => 'dashboard_menu-dashboard', // 子メニューID
      'meta'   => array(),
      'title'  => __( 'ダッシュボード', THEME_NAME ), // ラベル
      'href'   => site_url('/wp-admin/') // ページURL
  ));
  $wp_admin_bar->add_menu(array(
      'parent' => 'dashboard_menu', // 親メニューID
      'id'     => 'dashboard_menu-singles', // 子メニューID
      'meta'   => array(),
      'title'  => __( '投稿一覧', THEME_NAME ), // ラベル
      'href'   => site_url('/wp-admin/edit.php') // ページURL
  ));
  $wp_admin_bar->add_menu(array(
      'parent' => 'dashboard_menu', // 親メニューID
      'id'     => 'dashboard_menu-pages', // 子メニューID
      'meta'   => array(),
      'title'  => __( '固定ページ一覧', THEME_NAME ), // ラベル
      'href'   => site_url('/wp-admin/edit.php?post_type=page') // ページURL
  ));
  $wp_admin_bar->add_menu(array(
      'parent' => 'dashboard_menu', // 親メニューID
      'id'     => 'dashboard_menu-medias', // 子メニューID
      'meta'   => array(),
      'title'  => __( 'メディア一覧', THEME_NAME ), // ラベル
      'href'   => site_url('/wp-admin/upload.php') // ページURL
  ));
  $wp_admin_bar->add_menu(array(
      'parent' => 'dashboard_menu', // 親メニューID
      'id'     => 'dashboard_menu-themes', // 子メニューID
      'meta'   => array(),
      'title'  => __( 'テーマ', THEME_NAME ), // ラベル
      'href'   => site_url('/wp-admin/themes.php') // ページURL
  ));
  $wp_admin_bar->add_menu(array(
      'parent' => 'dashboard_menu', // 親メニューID
      'id'     => 'dashboard_menu-customize', // 子メニューID
      'meta'   => array(),
      'title'  => __( 'カスタマイズ', THEME_NAME ), // ラベル
      'href'   => site_url('/wp-admin/customize.php?return=' . esc_url(site_url('/wp-admin/themes.php'))) // ページURL
  ));
  $wp_admin_bar->add_menu(array(
      'parent' => 'dashboard_menu', // 親メニューID
      'id'     => 'dashboard_menu-widget', // 子メニューID
      'meta'   => array(),
      'title'  => __( 'ウィジェット', THEME_NAME ), // ラベル
      'href'   => site_url('/wp-admin/widgets.php') // ページURL
  ));
  $wp_admin_bar->add_menu(array(
      'parent' => 'dashboard_menu', // 親メニューID
      'id'     => 'dashboard_menu-nav-menus', // 子メニューID
      'meta'   => array(),
      'title'  => __( 'メニュー', THEME_NAME ), // ラベル
      'href'   => site_url('/wp-admin/nav-menus.php') // ページURL
  ));
  $wp_admin_bar->add_menu(array(
      'parent' => 'dashboard_menu', // 親メニューID
      'id'     => 'dashboard_menu-theme-editor', // 子メニューID
      'meta'   => array(),
      'title'  => __( 'テーマの編集', THEME_NAME ), // ラベル
      'href'   => site_url('/wp-admin/theme-editor.php') // ページURL
  ));
  $wp_admin_bar->add_menu(array(
      'parent' => 'dashboard_menu', // 親メニューID
      'id'     => 'dashboard_menu-plugins', // 子メニューID
      'meta'   => array(),
      'title'  => __( 'プラグイン一覧', THEME_NAME ), // ラベル
      'href'   => site_url('/wp-admin/plugins.php') // ページURL
  ));
  $wp_admin_bar->add_menu(array(
      'parent' => 'dashboard_menu', // 親メニューID
      'id'     => 'dashboard_menu-theme-settings', // 子メニューID
      'meta'   => array(),
      'title'  => __( SETTING_NAME_TOP, THEME_NAME ), // ラベル
      'href'   => site_url('/wp-admin/admin.php?page=theme-settings') // ページURL
  ));
}
endif;
add_action('admin_bar_menu', 'customize_admin_bar_menu', 9999);


//記事公開前に確認アラートを出す
if ( !function_exists( 'publish_confirm_admin_print_scripts' ) ):
function publish_confirm_admin_print_scripts() {
    $post_text = __( '公開', THEME_NAME );
    $confirm_text = __( '記事を公開してもよろしいですか？', THEME_NAME );
    echo <<< EOM
<script type="text/javascript">
<!--
window.onload = function() {
    var id = document.getElementById('publish');
    if (id.value.indexOf("$post_text", 0) != -1) {
        id.onclick = publish_confirm;
    }
}
function publish_confirm() {
    if (window.confirm("$confirm_text")) {
        return true;
    } else {
        var elements = document.getElementsByTagName('span');
        for (var i = 0; i < elements.length; i++) {
            var element = elements[i];
            if (element.className.indexOf("spinner", 0) != -1) {
                element.classList.remove('spinner');
            }
        }
        document.getElementById('publish').classList.remove('button-primary-disabled');
        document.getElementById('save-post').classList.remove('button-disabled');

            return false;
    }
}
// -->
</script>
EOM;
}
endif;
add_action('admin_print_scripts', 'publish_confirm_admin_print_scripts');

//投稿一覧リストの上にタグフィルターと管理者フィルターを追加する
if ( !function_exists( 'custmuize_restrict_manage_posts' ) ):
function custmuize_restrict_manage_posts(){
  global $post_type, $tag;
  if ( is_object_in_taxonomy( $post_type, 'post_tag' ) ) {
    $dropdown_options = array(
      'show_option_all' => get_taxonomy( 'post_tag' )->labels->all_items,
      'hide_empty' => 0,
      'hierarchical' => 1,
      'show_count' => 0,
      'orderby' => 'name',
      'selected' => $tag,
      'name' => 'tag',
      'taxonomy' => 'post_tag',
      'value_field' => 'slug'
    );
    wp_dropdown_categories( $dropdown_options );
  }

  wp_dropdown_users(
    array(
      'show_option_all' => 'すべてのユーザー',
      'name' => 'author'
    )
  );
}
endif;
add_action('restrict_manage_posts', 'custmuize_restrict_manage_posts');

//投稿一覧で「全てのタグ」選択時は$_GET['tag']をセットしない
if ( !function_exists( 'custmuize_load_edit_php' ) ):
function custmuize_load_edit_php(){
  if (isset($_GET['tag']) && '0' === $_GET['tag']) {
    unset ($_GET['tag']);
  }
}
endif;
add_action('load-edit.php', 'custmuize_load_edit_php');

// ビジュアルエディタにHTMLを直挿入するためのボタンを追加
if ( !function_exists( 'add_insert_html_button' ) ):
function add_insert_html_button( $buttons ) {
  $buttons[] = 'button_insert_html';
  return $buttons;
}
endif;
add_filter( 'mce_buttons', 'add_insert_html_button' );

if ( !function_exists( 'add_insert_html_button_plugin' ) ):
function add_insert_html_button_plugin( $plugin_array ) {
  $plugin_array['custom_button_script'] =  get_template_directory_uri() . "/js/button-insert-html.js";
  return $plugin_array;
}
endif;
add_filter( 'mce_external_plugins', 'add_insert_html_button_plugin' );


//投稿管理画面のカテゴリー選択にフィルタリング機能を付ける
if ( !function_exists( 'add_category_filter_form' ) ):
function add_category_filter_form() {
?>
<script type="text/javascript">
jQuery(function($) {
  function zenToHan(text) {
    return text.replace(/[Ａ-Ｚａ-ｚ０-９]/g, function(s) {
          return String.fromCharCode(s.charCodeAt(0) - 65248);
      });
  }

  function catFilter( header, list ) {
    var form  = $('<form>').attr({'class':'filterform', 'action':'#'}).css({'position':'absolute', 'top':'38px'}),
        input = $('<input>').attr({'class':'filterinput', 'type':'text', 'placeholder':'<?php _e( 'カテゴリー検索', THEME_NAME ) ?>' });
    $(form).append(input).appendTo(header);
    $(header).css({'padding-top':'42px'});
    $(input).change(function() {
      var filter = $(this).val();
      filter = zenToHan(filter).toLowerCase();
      if( filter ) {
        //ラベルテキストから検索文字の見つからなかった場合は非表示
        $(list).find('label').filter(
          function (index) {
            var labelText = zenToHan($(this).text()).toLowerCase();
            return labelText.indexOf(filter) == -1;
          }
        ).hide();
        //ラベルテキストから検索文字の見つかった場合は表示
        $(list).find('label').filter(
          function (index) {
            var labelText = zenToHan($(this).text()).toLowerCase();
            return labelText.indexOf(filter) != -1;
          }
        ).show();
      } else {
        $(list).find('label').show();
      }
      return false;
    })
    .keyup(function() {
      $(this).change();
    });
  }

  $(function() {
    catFilter( $('#category-all'), $('#categorychecklist') );
  });
});
</script>
<?php
}
endif;
add_action( 'admin_head-post-new.php', 'add_category_filter_form' );
add_action( 'admin_head-post.php', 'add_category_filter_form' );

// ユーザープロフィールの項目のカスタマイズ
if ( !function_exists( 'user_contactmethods_custom' ) ):
function user_contactmethods_custom($wb){
  //項目の追加
  $wb['twitter_url'] = __( 'Twitter URL', THEME_NAME );
  $wb['facebook_url'] = __( 'Facebook URL', THEME_NAME );
  $wb['google_plus_url'] = __( 'Google+ URL', THEME_NAME );
  $wb['hatebu_url'] = __( 'はてブ URL', THEME_NAME );
  $wb['instagram_url'] = __( 'Instagram URL', THEME_NAME );
  $wb['pinterest_url'] = __( 'Pinterest URL', THEME_NAME );
  $wb['youtube_url'] = __( 'YouTube URL', THEME_NAME );
  $wb['flickr_url'] = __( 'Flickr URL', THEME_NAME );
  $wb['line_t_url'] = __( 'LINE@ URL', THEME_NAME );
  $wb['github_url'] = __( 'GitHub URL', THEME_NAME );

  return $wb;
}
endif;
add_filter('user_contactmethods', 'user_contactmethods_custom', 10, 1);

//ユーザーIDの取得
if ( !function_exists( 'get_the_posts_author_id' ) ):
function get_the_posts_author_id(){
  global $post_id;
  $post = get_post($post_id);
  if ($post){
    $author = get_userdata($post->post_author);
    return $author->ID;
  }
}
endif;

//プロフィール画面で設定したウェブサイトURLの取得
if ( !function_exists( 'get_the_author_website_url' ) ):
function get_the_author_website_url(){
  return get_the_author_meta('url');
}
endif;

//プロフィール画面で設定したTwitter URLの取得
if ( !function_exists( 'get_the_author_twitter_url' ) ):
function get_the_author_twitter_url(){
  return get_the_author_meta('twitter_url');
}
endif;

//プロフィール画面で設定したFacebook URLの取得
if ( !function_exists( 'get_the_author_facebook_url' ) ):
function get_the_author_facebook_url(){
  return get_the_author_meta('facebook_url');
}
endif;

//プロフィール画面で設定したGoogle+ URLの取得
if ( !function_exists( 'get_the_author_google_plus_url' ) ):
function get_the_author_google_plus_url(){
  return get_the_author_meta('google_plus_url');
}
endif;

//プロフィール画面で設定したはてブ URLの取得
if ( !function_exists( 'get_the_author_hatebu_url' ) ):
function get_the_author_hatebu_url(){
  return get_the_author_meta('hatebu_url');
}
endif;

//プロフィール画面で設定したInstagram URLの取得
if ( !function_exists( 'get_the_author_instagram_url' ) ):
function get_the_author_instagram_url(){
  return get_the_author_meta('instagram_url');
}
endif;

//プロフィール画面で設定したPinterest URLの取得
if ( !function_exists( 'get_the_author_pinterest_url' ) ):
function get_the_author_pinterest_url(){
  return get_the_author_meta('pinterest_url');
}
endif;

//プロフィール画面で設定したYouTube URLの取得
if ( !function_exists( 'get_the_author_youtube_url' ) ):
function get_the_author_youtube_url(){
  return get_the_author_meta('youtube_url');
}
endif;

//プロフィール画面で設定した立夏 URLの取得
if ( !function_exists( 'get_the_author_flickr_url' ) ):
function get_the_author_flickr_url(){
  return get_the_author_meta('flickr_url');
}
endif;

//プロフィール画面で設定したLINE@ URLの取得
if ( !function_exists( 'get_the_author_line_at_url' ) ):
function get_the_author_line_at_url(){
  return get_the_author_meta('line_at_url');
}
endif;

//プロフィール画面で設定したGitHub URLの取得
if ( !function_exists( 'get_the_author_github_url' ) ):
function get_the_author_github_url(){
  return get_the_author_meta('github_url');
}
endif;