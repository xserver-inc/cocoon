import {
  InnerBlocks,
  RichText,
  getColorClassName,
  getFontSizeClass,
  useBlockProps,
} from '@wordpress/block-editor';
import classnames from 'classnames';

export default function save( { attributes } ) {
  const {
    title,
    backgroundColor,
    textColor,
    borderColor,
    customBackgroundColor,
    customTextColor,
    customBorderColor,
    pointColor,
    customPointColor,
    fontSize,
    notNestedStyle,
    backgroundColorValue,
    textColorValue,
    borderColorValue,
    pointColorValue,
  } = attributes;

  const backgroundClass = getColorClassName(
    'background-color',
    backgroundColor
  );
  const textClass = getColorClassName( 'color', textColor );
  const borderClass = getColorClassName( 'border-color', borderColor );
  const pointClass = getColorClassName( 'point-color', pointColor );
  const fontSizeClass = getFontSizeClass( fontSize );

  const className = classnames( {
    'timeline-box': true,
    'cf': true,// eslint-disable-line prettier/prettier
    'block-box': true,
    'has-text-color': textColor || customTextColor,
    'has-background': backgroundColor || customBackgroundColor,
    'has-border-color': borderColor || customBorderColor,
    'has-point-color': pointColor || customPointColor,
    [ textClass ]: textClass,
    [ backgroundClass ]: backgroundClass,
    [ borderClass ]: borderClass,
    [ pointClass ]: pointClass,
    [ fontSizeClass ]: fontSizeClass,
    'not-nested-style': notNestedStyle,
    'cocoon-block-timeline': true,
  } );

  const styles = {
    '--cocoon-custom-background-color': customBackgroundColor || undefined,
    '--cocoon-custom-text-color': customTextColor || undefined,
    '--cocoon-custom-border-color': customBorderColor || undefined,
    '--cocoon-custom-point-color': customPointColor || undefined,
  };

  if ( notNestedStyle ) {
    styles[ '--cocoon-custom-border-color' ] = borderColorValue;
    styles[ '--cocoon-custom-background-color' ] = backgroundColorValue;
    styles[ '--cocoon-custom-text-color' ] = textColorValue;
    styles[ '--cocoon-custom-point-color' ] = pointColorValue;
  }

  const blockProps = useBlockProps.save( {
    className,
    style: styles,
  } );

  return (
    <div { ...blockProps }>
      <div className="timeline-title">
        <RichText.Content value={ title } />
      </div>
      <ul className="timeline">
        <InnerBlocks.Content />
      </ul>
    </div>
  );
}
