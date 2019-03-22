/**
 * Cocoon Blocks
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */

const { __ } = wp.i18n;
const { registerFormatType } = wp.richText;
const THEME_NAME = 'cocoon';
const FORMAT_TYPE_NAME = 'cocoon-blocks/badge-green';

registerFormatType( FORMAT_TYPE_NAME, {
  title: __( '緑色', THEME_NAME ),
  tagName: 'span',
  className: 'badge-green',
} );