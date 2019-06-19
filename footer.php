<?php
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit; ?>
          </main>

        <?php get_sidebar(); ?>

      </div>

    </div>

    <?php
    ////////////////////////////
    //コンテンツ下部ウィジェット
    ////////////////////////////
    if ( is_active_sidebar( 'content-bottom' ) ) : ?>
    <div id="content-bottom" class="content-bottom">
      <div id="content-bottom-in" class="content-bottom-in wrap">
        <?php dynamic_sidebar( 'content-bottom' ); ?>
      </div>
    </div>
    <?php endif; ?>

    <?php //投稿パンくずリストがフッター手前の場合
    if (is_single() && is_single_breadcrumbs_position_footer_before()){
      get_template_part('tmp/breadcrumbs');
    } ?>

    <?php //固定ページパンくずリストがフッター手前の場合
    if (is_page() && is_page_breadcrumbs_position_footer_before()){
      get_template_part('tmp/breadcrumbs-page');
    } ?>

    <footer id="footer" class="footer footer-container nwa" itemscope itemtype="https://schema.org/WPFooter">

      <div id="footer-in" class="footer-in wrap cf">

        <?php //フッターにウィジェットが一つも入っていない時とモバイルの時は表示しない
        if ( (is_active_sidebar('footer-left') ||
          is_active_sidebar('footer-center') ||
          is_active_sidebar('footer-right') )  ): ?>
          <div class="footer-widgets cf">
             <div class="footer-left">
             <?php if ( function_exists('dynamic_sidebar') && dynamic_sidebar('footer-left') ) : else : ?>
             <?php endif; ?>
             </div>
             <div class="footer-center">
             <?php if ( function_exists('dynamic_sidebar') && dynamic_sidebar('footer-center') ) : else : ?>
             <?php endif; ?>
             </div>
             <div class="footer-right">
             <?php if ( function_exists('dynamic_sidebar') && dynamic_sidebar('footer-right') ) : else : ?>
             <?php endif; ?>
             </div>
          </div>
        <?php endif; ?>

        <?php //モバイルウィジェット
        if (is_active_sidebar('footer-mobile')): ?>
          <div class="footer-widgets-mobile cf">
             <div class="footer-mobile">
             <?php dynamic_sidebar('footer-mobile'); ?>
             </div>
          </div>
        <?php endif ?>

        <?php //フッターの最下部（フッターメニューやクレジットなど）
        get_template_part('tmp/footer-bottom'); ?>

      </div>

    </footer>

  </div>

  <?php //トップへ戻るボタンテンプレート
  get_template_part('tmp/button-go-to-top'); ?>

  <?php //管理者用パネル
  get_template_part('tmp/admin-panel'); ?>

  <?php //モバイルヘッダーメニューボタン
  get_template_part('tmp/mobile-header-menu-buttons'); ?>

  <?php //モバイルフッターメニューボタン
  get_template_part('tmp/mobile-footer-menu-buttons'); ?>

  <?php if (!is_amp()) wp_footer(); ?>

  <?php //フッターで読み込むJavaScript用テンプレート
  get_template_part('tmp/footer-javascript');?>

  <?php //カスタムフィールドの挿入（カスタムフィールド名：footer_custom）
  get_template_part('tmp/footer-custom-field');?>

  <?php //アクセス解析フッタータグの取得
  get_template_part('tmp/footer-analytics'); ?>

  <?php //フッター挿入用のユーザー用テンプレート
  if (is_amp()) {
    //AMP用のフッターアクションフック
    do_action( 'wp_amp_footer_insert_open' );
    //親テーマのAMPフッター用
    get_template_part('tmp/amp-footer-insert');
    //子テーマのAMPフッター用
    get_template_part('tmp-user/amp-footer-insert');
  } else {
    //フッター用のアクションフック
    do_action( 'wp_footer_insert_open' );
    //フッター用のテンプレート
    get_template_part('tmp-user/footer-insert');
  }
  ?>

</body>

</html>
