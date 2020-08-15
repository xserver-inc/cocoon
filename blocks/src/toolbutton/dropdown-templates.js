/**
 * Cocoon Blocks
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */

import { THEME_NAME, TemplateToolbarButton } from '../helpers.js';
const { Fragment } = wp.element;
const { __ } = wp.i18n;
const { registerFormatType, insert } = wp.richText;
const { BlockFormatControls } = wp.editor;
const { Slot, Toolbar, DropdownMenu } = wp.components;
const FORMAT_TYPE_NAME = 'cocoon-blocks/templates';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { orderBy } from 'lodash';

var isTemplateVisible = Number(gbSettings['isTemplateVisible'] ? gbSettings['isTemplateVisible'] : 0);
if (isTemplateVisible) {
  gbTemplates.map((temp, index) => {
    var name = 'template-' + temp.id;
    var title = temp.title;
    var formatType = 'cocoon-blocks/' + name;
    if (temp.visible == '1') {
      registerFormatType( formatType, {
        title: title,
        tagName: name,
        className: null,
        edit({value, onChange}){
          const onToggle = () => onChange( insert( value, '[temp id=' + temp.id + ']', value.start, value.end ) );

          return (
            <Fragment>
              <TemplateToolbarButton
                icon={'editor-code'}
                title={<span className={name}>{title}</span>}
                onClick={ onToggle }
              />
            </Fragment>
          );
        }
      } );
    }

  });

  registerFormatType( FORMAT_TYPE_NAME, {
    title: __( 'テンプレート', THEME_NAME ),
    tagName: 'span',
    className: 'templates',
    edit({isActive, value, onChange}){

      return (
        <BlockFormatControls>
          <div className="editor-format-toolbar block-editor-format-toolbar">
            <Toolbar>
              <Slot name="Template.ToolbarControls">
                { ( fills ) => fills.length !== 0 &&
                  <DropdownMenu
                    icon={<FontAwesomeIcon icon={['fas', 'file-alt']} />}
                    label={__( 'テンプレート', THEME_NAME )}
                    className='templates'
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
