/**
 * Cocoon Blocks
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */

import { InnerBlocks, useBlockProps } from '@wordpress/block-editor';

export default function LoginUserOnlySave() {
  const blockProps = useBlockProps.save( {
    className: 'login-user-only',
  } );

  return (
    <div { ...blockProps }>
      <InnerBlocks.Content />
    </div>
  );
}
