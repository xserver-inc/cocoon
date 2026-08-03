/**
 * Cocoon Blocks
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */

import metadata from './block.json';
import { THEME_NAME } from '../../helpers';
import { __ } from '@wordpress/i18n';
import { lock } from '@wordpress/icons';
import edit from './edit';
import save from './save';

const { name } = metadata;

export { metadata, name };

export const settings = {
  title: __( 'ログインユーザー限定', THEME_NAME ),
  description: __(
    'ログインしているユーザーにのみコンテンツを表示します',
    THEME_NAME
  ),
  category: 'cocoon-block',
  icon: lock,
  edit,
  save,
};
