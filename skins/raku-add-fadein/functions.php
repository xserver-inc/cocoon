<?php //スキンから親テーマの定義済み関数等をオーバーライドして設定の書き換えが可能
if ( !defined( 'ABSPATH' ) ) exit;

// ベーススキン・・・スタイルを適用するスキン。実際に表示されるスキン（当スキンはフェイドインを追加するのみ）
class SkinRaku {
    // DBに保存するためのオプション名
    const BASE_SKIN_OPTION_NAME   = 'raku_base_skin_url';
    const FADEIN_TYPE_OPTION_NAME = 'raku_fadein_type';
    // スキン
    const MYSKIN = 'raku-add-fadein';
    // ベーススキンの各種URL
    private $skin_base           = '';
    private $skin_base_keyframes = '';
    private $skin_base_js        = '';
    private $skin_base_func      = '';
    private $skin_base_csv       = '';
    private $skin_base_json      = '';
    private $skin_base_amp       = '';

    public $fadein_type;
    // コンストラクタ
    function __construct() {
        /*----------------------------------------------------
         ベーススキンの保存
        ----------------------------------------------------*/
        if (is_admin()) {
            if (isset($_POST[self::BASE_SKIN_OPTION_NAME])) {
                // スキン一覧表示（ベース）を保尊
                update_theme_option(self::BASE_SKIN_OPTION_NAME);
            }
            if (isset($_POST[self::FADEIN_TYPE_OPTION_NAME])) {
                // フェイドインタイプを保尊
                update_theme_option(self::FADEIN_TYPE_OPTION_NAME);
            }
        }
        /*----------------------------------------------------
         ベーススキンの情報取得・設定
        ----------------------------------------------------*/
        // フェイドインタイプ
        $this->fadein_type = get_theme_option(self::FADEIN_TYPE_OPTION_NAME, self::FADEIN_TYPE_OPTION_NAME.'1');
        // ベーススキンURL
        $this->skin_base = get_theme_option(self::BASE_SKIN_OPTION_NAME, '');
        // ベーススキンの各種URL
        $this->skin_base_keyframes = str_ireplace('style.css', 'keyframes.css', $this->skin_base);
        $this->skin_base_js        = str_ireplace('style.css', 'javascript.js', $this->skin_base);
        $this->skin_base_func      = str_ireplace('style.css', 'functions.php', $this->skin_base);
        $this->skin_base_csv       = str_ireplace('style.css', 'option.csv', $this->skin_base);
        $this->skin_base_json      = str_ireplace('style.css', 'option.json', $this->skin_base);
        $this->skin_base_amp       = str_ireplace('style.css', 'amp.css', $this->skin_base);
        /*----------------------------------------------------
        lib\amp.php(743)
        ----------------------------------------------------*/
        add_filter('amp_skin_css', [$this, 'amp_skin_css'], 1);
        add_filter('amp_skin_amp_css', [$this, 'amp_skin_amp_css'], 1);
        /*----------------------------------------------------
        lib\amp.php(825)
        ----------------------------------------------------*/
        add_filter('amp_skin_keyframes_css', [$this, 'amp_skin_keyframes_css'], 1);
        /*----------------------------------------------------
        lib\page-settings\skin-funcs.php(21)
        ----------------------------------------------------*/
        add_action('wp_enqueue_scripts', [$this, 'wp_enqueue_scripts_custom'], 2 );
        /*----------------------------------------------------
        lib\page-settings\skin-funcs.php(28)
        ----------------------------------------------------*/
        //スキン用のfunctions.phpがある場合
        $php_file_path = url_to_local($this->skin_base_func);
        if (file_exists($php_file_path)) {
            require_once $php_file_path;
        }
        /*----------------------------------------------------
        lib\page-settings\skin-funcs.php(35,42)
        ----------------------------------------------------*/
        $this->update_theme_option_by_csv_json();
        /*----------------------------------------------------
        lib\settings.php(134,136,138)
        ----------------------------------------------------*/
        add_filter('cocoon_gutenberg_stylesheets', [$this, 'cocoon_gutenberg_stylesheets'], 1);
        /*----------------------------------------------------
        lib\settings.php(197,199,201)
        ----------------------------------------------------*/
        ///////////////////////////////////////
        // GutenbergのCSSの読み込み順を変更する
        ///////////////////////////////////////
        add_action('enqueue_block_editor_assets', [$this, 'gutenberg_stylesheets_custom'], 11);
        /*----------------------------------------------------
        lib\utils.php(307,316,329,978)
        ----------------------------------------------------*/
        add_action('wp_enqueue_scripts_before_skin_style', [$this, 'wp_enqueue_style_theme_skin_style_keyframes'], 1 );
        /*----------------------------------------------------
         管理画面用のみの処理
        ----------------------------------------------------*/
        add_filter('admin_footer_text', [$this, 'custom_admin_footer']);
        /*----------------------------------------------------
        フェイドイン処理
        ----------------------------------------------------*/
        add_filter('body_class', [$this, 'add_body_class']);
    }
    /*----------------------------------------------------
    lib\amp.php(743)
    ----------------------------------------------------*/
    public function amp_skin_css ($css_all) {
        //通常のスキンスタイル
        $skin_css = css_url_to_css_minify_code($this->skin_base);
        if ($skin_css !== false) {
            $css_all .= $skin_css;
        }
        return $css_all;
    }
    public function amp_skin_amp_css ($css_all) {
        //AMPのスキンスタイル
        $amp_css = css_url_to_css_minify_code($this->skin_base_amp);
        if ($amp_css !== false) {
            $css_all .= $amp_css;
        }
        return $css_all;
    }
    /*----------------------------------------------------
    lib\amp.php(825)
    ----------------------------------------------------*/
    public function amp_skin_keyframes_css ($css_all) {
        //通常のスキンスタイル
        $skin_keyframes_css = css_url_to_css_minify_code($this->skin_base_keyframes);
        if ($skin_keyframes_css !== false) {
            $css_all .= $skin_keyframes_css;
        }
        return $css_all;
    }
    /*----------------------------------------------------
    lib\page-settings\skin-funcs.php(21)
    ----------------------------------------------------*/
    public function wp_enqueue_scripts_custom() {
        $js_path = url_to_local($this->skin_base_js);
        //javascript.jsファイルがスキンフォルダに存在する場合
        if ($this->skin_base_js && file_exists($js_path)) {
            wp_enqueue_script( THEME_SKIN_JS . '-base', $this->skin_base_js, array( 'jquery', THEME_JS ), false, true );
        }
    }
    /*----------------------------------------------------
    lib\page-settings\skin-funcs.php(35,42)
    ----------------------------------------------------*/
    private function update_theme_option_by_csv_json() {
        global $_THEME_OPTIONS;
        /*----------------------------------------------------
         35
        ----------------------------------------------------*/
        //スキン用のoption.csvがある場合
        $csv_file_path = url_to_local($this->skin_base_csv);
        if (file_exists($csv_file_path)) {
            $csv_file = new SplFileObject($csv_file_path);
            $csv_file->setFlags(SplFileObject::READ_CSV);
            foreach ($csv_file as $line) {
                //終端の空行を除く処理　空行の場合に取れる値は後述
                if(isset($line[0]) && isset($line[1])){
                    $name = trim($line[0]);
                    $value = trim($line[1]);
                    $_THEME_OPTIONS[$name] = $value;
                }
            }
        }
        /*----------------------------------------------------
         42
        ----------------------------------------------------*/
        //スキン用のoption.jsonがある場合
        $json_file_path = url_to_local($this->skin_base_json);
        if (file_exists($json_file_path)) {
            $json = wp_filesystem_get_contents($json_file_path);
            if ($json) {
                $json_options = json_decode($json, true);
                $_THEME_OPTIONS = array_merge($_THEME_OPTIONS, $json_options);
            }
        }
    }
    /*----------------------------------------------------
    lib\settings.php(134,136,138)
    ----------------------------------------------------*/
    public function cocoon_gutenberg_stylesheets($stylesheets) {
        if (is_visual_editor_style_enable()) {
            //スキンが設定されている場合
            if (//エディター除外スキンの場合
                !is_exclude_skin($this->skin_base, get_editor_exclude_skins())) {
                array_push($stylesheets,
                    add_file_ver_to_css_js($this->skin_base)
                );
            }
        }
        return $stylesheets;
    }
    /*----------------------------------------------------
    lib\settings.php(197,199,201)
    ----------------------------------------------------*/
    public function gutenberg_stylesheets_custom() {
        if ( is_visual_editor_style_enable() ) {
            //WordPressバージョンが5.8以上の時
            if (is_wp_5_8_or_over()) {
                //エディター除外スキンではない場合
                if (!is_exclude_skin($this->skin_base, get_editor_exclude_skins())) {
                    wp_enqueue_style( THEME_NAME . '-base-skin-style', $this->skin_base );
                }
            }
        }
    }
    /*----------------------------------------------------
    lib\utils.php(307,316,329,978)
    ----------------------------------------------------*/
    //スキンスタイルの読み込み
    public function wp_enqueue_style_theme_skin_style_keyframes(){
        /*----------------------------------------------------
         307
        ----------------------------------------------------*/
        // wp_enqueue_style( THEME_NAME.'-base-skin-style', $this->skin_base );
        wp_enqueue_style( THEME_NAME.'-base-skin-style', $this->skin_base, array() );
        /*----------------------------------------------------
         316
        ----------------------------------------------------*/
        $skin_keyframes_url = $this->get_theme_skin_keyframes_url();
        if ($skin_keyframes_url) {
            // wp_enqueue_style( THEME_NAME.'-base-skin-keyframes', $skin_keyframes_url );
            wp_enqueue_style( THEME_NAME.'-base-skin-keyframes', $skin_keyframes_url, array( THEME_NAME.'-keyframes' ) );
        }
        /*----------------------------------------------------
         978
         ※ハンドル名が異なるだけで中身は変わらないので、対応する必要はないと判断
           また、get_template_part('tmp/css-custom');が「require_once」によって1度目と2度目で内容が異なるやめておく）
        ----------------------------------------------------*/
        /*
        //設定変更CSSを読み込む
        ob_start();//バッファリング
        get_template_part('tmp/css-custom');
        $css_custom = ob_get_clean();
        //CSSの縮小化
        $css_custom = minify_css($css_custom);
        //HTMLにインラインでスタイルを書く
        //スキンがある場合
        $skin_keyframes_url = $this->get_theme_skin_keyframes_url();
        if ($skin_keyframes_url) {
            wp_add_inline_style( THEME_NAME.'-base-skin-keyframes', $css_custom );
        }
        */
    }

    //スキンのkeyframes.css URLを取得
    private function get_theme_skin_keyframes_url(){
        $keyframes_url = str_replace('style.css', 'keyframes.css', $this->skin_base);
        $keyframes_file = url_to_local($keyframes_url);
        if (file_exists($keyframes_file)) {
            return $keyframes_url;
        } else {
            return ;
        }
    }

    /*----------------------------------------------------
     管理画面用のみの処理
    ----------------------------------------------------*/
    public function custom_admin_footer() {
        /*----------------------------------------------------
         スキン情報
        ----------------------------------------------------*/
    ?>
<script>
(function ($) {
    $(document).ready(function() {
        const skin_url_list = $('#skin .form-table [name="skin_url"]');
        /*----------------------------------------------------
         追加する「フェイドイン」用HTMLを作成
        ----------------------------------------------------*/
        let tr_fadein = '<tr>';
        tr_fadein += '<th scope="row"><?php generate_label_tag(OP_SKIN_URL, __('ふわっとタイプ', THEME_NAME) ); ?></th>';
        tr_fadein += '<td><ul>';
        <?php
        $fadein_type = [
            self::FADEIN_TYPE_OPTION_NAME.'1' => __('１．ふわっと' , THEME_NAME),
            self::FADEIN_TYPE_OPTION_NAME.'2' => __('２．下からふわっと' , THEME_NAME),
            self::FADEIN_TYPE_OPTION_NAME.'3' => __('３．メインは左から、サイドは右からふわっと' , THEME_NAME),
            self::FADEIN_TYPE_OPTION_NAME.'4' => __('４．ヘッダー部分は上から、メインは左から、サイドは右からふわっと' , THEME_NAME),
        ];
        foreach ($fadein_type as $id => $type) : ?>
        tr_fadein += '<li><input type="radio" name="<?=self::FADEIN_TYPE_OPTION_NAME?>" id="<?=$id?>" value="<?=$id?>" <?php the_checkbox_checked($id, $this->fadein_type); ?>><label for="<?=$id?>"><?=$type?></label></li>';
        <?php endforeach; ?>
        tr_fadein += '</ul>';
        tr_fadein += '<p class="tips"><span class="fa fa-info-circle" aria-hidden="true"></span> <?php _e('ふわっとのタイプを選択してください。', THEME_NAME); ?><br><?php _e('（詳しくは実際にご確認ください）', THEME_NAME); ?></p>';
        tr_fadein += '</td></tr>';
        // 追加
        skin_url_list.closest('tr').after(tr_fadein);
        /*----------------------------------------------------
         追加する「スキン一覧表示（ベース）」用HTMLを作成
        ----------------------------------------------------*/
        let myskin_name = '';
        let tr_base_skin = '<tr>';
        tr_base_skin += '<th scope="row"><?php generate_label_tag(OP_SKIN_URL, __('スキン一覧表示（ベース）', THEME_NAME) ); ?></th>';
        tr_base_skin += '<td><select id="<?=self::BASE_SKIN_OPTION_NAME?>" name="<?=self::BASE_SKIN_OPTION_NAME?>" style="margin-bottom: 1em;">';
        // skin_urlのリストからtdを作成
        skin_url_list.each(function() {
            const li = $(this).closest('li');
            const skin_tooltip = li.find('.tooltip').eq(0).text(); // スキン概要
            const skin_label   = li.find('label').eq(0).text();    // スキンラベル
            const skin_url     = $(this).val();                    // スキンURL
            const skin_label2  = skin_label.replace(new RegExp(skin_tooltip,'g'), ''); // スキンラベルから概要を除外
            if (skin_url.indexOf('/<?=self::MYSKIN?>/') !== -1) {
                // 当スキン（名前を避けておく）
                myskin_name = skin_label2;
            } else {
                // その他のスキン
                if (skin_url === '') {
                    tr_base_skin += '<option value=""><?php _e('なし', THEME_NAME); ?></option>';
                } else {
                    let selected = '';
                    if (skin_url === '<?=$this->skin_base?>') {
                        selected = 'selected';
                    }
                    tr_base_skin += '<option '+selected+' value="'+skin_url+'">'+skin_label2+'</option>';
                }
            }
        });
        tr_base_skin += '</select>';
        tr_base_skin += '<p class="tips"><span class="fa fa-info-circle" aria-hidden="true"></span>'+'<?php _e('スキンを選択してください。', THEME_NAME); ?>'+'<?php _e('こちらで選択したスキンが表示されます。', THEME_NAME); ?>';
        tr_base_skin += '<br><?php echo sprintf(__('※現在選択中の【%s】は動作を追加するものです', THEME_NAME), "'+myskin_name+'"); ?></p>';
        tr_base_skin += '</td></tr>';
        // 追加
        skin_url_list.closest('tr').after(tr_base_skin);
    });
})(jQuery);
</script>
    <?php
    }
    /*----------------------------------------------------
    フェイドイン処理
    ----------------------------------------------------*/
    function add_body_class($classes){
        $classes[] = $this->fadein_type;
        return $classes;
    }
}

$skin_raku = new SkinRaku();

