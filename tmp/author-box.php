<?php
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

$user_id     = $args['user_id'];
$image_class = $args['is_image_circle'] ? ' circle-image' : null;
?>
<div class="author-box border-element no-icon cf">
  <?php if ($args['label']): ?>
    <div class="author-widget-name">
      <?php echo esc_html($args['label']); ?>
    </div>
  <?php endif; ?>

  <figure class="author-thumb<?php echo $image_class; ?>">
    <?php echo $args['avatar']; ?>
  </figure>

  <div class="author-content">
    <div class="author-name">
      <?php
      if ($user_id) {
        echo $args['name'];
      } else {
        echo __( '未登録のユーザーさん', THEME_NAME );
      }
      ?>
    </div>

    <div class="author-description">
      <?php
      if ($args['description']) {
        echo $args['description'];
      } elseif (!$user_id) {
        if ($args['is_bp_exist']) {
          $msg  = __( '未登録のユーザーさんです。', THEME_NAME );
          $msg .= '<br>';
          $msg .= __( 'ログイン・登録はこちら→', THEME_NAME );
          $msg .= '<a href="'.wp_login_url().'">';
          $msg .= __( 'ログインページ', THEME_NAME );
          $msg .= '</a>';
          echo wpautop($msg);
        } else {//WordPressインストール初期時のユーザーID。未ログインでCocoon設定を保存していない時
          echo wpautop(__( '未登録ユーザーです。ログインして、Cocoon設定の保存ボタンを押してください。', THEME_NAME ));
        }
      } elseif ($args['is_logged_in']) {
        $edit_url = esc_url(home_url() . '/wp-admin/user-edit.php?user_id='.$user_id);
        $msg  = __( 'プロフィール内容は管理画面から変更可能です→', THEME_NAME );
        $msg .= '<a href="' . $edit_url . '">'.__( 'プロフィール設定画面', THEME_NAME ).'</a><br>';
        $msg .= __( '※このメッセージは、ログインユーザーにしか表示されません。', THEME_NAME );
        echo wpautop($msg);
      }
      ?>
    </div>

    <?php if ($user_id): ?>
      <div class="profile-follows author-follows">
        <?php
        set_query_var( '_USER_ID', $user_id );
        get_template_part_with_option('tmp/sns-follow-buttons', SF_PROFILE);
        set_query_var( '_USER_ID', null ); ?>
      </div>
    <?php endif; ?>
  </div>
</div>
