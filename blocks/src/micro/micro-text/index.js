/**
 * Cocoon Blocks
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */

import { THEME_NAME } from '../../helpers';
import { __ } from '@wordpress/i18n';
import { Icon, textColor } from '@wordpress/icons';

import edit from './edit';
import save from './save';
import deprecated from './deprecated';
import transforms from './transforms';
import metadata from './block.json';

const { name } = metadata;
export { metadata, name };

export const settings = {
  title: __( 'マイクロテキスト', THEME_NAME ),
  icon: <Icon icon={textColor} size={32} />,
  description: __( 'コンバージョンリンク（ボタン）の直上もしくは直下に小さくテキスト表示して、コンバージョン率アップを図るためのマイクロコピーです。', THEME_NAME ),
  example: {},

  edit,
  save,
  deprecated,
  transforms,
};
