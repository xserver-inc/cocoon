/**
 * Cocoon Blocks
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */

import { THEME_NAME } from '../../helpers';
import { __ } from '@wordpress/i18n';
import { Icon, calendar } from '@wordpress/icons';

import edit from './edit';
import save from './save';
import metadata from './block.json';

// ブロック名をメタデータから取得
const { name } = metadata;
export { metadata, name };

// ブロック設定
export const settings = {
  title: __( 'キャンペーン', THEME_NAME ),
  icon: <Icon icon={ calendar } size={ 32 } />,
  description: __(
    '指定した期間中のみコンテンツを表示するブロックです。開始日時と終了日時を設定できます。',
    THEME_NAME
  ),

  edit,
  save,
};
