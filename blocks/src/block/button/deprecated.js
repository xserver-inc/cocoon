/**
 * Cocoon Blocks
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
import { THEME_NAME, BUTTON_BLOCK, colorValueToSlug, keyColor } from '../../helpers';
import classnames from 'classnames';

const { __ } = wp.i18n;
const { RichText } = wp.editor;

const { createBlock } = wp.blocks;

export const deprecated = [
  {
    attributes: {
      content: {
        type: 'string',
        default: __( 'ボタン', THEME_NAME ),
      },
      url: {
        type: 'string',
        default: '',
      },
      target: {
        type: 'string',
        default: '_self',
      },
      color: {
        type: 'string',
        default: keyColor,
      },
      size: {
        type: 'string',
        default: '',
      },
      isCircle: {
        type: 'boolean',
        default: false,
      },
      isShine: {
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
      const { content, color, size, url, target, isCircle, isShine, align } = attributes;

      return {
        content: content,
        size: size,
        url: url,
        target: target,
        isCircle: isCircle,
        isShine: isShine,
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
      const { content, color, size, url, target, isCircle, isShine } = attributes;
      const classes = classnames(
        {
          'btn': true,
          [ `btn-${ colorValueToSlug(color) }` ]: !! colorValueToSlug(color),
          [ size ]: size,
          [ 'btn-circle' ]: !! isCircle,
          [ 'btn-shine' ]: !! isShine,
        }
      );
      return (
        <div className={BUTTON_BLOCK}>
          <a
            href={ url }
            className={ classes }
            target={ target }
          >
            <RichText.Content
              value={ content }
            />
          </a>
        </div>
      );
    },
  },
];
