<?php

/**
 * Template Name: bbPress - Create Topic
 *
 * @package bbPress
 * @subpackage Theme
 */

/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

get_header(); ?>

	<?php do_action( 'bbp_before_main_content' ); ?>

	<?php do_action( 'bbp_template_notices' ); ?>

	<?php if(is_bbpress_exist()): ?>
		<?php while ( have_posts() ) : the_post(); ?>

			<div id="bbp-new-topic" class="bbp-new-topic">
				<h1 class="entry-title"><?php the_title(); ?></h1>
				<div class="entry-content">

					<?php the_content(); ?>

					<?php bbp_get_template_part( 'form', 'topic' ); ?>

				</div>
			</div><!-- #bbp-new-topic -->

		<?php endwhile; ?>
	<?php endif; ?>

	<?php do_action( 'bbp_after_main_content' ); ?>

<?php get_footer(); ?>
