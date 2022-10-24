import { RichText, useBlockProps } from '@wordpress/block-editor';

export default function save({ attributes, className }) {
  let { id } = attributes;
  const blockProps = useBlockProps.save({
    className: className,
  });

  return (
    <div {...blockProps}>
      {'[rank id=' + id + ']'}
    </div>
  );
}