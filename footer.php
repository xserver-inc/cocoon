
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
            <div id="footer-widget">
               <div class="footer-left">
               <?php if ( dynamic_sidebar('footer-left') ) : else : ?>
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


          <nav role="navigation">
            <?php wp_nav_menu(array(
              'container' => 'div',                           // enter '' to remove nav container (just make sure .footer-links in _base.scss isn't wrapping)
              'container_class' => 'footer-links cf',         // class of container (should you choose to use it)
              'menu' => __( 'Footer Links', 'bonestheme' ),   // nav name
              'menu_class' => 'nav footer-nav cf',            // adding custom nav class
              'theme_location' => 'footer-links',             // where it's located in the theme
              'before' => '',                                 // before the menu
              'after' => '',                                  // after the menu
              'link_before' => '',                            // before each link
              'link_after' => '',                             // after each link
              'depth' => 0,                                   // limit the depth of the nav
              'fallback_cb' => 'bones_footer_links_fallback'  // fallback function
            )); ?>
          </nav>

          <div class="source-org copyright">&copy; <?php echo date('Y'); ?> <?php bloginfo( 'name' ); ?>.</div>

        </div>

      </footer>

    </div>

    <?php // all js scripts are loaded in library/bones.php ?>
    <?php wp_footer(); ?>

  </body>

</html>
