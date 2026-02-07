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
import { __ } from '@wordpress/i18n';
import {
  InnerBlocks,
  getColorClassName,
  getFontSizeClass,
  useBlockProps,
} from '@wordpress/block-editor';

const { createBlock } = wp.blocks;

const v1 = {
  attributes: {
    content: {
      type: 'string',
      default: CLICK_POINT_MSG,
    },
    borderColor: {
      type: 'string',
      default: '',
    },
  },

  migrate( attributes ) {
    const { content, borderColor } = attributes;

    return {
      content: content,
      backgroundColor: undefined,
      customBackgroundColor: undefined,
      textColor: undefined,
      customTextColor: undefined,
      borderColor: colorValueToSlug( borderColor ),
      customBorderColor: undefined,
      fontSize: undefined,
      customFontSize: undefined,
    };
  },

  save( { attributes } ) {
    const { borderColor } = attributes;
    const classes = classnames( {
      'blank-box': true,
      [ `bb-${ colorValueToSlug( borderColor ) }` ]:
        !! colorValueToSlug( borderColor ),
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
  save( { attributes } ) {
    const {
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

    const className = classnames( {
      'blank-box': true,
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
