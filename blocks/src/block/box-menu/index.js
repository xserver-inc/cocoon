/**
 * Cocoon Blocks
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */

import { THEME_NAME } from '../../helpers';
import { __ } from '@wordpress/i18n';
// import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';  // 不要なのでコメントアウト
// import { faThLarge } from '@fortawesome/free-solid-svg-icons';  // 不要なのでコメントアウト

import edit from './edit';
import save from './save';
import metadata from './block.json';

const { name } = metadata;
export { metadata, name };

// SVGアイコンの定義
const boxMenuIcon = (
  <svg
    clipRule="evenodd"
    fillRule="evenodd"
    strokeLinejoin="round"
    strokeMiterlimit="2"
    viewBox="0 0 24 24"
    xmlns="http://www.w3.org/2000/svg"
  >
    <path
      d="m4 3c-.478 0-1 .379-1 1v16c0 .62.519 1 1 1h16c.621 0 1-.52 1-1v-16c0-.478-.379-1-1-1zm7.25 16.5h-6.75v-6.75h6.75zm8.25-6.75v6.75h-6.75v-6.75zm0-8.25v6.75h-6.75v-6.75zm-15 0h6.75v6.75h-6.75z"
      fillRule="nonzero"
    />
  </svg>
);

export const settings = {
  title: __( 'ボックスメニュー', THEME_NAME ),
  icon: boxMenuIcon,  // SVGアイコンを設定
  description: __( '登録されているボックスメニューを表示します。', THEME_NAME ),

  edit,
  save,
};
