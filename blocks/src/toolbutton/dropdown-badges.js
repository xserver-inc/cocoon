/**
 * Cocoon Blocks
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */

import { THEME_NAME } from '../helpers.js';
const { Fragment } = wp.element;
const { __ } = wp.i18n;
const { registerFormatType, toggleFormat } = wp.richText;
const { BlockFormatControls } = wp.editor;
const { Slot, Toolbar, DropdownMenu } = wp.components;
const FORMAT_TYPE_NAME = 'cocoon-blocks/badges';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { orderBy } from 'lodash';


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
                    icon={<FontAwesomeIcon icon="tag" />}
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
