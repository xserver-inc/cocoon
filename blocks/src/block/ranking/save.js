import { RichText, useBlockProps } from '@wordpress/block-editor';

export default function save({ attributes, className }) {
  let { code } = attributes;
  const blockProps = useBlockProps.save({
    className: className,
  });

  return (
    <div {...blockProps}>
      {'[rank id=' + code + ']'}
    </div>
  );
}