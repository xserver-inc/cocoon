<?php //スキンから親テーマの定義済み関数等をオーバーライドして設定の書き換えが可能
if ( !defined( 'ABSPATH' ) ) exit;

class SkinRakuColorChanging {
    // DBに保存するためのオプション名
    const COLOR1    = 'raku_color_changing_color1';
    const COLOR2    = 'raku_color_changing_color2';
    const COLOR3    = 'raku_color_changing_color3';
    const COLOR4    = 'raku_color_changing_color4';
    const OPACITY   = 'raku_color_changing_opacity';
    const DURATION  = 'raku_color_changing_duration';
    const SIDE_TITLE      = 'raku_color_changing_side_title';
    const SIDE_OPACITY    = 'raku_color_changing_side_opacity';
    const ARTICLE_TITLE   = 'raku_color_changing_article_title';
    const ARTICLE_OPACITY = 'raku_color_changing_article_opacity';
    const ARTICLE_H3_OPACITY = 'raku_color_changing_article_h3_opacity';
    // 各色
    private $color = [
        'color1' => '',
        'color2' => '',
        'color3' => '',
        'color4' => '',
    ];
    private $opacity = [
        'bg'         => '',
        'side'       => '',
        'article'    => '',
        'article_h3' => '',
    ];
    private $duration = '';
    private $side_title = '適用する';    // 適用する / 適用しない
    private $article_title = '適用する'; // 適用する / 適用しない

    // コンストラクタ
    function __construct() {
        $this->side_title = __('適用する', THEME_NAME);
        $this->article_title = __('適用する', THEME_NAME);
        /*----------------------------------------------------
         色・フラグの保存
        ----------------------------------------------------*/
        if (is_admin()) {
            if (isset($_POST[self::COLOR1])) {
                update_theme_option(self::COLOR1);
            }
            if (isset($_POST[self::COLOR2])) {
                update_theme_option(self::COLOR2);
            }
            if (isset($_POST[self::COLOR3])) {
                update_theme_option(self::COLOR3);
            }
            if (isset($_POST[self::COLOR4])) {
                update_theme_option(self::COLOR4);
            }
            if (isset($_POST[self::OPACITY])) {
                update_theme_option(self::OPACITY);
            }
            if (isset($_POST[self::DURATION])) {
                update_theme_option(self::DURATION);
            }
            if (isset($_POST[self::SIDE_TITLE])) {
                update_theme_option(self::SIDE_TITLE);
            }
            if (isset($_POST[self::SIDE_OPACITY])) {
                update_theme_option(self::SIDE_OPACITY);
            }
            if (isset($_POST[self::ARTICLE_TITLE])) {
                update_theme_option(self::ARTICLE_TITLE);
            }
            if (isset($_POST[self::ARTICLE_OPACITY])) {
                update_theme_option(self::ARTICLE_OPACITY);
            }
            if (isset($_POST[self::ARTICLE_H3_OPACITY])) {
                update_theme_option(self::ARTICLE_H3_OPACITY);
            }
        }
        /*----------------------------------------------------
         色・フラグの取得・設定
        ----------------------------------------------------*/
        $this->color['color1'] = get_theme_option(self::COLOR1, '#ff0000');
        $this->color['color2'] = get_theme_option(self::COLOR2, '#ffa500');
        $this->color['color3'] = get_theme_option(self::COLOR3, '#0000ff');
        $this->color['color4'] = get_theme_option(self::COLOR4, '#ff00ff');
        $this->opacity['bg'] = get_theme_option(self::OPACITY, '10'); // 不透明度（パーセント）
        $this->duration = get_theme_option(self::DURATION, '60');
        $this->side_title = get_theme_option(self::SIDE_TITLE, __('適用する' , THEME_NAME));
        $this->opacity['side'] = get_theme_option(self::SIDE_OPACITY, '20'); // 不透明度（パーセント）
        $this->article_title = get_theme_option(self::ARTICLE_TITLE, __('適用する' , THEME_NAME));
        $this->opacity['article'] = get_theme_option(self::ARTICLE_OPACITY, '20'); // 不透明度（パーセント）
        $this->opacity['article_h3'] = get_theme_option(self::ARTICLE_H3_OPACITY, '50'); // 不透明度（パーセント）
        /*----------------------------------------------------
         要素追加
        ----------------------------------------------------*/
        if (!is_admin()) {
            add_action('get_template_part_tmp/body-top', function() { ?><div class="raku-changing"></div><?php });
        }
        /*----------------------------------------------------
         css追加
        ----------------------------------------------------*/
        add_action('get_template_part_tmp/css-custom', [$this, 'add_css'], 11);
        /*----------------------------------------------------
         管理画面用のみの処理
        ----------------------------------------------------*/
        add_filter('admin_footer_text', [$this, 'custom_admin_footer']);
    }
    /*----------------------------------------------------
     css追加
    ----------------------------------------------------*/
    public function add_css(){
        echo '.raku-changing {';
        echo 'background: linear-gradient(225deg, '.$this->color['color1'].', '.$this->color['color2'].', '.$this->color['color3'].', '.$this->color['color4'].');';
        echo 'background-position: 100% 0%;';
        echo 'background-size: 800% 800%;';
        echo 'opacity: '.(intval($this->opacity['bg'])/100).';';
        echo 'animation-duration: '.$this->duration.'s;';
        echo '}';
        // 記事内のタイトル
        if($this->article_title === __('適用する' , THEME_NAME)) {
            // h2
            echo '.article h2:nth-of-type(4n+1) { background: '.$this->color_code('color1', 'article').'; }';
            echo '.article h2:nth-of-type(4n+2) { background: '.$this->color_code('color2', 'article').'; }';
            echo '.article h2:nth-of-type(4n+3) { background: '.$this->color_code('color3', 'article').'; }';
            echo '.article h2:nth-of-type(4n)   { background: '.$this->color_code('color4', 'article').'; }';
            echo '.article h2.skincolor1:nth-of-type(n) { background: '.$this->color_code('color1', 'article').'; }';
            echo '.article h2.skincolor2:nth-of-type(n) { background: '.$this->color_code('color2', 'article').'; }';
            echo '.article h2.skincolor3:nth-of-type(n) { background: '.$this->color_code('color3', 'article').'; }';
            echo '.article h2.skincolor4:nth-of-type(n) { background: '.$this->color_code('color4', 'article').'; }';
            // h3
            echo '.article h3:nth-of-type(4n+1) { border-color: '.$this->color_code('color1', 'article').'; border-left-color: '.$this->color_code('color1', 'article_h3').'; }';
            echo '.article h3:nth-of-type(4n+2) { border-color: '.$this->color_code('color2', 'article').'; border-left-color: '.$this->color_code('color2', 'article_h3').'; }';
            echo '.article h3:nth-of-type(4n+3) { border-color: '.$this->color_code('color3', 'article').'; border-left-color: '.$this->color_code('color3', 'article_h3').'; }';
            echo '.article h3:nth-of-type(4n)   { border-color: '.$this->color_code('color4', 'article').'; border-left-color: '.$this->color_code('color4', 'article_h3').'; }';
            echo '.article h3.skincolor1:nth-of-type(n) { border-color: '.$this->color_code('color1', 'article').'; border-left-color: '.$this->color_code('color1', 'article_h3').'; }';
            echo '.article h3.skincolor2:nth-of-type(n) { border-color: '.$this->color_code('color2', 'article').'; border-left-color: '.$this->color_code('color2', 'article_h3').'; }';
            echo '.article h3.skincolor3:nth-of-type(n) { border-color: '.$this->color_code('color3', 'article').'; border-left-color: '.$this->color_code('color3', 'article_h3').'; }';
            echo '.article h3.skincolor4:nth-of-type(n) { border-color: '.$this->color_code('color4', 'article').'; border-left-color: '.$this->color_code('color4', 'article_h3').'; }';
            // h4, h5, h6
            echo '.article h4:nth-of-type(4n+1), .article h5:nth-of-type(4n+1), .article h6:nth-of-type(4n+1) { border-color: '.$this->color_code('color1', 'article').'; }';
            echo '.article h4:nth-of-type(4n+2), .article h5:nth-of-type(4n+2), .article h6:nth-of-type(4n+2) { border-color: '.$this->color_code('color2', 'article').'; }';
            echo '.article h4:nth-of-type(4n+3), .article h5:nth-of-type(4n+3), .article h6:nth-of-type(4n+3) { border-color: '.$this->color_code('color3', 'article').'; }';
            echo '.article h4:nth-of-type(4n),   .article h5:nth-of-type(4n),   .article h6:nth-of-type(4n)   { border-color: '.$this->color_code('color4', 'article').'; }';
            echo '.article h4.skincolor1:nth-of-type(n), .article h5.skincolor1:nth-of-type(n), .article h6.skincolor1:nth-of-type(n) { border-color: '.$this->color_code('color1', 'article').'; }';
            echo '.article h4.skincolor2:nth-of-type(n), .article h5.skincolor2:nth-of-type(n), .article h6.skincolor2:nth-of-type(n) { border-color: '.$this->color_code('color2', 'article').'; }';
            echo '.article h4.skincolor3:nth-of-type(n), .article h5.skincolor3:nth-of-type(n), .article h6.skincolor3:nth-of-type(n) { border-color: '.$this->color_code('color3', 'article').'; }';
            echo '.article h4.skincolor4:nth-of-type(n), .article h5.skincolor4:nth-of-type(n), .article h6.skincolor4:nth-of-type(n) { border-color: '.$this->color_code('color4', 'article').'; }';
        }
        // サイドバーのタイトル
        if($this->side_title === __('適用する' , THEME_NAME)) {
            // sidebar
            echo '.widget-sidebar .widget-sidebar-title.skincolor1 { background: '.$this->color_code('color1', 'side').'; }';
            echo '.widget-sidebar .widget-sidebar-title.skincolor2 { background: '.$this->color_code('color2', 'side').'; }';
            echo '.widget-sidebar .widget-sidebar-title.skincolor3 { background: '.$this->color_code('color3', 'side').'; }';
            echo '.widget-sidebar .widget-sidebar-title.skincolor4 { background: '.$this->color_code('color4', 'side').'; }';
        }
    }
    /*----------------------------------------------------
     値変換・取得
    ----------------------------------------------------*/
    public function color_code($key_color, $key_opacity) {
        $code_red   = hexdec(substr($this->color[$key_color], 1, 2));
        $code_green = hexdec(substr($this->color[$key_color], 3, 2));
        $code_blue  = hexdec(substr($this->color[$key_color], 5, 2));
        $opacity = intval($this->opacity[$key_opacity]) / 100;
        return 'rgba('.$code_red.','.$code_green.','.$code_blue.','.$opacity.')';
    }
    /*----------------------------------------------------
     タグ取得
    ----------------------------------------------------*/
    public function get_color_picker_tag($name, $value, $label){
        ob_start();
        generate_color_picker_tag($name, $value, $label);
        $content = ob_get_clean();
        return $content;
    }
    public function get_generate_number_tag($name, $value, $placeholder = '', $min = 1, $max = 100, $step = 1, $width = 100){
        ob_start();
        generate_number_tag($name, $value, $placeholder, $min, $max, $step, $width);
        $content = ob_get_clean();
        return $content;
    }
    /*----------------------------------------------------
     管理画面用のみの処理
    ----------------------------------------------------*/
    public function custom_admin_footer() {
    ?>
<script>
(function ($) {
    let tr_setting = '';
    /*----------------------------------------------------
     「スキンカラー」用HTMLを作成
    ----------------------------------------------------*/
    tr_setting += '<tr>';
    tr_setting += '<th scope="row"><?php generate_label_tag(OP_SKIN_URL, __('スキンカラー', THEME_NAME) ); ?> <?php echo str_replace("\n", "", get_select_color_tip_tag()); ?></th>';
    tr_setting += '<td><ul>';
    tr_setting += '<li><?php echo str_replace("\n", "", $this->get_color_picker_tag(self::COLOR1,  $this->color['color1'], '')); ?></li>';
    tr_setting += '<li><?php echo str_replace("\n", "", $this->get_color_picker_tag(self::COLOR2,  $this->color['color2'], '')); ?></li>';
    tr_setting += '<li><?php echo str_replace("\n", "", $this->get_color_picker_tag(self::COLOR3,  $this->color['color3'], '')); ?></li>';
    tr_setting += '<li><?php echo str_replace("\n", "", $this->get_color_picker_tag(self::COLOR4,  $this->color['color4'], '')); ?></li>';
    tr_setting += '</ul>';
    tr_setting += '<p class="tips"><span class="fa fa-info-circle" aria-hidden="true"></span> <?php _e('4色 選択してください。', THEME_NAME); ?></p>';
    tr_setting += '</td></tr>';
    /*----------------------------------------------------
     「背景アニメーション」用HTMLを作成
    ----------------------------------------------------*/
    tr_setting += '<tr>';
    tr_setting += '<th scope="row"><?php generate_label_tag(OP_SKIN_URL, __('背景アニメーション<br><small>（選択した4色がゆっくり入れ替わる）</small>', THEME_NAME) ); ?></th>';
    tr_setting += '<td>';
    tr_setting += '<p><?php _e('【一通り変化するまでの時間】', THEME_NAME); ?></p>';
    tr_setting += '<p><?php echo str_replace("\n", "", $this->get_generate_number_tag(self::DURATION, $this->duration, "", 1, 120)); ?><?php _e('秒（1～120秒）', THEME_NAME); ?></p>';
    tr_setting += '<p class="tips"><span class="fa fa-info-circle" aria-hidden="true"></span> <?php _e('時間を短くする程「早く」、長くする程「ゆっくり」変化します。', THEME_NAME); ?></p>';
    tr_setting += '<p><?php _e('【不透明度】', THEME_NAME); ?></p>';
    tr_setting += '<p><?php echo str_replace("\n", "", $this->get_generate_number_tag(self::OPACITY, $this->opacity['bg'])); ?>%（1～100%）</p>';
    tr_setting += '<p><span class="fa fa-info-circle" aria-hidden="true"></span> <?php _e('数字を小さくする程、透明になります。', THEME_NAME); ?>';
    tr_setting += '<br><?php _e('透明にする程、【Cocoon設定 > 全体】の「サイト背景色」や「サイト背景画像」がより透けて見えます。', THEME_NAME); ?>';
    tr_setting += '<br><?php _e('設定していない場合は単純に色が薄くなります。', THEME_NAME); ?></p>';
    tr_setting += '</td></tr>';
    /*----------------------------------------------------
    「記事内のタイトルに色を適応させるかどうか」用HTMLを作成
    ----------------------------------------------------*/
    tr_setting += '<tr>';
    tr_setting += '<th scope="row"><?php generate_label_tag(OP_SKIN_URL, __('記事内のタイトル<br><small>（選択した4色を順に適用する）</small>', THEME_NAME) ); ?></th>';
    tr_setting += '<td><ul>';
    <?php
    $on_off_article_title = [
        self::ARTICLE_TITLE.'1' => __('適用する' , THEME_NAME),
        self::ARTICLE_TITLE.'2' => __('適用しない' , THEME_NAME),
    ];
    foreach ($on_off_article_title as $id => $val) : ?>
    tr_setting += '<li><input type="radio" name="<?=self::ARTICLE_TITLE?>" id="<?=$id?>" value="<?=$val?>" <?php the_checkbox_checked($val, $this->article_title); ?>><label for="<?=$id?>"><?=$val?></label></li>';
    <?php endforeach; ?>
    tr_setting += '</ul>';
    tr_setting += '<p><?php _e('【不透明度（背景と線の色）】', THEME_NAME); ?></p>';
    tr_setting += '<p><?php echo str_replace("\n", "", $this->get_generate_number_tag(self::ARTICLE_OPACITY, $this->opacity['article'])); ?>%（1～100%）</p>';
    tr_setting += '<p class="tips"><span class="fa fa-info-circle" aria-hidden="true"></span> <?php _e('数字を小さくする程、色が薄くなります。', THEME_NAME); ?>';
    tr_setting += '<p><?php _e('【不透明度（H3タイトルの左側の線の色）】', THEME_NAME); ?></p>';
    tr_setting += '<p><?php echo str_replace("\n", "", $this->get_generate_number_tag(self::ARTICLE_H3_OPACITY, $this->opacity['article_h3'])); ?>%（1～100%）</p>';
    tr_setting += '<p><span class="fa fa-info-circle" aria-hidden="true"></span><?php _e(' H3タイトルの左側の線だけ色が濃い方が綺麗なため、個別に設定できます。', THEME_NAME); ?>';
    tr_setting += '</td></tr>';
    /*----------------------------------------------------
     「サイドバーのタイトルに色を適応させるかどうか」用HTMLを作成
    ----------------------------------------------------*/
    tr_setting += '<tr>';
    tr_setting += '<th scope="row"><?php generate_label_tag(OP_SKIN_URL, __('サイドバー内のタイトル<br><small>（選択した4色を順に適用する）</small>', THEME_NAME) ); ?></th>';
    tr_setting += '<td><ul>';
    <?php
    $on_off_side_title = [
        self::SIDE_TITLE.'1' => __('適用する' , THEME_NAME),
        self::SIDE_TITLE.'2' => __('適用しない' , THEME_NAME),
    ];
    foreach ($on_off_side_title as $id => $val) : ?>
    tr_setting += '<li><input type="radio" name="<?=self::SIDE_TITLE?>" id="<?=$id?>" value="<?=$val?>" <?php the_checkbox_checked($val, $this->side_title); ?>><label for="<?=$id?>"><?=$val?></label></li>';
    <?php endforeach; ?>
    tr_setting += '</ul>';
    tr_setting += '<p><?php _e('【不透明度（背景色）】', THEME_NAME); ?></p>';
    tr_setting += '<p><?php echo str_replace("\n", "", $this->get_generate_number_tag(self::SIDE_OPACITY, $this->opacity['side'])); ?>%（1～100%）</p>';
    tr_setting += '<p><span class="fa fa-info-circle" aria-hidden="true"></span> <?php _e('数字を小さくする程、色が薄くなります。', THEME_NAME); ?>';
    tr_setting += '</td></tr>';
    /*----------------------------------------------------
     追加
    ----------------------------------------------------*/
    const skin_url_list = $('#skin .form-table [name="include_skin_type"]');
    skin_url_list.closest('tr').after(tr_setting);
})(jQuery);
</script>
    <?php
    }
}

$skin_raku = new SkinRakuColorChanging();

