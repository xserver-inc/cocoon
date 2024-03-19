/**
 * Cocoon Blocks
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */

/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { faFire } from '@fortawesome/free-solid-svg-icons';

/**
 * Internal dependencies
 */
import edit from './edit';
import save from './save';
import metadata from './block.json';
import { THEME_NAME } from '../../helpers';

const { name } = metadata;

export { metadata, name };

export const settings = {
  title: __( '人気記事', THEME_NAME ),
  icon: <FontAwesomeIcon icon={ faFire } />,
  description: __( '人気記事の一覧を表示します。', THEME_NAME ),

  edit,
  save,
};
