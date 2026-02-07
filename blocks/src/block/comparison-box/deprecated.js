/**
 * Cocoon Blocks
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */

import { InnerBlocks } from '@wordpress/block-editor';
import classnames from 'classnames';

// API version 2で保存されたブロック（wp-block-*クラスなし）
const v1 = {
  save( { attributes } ) {
    const classes = classnames( 'comparison-box', 'block-box', {} );

    return (
      <div className={ classes }>
        <InnerBlocks.Content />
      </div>
    );
  },
};

export default [ v1 ];
