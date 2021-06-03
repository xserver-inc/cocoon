/**
 * Cocoon Blocks
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 * @reference: https://ja.wordpress.org/plugins/rich-text-extension/
 */

import { THEME_NAME } from '../helpers.js';
import { select } from '@wordpress/data';
import { RichTextToolbarButton } from '@wordpress/block-editor';
import { registerFormatType, removeFormat } from '@wordpress/rich-text';
import { __ } from '@wordpress/i18n';
import { map } from 'lodash';

const TITLE = __( '書式のクリア', THEME_NAME );

const isClearFormatVisible = Number(gbSettings['isClearFormatVisible'] ? gbSettings['isClearFormatVisible'] : 0);

if (isClearFormatVisible) {
  registerFormatType( 'cocoon-blocks/clear-format', {
    title: TITLE,
    tagName: 'span',
    className: 'clear-format',

    edit({ isActive, value, onChange }) {
      const onToggle = () => {
        const formatTypes = select( 'core/rich-text' ).getFormatTypes();
        if ( 0 < formatTypes.length ) {
          let newValue = value;
          map( formatTypes, ( activeFormat ) => {
            newValue = removeFormat( newValue, activeFormat.name );
          });
          onChange({ ...newValue });
        }
      };

      return (
        <RichTextToolbarButton
          icon="editor-removeformatting"
          title={ TITLE }
          onClick={ onToggle }
          isActive={ isActive }
        />
      );
    }
  });
}
