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
        default: __( 'こちらをクリックしてリンクタグを設定エリア入力してください。この入力は公開ページで反映されません。', THEME_NAME ),
      },
      tag: {
        type: 'string',
        default: '',
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
      align: {
        type: 'string',
      },
    },
    supports: {
      align: [ 'left', 'center', 'right' ],
      customClassName: true,
    },

    migrate( attributes ) {
      const { content, tag, color, size, isCircle, isShine, align } = attributes;

      return {
        content: content,
        tag: tag,
        size: size,
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
      const { content, tag, color, size, isCircle, isShine, align } = attributes;
      const classes = classnames(
        {
          [ 'btn-wrap' ]: true,
          [ `btn-wrap-${ colorValueToSlug(color) }` ]: !! colorValueToSlug(color),
          [ size ]: size,
          [ BUTTON_BLOCK ]: true,
          [ 'btn-wrap-circle' ]: !! isCircle,
          [ 'btn-wrap-shine' ]: !! isShine,
        }
      );
      return (
        <div className={ classes }>
          <RichText.Content
            value={ tag }
          />
        </div>
      );
    },
  },
];
