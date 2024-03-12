import {
  InnerBlocks,
  RichText,
  useBlockProps,
  getColorClassName,
} from '@wordpress/block-editor';
import classnames from 'classnames';
const { RawHTML } = wp.element;

export default function save( {attributes}) {
  const {
    title,
    tabLabelsArray,
    backgroundColor,
    textColor,
    borderColor,
    customBackgroundColor,
    customTextColor,
    customBorderColor,
  } = attributes;

  const backgroundClass = getColorClassName(
    'background-color',
    backgroundColor
  );
  const textClass = getColorClassName( 'color', textColor );
  const borderClass = getColorClassName( 'border-color', borderColor );

  const classes = classnames( {
    'tab-block': true,
    'block-box': true,
    'cocoon-block-tab': true,
    'has-text-color': textColor || customTextColor,
    'has-background': backgroundColor || customBackgroundColor,
    'has-border-color': borderColor || customBorderColor,
    [ textClass ]: textClass,
    [ backgroundClass ]: backgroundClass,
    [ borderClass ]: borderClass,
  });

  const styles = {
    '--cocoon-custom-background-color': customBackgroundColor || undefined,
    '--cocoon-custom-text-color': customTextColor || undefined,
    '--cocoon-custom-border-color': customBorderColor || undefined,
  };

  const blockProps = useBlockProps.save( {
    className: classes,
    style: styles
  });

  return (
      <div { ...blockProps }>
        <div className="tab-title">
          <RichText.Content value={title} />
        </div>
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
