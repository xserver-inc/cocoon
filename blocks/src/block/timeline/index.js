/**
 * Cocoon Blocks
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */

import {THEME_NAME} from '../../helpers';
import { __ } from '@wordpress/i18n';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';

import edit from './edit';
import save from './save';
import deprecated from './deprecated';
import metadata from './block.json';

const { name } = metadata;
export { metadata, name };

export const settings = {
  title: __( 'タイムライン', THEME_NAME ),
  icon: <FontAwesomeIcon icon={['far', 'clock']} />,
  description: __( '時系列を表現するためのブロックです。', THEME_NAME ),
  example: {},

  edit,
  save,
  deprecated,
};