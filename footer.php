
            </main>

          <?php get_sidebar(); ?>

        </div>

      </div>
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

          <div class="footer-bottom cf">
            <?php get_template_part('tmp/navi-footer') ?>

            <div class="source-org copyright">&copy; <?php echo date('Y'); ?> <?php bloginfo('name'); ?>.</div>
          </div>


        </div>

      </footer>

    </div>

    <?php wp_footer(); ?>

    <?php //アクセス解析フッタータグの取得
    get_template_part('tmp/analytics-footer'); ?>

  </body>

</html>
