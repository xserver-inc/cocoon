/**
 * Cocoon Blocks
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
import {
  THEME_NAME,
  BUTTON_BLOCK,
  colorValueToSlug,
  keyColor,
} from '../../helpers';
import classnames from 'classnames';

import {
  RichText,
  getColorClassName,
  getFontSizeClass,
  useBlockProps,
} from '@wordpress/block-editor';
import { __ } from '@wordpress/i18n';

const { createBlock } = wp.blocks;

export const v1 = {
  attributes: {
    content: {
      type: 'string',
      default: __(
        'こちらをクリックしてリンクタグを設定エリア入力してください。この入力は公開ページで反映されません。',
        THEME_NAME
      ),
    },
    tag: {
      type: 'string',
      default: '',
    },
    color: {
      type: 'string',
      default: keyColor,
    },
    size: {
      type: 'string',
      default: '',
    },
    isCircle: {
      type: 'boolean',
      default: false,
    },
    isShine: {
      type: 'boolean',
      default: false,
    },
    align: {
      type: 'string',
    },
  },
  supports: {
    align: [ 'left', 'center', 'right' ],
    customClassName: true,
  },

  migrate( attributes ) {
    const { content, tag, color, size, isCircle, isShine, align } = attributes;

    return {
      content: content,
      tag: tag,
      size: size,
      isCircle: isCircle,
      isShine: isShine,
      align: align,
      backgroundColor: colorValueToSlug( color ),
      customBackgroundColor: undefined,
      textColor: undefined,
      customTextColor: undefined,
      borderColor: undefined,
      customBorderColor: undefined,
      fontSize: undefined,
      customFontSize: undefined,
    };
  },

  save( { attributes } ) {
    const { content, tag, color, size, isCircle, isShine, align } = attributes;
    const classes = classnames( {
      [ 'btn-wrap' ]: true,
      [ `btn-wrap-${ colorValueToSlug( color ) }` ]:
        !! colorValueToSlug( color ),
      [ size ]: size,
      [ BUTTON_BLOCK ]: true,
      [ 'btn-wrap-circle' ]: !! isCircle,
      [ 'btn-wrap-shine' ]: !! isShine,
    } );
    return (
      <div className={ classes }>
        <RichText.Content value={ tag } />
      </div>
    );
  },
};

const v2 = {
  save( { attributes } ) {
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
      'has-text-color': textColor,
      'has-background': backgroundColor,
      'has-border-color': borderColor || customBorderColor,
      [ textClass ]: textClass,
      [ backgroundClass ]: backgroundClass,
      [ borderClass ]: borderClass,
      [ fontSizeClass ]: fontSizeClass,
    } );

    const blockProps = useBlockProps.save( {
      className: classes,
    } );

    return (
      <div { ...blockProps }>
        <RichText.Content value={ tag } />
      </div>
    );
  },
};

export default [ v2, v1 ];
