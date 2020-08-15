/**
 * Cocoon Blocks
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */

import { THEME_NAME, RankingToolbarButton } from '../helpers.js';
const { Fragment } = wp.element;
const { __ } = wp.i18n;
const { registerFormatType, insert } = wp.richText;
const { BlockFormatControls } = wp.editor;
const { Slot, Toolbar, DropdownMenu } = wp.components;
const FORMAT_TYPE_NAME = 'cocoon-blocks/rankings';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { orderBy } from 'lodash';

var isRankingVisible = Number(gbSettings['isRankingVisible'] ? gbSettings['isRankingVisible'] : 0);
if (isRankingVisible) {
  gbItemRankings.map((rank, index) => {
    var name = 'ranking-' + rank.id;
    var title = rank.title;
    var formatType = 'cocoon-blocks/' + name;
    if (rank.visible == '1') {
      registerFormatType( formatType, {
        title: title,
        tagName: name,
        className: null,
        edit({value, onChange}){
          const onToggle = () => onChange( insert( value, '[rank id=' + rank.id + ']', value.start, value.end ) );

          return (
            <Fragment>
              <RankingToolbarButton
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
    title: __( 'ランキング', THEME_NAME ),
    tagName: 'span',
    className: 'rankings',
    edit({isActive, value, onChange}){

      return (
        <BlockFormatControls>
          <div className="editor-format-toolbar block-editor-format-toolbar">
            <Toolbar>
              <Slot name="Ranking.ToolbarControls">
                { ( fills ) => fills.length !== 0 &&
                  <DropdownMenu
                    icon={<FontAwesomeIcon icon={['fas', 'crown']} />}
                    label={__( 'ランキング', THEME_NAME )}
                    className='rankings'
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
