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
  getBalloonClasses,
} from '../../helpers';
import classnames from 'classnames';

import {
  InnerBlocks,
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
      name,
      id,
      icon,
      style,
      position,
      iconstyle,
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

    const classes = getBalloonClasses( id, style, position, iconstyle );
    const blockProps = useBlockProps.save( {
      className: classes,
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
            'has-text-color': textColor,
            'has-background': backgroundColor,
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
  },
};

export default [ v2, v1 ];
