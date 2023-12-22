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
      content,
      backgroundColor: undefined,
      customBackgroundColor: undefined,
      textColor: undefined,
      customTextColor: undefined,
      borderColor: colorValueToSlug( borderColor ),
      customBorderColor: undefined,
      fontSize: undefined,
      customFontSize: undefined,
      notNestedStyle: false,
    };
  },

  save( { attributes } ) {
    const { borderColor } = attributes;
    const classes = classnames( {
      'blank-box': true,
      [ `bb-${ colorValueToSlug( borderColor ) }` ]:
        !! colorValueToSlug( borderColor ),
      'block-box': true,
    } );
    return (
      <div className={ classes }>
        <InnerBlocks.Content />
      </div>
    );
  },
};

const v2 = {
  migrate( attributes ) {
    return {
      ...attributes,
      notNestedStyle: false,
    };
  },
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

const v3 = {
  attributes: {
    name: {
      type: 'string',
      default: '',
    },
    index: {
      type: 'string',
      default: '0',
    },
    id: {
      type: 'string',
      default: '',
    },
    icon: {
      type: 'string',
      default: '',
    },
    style: {
      type: 'string',
      default: 'stn',
    },
    position: {
      type: 'string',
      default: 'l',
    },
    iconstyle: {
      type: 'string',
      default: 'cb',
    },
    iconid: {
      type: 'number',
      default: 0,
    },
    backgroundColor: {
      type: 'string',
    },
    textColor: {
      type: 'string',
    },
    borderColor: {
      type: 'string',
    },
    customBackgroundColor: {
      type: 'string',
    },
    customTextColor: {
      type: 'string',
    },
    customBorderColor: {
      type: 'string',
    },
    fontSize: {
      type: 'string',
    },
  },
  migrate( attributes ) {
    return {
      ...attributes,
      notNestedStyle: false,
    };
  },
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
  },
};

export default [ v3, v2, v1 ];
