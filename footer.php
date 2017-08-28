
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


          <nav id="footer-navi" class="footer-navi" role="navigation">
            <div id="footer-navi-in" class="footer-navi-in">
              <?php wp_nav_menu(
                array(
                  //'container' => false,
                  'theme_location' => 'footer-navi',
                  'depth' => 1,
                  // 'menu_class' => '',
                  // 'menu_id' => '',
                  'container' => 'div',
                  'container_class' => 'menu',
                  'fallback_cb' => false,
                  'items_wrap' => '<ul>%3$s</ul>',
                )
              ); ?>
            </div>
          </nav>

          <div class="source-org copyright">&copy; <?php echo date('Y'); ?> <?php bloginfo( 'name' ); ?>.</div>

        </div>

      </footer>

    </div>

    <?php wp_footer(); ?>

  </body>

</html>
