/**
 * Cocoon Blocks
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
import { THEME_NAME, BUTTON_BLOCK, colorValueToSlug } from '../../helpers';
import classnames from 'classnames';

import {
  RichText,
  getColorClassName,
  useBlockProps,
} from '@wordpress/block-editor';
import { __ } from '@wordpress/i18n';

const DEFAULT_MSG = __( 'マイクロコピーテキスト', THEME_NAME );
const MICRO_COPY_CLASS = 'micro-copy';

const v1 = {
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
    const classes = classnames( {
      [ MICRO_COPY_CLASS ]: true,
      [ style ]: !! style,
      [ 'block-box' ]: true,
    } );
    return (
      <div className={ classes }>
        <RichText.Content value={ content } />
      </div>
    );
  },
};

const v2 = {
  save( { attributes } ) {
    const { content, type, icon, textColor } = attributes;

    const textClass = getColorClassName( 'color', textColor );

    const className = classnames( {
      [ 'micro-text' ]: true,
      [ MICRO_COPY_CLASS ]: true,
      [ type ]: !! type,
      'has-text-color': textColor,
      [ textClass ]: textClass,
    } );
    const blockProps = useBlockProps.save( {
      className: className,
    } );

    return (
      <div { ...blockProps }>
        <span className="micro-text-content micro-content">
          { icon && (
            <span
              className={ classnames( 'micro-text-icon', 'micro-icon', icon ) }
            ></span>
          ) }
          <RichText.Content value={ content } />
        </span>
      </div>
    );
  },
};

export default [ v2, v1 ];
