const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;
const { InnerBlocks, RichText, InspectorControls } = wp.editor;
const { PanelBody, SelectControl, BaseControl, ServerSideRender } = wp.components;
const { Fragment } = wp.element;

registerBlockType( 'my-plugin/test-get-id', {
  title: 'ID取得サンプル',
  icon: 'megaphone',
  category: 'layout',

  edit: function( props ) {
    const { content } = attributes;

    const onChangeContent = newContent => {
      setAttributes( { content: newContent } );
    };
    // ensure the block attributes matches this plugin's name
    return (
      <Fragment>
        <TextControl
          onChange={onChangeContent}
          value={content}
        />
        <ServerSideRender
          block="my-plugin/test-get-id-editor"
          attributes={ props.attributes }
        />
        </Fragment>
    );
  },

  save() {
    // Rendering in PHP
    return null;
  },
} );