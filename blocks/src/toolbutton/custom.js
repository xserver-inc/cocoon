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
import { Icon, pencil } from '@wordpress/icons';

//表示数
const isCustomTexVisible = Number(
  gbSettings.customTextCount ? gbSettings.customTextCount : 0
);
if (isCustomTexVisible) {
  Array.from({ length: gbSettings.customTextCount }, (_, i) => i + 1).map(number => {
    let name = 'cocoon-custom-text-' + number;
    let formatTypeName = 'cocoon-blocks/' + name;
    let caption = __( 'カスタムテキスト', THEME_NAME ) + ' ' + number;
    registerFormatType( formatTypeName, {
      title: caption,
      tagName: 'span',
      className: name,
      edit( { isActive, value, onChange } ) {
        const onToggle = () =>
          onChange( toggleFormat( value, { type: formatTypeName } ) );

        return (
          <Fragment>
            <LetterToolbarButton
              icon={ <Icon icon={ pencil } size={ 32 } /> }
              title={ <span className={name}>{ caption }</span> }
              onClick={ onToggle }
              isActive={ isActive }
            />
          </Fragment>
        );
      },
    } );
  })
}

