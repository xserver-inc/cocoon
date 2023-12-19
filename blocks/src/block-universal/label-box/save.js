import {
  InnerBlocks,
  RichText,
  getColorClassName,
  getFontSizeClass,
  useBlockProps,
} from '@wordpress/block-editor';
import classnames from 'classnames';

const CAPTION_BOX_CLASS = 'label-box';

export default function save( props ) {
  const {
    content,
    icon,
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
  } = props.attributes;

  const backgroundClass = getColorClassName(
    'background-color',
    backgroundColor
  );
  const textClass = getColorClassName( 'color', textColor );
  const borderClass = getColorClassName( 'border-color', borderColor );
  const fontSizeClass = getFontSizeClass( fontSize );

  const className = classnames( {
    [ CAPTION_BOX_CLASS ]: true,
    'block-box': true,
    'has-text-color': textColor || customTextColor,
    'has-background': backgroundColor || customBackgroundColor,
    'has-border-color': borderColor || customBorderColor,
    [ textClass ]: textClass,
    [ backgroundClass ]: backgroundClass,
    [ borderClass ]: borderClass,
    [ fontSizeClass ]: fontSizeClass,
    'not-nested-style': notNestedStyle,
    'cocoon-block-label-box': true,
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
      <div
        className={ classnames(
          'label-box-label',
          'block-box-label',
          'box-label',
          icon
        ) }
      >
        <span
          className={ classnames(
            'label-box-label-text',
            'block-box-label-text',
            'box-label-text'
          ) }
        >
          <RichText.Content value={ content } />
        </span>
      </div>
      <div
        className={ classnames(
          'label-box-content',
          'block-box-content',
          'box-content'
        ) }
      >
        <InnerBlocks.Content />
      </div>
    </div>
  );
}
