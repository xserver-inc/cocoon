
          </main>

        <?php get_sidebar(); ?>

      </div>

    </div>

    <?php //パンくずリストがフッター手前の場合
    if (is_single() && is_single_breadcrumbs_position_footer_before()){
      get_template_part('tmp/breadcrumbs');
    } ?>

    <footer id="footer" class="footer" role="contentinfo" itemscope itemtype="http://schema.org/WPFooter">

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

        <?php //フッターの最下部（フッターメニューやクレジットなど）
        get_template_part('tmp/footer-bottom'); ?>

      </div>

    </footer>

  </div>

  <?php wp_footer(); ?>

  <?php //アクセス解析フッタータグの取得
  get_template_part('tmp/footer-analytics'); ?>

  <?php //フッター挿入用のユーザー用テンプレート
  get_template_part('tmp-user/footer-insert'); ?>

</body>

</html>
