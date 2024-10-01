/**
 * Cocoon Blocks
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */

import { THEME_NAME } from '../../helpers';
import { __ } from '@wordpress/i18n';
// import { Icon, file } from '@wordpress/icons';

import edit from './edit';
import save from './save';
import deprecated from './deprecated';
import transforms from './transforms';
import metadata from './block.json';

const { name } = metadata;
export { metadata, name };

// SVGアイコンの定義
const tabIcon = (
  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
    <path d="M21,3H3A2,2 0 0,0 1,5V19A2,2 0 0,0 3,21H21A2,2 0 0,0 23,19V5A2,2 0 0,0 21,3M21,19H3V5H13V9H21V19Z" />
  </svg>
);

export const settings = {
  title: __( 'タブ', THEME_NAME ),
  icon: tabIcon,  // SVGアイコンを設定
  description: __(
    '表示する内容をタブで切り替えることができるボックスです。',
    THEME_NAME
  ),

  edit,
  save,
  deprecated,
  transforms,
};
