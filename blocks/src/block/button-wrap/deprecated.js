/**
 * Cocoon Blocks
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
import {
  THEME_NAME,
  BUTTON_BLOCK,
  colorValueToSlug,
  keyColor,
} from '../../helpers';
import classnames from 'classnames';

import {
  RichText,
  getColorClassName,
  getFontSizeClass,
  useBlockProps,
} from '@wordpress/block-editor';
import { __ } from '@wordpress/i18n';

const { createBlock } = wp.blocks;

export const v1 = {
  attributes: {
    content: {
      type: 'string',
      default: __(
        'こちらをクリックしてリンクタグを設定エリア入力してください。この入力は公開ページで反映されません。',
        THEME_NAME
      ),
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
      backgroundColor: colorValueToSlug( color ),
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
    const classes = classnames( {
      [ 'btn-wrap' ]: true,
      [ `btn-wrap-${ colorValueToSlug( color ) }` ]:
        !! colorValueToSlug( color ),
      [ size ]: size,
      [ BUTTON_BLOCK ]: true,
      [ 'btn-wrap-circle' ]: !! isCircle,
      [ 'btn-wrap-shine' ]: !! isShine,
    } );
    return (
      <div className={ classes }>
        <RichText.Content value={ tag } />
      </div>
    );
  },
};

const v2 = {
  save( { attributes } ) {
    const {
      tag,
      size,
      isCircle,
      isShine,
      backgroundColor,
      textColor,
      borderColor,
      customBorderColor,
      fontSize,
    } = attributes;

    const backgroundClass = getColorClassName(
      'background-color',
      backgroundColor
    );
    const textClass = getColorClassName( 'color', textColor );
    const borderClass = getColorClassName( 'border-color', borderColor );
    const fontSizeClass = getFontSizeClass( fontSize );

    const classes = classnames( {
      [ 'btn-wrap' ]: true,
      [ 'btn-wrap-block' ]: true,
      [ BUTTON_BLOCK ]: true,
      [ size ]: size,
      [ 'btn-wrap-circle' ]: !! isCircle,
      [ 'btn-wrap-shine' ]: !! isShine,
      'has-text-color': textColor,
      'has-background': backgroundColor,
      'has-border-color': borderColor || customBorderColor,
      [ textClass ]: textClass,
      [ backgroundClass ]: backgroundClass,
      [ borderClass ]: borderClass,
      [ fontSizeClass ]: fontSizeClass,
    } );

    const blockProps = useBlockProps.save( {
      className: classes,
    } );

    return (
      <div { ...blockProps }>
        <RichText.Content value={ tag } />
      </div>
    );
  },
};

// 修正前の現行saveが生成した空HTMLを認識するための固定属性スキーマ。
const preNoopenerFixAttributes = {
  tag: {
    type: 'string',
    default: '',
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
  fontSize: {
    type: 'string',
  },
  customFontSize: {
    type: 'string',
  },
  width: {
    type: 'string',
  },
  justifyContent: {
    type: 'string',
  },
  verticalAlignment: {
    type: 'string',
  },
};

// 修正前の現行saveが生成したHTMLを将来の変更から守る固定定義。
const preNoopenerFix = {
  apiVersion: 3,
  attributes: preNoopenerFixAttributes,
  supports: {
    align: [ 'left', 'center', 'right' ],
    customClassName: true,
    anchor: true,
    html: false,
  },

  migrate( attributes ) {
    return { ...attributes };
  },

  save( { attributes } ) {
    const {
      tag,
      size,
      isCircle,
      isShine,
      backgroundColor,
      textColor,
      borderColor,
      customBackgroundColor,
      customTextColor,
      customBorderColor,
      fontSize,
      width,
      justifyContent,
      verticalAlignment,
    } = attributes;

    const backgroundClass = getColorClassName(
      'background-color',
      backgroundColor
    );
    const textClass = getColorClassName( 'color', textColor );
    const borderClass = getColorClassName( 'border-color', borderColor );
    const fontSizeClass = getFontSizeClass( fontSize );

    const classes = classnames( {
      'btn-wrap': true,
      'btn-wrap-block': true,
      [ BUTTON_BLOCK ]: true,
      [ `is-content-justification-${ justifyContent }` ]: justifyContent,
      [ `is-vertically-aligned-${ verticalAlignment }` ]: verticalAlignment,
      [ size ]: size,
      'btn-wrap-circle': !! isCircle,
      'btn-wrap-shine': !! isShine,
      'has-text-color': textColor || customTextColor,
      'has-background': backgroundColor || customBackgroundColor,
      'has-border-color': borderColor || customBorderColor,
      [ textClass ]: textClass,
      [ backgroundClass ]: backgroundClass,
      [ borderClass ]: borderClass,
      [ fontSizeClass ]: fontSizeClass,
      'has-custom-width': width,
      [ `cocoon-block-button__width-${ width }` ]: width,
    } );

    const styles = {
      '--cocoon-custom-background-color': customBackgroundColor || undefined,
      '--cocoon-custom-text-color': customTextColor || undefined,
      '--cocoon-custom-border-color': customBorderColor || undefined,
    };

    const blockProps = useBlockProps.save( {
      className: classes,
      style: styles,
    } );

    // 修正前の変換不要時にundefinedを渡す保存挙動の固定再現。
    let tagCode;
    if ( ! tag.includes( ' rel="noopener' ) ) {
      tagCode = tag.replace(
        ' target="_blank"',
        ' target="_blank" rel="noopener"'
      );
    }

    return (
      <div { ...blockProps }>
        <RichText.Content value={ tagCode } />
      </div>
    );
  },
};

export default [ preNoopenerFix, v2, v1 ];
