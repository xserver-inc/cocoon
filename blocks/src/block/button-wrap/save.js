
import { BUTTON_BLOCK } from '../../helpers';
import {
  RichText,
  getColorClassName,
  getFontSizeClass,
  useBlockProps,
} from '@wordpress/block-editor';
import classnames from 'classnames';

export default function save({ attributes }) {
  const {
    tag,
    size,
    isCircle,
    isShine,
    backgroundColor,
    textColor,
    borderColor,
    customBorderColor,
    fontSize,
  } = attributes;

  const backgroundClass = getColorClassName( 'background-color', backgroundColor );
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
    'has-text-color': textColor,
    'has-background': backgroundColor,
    'has-border-color': borderColor || customBorderColor,
    [ textClass ]: textClass,
    [ backgroundClass ]: backgroundClass,
    [ borderClass ]: borderClass,
    [ fontSizeClass ]: fontSizeClass,
  } );

  const blockProps = useBlockProps.save({
    className: classes,
  });

  return (
    <div { ...blockProps }>
      <RichText.Content
        value={ tag }
      />
    </div>
  );
}