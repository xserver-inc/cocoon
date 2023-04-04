import { getBalloonClasses } from '../../helpers';
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
    name,
    id,
    icon,
    style,
    position,
    iconstyle,
    backgroundColor,
    textColor,
    borderColor,
    customBackgroundColor,
    customTextColor,
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

  const classes = getBalloonClasses( id, style, position, iconstyle );

  const styles = {
    '--cocoon-custom-background-color': customBackgroundColor || undefined,
    '--cocoon-custom-text-color': customTextColor || undefined,
    '--cocoon-custom-border-color': customBorderColor || undefined,
  };

  const blockProps = useBlockProps.save( {
    className: classes,
    style: styles,
  } );

  return (
    <div { ...blockProps }>
      <div className="speech-person">
        <figure className="speech-icon">
          <img src={ icon } alt={ name } className="speech-icon-image" />
        </figure>
        <div className="speech-name">
          <RichText.Content value={ name } />
        </div>
      </div>
      <div
        className={ classnames( {
          'speech-balloon': true,
          'has-text-color': textColor || customTextColor,
          'has-background': backgroundColor || customBackgroundColor,
          'has-border-color': borderColor || customBorderColor,
          [ textClass ]: textClass,
          [ backgroundClass ]: backgroundClass,
          [ borderClass ]: borderClass,
          [ fontSizeClass ]: fontSizeClass,
        } ) }
      >
        <InnerBlocks.Content />
      </div>
    </div>
  );
}
