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
  RichText,
  getColorClassName,
  getFontSizeClass,
  useBlockProps,
} from '@wordpress/block-editor';
import { __ } from '@wordpress/i18n';

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
      title: title,
      icon: icon,
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
      [ 'block-box' ]: true,
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
      [ 'timeline-box' ]: true,
      [ 'cf' ]: true,
      [ 'block-box' ]: true,
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
      className: className,
    } );

    return (
      <div { ...blockProps }>
        <div class="timeline-title">
          <RichText.Content value={ title } />
        </div>
        <ul className="timeline">
          <InnerBlocks.Content />
        </ul>
      </div>
    );
  },
};

export default [ v2, v1 ];
