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

const v1 = {
  attributes: {
    content: {
      type: 'string',
      default: __( 'ボタン', THEME_NAME ),
    },
    url: {
      type: 'string',
      default: '',
    },
    target: {
      type: 'string',
      default: '_self',
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
    const { content, color, size, url, target, isCircle, isShine, align } =
      attributes;

    return {
      content: content,
      size: size,
      url: url,
      target: target,
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
    const { content, color, size, url, target, isCircle, isShine } = attributes;
    const classes = classnames( {
      btn: true,
      [ `btn-${ colorValueToSlug( color ) }` ]: !! colorValueToSlug( color ),
      [ size ]: size,
      [ 'btn-circle' ]: !! isCircle,
      [ 'btn-shine' ]: !! isShine,
    } );
    return (
      <div className={ BUTTON_BLOCK }>
        <a href={ url } className={ classes } target={ target }>
          <RichText.Content value={ content } />
        </a>
      </div>
    );
  },
};

const v2 = {
  save( { attributes } ) {
    const {
      content,
      size,
      url,
      target,
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

    // const classes = BUTTON_BLOCK;
    const classes = classnames( {
      [ BUTTON_BLOCK ]: true,
    } );
    const blockProps = useBlockProps.save( {
      className: classes,
    } );

    return (
      <div { ...blockProps }>
        <a
          href={ url }
          className={ classnames( {
            btn: true,
            [ size ]: size,
            [ 'btn-circle' ]: !! isCircle,
            [ 'btn-shine' ]: !! isShine,
            'has-text-color': textColor,
            'has-background': backgroundColor,
            'has-border-color': borderColor || customBorderColor,
            [ textClass ]: textClass,
            [ backgroundClass ]: backgroundClass,
            [ borderClass ]: borderClass,
            [ fontSizeClass ]: fontSizeClass,
          } ) }
          target={ target }
          rel="noopener"
        >
          <RichText.Content value={ content } />
        </a>
      </div>
    );
  },
};

export default [ v2, v1 ];
