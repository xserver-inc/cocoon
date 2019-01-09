<?php //バックアップフォーム
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit; ?>

<div class="metabox-holder">

<!-- キャッシュ削除 -->
<div id="cache" class="postbox">
  <h2 class="hndle"><?php _e( 'キャッシュ削除', THEME_NAME ) ?></h2>
  <div class="inside">

    <p><?php _e( 'テーマで利用されている各種キャッシュを削除します。', THEME_NAME ) ?></p>

    <table class="form-table">
      <tbody>

        <!-- すべて  -->
        <tr>
          <th scope="row">
            <?php generate_label_tag('', __( 'すべて', THEME_NAME ) ); ?>
          </th>
          <td>
            <a href="<?php echo add_query_arg(array('cache' => 'all_theme_caches', HIDDEN_DELETE_FIELD_NAME => wp_create_nonce('delete-cache'))); ?>" class="button"<?php echo ONCLICK_DELETE_CONFIRM; ?>><?php _e( '全てのキャッシュの削除', THEME_NAME ) ?></a>
            <?php
              generate_tips_tag(__( 'テーマで利用されているすべてのキャッシュを削除します。', THEME_NAME ));
            ?>
          </td>
        </tr>

        <!-- SNSキャッシュ  -->
        <tr>
          <th scope="row">
            <?php generate_label_tag('', __( 'SNSキャッシュ', THEME_NAME ) ); ?>
          </th>
          <td>
            <a href="<?php echo add_query_arg(array('cache' => 'sns_count_caches', HIDDEN_DELETE_FIELD_NAME => wp_create_nonce('delete-cache'))); ?>" class="button"<?php echo ONCLICK_DELETE_CONFIRM; ?>><?php _e( 'SNSキャッシュの削除', THEME_NAME ) ?></a>
            <?php
              generate_tips_tag(__( 'SNSカウントのキャッシュを削除します。', THEME_NAME ));
            ?>
          </td>
        </tr>

        <!-- 人気記事ウィジェット  -->
        <tr>
          <th scope="row">
            <?php generate_label_tag('', __( '人気記事ウィジェット', THEME_NAME ) ); ?>
          </th>
          <td>
            <a href="<?php echo add_query_arg(array('cache' => 'popular_entries_caches', HIDDEN_DELETE_FIELD_NAME => wp_create_nonce('delete-cache'))); ?>" class="button"<?php echo ONCLICK_DELETE_CONFIRM; ?>><?php _e( '人気記事ウィジェットキャッシュの削除', THEME_NAME ) ?></a>
            <?php
              generate_tips_tag(__( '人気記事ウィジェットのランキング結果キャッシュを削除します。', THEME_NAME ));
            ?>
          </td>
        </tr>

        <!-- ブログカード  -->
        <tr>
          <th scope="row">
            <?php generate_label_tag('', __( 'ブログカード', THEME_NAME ) ); ?>
          </th>
          <td>
            <a href="<?php echo add_query_arg(array('cache' => 'blogcard_caches', HIDDEN_DELETE_FIELD_NAME => wp_create_nonce('delete-cache'))); ?>" class="button"<?php echo ONCLICK_DELETE_CONFIRM; ?>><?php _e( 'ブログカードキャッシュの削除', THEME_NAME ) ?></a>
            <?php
              generate_tips_tag(__( '外部ブログカードのOGP情報キャッシュを削除します。', THEME_NAME ));
            ?>
          </td>
        </tr>

        <!-- AMP  -->
        <tr>
          <th scope="row">
            <?php generate_label_tag('', __( 'AMP', THEME_NAME ) ); ?>
          </th>
          <td>
            <a href="<?php echo add_query_arg(array('cache' => 'amp_caches', 'ampid' => null, HIDDEN_DELETE_FIELD_NAME => wp_create_nonce('delete-cache'))); ?>" class="button"<?php echo ONCLICK_DELETE_CONFIRM; ?>><?php _e( 'AMPキャッシュの削除', THEME_NAME ) ?></a>
            <?php
              generate_tips_tag(__( 'AMPページの全キャッシュを削除します。', THEME_NAME ));
            ?>
          </td>
        </tr>

        <!-- Amazon API  -->
        <tr>
          <th scope="row">
            <?php generate_label_tag('', __( 'Amazon API', THEME_NAME ) ); ?>
          </th>
          <td>
            <a href="<?php echo add_query_arg(array('cache' => 'amazon_api_caches', 'asin' => null, 'id' => null, 'ampid' => null, HIDDEN_DELETE_FIELD_NAME => wp_create_nonce('delete-cache'))); ?>" class="button"<?php echo ONCLICK_DELETE_CONFIRM; ?>><?php _e( 'Amazon APIキャッシュの削除', THEME_NAME ) ?></a>
            <?php
              generate_tips_tag(__( 'Amazonの商品情報全キャッシュを削除します。全てのキャッシュを削除すると、Amazon APIのレスポンスが追いつかない可能性があります。キャッシュが生成されるまでは商品リンクが正常表示しない可能性があるのでお勧めはしません。ただ、時間が経てばいずれ正常表示されます。', THEME_NAME ).get_help_page_tag('https://wp-cocoon.com/amazon-api-cache/'));
            ?>
          </td>
        </tr>

        <!-- 楽天API  -->
        <tr>
          <th scope="row">
            <?php generate_label_tag('', __( '楽天 API', THEME_NAME ) ); ?>
          </th>
          <td>
            <a href="<?php echo add_query_arg(array('cache' => 'rakuten_api_caches', 'asin' => null, 'id' => null, 'ampid' => null, HIDDEN_DELETE_FIELD_NAME => wp_create_nonce('delete-cache'))); ?>" class="button"<?php echo ONCLICK_DELETE_CONFIRM; ?>><?php _e( '楽天APIキャッシュの削除', THEME_NAME ) ?></a>
            <?php
              generate_tips_tag(__( '楽天商品情報の全キャッシュを削除します。全てのキャッシュを削除すると、楽天APIのレスポンスが追いつかない可能性があります。キャッシュが生成されるまでは商品リンクが正常表示しない可能性があるのでお勧めはしません。ただ、時間が経てばいずれ正常表示されます。', THEME_NAME ));
            ?>
          </td>
        </tr>

      </tbody>
    </table>

  </div>
</div>

</div><!-- /.metabox-holder -->
