/**
 * Cocoon Blocks
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */

import { THEME_NAME } from '../../helpers';
import { __ } from '@wordpress/i18n';
import { Icon, reusableBlock } from '@wordpress/icons'

import edit from './edit';
import save from './save';
import deprecated from './deprecated';
import metadata from './block.json';

const { name } = metadata;
export { metadata, name };

export const settings = {
  title: __( 'アコーディオン（トグル）', THEME_NAME ),
  icon: <Icon icon={reusableBlock} size={32} />,
  description: __( '旧トグルボックス。クリックすることでコンテンツ内容の表示を切り替えることができるボックスです。', THEME_NAME ),

  edit,
  save,
  deprecated,
};
