<?php
///////////////////////////////////////////////////
//Facebookページ「フォロー」ウィジェットの追加
///////////////////////////////////////////////////
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

//Facebookページ言われるか設定されている時
if ( get_the_author_facebook_url() && !is_amp() ) {
  add_action('widgets_init', function(){register_widget('FBLikeBallooneWidgetItem');});
}
if ( !class_exists( 'FBLikeBallooneWidgetItem' ) ):
class FBLikeBallooneWidgetItem extends WP_Widget {
  function __construct() {
    parent::__construct(
      'facebook_page_like',
      WIDGET_NAME_PREFIX.__( 'Facebookバルーン', THEME_NAME ), //ウィジェット名
      array('description' => __( '投稿・個別ページのアイキャッチを利用したFacebookページへの「フォロー」ボタンを表示するウィジェットです。', THEME_NAME )),
      array( 'width' => 400, 'height' => 350 )
    );
  }
  function widget($args, $instance) {
    extract( $args );
    //タイトル名を取得
    $title = apply_filters( 'widget_title_facebook_page_like', $instance['title_facebook_page_like'] );
    $title = apply_filters( 'widget_title', $title, $instance, $this->id_base );
    $text = apply_filters( 'widget_text_facebook_page_like', $instance['text_facebook_page_like'] );
    $facebook_url = !empty( $instance['facebook_url'] ) ? $instance['facebook_url'] : get_the_author_facebook_url();

    //classにwidgetと一意となるクラス名を追加する
    if ( is_singular() && $facebook_url ){ //投稿・固定ページのトップ表示
      echo $args['before_widget'];
      if ($title) {
        echo $args['before_title'].$title.$args['after_title'];//タイトルが設定されている場合は使用する
      }
      set_query_var('_FACEBOOK_PAGE_LIKE_TEXT', $text);
      set_query_var('_FACEBOOK_URL', $facebook_url);
      cocoon_template_part('tmp/fb-like-balloon');
      echo $args['after_widget'];
    }//is_singular
  }
  function update($new_instance, $old_instance) {
    $instance = $old_instance;
    $instance['title_facebook_page_like'] = strip_tags(!empty($new_instance['title_facebook_page_like']) ? $new_instance['title_facebook_page_like'] : '');
    $instance['text_facebook_page_like'] = !empty($new_instance['text_facebook_page_like']) ? $new_instance['text_facebook_page_like'] : '';
    $instance['facebook_url'] = strip_tags(!empty($new_instance['facebook_url']) ? $new_instance['facebook_url'] : '');
      return $instance;
  }
  function form($instance) {
    if(empty($instance)){//notice回避
      $instance = array(
        'title_facebook_page_like' => null,
        'text_facebook_page_like' => null,
        'facebook_url' => null,
      );
    }
    $title = esc_attr(!empty($instance['title_facebook_page_like']) ? $instance['title_facebook_page_like'] : '');
    $text = esc_attr(!empty($instance['text_facebook_page_like']) ? $instance['text_facebook_page_like'] : '');
    $facebook_url = esc_attr(!empty($instance['facebook_url']) ? $instance['facebook_url'] : '');
    ?>
    <?php //タイトル入力フォーム ?>
    <p>
      <label for="<?php echo $this->get_field_id('title_facebook_page_like'); ?>">
      <?php _e( 'タイトル', THEME_NAME ) ?>
      </label>
      <input class="widefat" id="<?php echo $this->get_field_id('title_facebook_page_like'); ?>" name="<?php echo $this->get_field_name('title_facebook_page_like'); ?>" type="text" value="<?php echo $title; ?>" />
    </p>
    <?php //テキスト入力フォーム ?>
    <p>
      <label for="<?php echo $this->get_field_id('text_facebook_page_like'); ?>">
        <?php _e( 'メッセージ', THEME_NAME ) ?>
      <?php
        if ( !$text ) {
          $text = __( 'この記事が気に入ったら最新ニュース情報を、<br><span class="bold-red">フォロー</span>してチェックしよう！', THEME_NAME );
        }?>
      </label>
      <textarea class="widefat" id="<?php echo $this->get_field_id('text_facebook_page_like'); ?>" name="<?php echo $this->get_field_name('text_facebook_page_like'); ?>" cols="20" rows="4"><?php echo $text; ?></textarea>
    </p>
    <?php //FacebookページURL ?>
    <p>
      <label for="<?php echo $this->get_field_id('facebook_url'); ?>">
        <?php _e( 'FacebookページURL', THEME_NAME ) ?>
      </label>
      <input class="widefat" id="<?php echo $this->get_field_id('facebook_url'); ?>" name="<?php echo $this->get_field_name('facebook_url'); ?>" type="text" value="<?php echo $facebook_url; ?>" placeholder="<?php _e( 'https://www.facebook.com/XXXXXXX', THEME_NAME ) ?>" /><br>
      <?php _e( '※未入力だとプロフィールページで設定されているものを利用。', THEME_NAME ) ?>
    </p>
    <?php
  }
}
endif;
