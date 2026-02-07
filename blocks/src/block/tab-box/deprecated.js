/**
 * Cocoon Blocks
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
import {
  THEME_NAME,
  BUTTON_BLOCK,
  CLICK_POINT_MSG,
  colorValueToSlug,
} from '../../helpers';
import classnames from 'classnames';

import {
  InnerBlocks,
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
      default: CLICK_POINT_MSG,
    },
    style: {
      type: 'string',
      default: 'bb-check',
    },
    color: {
      type: 'string',
      default: '',
    },
  },

  migrate( attributes ) {
    const { content, style, color } = attributes;
    return {
      content: content,
      label: style,
      backgroundColor: undefined,
      customBackgroundColor: undefined,
      textColor: undefined,
      customTextColor: undefined,
      borderColor: colorValueToSlug( color ),
      customBorderColor: undefined,
      fontSize: undefined,
      customFontSize: undefined,
    };
  },

  save( { attributes } ) {
    const { content, style, color } = attributes;
    const classes = classnames( {
      'blank-box': true,
      'bb-tab': true,
      [ style ]: !! style,
      [ `bb-${ colorValueToSlug( color ) }` ]: !! colorValueToSlug( color ),
      [ 'block-box' ]: true,
    } );
    return (
      <div className={ classes }>
        <InnerBlocks.Content />
      </div>
    );
  },
};

const v2 = {
  save( props ) {
    const {
      label,
      backgroundColor,
      textColor,
      borderColor,
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
    } );
    const blockProps = useBlockProps.save( {
      className: className,
    } );

    return (
      <div { ...blockProps }>
        <InnerBlocks.Content />
      </div>
    );
  },
};

export default [ v2, v1 ];
