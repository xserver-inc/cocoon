/**
 * Cocoon Blocks
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */

import { InnerBlocks, useBlockProps } from '@wordpress/block-editor';

// サーバーサイドレンダリングを使用するため、saveはnullを返す
export default function save() {
  return (
    <div { ...useBlockProps.save( { className: 'campaign block-box' } ) }>
      <InnerBlocks.Content />
    </div>
  );
}
