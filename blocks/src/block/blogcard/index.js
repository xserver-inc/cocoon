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

// SVGアイコンの定義
const blogCardIcon = (
  <svg
    xmlns="http://www.w3.org/2000/svg"
    width="512"
    height="512"
    version="1.1"
    viewBox="0 0 512 512"
  >
    <defs>
      <filter
        id="filter3163"
        x="0"
        y="0"
        width="1"
        height="1"
        colorInterpolationFilters="sRGB"
      >
        <feColorMatrix id="feColorMatrix3165" type="saturate" values="0" />
      </filter>
    </defs>
    <g transform="translate(0,-540.36218)">
      <path
        id="path3169"
        d="m 469.30723,705.64157 v 181.44126 h -426.614453 v -181.44126 z m 42.66144,-30.24021 h -511.937338 v 241.92165 h 511.937338 z m -85.32289,60.48042 h -127.98433 v 15.1201 h 127.98433 z m 0,30.2402 h -127.98433 v 15.12009 h 127.98433 z m 0,30.24021 h -127.98433 v 15.12011 h 127.98433 z m -63.99216,30.24023 h -63.99217 v 15.1201 h 63.99217 z"
      />
      <path
        id="path3224"
        d="M 75.556138,729.54756 H 247.953 v 123.6031 H 75.556138 z"
      />
    </g>
  </svg>
);

export const settings = {
  title: __( 'ブログカード', THEME_NAME ),
  icon: blogCardIcon,  // SVGアイコンを設定
  description: __(
    'ブログカード表示用の入力ブロックを表示します。URLは複数入力可能です。',
    THEME_NAME
  ),

  edit,
  save,
  deprecated,
};
