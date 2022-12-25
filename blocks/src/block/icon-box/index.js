/**
 * @package Cocoon Blocks
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */

/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { Icon, warning } from '@wordpress/icons';

/**
 * Internal dependencies
 */
import deprecated from './deprecated';
import edit from './edit';
import metadata from './block.json';
import save from './save';
import transforms from './transforms';
import { THEME_NAME } from '../../helpers';

const { name } = metadata;

export { metadata, name };

export const settings = {
	title: __( 'アイコンボックス', THEME_NAME ),
	icon: <Icon icon={ warning } size={ 32 } />,
	description: __(
		'アイコンを用いて直感的にメッセージ内容を伝えるためのボックスです。',
		THEME_NAME
	),
	styles: [
		{
			name: 'information-box',
			label: __( '補足情報(i)', THEME_NAME ),
			isDefault: true,
		},
		{
			name: 'question-box',
			label: __( '補足情報(?)', THEME_NAME ),
		},
		{
			name: 'alert-box',
			label: __( '補足情報(!)', THEME_NAME ),
		},
		{
			name: 'memo-box',
			label: __( 'メモ', THEME_NAME ),
		},
		{
			name: 'comment-box',
			label: __( 'コメント', THEME_NAME ),
		},
		{
			name: 'ok-box',
			label: __( 'OK', THEME_NAME ),
		},
		{
			name: 'ng-box',
			label: __( 'NG', THEME_NAME ),
		},
		{
			name: 'good-box',
			label: __( 'GOOD', THEME_NAME ),
		},
		{
			name: 'bad-box',
			label: __( 'BAD', THEME_NAME ),
		},
		{
			name: 'profile-box',
			label: __( 'プロフィール', THEME_NAME ),
		},
	],
	edit,
	save,
	deprecated,
	transforms,
};
