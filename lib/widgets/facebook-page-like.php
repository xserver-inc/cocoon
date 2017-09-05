<?php
///////////////////////////////////////////////////
//Facebookページ「いいね！」ウイジェットの追加
///////////////////////////////////////////////////
class FacebookPageLikeWidgetItem extends WP_Widget {
  function __construct() {
    parent::__construct(
      'facebook_page_like',
      __( '[S] Facebookページ「いいね！」', 'simplicity2' ), //ウイジェット名
      array('description' => __( '投稿・個別ページのアイキャッチを利用したFacebookページへの「いいね！」ボタンを表示するSimplicity用ウィジェットです。（※「Facebookページ」のみで利用できます。「個人ページ」で設定されている場合は利用できません）', 'simplicity2' ))
    );
  }
  function widget($args, $instance) {
    extract( $args );
    //タイトル名を取得
    $title = apply_filters( 'widget_title_facebook_page_like', $instance['title_facebook_page_like'] );
    $text = apply_filters( 'widget_text_facebook_page_like', $instance['text_facebook_page_like'] );
    global $g_facebook_page_like_text;
    $g_facebook_page_like_text = $text;
    global $g_is_facebook_page_like_widget_exist;
    $g_is_facebook_page_like_widget_exist = true;
    //Facebookスクリプトを利用するか
    global $g_facebook_sdk;
    $g_facebook_sdk = true;
    //classにwidgetと一意となるクラス名を追加する
    if ( is_singular() ): //投稿・固定ページのトップ表示
      echo $args['before_widget'];
      if ($title) {
        echo $args['before_title'].$title.$args['after_title'];//タイトルが設定されている場合は使用する
      }
      get_template_part('facebook-page-like');
      echo $args['after_widget'];
    endif;//is_singular ?>
    <?php
  }
  function update($new_instance, $old_instance) {
    $instance = $old_instance;
    $instance['title_facebook_page_like'] = strip_tags($new_instance['title_facebook_page_like']);
    $instance['text_facebook_page_like'] = $new_instance['text_facebook_page_like'];
      return $instance;
  }
  function form($instance) {
    if(empty($instance)){//notice回避
      $instance = array(
        'title_facebook_page_like' => null,
        'text_facebook_page_like' => null,
      );
    }
    $title = esc_attr($instance['title_facebook_page_like']);
    $text = esc_attr($instance['text_facebook_page_like']);
    ?>
    <?php //タイトル入力フォーム ?>
    <p>
      <label for="<?php echo $this->get_field_id('title_facebook_page_like'); ?>">
      <?php _e( 'タイトル（未入力で非表示）', 'simplicity2' ) ?>
      </label>
      <input class="widefat" id="<?php echo $this->get_field_id('title_facebook_page_like'); ?>" name="<?php echo $this->get_field_name('title_facebook_page_like'); ?>" type="text" value="<?php echo $title; ?>" />
    </p>
    <?php //テキスト入力フォーム ?>
    <p>
      <label for="<?php echo $this->get_field_id('text_facebook_page_like'); ?>">
        <?php _e( 'メッセージ', 'simplicity2' ) ?>
      <?php
        if ( !$text ) {
          $text = sprintf( __( 'この記事をお届けした<br>%sの最新ニュース情報を、<br><span class="bold-red">いいね</span>してチェックしよう！', 'simplicity2' ), get_bloginfo('name') );
        }?>
      </label>
      <textarea class="widefat" id="<?php echo $this->get_field_id('text_facebook_page_like'); ?>" name="<?php echo $this->get_field_name('text_facebook_page_like'); ?>" cols="20" rows="16"><?php echo $text; ?></textarea>
    </p>
    <?php
  }
}
if ( get_facebook_follow_id() ) {//FacebookページのIDがカスタマイザーで設定されている時
  add_action('widgets_init', create_function('', 'return register_widget("FacebookPageLikeWidgetItem");'));
}