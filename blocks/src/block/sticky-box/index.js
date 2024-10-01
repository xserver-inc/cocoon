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
import transforms from './transforms';
import metadata from './block.json';

const { name } = metadata;
export { metadata, name };

// SVGアイコンの定義
const stickyNoteIcon = (
  <svg
    xmlns="http://www.w3.org/2000/svg"
    xmlnsXlink="http://www.w3.org/1999/xlink"
    id="icon"
    x="0px"
    y="0px"
    viewBox="0 0 1000 1000"
    style={{ enableBackground: "new 0 0 1000 1000" }}
    xmlSpace="preserve"
  >
    <path d="M400,360H40v320h880v-69.7c19.3-7.3,40-18.3,40-18.3V269.6C897.8,293.5,745.7,360,400,360z M80,400h100v240H80V400z M920,567.2c-39.8,17-195.1,72.8-520,72.8H220V400h180c216.5,0,410-36,520-71.3V567.2z"></path>
  </svg>
);

export const settings = {
  title: __( '付箋風ボックス', THEME_NAME ),
  icon: stickyNoteIcon,  // SVGアイコンを設定
  description: __(
    '目立つ濃いめの色で付箋風にメッセージを伝えるためのボックスです。',
    THEME_NAME
  ),

  edit,
  save,
  deprecated,
  transforms,
};
