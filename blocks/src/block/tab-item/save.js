import {
  InnerBlocks,
  RichText,
  useBlockProps,
} from '@wordpress/block-editor';
import classnames from 'classnames';

export default function save( { attributes } ) {
  const { title, id } = attributes;

  const classes = classnames( {
    'tab-content': true,
  });

  const blockProps = useBlockProps.save( {
    className: classes
  });

  return (
    <div {...blockProps}>
      <InnerBlocks.Content />
    </div>
  );
}
