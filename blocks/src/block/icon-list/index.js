/**
 * Cocoon Blocks
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */

import { THEME_NAME } from '../../helpers';
import { __ } from '@wordpress/i18n';
import { Icon, postList } from '@wordpress/icons'

import edit from './edit';
import save from './save';
import deprecated from './deprecated';
import metadata from './block.json';

const { name } = metadata;
export { metadata, name };

export const settings = {
  title: __( 'アイコンリスト', THEME_NAME ),
  icon: <Icon icon={postList} size={32} />,
  description: __( 'リストポイントにアイコンを適用した非順序リストです。', THEME_NAME ),
  example: {},

  edit,
  save,
  deprecated,
};
