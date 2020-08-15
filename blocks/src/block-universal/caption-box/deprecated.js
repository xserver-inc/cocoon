/**
 * Cocoon Blocks
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
import { THEME_NAME, BUTTON_BLOCK, getIconClass, colorValueToSlug } from '../../helpers';
import classnames from 'classnames';

const { __ } = wp.i18n;
const { InnerBlocks, RichText } = wp.editor;

const CAPTION_BOX_CLASS = 'caption-box';
const DEFAULT_MSG = __( '見出し', THEME_NAME );

export const deprecated = [
  {
    attributes: {
      content: {
        type: 'string',
        default: DEFAULT_MSG,
      },
      color: {
        type: 'string',
        default: '',
      },
      icon: {
        type: 'string',
        default: '',
      },
    },

    migrate( attributes ) {
      const { content, color, icon } = attributes;

      return {
        content: content,
        icon: icon,
        backgroundColor: undefined,
        customBackgroundColor: undefined,
        textColor: undefined,
        customTextColor: undefined,
        borderColor: colorValueToSlug(color),
        customBorderColor: undefined,
        fontSize: undefined,
        customFontSize: undefined,
      };
    },

    save( { attributes } ) {
      const { content, color, icon } = attributes;
      const classes = classnames(
        {
          [ CAPTION_BOX_CLASS ]: true,
          [ `cb-${ colorValueToSlug(color) }` ]: !! colorValueToSlug(color),
          [ 'block-box' ]: true,
        }
      );
      return (
        <div className={ classes }>
          <div className={'caption-box-label block-box-label' + getIconClass(icon)}>
            <span className={'caption-box-label-text block-box-label-text'}>
              <RichText.Content
                value={ content }
              />
            </span>
          </div>
          <div className="caption-box-content">
            <InnerBlocks.Content />
          </div>
        </div>
      );
    },
  },
];