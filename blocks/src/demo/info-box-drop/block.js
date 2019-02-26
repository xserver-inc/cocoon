const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;
const { SelectControl } = wp.components;
const RichText = wp.editor.RichText;
const THEME_NAME = 'cocoon';

registerBlockType( 'cocoon-blocks/info-drop', {

  title: __( 'ドロップダウンデモ', THEME_NAME ),
  icon: 'info',
  category: THEME_NAME + '-block',

  attributes: {
    style: {
      type: 'string',
      default: 'primary-box'
    },
    content: {
      type: 'array',
      source: 'children',
      selector: 'div',
    },
  },

  edit( { attributes, setAttributes } ) {
    const { content, style } = attributes;

    function onChange(event){
      setAttributes({style: event.target.value});
    }

    function onChangeContent(newContent){
      setAttributes( { content: newContent } );
    }
    // var fs = new ActiveXObject("Scripting.FileSystemObject");
    // var file = fs.OpenTextFile("test.txt");

    // /* 1行目のみ読み込む */
    // text[0] = file.ReadLine();
    // console.log(text);

    return (
      <div className={style}>
        <SelectControl
          value={ style }
          onChange={ ( value ) => setAttributes( { style: value } ) }
          options={ [
            {
              value: 'primary-box',
              label: __( 'プライマリー（濃い水色）', THEME_NAME ),
            },
            {
              value: 'secondary-box',
              label: __( 'セカンダリー（濃い灰色）', THEME_NAME ),
            },
            {
              value: 'info-box',
              label: __( 'インフォ（薄い青）', THEME_NAME ),
            },
            {
              value: 'success-box',
              label: __( 'サクセス（薄い緑）', THEME_NAME ),
            },
            {
              value: 'warning-box',
              label: __( 'ワーニング（薄い黄色）', THEME_NAME ),
            },
            {
              value: 'danger-box',
              label: __( 'デンジャー（薄い赤色）', THEME_NAME ),
            },
            {
              value: 'light-box',
              label: __( 'ライト（白色）', THEME_NAME ),
            },
            {
              value: 'dark-box',
              label: __( 'ダーク（暗い灰色）', THEME_NAME ),
            },
          ] }
        />
        <RichText
          onChange={ onChangeContent }
          value={ content }
          multiline="p"
        />
      </div>
    )
  },

  save( { attributes } ) {
    const { content, style } = attributes;
    return (
      <div className={ style }>
          <RichText.Content
            value={ content }
            multiline="p"
          />
      </div>
    );
  }
} );