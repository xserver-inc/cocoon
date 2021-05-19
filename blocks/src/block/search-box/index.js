/**
 * Cocoon Blocks
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */

import { THEME_NAME } from '../../helpers';
import { __ } from '@wordpress/i18n';
import { Icon, search } from '@wordpress/icons';

import edit from './edit';
import save from './save';
import deprecated from './deprecated';
import metadata from './block.json';

const { name } = metadata;
export { metadata, name };

export const settings = {
  title: __( '検索案内', THEME_NAME ),
  icon: <Icon icon={search} size={32} />,
  description: __( '訪問者に検索を促すためのボックスです。検索をクリックすることで検索結果へ跳びます（※AMPページ以外）。', THEME_NAME ),
  example: {},

  edit,
  save,
  deprecated,
};
