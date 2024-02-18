(function() {

  const { Fragment, createElement } = wp.element;
  const { registerFormatType, toggleFormat } = wp.richText;
  const { BlockFormatControls } = wp.editor;
  const { __ } = wp.i18n;
  const { Fill, Slot, Toolbar, ToolbarButton, DropdownMenu } = wp.components;
  const { displayShortcut } = wp.keycodes;
  const { orderBy } = lodash;

  const el = createElement;

  function CocoonRichTextToolbarButton( { name, shortcutType, shortcutCharacter, ...props } ) {
    let shortcut;
    let fillName = 'CocoonToolbarControls';

    if ( name ) {
      fillName += `.${ name }`;
    }

    if ( shortcutType && shortcutCharacter ) {
      shortcut = displayShortcut[ shortcutType ]( shortcutCharacter );
    }

    return (
      el( Fill,
        { name: fillName },
        el( ToolbarButton,
          props,
          { shortcut: shortcut }
        ),
      )
    );
  };

  // 専用のドロップダウン用ボタンとメニューを登録
  registerFormatType( 'cocoon/dropdown', {
    title: 'buttons',
    tagName: 'dropdown',
    className: null,

    edit( { isActive, value, onChange } ) {
      return (
        el( BlockFormatControls,
          {},
          el( 'div',
            { className: 'editor-format-toolbar block-editor-format-toolbar' },
            el( Toolbar,
              {},
              el( Slot,
                { name: 'CocoonToolbarControls' },
                (fills) => {
                  return ( fills.length !== 0 &&
                    el( DropdownMenu,
                      { icon: 'marker',
                        label: __( '背景アイコン', 'cocoon' ),
                        hasArrowIndicator: true,
                        position: 'bottom left',
                        controls : orderBy( fills.map( ( [ { props } ] ) => props ), 'title'),
                      }
                    )
                  );
                }
              )
            ),
          )
        )
      );
    }
  });

  const tagTypes = [];
  tagTypes.push({ tag: 'span', class: 'd-circle', title: __( '◎', 'cocoon' ), icon: 'edit' });
  tagTypes.push({ tag: 'span', class: 's-circle', title: __( '○', 'cocoon' ), icon: 'edit' });
  tagTypes.push({ tag: 'span', class: 'triangle', title: __( '△', 'cocoon' ), icon: 'edit' });
  tagTypes.push({ tag: 'span', class: 'cross',    title: __( '×', 'cocoon' ), icon: 'edit' });

  tagTypes.map( (idx) => {
    let type = 'cocoon/richtext-' + idx.tag;
    if (idx.class !== null) {
      type += '-' + idx.class;
    }
    registerFormatType( type, {
      title: idx.title,
      tagName: idx.tag,
      className: idx.class,

      edit( { isActive, value, onChange } ) {
        return (
          el( Fragment,
            {},
            // RichTextToolbarButtonからCocoonRichTextToolbarButtonへ登録先変更
            el( CocoonRichTextToolbarButton,
              { icon: idx.icon,
                title: idx.title,
                isActive: isActive,
                onClick: () => onChange( toggleFormat(value, { type: type })),
              }
            )
          )
        );
      },
    });
  })
}());
