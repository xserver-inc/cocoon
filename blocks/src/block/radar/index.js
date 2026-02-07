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

const radarChartIcon = (
<svg version="1.1" id="_x34_" xmlns="http://www.w3.org/2000/svg" xmlnsXlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 512 487" style={{ width: '512px', height: '487px' }} xmlSpace="preserve"><style type="text/css">{`.st0 { opacity: 0.8; fill: #BCBCBC; }.st1 { opacity: 0.8; fill: #B4B2B4; }.st2 { opacity: 0.6; fill: #E6294B; }`}</style><g><g><path className="st0" d="M507.2,182.6L260.6,3.3L256,0l-4.5,3.3L4.8,182.5L0,186l1.6,5l94.1,289.4l2.2,6.6h316.4l2.1-6.5l94-289.4l1.7-5L507.2,182.6z M492.8,196.8l-87.3,268.3l-2.2,6.8H108.7l-2.2-6.9L19.3,196.8l-1.6-5l4.8-3.5l229-166.4l4.5-3.3l4.6,3.3l229,166.4l4.8,3.5L492.8,196.8z" style={{ fill: 'rgb(188, 188, 188)' }}></path><path className="st0" d="M256,69.9l-4.5,3.3l-178,129.3l0.9,2.6l3.4,10.6l64.6,198.5l0.9,2.8h225.5l0.9-2.7l64.5-198.6l3.4-10.6l0.9-2.6L260.6,73.2L256,69.9z M420,210.9l-3.4,10.6L358.9,399l-1,3H154.2l-1-3L95.5,221.5l-3.4-10.6l-0.9-2.6L251.5,91.8l4.5-3.3l4.6,3.3l160.3,116.5L420,210.9z" style={{ fill: 'rgb(188, 188, 188)' }}></path><path className="st0" d="M256,139.8l-4.5,3.3L147,219l3.5,11l3.5,10.6L188.7,347h134.6l34.6-106.5l3.5-10.6l3.5-11l-104.4-75.8L256,139.8z M343.8,235.6l-3.4,10.6l-27.9,85.8H199.6l-27.9-85.8l-3.4-10.6l-3.5-10.9l86.8-63l4.5-3.3l4.6,3.3l86.8,63L343.8,235.6z" style={{ fill: 'rgb(188, 188, 188)' }}></path></g><polygon className="st1" points="512,186 510.3,191 492.8,196.8 434.2,215.8 416.6,221.5 357.9,240.6 340.4,246.2 267.7,269.8 311.6,332.1 322.2,347 358.9,399 369.7,414.3 405.5,465.1 416.3,480.5 414.2,487 409.9,487 399.3,471.9 360.5,417 349.8,402 311,347 300.5,332.1 259,273.4 252.9,273.4 211.5,332.1 201,347 162.2,402 151.5,417 112.8,471.9 102.2,487 97.8,487 95.7,480.4 106.5,465 142.4,414.2 153.2,399 189.8,347 200.4,332.1 244.3,269.8 171.7,246.2 154.1,240.6 95.5,221.5 77.8,215.8 19.3,196.8 1.6,191 0,186 4.8,182.5 22.5,188.2 74.4,205.1 92.1,210.9 150.6,229.9 168.2,235.6 249.9,262.2 251,260.6 251.5,259.4 251.5,3.3 256,0 260.6,3.3 260.6,260.2 261.7,261.7 263.3,261.8 343.8,235.6 361.4,229.9 420,210.9 437.6,205.1 489.6,188.2 507.2,182.6" style={{ fill: 'rgb(180, 178, 180)' }}></polygon><polygon className="st2" points="140.5,428 44.2,199.3 256.7,59.4 405.7,219.4 335.2,377" style={{ fill: 'rgb(0, 0, 0)' }}></polygon></g></svg>
);

export const settings = {
  title: __( 'レーダーチャート', THEME_NAME ),
  icon: radarChartIcon,
  description: __( '評価や能力値表示に。', THEME_NAME ),

  edit,
  save,
};
