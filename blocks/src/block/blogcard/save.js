import { RichText, useBlockProps } from '@wordpress/block-editor';

export default function save({ attributes }) {
  let { content, style } = attributes;
  const classes = style;
  const blockProps = useBlockProps.save({
    className: classes,
  });
  return (
    <div { ...blockProps}>
      <RichText.Content
        value={ content.replace(/<\/p><p>/g, '</p>\n<p>').replace(/^<p>/g, '\n<p>').replace(/<\/p>$/g, '<\/p>\n') }
        multiline={"p"}
      />
    </div>
  );
}