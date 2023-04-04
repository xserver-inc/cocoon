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
  save( props ) {
    const {
      title,
      icon,
      backgroundColor,
      textColor,
      borderColor,
      customBorderColor,
      iconColor,
      customIconColor,
      fontSize,
    } = props.attributes;

    const backgroundClass = getColorClassName(
      'background-color',
      backgroundColor
    );
    const textClass = getColorClassName( 'color', textColor );
    const borderClass = getColorClassName( 'border-color', borderColor );
    const iconClass = getColorClassName( 'icon-color', iconColor );
    const fontSizeClass = getFontSizeClass( fontSize );

    const className = classnames( {
      'iconlist-box': true,
      'blank-box': true,
      [ icon ]: !! icon,
      'block-box': true,
      'has-text-color': textColor,
      'has-background': backgroundColor,
      'has-border-color': borderColor || customBorderColor,
      'has-icon-color': iconColor || customIconColor,
      [ textClass ]: textClass,
      [ backgroundClass ]: backgroundClass,
      [ borderClass ]: borderClass,
      [ iconClass ]: iconClass,
      [ fontSizeClass ]: fontSizeClass,
    } );
    const iconListBlockProps = useBlockProps.save( {
      className: className,
    } );
    // const iconListTitleBlockProps = useBlockProps.save({
    //     className: 'iconlist-title',
    // });
    return (
      <div { ...iconListBlockProps }>
        <div className="iconlist-title">
          <RichText.Content value={ title } />
        </div>
        <InnerBlocks.Content />
      </div>
    );
  },
};

export default [ v2, v1 ];
