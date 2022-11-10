import { useBlockProps } from '@wordpress/block-editor';
import classnames from 'classnames';

export default function save({ attributes, className }) {
  let { id } = attributes;
  const classes = classnames('ranking-box', 'block-box', 
    {
      [ 'ranking-' + id ]: !! (id !== '-1'),
      [ className ]: !! className,
    }
  );
  const blockProps = useBlockProps.save({
    className: classes,
  });

  return (
    <div {...blockProps}>
      {'[rank id=' + id + ']'}
    </div>
  );
}