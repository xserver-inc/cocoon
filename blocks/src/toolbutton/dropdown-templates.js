/**
 * Cocoon Blocks
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */

import { THEME_NAME, TemplateToolbarButton } from '../helpers.js';
import { Fragment } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import { registerFormatType, insert } from '@wordpress/rich-text';
import { BlockFormatControls } from '@wordpress/block-editor';
import { Slot, ToolbarGroup, ToolbarDropdownMenu } from '@wordpress/components';
import { Icon, code, page } from '@wordpress/icons';
import { orderBy } from 'lodash';
const FORMAT_TYPE_NAME = 'cocoon-blocks/templates';

var isTemplateVisible = Number(
  gbSettings[ 'isTemplateVisible' ] ? gbSettings[ 'isTemplateVisible' ] : 0
);
if ( isTemplateVisible ) {
  gbTemplates.map( ( temp, index ) => {
    var name = 'template-' + temp.id;
    var title = temp.title;
    var formatType = 'cocoon-blocks/' + name;
    if ( temp.visible == '1' ) {
      registerFormatType( formatType, {
        title: title,
        tagName: name,
        className: null,
        edit( { value, onChange } ) {
          const onToggle = () =>
            onChange(
              insert(
                value,
                '[temp id=' + temp.id + ']',
                value.start,
                value.end
              )
            );

          return (
            <Fragment>
              <TemplateToolbarButton
                icon={ <Icon icon={ code } size={ 32 } /> }
                title={ <span className={ name }>{ title }</span> }
                onClick={ onToggle }
              />
            </Fragment>
          );
        },
      } );
    }
  } );

  registerFormatType( FORMAT_TYPE_NAME, {
    title: __( 'テンプレート', THEME_NAME ),
    tagName: 'span',
    className: 'templates',
    edit( { isActive, value, onChange } ) {
      return (
        <BlockFormatControls>
          <div className="editor-format-toolbar block-editor-format-toolbar">
            <ToolbarGroup>
              <Slot name="Template.ToolbarControls">
                { ( fills ) =>
                  fills.length !== 0 && (
                    <ToolbarDropdownMenu
                      icon={ <Icon icon={ page } size={ 32 } /> }
                      label={ __( 'テンプレート', THEME_NAME ) }
                      className="templates"
                      controls={ orderBy(
                        fills.map( ( [ { props } ] ) => props ),
                        'title'
                      ) }
                    />
                  )
                }
              </Slot>
            </ToolbarGroup>
          </div>
        </BlockFormatControls>
      );
    },
  } );
}
