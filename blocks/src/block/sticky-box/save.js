import { InnerBlocks, useBlockProps } from '@wordpress/block-editor';
import classnames from 'classnames';

export default function save({ attributes }) {
  const { style } = attributes;
  const classes = classnames('blank-box', 'block-box', 'sticky',
    {
      [ style ]: !! style,
    }
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