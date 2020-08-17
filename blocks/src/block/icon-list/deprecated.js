/**
 * Cocoon Blocks
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
import { THEME_NAME, BUTTON_BLOCK, CLICK_POINT_MSG, colorValueToSlug } from '../../helpers';
import classnames from 'classnames';

const { __ } = wp.i18n;
const { InnerBlocks, RichText } = wp.editor;

export const deprecated = [
  {
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
        borderColor: colorValueToSlug(borderColor),
        customBorderColor: undefined,
        iconColor: colorValueToSlug(iconColor),
        customIconColor: undefined,
        fontSize: undefined,
        customFontSize: undefined,
      };
    },

    save( { attributes } ) {
    const { title, icon, iconColor, borderColor } = attributes;
      const classes = classnames(
        {
          'iconlist-box': true,
          [ icon ]: !! icon,
          [ `iic-${ colorValueToSlug(iconColor) }` ]: !! colorValueToSlug(iconColor),
          [ `blank-box bb-${ colorValueToSlug(borderColor) }` ]: !! colorValueToSlug(borderColor),
          [ 'block-box' ]: true,
        }
      );
      return (
        <div className={ classes }>
          <div className="iconlist-title">
            <RichText.Content
              value={ title }
            />
          </div>
          <InnerBlocks.Content />
        </div>
      );
    },
  },
];