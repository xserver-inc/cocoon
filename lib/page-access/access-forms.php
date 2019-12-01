<?php //アクセス集計フォーム
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit; ?>

<div class="metabox-holder">

<!-- アクセス集計 -->
<div id="speed-up" class="postbox">
  <h2 class="hndle"><?php _e( 'アクセス集計', THEME_NAME ) ?></h2>
  <div class="inside">

    <p><?php _e( 'テーマ独自のアクセス集計機能の設定です。', THEME_NAME );
    echo get_help_page_tag('https://wp-cocoon.com/access-aggregate/'); ?></p>

    <table class="form-table">
      <tbody>

        <!-- アクセス集計  -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_ACCESS_COUNT_ENABLE, __( 'アクセス集計', THEME_NAME ) ); ?>
          </th>
          <td>
            <?php
            generate_checkbox_tag(OP_ACCESS_COUNT_ENABLE , is_access_count_enable(), __( 'アクセス集計の有効化', THEME_NAME ));
            generate_tips_tag(__( '有効にすることで「人気記事」ウィジェット等が利用できるようになります。簡易的なWordPress Popular Postsプラグインを実装しているようなものなのでレンタルサーバーから負荷が高いといわれたら無効にしてください。', THEME_NAME ));
            ?>
          </td>
        </tr>

        <!-- キャッシュ -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_ACCESS_COUNT_CACHE_ENABLE, __('キャッシュ', THEME_NAME) ); ?>
          </th>
          <td>
            <?php
            generate_checkbox_tag(OP_ACCESS_COUNT_CACHE_ENABLE , is_access_count_cache_enable(), __( 'アクセスキャッシュの有効化', THEME_NAME ));
            generate_tips_tag(__( 'アクセス統計情報の取得は多少なりともサーバーに負荷をかけるのでキャッシュの利用をおすすめします。', THEME_NAME ));
            ?>
          </td>
        </tr>

        <!-- キャッシュ更新間隔 -->
        <tr>
          <th scope="row">
            <?php generate_label_tag(OP_ACCESS_COUNT_CACHE_INTERVAL, __('キャッシュ更新間隔', THEME_NAME) ); ?>
          </th>
          <td>
            <?php
            $options = array(
              '10' => __( '10分', THEME_NAME ),
              '20' => __( '20分', THEME_NAME ),
              '30' => __( '30分', THEME_NAME ),
              '45' => __( '45分', THEME_NAME ),
              '60' => __( '1時間', THEME_NAME ),
              '90' => __( '1時間半', THEME_NAME ),
              '120' => __( '2時間', THEME_NAME ),
              '180' => __( '3時間', THEME_NAME ),
              '360' => __( '6時間', THEME_NAME ),
              '540' => __( '9時間', THEME_NAME ),
              '720' => __( '12時間', THEME_NAME ),
              '1080' => __( '18時間', THEME_NAME ),
              '1440' => __( '24時間', THEME_NAME ),
              '2880' => __( '2日', THEME_NAME ),
              '4320' => __( '3日', THEME_NAME ),
              '10080' => __( '1週間', THEME_NAME ),
            );
            generate_selectbox_tag(OP_ACCESS_COUNT_CACHE_INTERVAL, $options, get_access_count_cache_interval());
            generate_tips_tag(__( '設定間隔ごとに新しいキャッシュを生成します', THEME_NAME ));
            ?>
          </td>
        </tr>


      </tbody>
    </table>

  </div>
</div>



</div><!-- /.metabox-holder -->
