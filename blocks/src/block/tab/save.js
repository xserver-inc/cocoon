import {
  InnerBlocks,
  useBlockProps,
} from '@wordpress/block-editor';
import classnames from 'classnames';
const { RawHTML } = wp.element;

export default function save( {attributes}) {
  const {
    tabLabelsArray,
  } = attributes;

  const classes = classnames( {
    'tab-block': true,
    'cocoon-block-tab': true,
  });


  const blockProps = useBlockProps.save( {
    className: classes,
  });

  return (
      <div { ...blockProps }>
        <ul className="tab-label-group">
          {tabLabelsArray.map((label, index) => {
            return (<li class={"tab-label " + "tab-label-" + index}><RawHTML>{label}</RawHTML></li>);
          })}
        </ul>
        <div className="tab-content-group">
          <InnerBlocks.Content />
        </div>
      </div>
  );
}
