import {
  RichText,
  getColorClassName,
  getFontSizeClass,
  useBlockProps,
} from '@wordpress/block-editor';
import classnames from 'classnames';

const MICRO_COPY_CLASS = 'micro-copy';

export default function save( { attributes } ) {
  const {
    content,
    type,
    isCircle,
    icon,
    backgroundColor,
    customBackgroundColor,
    textColor,
    customTextColor,
    borderColor,
    customBorderColor,
    fontSize,
  } = attributes;

  const backgroundClass = getColorClassName(
    'background-color',
    backgroundColor
  );
  const textClass = getColorClassName( 'color', textColor );
  const borderClass = getColorClassName( 'border-color', borderColor );
  const fontSizeClass = getFontSizeClass( fontSize );

  const className = classnames( {
    [ 'micro-balloon' ]: true,
    [ type ]: !! type,
    [ 'mc-circle' ]: !! isCircle,
    [ MICRO_COPY_CLASS ]: true,
    'has-text-color': textColor || customTextColor,
    'has-background': backgroundColor || customBackgroundColor,
    'has-border-color': borderColor || customBorderColor,
    [ textClass ]: textClass,
    [ backgroundClass ]: backgroundClass,
    [ borderClass ]: borderClass,
    [ fontSizeClass ]: fontSizeClass,
  } );

  const styles = {
    '--cocoon-custom-background-color': customBackgroundColor || undefined,
    '--cocoon-custom-text-color': customTextColor || undefined,
    '--cocoon-custom-border-color': customBorderColor || undefined,
  };

  const blockProps = useBlockProps.save( {
    className: className,
    style: styles,
  } );

  return (
    <div { ...blockProps }>
      <span className="micro-balloon-content micro-content">
        { icon && (
          <span
            className={ classnames( 'micro-balloon-icon', 'micro-icon', icon ) }
          ></span>
        ) }
        <RichText.Content value={ content } />
      </span>
    </div>
  );
}
