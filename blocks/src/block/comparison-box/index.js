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
import transforms from './transforms';
import metadata from './block.json';

const { name } = metadata;
export { metadata, name };

export const settings = {
  title: __( '比較ボックス', THEME_NAME ),
  icon: <FontAwesomeIcon icon={['fas', 'columns']} />,
  description: __( '2つの商品（サービス）の比較用の装飾です。', THEME_NAME ),
  example: {},

  edit,
  save,
  deprecated,
  transforms,
};
