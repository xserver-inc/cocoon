/**
 * Cocoon Blocks
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */

import { THEME_NAME } from '../../helpers';
import { __ } from '@wordpress/i18n';
import { Icon, info } from '@wordpress/icons';

import edit from './edit';
import save from './save';
import deprecated from './deprecated';
import transforms from './transforms';
import metadata from './block.json';

const { name } = metadata;
export { metadata, name };

export const settings = {
  title: __( '案内ボックス', THEME_NAME ),
  icon: <Icon icon={ info } size={ 32 } />,
  description: __(
    'ボックスの背景色により、直感的にメッセージ内容を伝えるためのボックスです。',
    THEME_NAME
  ),

  edit,
  save,
  deprecated,
  transforms,
};
