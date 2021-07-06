/**
 * Cocoon Blocks
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */

import { THEME_NAME } from '../helpers.js';
import { __ } from '@wordpress/i18n';
import { registerFormatType } from '@wordpress/rich-text';
import { BlockFormatControls } from '@wordpress/block-editor';
import { Slot, Toolbar, DropdownMenu } from '@wordpress/components';
import { Icon, tag } from '@wordpress/icons';
import { orderBy } from 'lodash';
const FORMAT_TYPE_NAME = 'cocoon-blocks/badges';


var isBadgeVisible = Number(gbSettings['isBadgeVisible'] ? gbSettings['isBadgeVisible'] : 0);
if (isBadgeVisible) {
  registerFormatType( FORMAT_TYPE_NAME, {
    title: __( 'バッジ', THEME_NAME ),
    tagName: 'span',
    className: 'badges',
    edit({isActive, value, onChange}){

      return (
        <BlockFormatControls>
          <div className="editor-format-toolbar block-editor-format-toolbar">
            <Toolbar>
              <Slot name="Badge.ToolbarControls">
                { ( fills ) => fills.length !== 0 &&
                  <DropdownMenu
                    icon={<Icon icon={tag} size={32} />}
                    label={__( 'バッジ', THEME_NAME )}
                    className='badges'
                    controls={ orderBy( fills.map( ( [ { props } ] ) => props ), 'title' ) }
                  />
                }
              </Slot>
            </Toolbar>
          </div>
        </BlockFormatControls>
      );
    }
  } );
}
