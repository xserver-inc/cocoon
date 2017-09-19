<div class="metabox-holder">

<?php
//本文上ボタン用フォーム
require_once 'sns-share-forms-top.php';
//本文下ボタン用フォーム
require_once 'sns-share-forms-bottom.php';
?>

<!-- ツイート設定 -->
<div id="sns-share" class="postbox">
  <h2 class="hndle"><?php _e( 'ツイート設定', THEME_NAME ) ?></h2>
  <div class="inside">
    <p><?php _e( 'Twitter上でのツイート動作の設定です。', THEME_NAME ) ?></p>
    <table class="form-table">
      <tbody>

        <!-- メンション -->
        <tr>
          <th scope="row">
            <label for="<?php echo OP_TWITTER_ID_INCLUDE; ?>"><?php _e( 'メンション', THEME_NAME ) ?></label>
          </th>
          <td>
             <input type="checkbox" name="<?php echo OP_TWITTER_ID_INCLUDE; ?>" value="1"<?php the_checkbox_checked(is_twitter_id_include()); ?>><?php _e("ツイートにメンションを含める",THEME_NAME ); ?>
            <p class="tips"><?php _e( 'シェアされたツイートに著者のTwitter IDを含めます。', THEME_NAME ) ?></p>
          </td>
        </tr>

        <!-- プロモーション -->
        <tr>
          <th scope="row">
            <label for="<?php echo OP_TWITTER_RELATED_FOLLOW_ENABLE; ?>"><?php _e( 'プロモーション', THEME_NAME ) ?></label>
          </th>
          <td>
             <input type="checkbox" name="<?php echo OP_TWITTER_RELATED_FOLLOW_ENABLE; ?>" value="1"<?php the_checkbox_checked(is_twitter_related_follow_enable()); ?>><?php _e("ツイート後にフォローを促す",THEME_NAME ); ?>
            <p class="tips"><?php _e( 'ツイート後に著者のフォローボタンを表示します。', THEME_NAME ) ?></p>
          </td>
        </tr>

      </tbody>
    </table>

  </div>
</div>





</div><!-- /.metabox-holder -->