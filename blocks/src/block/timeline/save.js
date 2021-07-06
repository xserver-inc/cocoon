import {
  InnerBlocks,
  RichText,
  getColorClassName,
  getFontSizeClass,
  useBlockProps
} from '@wordpress/block-editor';
import classnames from 'classnames';

export default function save({ attributes }) {
  const {
    title,
    backgroundColor,
    textColor,
    borderColor,
    customBorderColor,
    pointColor,
    customPointColor,
    fontSize,
  } = attributes;

  const backgroundClass = getColorClassName( 'background-color', backgroundColor );
  const textClass = getColorClassName( 'color', textColor );
  const borderClass = getColorClassName( 'border-color', borderColor );
  const pointClass = getColorClassName( 'point-color', pointColor );
  const fontSizeClass = getFontSizeClass( fontSize );

  const className = classnames( {
    [ 'timeline-box' ]: true,
    [ 'cf' ]: true,
    [ 'block-box' ]: true,
    'has-text-color': textColor ,
    'has-background': backgroundColor,
    'has-border-color': borderColor || customBorderColor,
    'has-point-color': pointColor || customPointColor,
    [ textClass ]: textClass,
    [ backgroundClass ]: backgroundClass,
    [ borderClass ]: borderClass,
    [ pointClass ]: pointClass,
    [ fontSizeClass ]: fontSizeClass,
  } );

  const blockProps = useBlockProps.save({
    className: className,
  });

  return (
    <div { ...blockProps }>
      <div class="timeline-title">
        <RichText.Content
            value={ title }
        />
      </div>
      <ul className="timeline">
        <InnerBlocks.Content />
      </ul>
    </div>
  );  
}