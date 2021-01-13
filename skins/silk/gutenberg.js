// フォーマット（下線）
(function (richText, element, blockEditor, primitives) {
  var el = element.createElement;
  richText.registerFormatType("silk/span-underline", {
    title: "下線",
    tagName: "span",
    className: "span-underline",
    edit(props) {
      return el(blockEditor.RichTextToolbarButton, {
        icon: el(
          primitives.SVG,
          { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 20 20" },
          el(primitives.Path, {
            d:
              "M14 5h-2v5.71c0 1.99-1.12 2.98-2.45 2.98-1.32 0-2.55-1-2.55-2.96v-5.73h-2v5.87c0 1.91 1 4.54 4.48 4.54 3.49 0 4.52-2.58 4.52-4.5v-5.91zM14 18v-2h-9v2h9z",
          })
        ),
        title: "下線",
        onClick: function () {
          props.onChange(
            richText.toggleFormat(props.value, {
              type: "silk/span-underline",
            })
          );
        },
        isActive: props.isActive,
      });
    },
  });
})(
  window.wp.richText,
  window.wp.element,
  window.wp.blockEditor,
  window.wp.primitives
);
