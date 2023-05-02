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

export const settings = {
  title: __( 'タイムライン', THEME_NAME ),
  icon: (
    <svg
      id="Capa_1"
      enable-background="new 0 0 443.294 443.294"
      height="512"
      viewBox="0 0 443.294 443.294"
      width="512"
      xmlns="http://www.w3.org/2000/svg"
    >
      <path d="m221.647 0c-122.214 0-221.647 99.433-221.647 221.647s99.433 221.647 221.647 221.647 221.647-99.433 221.647-221.647-99.433-221.647-221.647-221.647zm0 415.588c-106.941 0-193.941-87-193.941-193.941s87-193.941 193.941-193.941 193.941 87 193.941 193.941-87 193.941-193.941 193.941z" />
      <path d="m235.5 83.118h-27.706v144.265l87.176 87.176 19.589-19.589-79.059-79.059z" />
    </svg>
  ),
  description: __( '時系列を表現するためのブロックです。', THEME_NAME ),

  edit,
  save,
  deprecated,
};
