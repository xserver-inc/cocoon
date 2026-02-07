/**
 * Cocoon Blocks
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */

import {
  InnerBlocks,
} from '@wordpress/block-editor';
import classnames from 'classnames';
const { RawHTML } = wp.element;

// API version 2で保存されたブロック（wp-block-*クラスなし）
const v1 = {
  save( {attributes}) {
    const {
      tabLabelsArray,
    } = attributes;

    const classes = classnames( {
      'tab-block': true,
      'cocoon-block-tab': true,
    });

    return (
        <div className={ classes }>
          <ul className="tab-label-group">
            {tabLabelsArray.map((label, index) => {
              return (<li class={"tab-label " + "tab-label-" + index}><RawHTML>{label}</RawHTML></li>);
            })}
          </ul>
          <div className="tab-content-group">
            <InnerBlocks.Content />
          </div>
        </div>
    );
  },
};

export default [ v1 ];

