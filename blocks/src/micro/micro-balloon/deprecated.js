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
const DEFAULT_MSG = __( 'マイクロコピーバルーン', THEME_NAME );
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
      color: {
        type: 'string',
        default: '',
      },
      isCircle: {
        type: 'boolean',
        default: false,
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
      const { content, style, color, isCircle, align } = attributes;

      return {
        content: content,
        type: style,
        isCircle: isCircle,
        align: align,
        backgroundColor: colorValueToSlug(color),
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
      const { content, style, color, isCircle, align } = attributes;
      const classes = classnames(
        {
          [ 'micro-balloon' ]: true,
          [ style ]: !! style,
          [ `mc-${ colorValueToSlug(color) }` ]: !! colorValueToSlug(color),
          [ 'mc-circle' ]: !! isCircle,
          [ MICRO_COPY_CLASS ]: true,
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