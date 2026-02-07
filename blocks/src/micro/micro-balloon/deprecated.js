/**
 * Cocoon Blocks
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
import { THEME_NAME, BUTTON_BLOCK, colorValueToSlug } from '../../helpers';
import classnames from 'classnames';

import {
  RichText,
  getColorClassName,
  getFontSizeClass,
  useBlockProps,
} from '@wordpress/block-editor';
import { __ } from '@wordpress/i18n';

const DEFAULT_MSG = __( 'マイクロコピーバルーン', THEME_NAME );
const MICRO_COPY_CLASS = 'micro-copy';

const v1 = {
  attributes: {
    content: {
      type: 'string',
      default: DEFAULT_MSG,
    },
    style: {
      type: 'string',
      default: 'micro-top',
    },
    color: {
      type: 'string',
      default: '',
    },
    isCircle: {
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
    const { content, style, color, isCircle, align } = attributes;

    return {
      content: content,
      type: style,
      isCircle: isCircle,
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
    const { content, style, color, isCircle, align } = attributes;
    const classes = classnames( {
      [ 'micro-balloon' ]: true,
      [ style ]: !! style,
      [ `mc-${ colorValueToSlug( color ) }` ]: !! colorValueToSlug( color ),
      [ 'mc-circle' ]: !! isCircle,
      [ MICRO_COPY_CLASS ]: true,
      [ 'block-box' ]: true,
    } );
    return (
      <div className={ classes }>
        <RichText.Content value={ content } />
      </div>
    );
  },
};

const v2 = {
  save( { attributes } ) {
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
    const blockProps = useBlockProps.save( {
      className: className,
    } );

    return (
      <div { ...blockProps }>
        <span className="micro-balloon-content micro-content">
          { icon && (
            <span
              className={ classnames(
                'micro-balloon-icon',
                'micro-icon',
                icon
              ) }
            ></span>
          ) }
          <RichText.Content value={ content } />
        </span>
      </div>
    );
  },
};

export default [ v2, v1 ];
