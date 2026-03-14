/**
 * Cocoon Blocks
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */

import metadata from './block.json';
import { lock } from '@wordpress/icons';
import edit from './edit';
import save from './save';

const { name } = metadata;

export { metadata, name };

export const settings = {
  title: 'ログインユーザー限定',
  description: 'ログインしているユーザーにのみコンテンツを表示します',
  category: 'cocoon-block',
  icon: lock,
  edit,
  save,
};
