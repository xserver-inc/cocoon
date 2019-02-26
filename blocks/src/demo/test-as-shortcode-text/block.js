const { registerBlockType } = wp.blocks;
const { Fragment } = wp.element;
const { TextControl } = wp.components;
const { ServerSideRender } = wp.editor;

registerBlockType( 'my-plugin/test-as-shortcode-text', {
  title: 'test-as-shortcode-text',
  icon: 'universal-access-alt',
  category: 'layout',

  attributes: {
    content: {
      type: 'string'
    },
    id: {
      type: 'integer'
    },
  },
  edit( {attributes, setAttributes} ) {
    const { content } = attributes;

    const onChangeContent = newContent => {
      setAttributes( { content: newContent } );
    };

    return(
      <Fragment>
        <TextControl
          onChange={onChangeContent}
          value={content}
        />
        //ブロック内でサーバーでの処理(PHP側での処理)を表示する必要がない場合は、ServerSideRenderは削除しても可
        <ServerSideRender
          block='my-plugin/test-as-shortcode-text'
          attributes={ attributes }
        />
      </Fragment>
    );
  },
  save() {
    return null;
  }
} );