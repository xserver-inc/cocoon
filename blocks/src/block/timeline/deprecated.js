/**
 * Cocoon Blocks
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
import { colorValueToSlug } from '../../helpers';
import classnames from 'classnames';

import {
  InnerBlocks,
  RichText,
  getColorClassName,
  getFontSizeClass,
  useBlockProps,
} from '@wordpress/block-editor';

const v1 = {
  attributes: {
    title: {
      type: 'string',
      default: '',
    },
    iconColor: {
      type: 'string',
      default: '',
    },
    borderColor: {
      type: 'string',
      default: '',
    },
    icon: {
      type: 'string',
      default: 'list-caret-right',
    },
  },

  migrate( attributes ) {
    const { title, icon, iconColor, borderColor } = attributes;

    return {
      title,
      icon,
      backgroundColor: undefined,
      customBackgroundColor: undefined,
      textColor: undefined,
      customTextColor: undefined,
      borderColor: colorValueToSlug( borderColor ),
      customBorderColor: undefined,
      iconColor: colorValueToSlug( iconColor ),
      customIconColor: undefined,
      fontSize: undefined,
      customFontSize: undefined,
    };
  },

  save( { attributes } ) {
    const { title, icon, iconColor, borderColor } = attributes;
    const classes = classnames( {
      'iconlist-box': true,
      [ icon ]: !! icon,
      [ `iic-${ colorValueToSlug( iconColor ) }` ]:
        !! colorValueToSlug( iconColor ),
      [ `blank-box bb-${ colorValueToSlug( borderColor ) }` ]:
        !! colorValueToSlug( borderColor ),
      'block-box': true,
    } );
    return (
      <div className={ classes }>
        <div className="iconlist-title">
          <RichText.Content value={ title } />
        </div>
        <InnerBlocks.Content />
      </div>
    );
  },
};

const v2 = {
  save( { attributes } ) {
    const {
      title,
      backgroundColor,
      textColor,
      borderColor,
      customBorderColor,
      pointColor,
      customPointColor,
      fontSize,
    } = attributes;

    const backgroundClass = getColorClassName(
      'background-color',
      backgroundColor
    );
    const textClass = getColorClassName( 'color', textColor );
    const borderClass = getColorClassName( 'border-color', borderColor );
    const pointClass = getColorClassName( 'point-color', pointColor );
    const fontSizeClass = getFontSizeClass( fontSize );

    const className = classnames( {
      'timeline-box': true,
      'cf': true,// eslint-disable-line prettier/prettier
      'block-box': true,
      'has-text-color': textColor,
      'has-background': backgroundColor,
      'has-border-color': borderColor || customBorderColor,
      'has-point-color': pointColor || customPointColor,
      [ textClass ]: textClass,
      [ backgroundClass ]: backgroundClass,
      [ borderClass ]: borderClass,
      [ pointClass ]: pointClass,
      [ fontSizeClass ]: fontSizeClass,
    } );

    const blockProps = useBlockProps.save( {
      className,
    } );

    return (
      <div { ...blockProps }>
        <div className="timeline-title">
          <RichText.Content value={ title } />
        </div>
        <ul className="timeline">
          <InnerBlocks.Content />
        </ul>
      </div>
    );
  },
};

const v3 = {
  attributes: {
    title: {
      type: 'string',
      default: 'タイムラインのタイトル',
    },
    items: {
      type: 'number',
      default: 1,
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
    pointColor: {
      type: 'string',
    },
    customPointColor: {
      type: 'string',
    },
    fontSize: {
      type: 'string',
    },
    notNestedStyle: {
      type: 'boolean',
      default: true,
    },
    backgroundColorValue: {
      type: 'string',
    },
    textColorValue: {
      type: 'string',
    },
    borderColorValue: {
      type: 'string',
    },
    pointColorValue: {
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
      title,
      backgroundColor,
      textColor,
      borderColor,
      customBackgroundColor,
      customTextColor,
      customBorderColor,
      pointColor,
      customPointColor,
      fontSize,
    } = attributes;

    const backgroundClass = getColorClassName(
      'background-color',
      backgroundColor
    );
    const textClass = getColorClassName( 'color', textColor );
    const borderClass = getColorClassName( 'border-color', borderColor );
    const pointClass = getColorClassName( 'point-color', pointColor );
    const fontSizeClass = getFontSizeClass( fontSize );

    const className = classnames( {
      'timeline-box': true,
      'cf': true,// eslint-disable-line prettier/prettier
      'block-box': true,
      'has-text-color': textColor || customTextColor,
      'has-background': backgroundColor || customBackgroundColor,
      'has-border-color': borderColor || customBorderColor,
      'has-point-color': pointColor || customPointColor,
      [ textClass ]: textClass,
      [ backgroundClass ]: backgroundClass,
      [ borderClass ]: borderClass,
      [ pointClass ]: pointClass,
      [ fontSizeClass ]: fontSizeClass,
    } );

    const styles = {
      '--cocoon-custom-background-color': customBackgroundColor || undefined,
      '--cocoon-custom-text-color': customTextColor || undefined,
      '--cocoon-custom-border-color': customBorderColor || undefined,
      '--cocoon-custom-point-color': customPointColor || undefined,
    };

    const blockProps = useBlockProps.save( {
      className,
      style: styles,
    } );

    return (
      <div { ...blockProps }>
        <div className="timeline-title">
          <RichText.Content value={ title } />
        </div>
        <ul className="timeline">
          <InnerBlocks.Content />
        </ul>
      </div>
    );
  },
};

export default [ v3, v2, v1 ];
