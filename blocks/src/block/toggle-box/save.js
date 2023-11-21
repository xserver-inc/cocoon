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
    content,
    dateID,
    backgroundColor,
    textColor,
    borderColor,
    customBackgroundColor,
    customTextColor,
    customBorderColor,
    fontSize,
    notNestedStyle,
    backgroundColorValue,
    textColorValue,
    borderColorValue,
  } = attributes;

  const backgroundClass = getColorClassName(
    'background-color',
    backgroundColor
  );
  const textClass = getColorClassName( 'color', textColor );
  const borderClass = getColorClassName( 'border-color', borderColor );
  const fontSizeClass = getFontSizeClass( fontSize );

  const className = classnames( {
    'toggle-wrap': true,
    'toggle-box': true,
    'block-box': true,
    'has-text-color': textColor || customTextColor,
    'has-background': backgroundColor || customBackgroundColor,
    'has-border-color': borderColor || customBorderColor,
    [ textClass ]: textClass,
    [ backgroundClass ]: backgroundClass,
    [ borderClass ]: borderClass,
    [ fontSizeClass ]: fontSizeClass,
    'not-nested-style': notNestedStyle,
    'cocoon-block-toggle': true,
  } );

  const styles = {
    '--cocoon-custom-background-color': customBackgroundColor || undefined,
    '--cocoon-custom-text-color': customTextColor || undefined,
    '--cocoon-custom-border-color': customBorderColor || undefined,
  };

  if ( notNestedStyle ) {
    styles[ '--cocoon-custom-border-color' ] = borderColorValue;
    styles[ '--cocoon-custom-background-color' ] = backgroundColorValue;
    styles[ '--cocoon-custom-text-color' ] = textColorValue;
  }

  const blockProps = useBlockProps.save( {
    className,
    style: styles,
  } );

  return (
    <div { ...blockProps }>
      <input
        id={ 'toggle-checkbox-' + dateID }
        className="toggle-checkbox"
        type="checkbox"
      />
      <label className="toggle-button" htmlFor={ 'toggle-checkbox-' + dateID }>
        <RichText.Content value={ content } />
      </label>
      <div className="toggle-content">
        <InnerBlocks.Content />
      </div>
    </div>
  );
}
