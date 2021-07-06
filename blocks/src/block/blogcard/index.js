/**
 * Cocoon Blocks
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */

import { THEME_NAME } from '../../helpers';
import { __ } from '@wordpress/i18n';

import edit from './edit';
import save from './save';
import deprecated from './deprecated';
import metadata from './block.json';

const { name } = metadata;
export { metadata, name };

export const settings = {
  title: __( 'ブログカード', THEME_NAME ),
  description: __( 'ブログカード表示用の入力ブロックを表示します。URLは複数入力可能です。', THEME_NAME ),

  edit,
  save,
  deprecated,
};
