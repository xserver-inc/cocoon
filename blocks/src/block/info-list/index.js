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
import { faFileAlt } from '@fortawesome/free-regular-svg-icons';

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
  title: __( '新着情報', THEME_NAME ),
  icon: <FontAwesomeIcon icon={ faFileAlt } />,
  description: __( '新着情報の一覧を表示します。', THEME_NAME ),

  edit,
  save,
};
