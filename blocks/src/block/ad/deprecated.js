/**
 * Cocoon Blocks
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */

// API version 2で保存されたブロック（wp-block-*クラスなし）
const v1 = {
  save( props ) {
    const { attributes } = props;

    return <div className={ attributes.classNames }>{ '[ad]' }</div>;
  },
};

export default [ v1 ];
