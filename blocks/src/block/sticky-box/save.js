import { InnerBlocks, useBlockProps } from '@wordpress/block-editor';
import classnames from 'classnames';

export default function save( { attributes } ) {
  const { boxStyle } = attributes;
  const classes = classnames( 'blank-box', 'block-box', 'sticky', {
    [ boxStyle ]: !! boxStyle,
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
