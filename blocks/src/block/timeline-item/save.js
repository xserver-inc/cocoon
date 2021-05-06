import {
  InnerBlocks,
  RichText,
  getColorClassName,
  getFontSizeClass,
  useBlockProps
} from '@wordpress/block-editor';
import classnames from 'classnames';

export default function save({ attributes }) {
  const { title, label } = attributes;

  const blockProps = useBlockProps.save({
    className: "timeline-item cf",
  });

  return (
    <li { ...blockProps }>
      <div className="timeline-item-label">
        <RichText.Content
          value={ label }
        />
      </div>
      <div className="timeline-item-content cf">
        <div className="timeline-item-title">
          <RichText.Content
            value={ title }
          />
        </div>
        <div className="timeline-item-snippet">
          <InnerBlocks.Content />
        </div>
      </div>
    </li>
  );
}