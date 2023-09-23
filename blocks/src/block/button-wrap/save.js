import { BUTTON_BLOCK } from '../../helpers';
import {
  RichText,
  getColorClassName,
  getFontSizeClass,
  useBlockProps,
} from '@wordpress/block-editor';
import classnames from 'classnames';

export default function save( { attributes } ) {
  const {
    tag,
    size,
    isCircle,
    isShine,
    backgroundColor,
    textColor,
    borderColor,
    customBackgroundColor,
    customTextColor,
    customBorderColor,
    fontSize,
    width,
  } = attributes;

  const backgroundClass = getColorClassName(
    'background-color',
    backgroundColor
  );
  const textClass = getColorClassName( 'color', textColor );
  const borderClass = getColorClassName( 'border-color', borderColor );
  const fontSizeClass = getFontSizeClass( fontSize );

  const classes = classnames( {
    [ 'btn-wrap' ]: true,
    [ 'btn-wrap-block' ]: true,
    [ BUTTON_BLOCK ]: true,
    [ size ]: size,
    [ 'btn-wrap-circle' ]: !! isCircle,
    [ 'btn-wrap-shine' ]: !! isShine,
    'has-text-color': textColor || customTextColor,
    'has-background': backgroundColor || customBackgroundColor,
    'has-border-color': borderColor || customBorderColor,
    [ textClass ]: textClass,
    [ backgroundClass ]: backgroundClass,
    [ borderClass ]: borderClass,
    [ fontSizeClass ]: fontSizeClass,
    [ 'has-custom-width' ]: width,
    [ `cocoon-block-button__width-${ width }` ]: width,
  } );

  const styles = {
    '--cocoon-custom-background-color': customBackgroundColor || undefined,
    '--cocoon-custom-text-color': customTextColor || undefined,
    '--cocoon-custom-border-color': customBorderColor || undefined,
  };

  const blockProps = useBlockProps.save( {
    className: classes,
    style: styles,
  } );

  // rel属性の値をチェックして変換を行う
  let tagCode;
  if ( ! tag.includes( ' rel="noopener' ) ) {
    tagCode = tag.replace(
      ' target="_blank"',
      ' target="_blank" rel="noopener"'
    );
  }
  return (
    <div { ...blockProps }>
      <RichText.Content value={ tagCode } />
    </div>
  );
}
