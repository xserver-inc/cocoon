const { registerBlockType } = wp.blocks;
const { Fragment } = wp.element;
const { ServerSideRender } = wp.editor;

registerBlockType( 'my-plugin/test-severside-as-shortcode-input', {
  title: 'test-severside-as-shortcode-input',
  icon: 'universal-access-alt',
  category: 'layout',

  attributes: {
    id: {
      type: 'integer',
      default: '1',
    },
    num: {
      type: 'integerg',
      default: '10',
    },
  },
  edit( {attributes, setAttributes} ) {
    const { id, num } = attributes;

    const onChangeInputId = newId => {
     setAttributes( { id: newId.target.value } );
    };
    const onChangeInputNum = newNum => {
     setAttributes( { num: newNum.target.value } );
    };

    return(
      <Fragment>
        <p>カテゴリーID:<input value={ id } onChange={ onChangeInputId } type="text" /></p>
        <p>表示数:<input value={ num } onChange={ onChangeInputNum } type="text" />※初期値は10件表示</p>
        <ServerSideRender
          block='my-plugin/test-severside-as-shortcode-input'
          attributes={ attributes }
        />
      </Fragment>
    );
  },
  save() {
    return null;
  }
} );