import { BUTTON_BLOCK } from '../../helpers';
import { normalizeCustomFontSize } from '../../components/custom-font-size';
import {
  RichText,
  getColorClassName,
  getFontSizeClass,
  useBlockProps,
} from '@wordpress/block-editor';
import classnames from 'classnames';
import { addNoopenerToBlankTargets } from './add-noopener';

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
    customFontSize,
    width,
    justifyContent,
    verticalAlignment,
  } = attributes;

  const backgroundClass = getColorClassName(
    'background-color',
    backgroundColor
  );
  const textClass = getColorClassName( 'color', textColor );
  const borderClass = getColorClassName( 'border-color', borderColor );
  const fontSizeClass = getFontSizeClass( fontSize );

  const classes = classnames( {
    'btn-wrap': true,
    'btn-wrap-block': true,
    [ BUTTON_BLOCK ]: true,
    [ `is-content-justification-${ justifyContent }` ]: justifyContent,
    [ `is-vertically-aligned-${ verticalAlignment }` ]: verticalAlignment,
    [ size ]: size,
    'btn-wrap-circle': !! isCircle,
    'btn-wrap-shine': !! isShine,
    'has-text-color': textColor || customTextColor,
    'has-background': backgroundColor || customBackgroundColor,
    'has-border-color': borderColor || customBorderColor,
    [ textClass ]: textClass,
    [ backgroundClass ]: backgroundClass,
    [ borderClass ]: borderClass,
    [ fontSizeClass ]: fontSizeClass,
    'has-custom-width': width,
    [ `cocoon-block-button__width-${ width }` ]: width,
  } );

  const styles = {
    // 定型サイズの既存HTMLを保つカスタム文字サイズの保存
    fontSize: fontSize ? undefined : normalizeCustomFontSize( customFontSize ),
    '--cocoon-custom-background-color': customBackgroundColor || undefined,
    '--cocoon-custom-text-color': customTextColor || undefined,
    '--cocoon-custom-border-color': customBorderColor || undefined,
  };

  const blockProps = useBlockProps.save( {
    className: classes,
    style: styles,
  } );

  // 各リンクの既存属性を保持したnoopener補完結果。
  const tagCode = addNoopenerToBlankTargets( tag );
  return (
    <div { ...blockProps }>
      <RichText.Content value={ tagCode } />
    </div>
  );
}
