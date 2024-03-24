import {
  InnerBlocks,
  useBlockProps,
  useInnerBlocksProps,
} from '@wordpress/block-editor';
import classnames from 'classnames';

export default function save( props ) {
  const classes = classnames( {
    'tab-content': true,
  });

  return (
    <div class={classes}>
      <InnerBlocks.Content />
    </div>
  );
}