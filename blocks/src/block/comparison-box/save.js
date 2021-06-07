import { InnerBlocks, useBlockProps } from '@wordpress/block-editor';
import classnames from 'classnames';

export default function save({ attributes }) {
  const classes = classnames(
    'comparison-box',
    'block-box',
    {}
  );

  const blockProps = useBlockProps.save({
    className: classes,
  });

  return (
    <div { ...blockProps }>
      <InnerBlocks.Content />
    </div>
  );
}
