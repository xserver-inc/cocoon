/**
 * Cocoon Blocks
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */

import { THEME_NAME, LetterToolbarButton } from '../helpers.js';
import { Fragment } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import { registerFormatType, toggleFormat } from '@wordpress/rich-text';
import { ToolbarButton } from '@wordpress/components';
import { Icon, formatBold } from '@wordpress/icons';
const FORMAT_TYPE_NAME = 'cocoon-blocks/bold';
const TITLE = __( '太字（boldクラス指定）', THEME_NAME );

registerFormatType( FORMAT_TYPE_NAME, {
  title: TITLE,
  tagName: 'span',
  className: 'bold',
  edit( { isActive, value, onChange } ) {
    const onToggle = () =>
      onChange( toggleFormat( value, { type: FORMAT_TYPE_NAME } ) );

    return (
      <Fragment>
        <LetterToolbarButton
          icon={ <Icon icon={ formatBold } size={ 32 } /> }
          title={ <span className="bold">{ TITLE }</span> }
          onClick={ onToggle }
          isActive={ isActive }
        />
      </Fragment>
    );
  },
} );
