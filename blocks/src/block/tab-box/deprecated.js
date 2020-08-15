/**
 * Cocoon Blocks
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
import { THEME_NAME, BUTTON_BLOCK, CLICK_POINT_MSG, colorValueToSlug } from '../../helpers';
import classnames from 'classnames';

const { __ } = wp.i18n;
const { InnerBlocks } = wp.editor;

const { createBlock } = wp.blocks;

export const deprecated = [
  {
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
        borderColor: colorValueToSlug(color),
        customBorderColor: undefined,
        fontSize: undefined,
        customFontSize: undefined,
      };
    },

    save( { attributes } ) {
    const { content, style, color } = attributes;
      const classes = classnames(
        {
          'blank-box': true,
          'bb-tab': true,
          [ style ]: !! style,
          [ `bb-${ colorValueToSlug(color) }` ]: !! colorValueToSlug(color),
          [ 'block-box' ]: true,
        }
      );
      return (
        <div className={ classes }>
          <InnerBlocks.Content />
        </div>
      );
    },
  },
];