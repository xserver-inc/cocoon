import { InnerBlocks, useBlockProps } from '@wordpress/block-editor';
import classnames from 'classnames';

export default function save( { attributes } ) {
  const { style } = attributes;
  const classes = classnames( 'block-box', {
    [ style ]: !! style,
  } );
  const blockProps = useBlockProps.save( {
    className: classes,
  } );
  return (
    <div { ...blockProps }>
      <InnerBlocks.Content />
    </div>
  );
}
