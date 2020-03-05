<?php //リストのタイトル
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit; ?>

<h1 id="archive-title" class="archive-title"><?php echo get_archive_chapter_text(); ?></h1>
<?php if (is_search()){
  get_template_part('searchform');
} ?>
