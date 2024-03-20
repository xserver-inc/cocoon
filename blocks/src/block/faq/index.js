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
  title: __( 'FAQ', THEME_NAME ),
  icon: (
    <svg
      xmlns="http://www.w3.org/2000/svg"
      width="512"
      height="512"
      viewBox="0 0 512 512"
    >
      <g>
        <path d="M496,128H296V64a8,8,0,0,0-8-8H16a8,8,0,0,0-8,8V304a8,8,0,0,0,8,8H40v56a8,8,0,0,0,13.66,5.66L115.31,312H216v64a8,8,0,0,0,8,8H396.69l61.65,61.66A8,8,0,0,0,472,440V384h24a8,8,0,0,0,8-8V136A8,8,0,0,0,496,128ZM112,296a8.008,8.008,0,0,0-5.66,2.34L56,348.69V304a8,8,0,0,0-8-8H24V72H280V296Zm376,72H464a8,8,0,0,0-8,8v44.69l-50.34-50.35A8.008,8.008,0,0,0,400,368H232V312h56a8,8,0,0,0,8-8V144H488Z" />
        <path d="M341.31,311.534a8,8,0,0,0,10.224-4.843L363.924,272h40.152l12.39,34.691a8,8,0,0,0,15.068-5.382l-40-112a8,8,0,0,0-15.068,0l-40,112A8,8,0,0,0,341.31,311.534ZM384,215.786,398.362,256H369.638Z" />
        <path d="M194.19,214.876A47.708,47.708,0,0,0,200,192V160a48,48,0,0,0-96,0v32a47.976,47.976,0,0,0,80.228,35.542l10.115,10.115a8,8,0,0,0,11.314-11.314ZM152,224a32.036,32.036,0,0,1-32-32V160a32,32,0,0,1,64,0v32a31.848,31.848,0,0,1-1.882,10.8l-8.461-8.461a8,8,0,0,0-11.314,11.314l10.55,10.55A31.852,31.852,0,0,1,152,224Z" />
      </g>
    </svg>
  ),
  description: __( 'よくある質問と回答。', THEME_NAME ),

  edit,
  save,
  deprecated,
};
