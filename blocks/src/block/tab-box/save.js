import {
  InnerBlocks,
  getColorClassName,
  getFontSizeClass,
  useBlockProps,
} from '@wordpress/block-editor';
import classnames from 'classnames';

export default function save( props ) {
  const {
    label,
    backgroundColor,
    textColor,
    borderColor,
    customBorderColor,
    fontSize,
  } = props.attributes;

  const backgroundClass = getColorClassName( 'background-color', backgroundColor );
  const textClass = getColorClassName( 'color', textColor );
  const borderClass = getColorClassName( 'border-color', borderColor );
  const fontSizeClass = getFontSizeClass( fontSize );

  const className = classnames( {
    'blank-box': true,
    'bb-tab': true,
    [ label ]: !! label,
    'block-box': true,
    'has-text-color': textColor,
    'has-background': backgroundColor,
    'has-border-color': borderColor || customBorderColor,
    [ textClass ]: textClass,
    [ backgroundClass ]: backgroundClass,
    [ borderClass ]: borderClass,
    [ fontSizeClass ]: fontSizeClass,
  });
  const blockProps = useBlockProps.save({
    className: className,
  });

  return (
    <div { ...blockProps }>
      <InnerBlocks.Content />
    </div>
  );
}