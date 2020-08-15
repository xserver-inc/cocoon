/**
 * Cocoon Blocks
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
import { THEME_NAME, BUTTON_BLOCK, colorValueToSlug } from '../../helpers';
import classnames from 'classnames';

const { __ } = wp.i18n;
const { RichText } = wp.editor;
const DEFAULT_MSG = __( 'マイクロコピーテキスト', THEME_NAME );
const MICRO_COPY_CLASS = 'micro-copy';

export const deprecated = [
  {
    attributes: {
      content: {
        type: 'string',
        default: DEFAULT_MSG,
      },
      style: {
        type: 'string',
        default: 'micro-top',
      },
      align: {
        type: 'string',
      },
    },
    supports: {
      align: [ 'left', 'center', 'right' ],
      customClassName: true,
    },

    migrate( attributes ) {
      const { content, style, align } = attributes;

      return {
        content: content,
        type: style,
        align: align,
        backgroundColor: undefined,
        customBackgroundColor: undefined,
        textColor: undefined,
        customTextColor: undefined,
        borderColor: undefined,
        customBorderColor: undefined,
        fontSize: undefined,
        customFontSize: undefined,
      };
    },

    save( { attributes } ) {
      const { content, style, align } = attributes;
      const classes = classnames(
        {
          [ MICRO_COPY_CLASS ]: true,
          [ style ]: !! style,
          [ 'block-box' ]: true,
        }
      );
      return (
        <div className={ classes }>
          <RichText.Content
            value={ content }
          />
        </div>
      );
    },
  },
];