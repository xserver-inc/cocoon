/**
 * Cocoon Blocks
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */

import { THEME_NAME } from '../helpers.js';
import { Fragment } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import { registerFormatType, toggleFormat } from '@wordpress/rich-text';
import { BlockFormatControls } from '@wordpress/block-editor';
import { Fill, Slot, Toolbar, ToolbarButton, DropdownMenu } from '@wordpress/components';
import { displayShortcut } from '@wordpress/keycodes';
import { orderBy } from 'lodash';
const FORMAT_TYPE_NAME = 'cocoon-blocks/background-icons';

function CocoonRichTextToolbarButton({ name, shortcutType, shortcutCharacter, ...props }) {
  let shortcut;
  let fillName = 'CocoonToolbarControls';

  if (name) {
    fillName += `.${name}`;
  }

  if (shortcutType && shortcutCharacter) {
    shortcut = displayShortcut[shortcutType](shortcutCharacter);
  }

  return (
    <Fill name={fillName}>
      <ToolbarButton {...props} shortcut={shortcut} />
    </Fill>
  );
}

// 専用のドロップダウン用ボタンとメニューを登録
registerFormatType(FORMAT_TYPE_NAME, {
  title: 'buttons',
  tagName: 'dropdown',
  className: null,

  edit({ isActive, value, onChange }) {
    return (
      <BlockFormatControls>
        <div className="editor-format-toolbar block-editor-format-toolbar">
          <Toolbar>
            <Slot name="CocoonToolbarControls">
              {(fills) => {
                return (
                  fills.length !== 0 && (
                    <DropdownMenu
                      icon="marker"
                      label={__( '背景アイコン', THEME_NAME )}
                      hasArrowIndicator={true}
                      position="bottom left"
                      controls={orderBy(fills.map(([ { props } ]) => props), 'title')}
                    />
                  )
                );
              }}
            </Slot>
          </Toolbar>
        </div>
      </BlockFormatControls>
    );
  }
});

const tagTypes = [
  { tag: 'span', class: 'd-circle', title: __( '◎', THEME_NAME ) , icon: 'edit' },
  { tag: 'span', class: 's-circle', title: __( '○', THEME_NAME ), icon: 'edit' },
  { tag: 'span', class: 'triangle', title: __( '△', THEME_NAME ), icon: 'edit' },
  { tag: 'span', class: 'cross', title: __( '×', THEME_NAME ), icon: 'edit' },
  { tag: 'span', class: 'b-check', title: __( '✓', THEME_NAME ), icon: 'edit' },
  { tag: 'span', class: 'b-question', title: __( '？', THEME_NAME ), icon: 'edit' },
  // { tag: 'span', class: 'b-none', title: __( '-', THEME_NAME ), icon: 'edit' },
];

tagTypes.forEach(({ tag, class: className, title, icon }) => {
  let type = `cocoon-blocks/richtext-${tag}`;
  if (className !== null) {
    type += `-${className}`;
  }

  registerFormatType(type, {
    title: title,
    tagName: tag,
    className: className,

    edit({ isActive, value, onChange }) {
      return (
        <Fragment>
          {/* RichTextToolbarButtonからCocoonRichTextToolbarButtonへ登録先変更 */}
          <CocoonRichTextToolbarButton
            icon={icon}
            title={title}
            isActive={isActive}
            onClick={() => onChange(toggleFormat(value, { type: type }))}
          />
        </Fragment>
      );
    },
  });
});
