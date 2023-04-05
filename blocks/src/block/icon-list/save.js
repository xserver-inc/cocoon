import {
  InnerBlocks,
  RichText,
  getColorClassName,
  getFontSizeClass,
  useBlockProps,
} from '@wordpress/block-editor';
import classnames from 'classnames';

export default function save( props ) {
  const {
    title,
    icon,
    backgroundColor,
    textColor,
    borderColor,
    customBackgroundColor,
    customTextColor,
    customBorderColor,
    iconColor,
    customIconColor,
    fontSize,
  } = props.attributes;

  const backgroundClass = getColorClassName(
    'background-color',
    backgroundColor
  );
  const textClass = getColorClassName( 'color', textColor );
  const borderClass = getColorClassName( 'border-color', borderColor );
  const iconClass = getColorClassName( 'icon-color', iconColor );
  const fontSizeClass = getFontSizeClass( fontSize );

  const className = classnames( {
    'iconlist-box': true,
    'blank-box': true,
    [ icon ]: !! icon,
    'block-box': true,
    'has-text-color': textColor || customTextColor,
    'has-background': backgroundColor || customBackgroundColor,
    'has-border-color': borderColor || customBorderColor,
    'has-icon-color': iconColor || customIconColor,
    [ textClass ]: textClass,
    [ backgroundClass ]: backgroundClass,
    [ borderClass ]: borderClass,
    [ iconClass ]: iconClass,
    [ fontSizeClass ]: fontSizeClass,
  } );

  const styles = {
    '--cocoon-custom-background-color': customBackgroundColor || undefined,
    '--cocoon-custom-text-color': customTextColor || undefined,
    '--cocoon-custom-border-color': customBorderColor || undefined,
    '--cooon-custom-icon-color': customIconColor || undefined,
  };

  const iconListBlockProps = useBlockProps.save( {
    className: className,
    style: styles,
  } );

  // const iconListTitleBlockProps = useBlockProps.save({
  //     className: 'iconlist-title',
  // });
  return (
    <div { ...iconListBlockProps }>
      <div className="iconlist-title">
        <RichText.Content value={ title } />
      </div>
      <InnerBlocks.Content />
    </div>
  );
}
