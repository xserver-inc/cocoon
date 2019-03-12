const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;
const { ServerSideRender } = wp.components;
const THEME_NAME = 'cocoon';
//var speechBalloons;


registerBlockType( 'cocoon-blocks/balloon', {
  title: __( '吹き出し', THEME_NAME ),
  icon: 'format-chat',
  category: THEME_NAME + '-block',

  edit: function( props ) {
    // ensure the block attributes matches this plugin's name
    //console.log(props);
console.log(speechBaloons);
var items = [1,2,3,4];
items.push( 5,6,7,8 );
console.log(items);
    return (
      <ServerSideRender
        block="cocoon-blocks/balloon-editor"
        attributes={ props.attributes }
      />
    );
  },

  save() {
    // Rendering in PHP
    return null;
  },
} );