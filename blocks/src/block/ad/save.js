import { useBlockProps } from '@wordpress/block-editor';

export default function save( props ) {
  const { attributes } = props;

  const blockProps = useBlockProps.save( {
    className: attributes.classNames,
  } );

  return <div { ...blockProps }>{ '[ad]' }</div>;
}
