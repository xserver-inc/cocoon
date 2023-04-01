import {
  InnerBlocks,
  RichText,
  getColorClassName,
  getFontSizeClass,
  useBlockProps,
} from '@wordpress/block-editor';
import classnames from 'classnames';

const CAPTION_BOX_CLASS = 'tab-caption-box';

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
  } );

  const styles = {
    '--cocoon-custom-background-color':
      backgroundColor || customBackgroundColor || undefined,
    '--cocoon-custom-text-color': textColor || customTextColor || undefined,
    '--cocoon-custom-border-color':
      borderColor || customBorderColor || undefined,
  };

  const blockProps = useBlockProps.save( {
    className: className,
    style: styles,
  } );

  return (
    <div { ...blockProps }>
      <div
        className={ classnames(
          'tab-caption-box-label',
          'block-box-label',
          'box-label',
          icon
        ) }
      >
        <span
          className={ classnames(
            'tab-caption-box-label-text',
            'block-box-label-text',
            'box-label-text'
          ) }
        >
          <RichText.Content value={ content } />
        </span>
      </div>
      <div
        className={ classnames(
          'tab-caption-box-content',
          'block-box-content',
          'box-content'
        ) }
      >
        <InnerBlocks.Content />
      </div>
    </div>
  );
}
