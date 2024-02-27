//
//  ボタン
//
(function () {
    const { Fragment, createElement } = wp.element;
    const { registerFormatType, toggleFormat } = wp.richText;
    const { RichTextToolbarButton, RichTextShortcut, BlockFormatControls } = wp.blockEditor;
 
    const el = createElement;
 
    const tagTypes = [];
    tagTypes.push({ tag: 'span', class: 'hvn-btn', title: 'インラインボタン', icon: 'button' });
 
    tagTypes.map( (idx) => {
        let type = 'celtis/richtext-' + idx.tag;
        if(idx.class !== null){
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
                        el( RichTextToolbarButton,
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
