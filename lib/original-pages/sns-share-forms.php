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
            <?php genelate_label_tag(OP_TWITTER_ID_INCLUDE, __( 'メンション', THEME_NAME )); ?>
          </th>
          <td>
            <?php
            genelate_checkbox_tag( OP_TWITTER_ID_INCLUDE, is_twitter_id_include(), __( 'ツイートにメンションを含める', THEME_NAME ));
            genelate_tips_tag(__( 'シェアされたツイートに著者のTwitter', THEME_NAME ));
            ?>
          </td>
        </tr>

        <!-- プロモーション -->
        <tr>
          <th scope="row">
            <?php genelate_label_tag(OP_TWITTER_RELATED_FOLLOW_ENABLE, __( 'プロモーション', THEME_NAME )); ?>
          </th>
          <td>
            <?php
            genelate_checkbox_tag( OP_TWITTER_RELATED_FOLLOW_ENABLE, is_twitter_related_follow_enable(), __( 'ツイート後にフォローを促す', THEME_NAME ));
            genelate_tips_tag(__( 'ツイート後に著者のフォローボタンを表示します。', THEME_NAME ));
            ?>
          </td>
        </tr>

      </tbody>
    </table>

  </div>
</div>





</div><!-- /.metabox-holder -->