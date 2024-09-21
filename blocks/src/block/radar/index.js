/**
 * Cocoon Blocks
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */

import { THEME_NAME } from '../../helpers';
import { __ } from '@wordpress/i18n';
// import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
// import { faIdCard } from '@fortawesome/free-regular-svg-icons';

import edit from './edit';
import save from './save';
import metadata from './block.json';

const { name } = metadata;
export { metadata, name };

export const settings = {
  title: __( 'レーダーチャート', THEME_NAME ),
  icon: 'chart-line',
  description: __( '評価や能力値表示向けにレーダーチャートを表示します。', THEME_NAME ) + __( '基本的に投稿・固定ページ・カスタム投稿ページでのみ表示します。', THEME_NAME ),

  edit,
  save,
};
