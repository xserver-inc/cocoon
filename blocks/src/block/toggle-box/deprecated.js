/**
 * Cocoon Blocks
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
import { THEME_NAME, colorValueToSlug } from '../../helpers';
import classnames from 'classnames';

import {
  InnerBlocks,
  RichText,
  getColorClassName,
  getFontSizeClass,
  useBlockProps,
} from '@wordpress/block-editor';
import { __ } from '@wordpress/i18n';

const DEFAULT_MSG = __( 'トグルボックス見出し', THEME_NAME );

const v1 = {
  attributes: {
    content: {
      type: 'string',
      selector: 'div',
      default: DEFAULT_MSG,
    },
    color: {
      type: 'string',
      default: '',
    },
    dateID: {
      type: 'string',
      default: '',
    },
  },

  migrate( attributes ) {
    const { content, color, dateID } = attributes;

    return {
      content,
      dateID,
      backgroundColor: undefined,
      customBackgroundColor: undefined,
      textColor: undefined,
      customTextColor: undefined,
      borderColor: colorValueToSlug( color ),
      customBorderColor: undefined,
      fontSize: undefined,
      customFontSize: undefined,
      notNestedStyle: false,
    };
  },

  save( { attributes } ) {
    const { content, color, dateID } = attributes;
    const classes = classnames( {
      'toggle-wrap': true,
      [ `tb-${ colorValueToSlug( color ) }` ]: !! colorValueToSlug( color ),
      'block-box': true,
    } );
    return (
      <div className={ classes }>
        <input
          id={ 'toggle-checkbox-' + dateID }
          className="toggle-checkbox"
          type="checkbox"
        />
        <label
          className="toggle-button"
          htmlFor={ 'toggle-checkbox-' + dateID }
        >
          <RichText.Content value={ content } />
        </label>
        <div className="toggle-content">
          <InnerBlocks.Content />
        </div>
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
      content,
      dateID,
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
      'toggle-wrap': true,
      'toggle-box': true,
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
      className,
    } );

    return (
      <div { ...blockProps }>
        <input
          id={ 'toggle-checkbox-' + dateID }
          className="toggle-checkbox"
          type="checkbox"
        />
        <label
          className="toggle-button"
          htmlFor={ 'toggle-checkbox-' + dateID }
        >
          <RichText.Content value={ content } />
        </label>
        <div className="toggle-content">
          <InnerBlocks.Content />
        </div>
      </div>
    );
  },
};

const v3 = {
  attributes: {
    content: {
      type: 'string',
      selector: 'div',
      default: 'アコーディオン見出し',
    },
    dateID: {
      type: 'string',
      default: '',
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
      content,
      dateID,
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

    const className = classnames( {
      'toggle-wrap': true,
      'toggle-box': true,
      'block-box': true,
      'has-text-color': textColor || customTextColor,
      'has-background': backgroundColor || customBackgroundColor,
      'has-border-color': borderColor || customBorderColor,
      [ textClass ]: textClass,
      [ backgroundClass ]: backgroundClass,
      [ borderClass ]: borderClass,
      [ fontSizeClass ]: fontSizeClass,
    } );

    const styles = {
      '--cocoon-custom-background-color': customBackgroundColor || undefined,
      '--cocoon-custom-text-color': customTextColor || undefined,
      '--cocoon-custom-border-color': customBorderColor || undefined,
    };

    const blockProps = useBlockProps.save( {
      className,
      style: styles,
    } );

    return (
      <div { ...blockProps }>
        <input
          id={ 'toggle-checkbox-' + dateID }
          className="toggle-checkbox"
          type="checkbox"
        />
        <label
          className="toggle-button"
          htmlFor={ 'toggle-checkbox-' + dateID }
        >
          <RichText.Content value={ content } />
        </label>
        <div className="toggle-content">
          <InnerBlocks.Content />
        </div>
      </div>
    );
  },
};

export default [ v3, v2, v1 ];
