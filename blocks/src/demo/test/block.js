import apiFetch from '@wordpress/api-fetch';
//const { apiFetch } = wp;
const { registerBlockType } = wp.blocks;
const { ServerSideRender } = wp.components;

registerBlockType( 'my-plugin/latest-post', {
  title: 'Latest Post',
  icon: 'megaphone',
  category: 'layout',


  edit: function( props ) {
    // const allPosts = apiFetch({path: '/wp-json/cocoon/v1/balloon/'}).then(fps => {
    //     postSelections.push({label: "Select a Post", value: 0});
    //     $.each( fps, function( key, val ) {
    //         postSelections.push({label: val.title.rendered, value: val.id});
    //     });
    //     console.log( postSelections );
    //     return postSelections;
    // });
    // const rootURL = "https://cocoon.local/wp-json/";
    // var a = apiFetch.use( apiFetch.createRootURLMiddleware( rootURL ) );
    // console.log( a );
    //let g_balloons = 'aaa';
    apiFetch( { path: '/wp-json/cocoon/v1/balloon/' } ).then( balloons => {
      //console.log( balloons );
      // props.balloons = balloons;
      // console.log( props );
      // return balloons;
      //const BALLOONS = balloons;
      // return ServerSideRender({
      //   block: "my-plugin/latest-post",
      //   attributes:  props.attributes
      // })

    } );
    //console.log( props );
    // ensure the block attributes matches this plugin's name
    return (
      <ServerSideRender
        block="my-plugin/latest-post-editor"
        attributes={ props.attributes }
      />
    );
  },

  save() {
    // Rendering in PHP
    return null;
  },
} );