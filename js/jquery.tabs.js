
// jquery.tabs.js

var tabs_new = function( tabs_id, opts ) {

    // use module pattern (see JavaScript - The Good Parts)

    function _debug( msg )
    {
       if( window.console && window.console.log ) 
         console.log( "[debug] " + msg ); 
    }
    
    var $tabs,
        $tabsNav,
        $tabsNavItems,
        $tabsPanels;

    var defaults = { };
    var settings;
        
  function _init( tabs_id, opts )
  {
    settings = $.extend( {}, defaults, opts );

    // add default style only once to head
    if( $( 'head #tabs-styles-default').length == 0 ) {

      // little hack: by default - use background-color of body
      var bodyBackgroundColor = $('body').css('backgroundColor');

      // _debug( 'body backgroundColor:' + bodyBackgroundColor + " : " + typeof( bodyBackgroundColor ));
      if( bodyBackgroundColor === 'rgba(0, 0, 0, 0)' )
        bodyBackgroundColor = 'white';   // standard white transparent background? use just white

      // todo/fix: possible in css - non transparent background-color using default ??
      //  -- setting opacity: 1.0 tried but was NOT working; anything else to try?
        
      $( 'head' ).append( "<style id='tabs-styles-default'>\n"+
       ".tabs-nav  { \n"+
       "  margin: 0; \n"+
       "  padding: 0; \n"+
       "  list-style-type: none; \n"+
       "  border-bottom: 1px solid #999; \n"+
       "} \n"+
       ".tabs-nav li { \n"+
       "  display: inline-block; \n"+
       "  margin: 0 0 0 5px; \n"+            // top right bottom left
       "  padding: 5px 15px 3px 15px; \n"+   // top right bottom left
       "  border: 1px solid transparent; \n"+
       "  border-bottom: none;  \n"+
       "  cursor: pointer; \n"+
       "  background-color: "+ bodyBackgroundColor +"; \n"+
       "} \n"+
       ".tabs-nav li.selected { \n"+
       "  padding-bottom: 4px;  \n"+            // note: for bottom use +1px (3px+1px=4px) to overwrite border for "tab" selected effect (1/2)
       "  margin-bottom: -1px;  \n"+            // note: for bottom use -1px (0px-1px=-1px) to overwrite border for "tab" selected effect (2/2)
       "  border: 1px solid #999; \n"+
       "  border-bottom: none;  \n"+
       "} \n"+
       ".tabs-panel { \n"+
       "  padding: 10px; \n"+
       "} \n"+
       "</style>" );
    }

    $tabs         = $( tabs_id );
    $tabsNav      = $tabs.children( 'ul' );    // note: ul must be child of tabs div
    $tabsNavItems = $tabsNav.children( 'li' );  // note: li must be child of tabs div > ul
    $tabsPanels   = $tabs.children( 'div' );  // all div children - note: divs must be child of tabs div

    $tabsNav.addClass( 'tabs-nav' );
    $tabsPanels.addClass( 'tabs-panel' );
    $tabsPanels.hide();

    // when a tab gets clicked; add handler
    $tabsNavItems.each( function( itemIndex, item ) {
       $(item).click( function() {
           // _debug( "itemIndex:" + itemIndex );
           _select( itemIndex );
       });
    });

    // auto-select first tab on init/startup
    _select( 0 );


  } // function _init
  
  _init( tabs_id, opts );  

  
  function _select( index ) {
    // step 1) reset selected tab (if present)
    $tabsNavItems.filter( '.selected' ).removeClass( 'selected' );
    $tabsPanels.filter( '.selected' ).removeClass( 'selected' ).hide();

    // step 2) set new selected tab   
    $tabsNavItems.eq( index ).addClass( 'selected' );
    $tabsPanels.eq( index ).addClass( 'selected' ).show(); 
  }
  
    return {
      select: function( index ) {
        _select( index );
        return this;
      }
    };  
};



function tabify( tabs_id, opts ) {
  var tabs = tabs_new( tabs_id, opts );
  return tabs;
}


////////////////////
// wrapper for jquery plugin


(function( $ ) {

    function debug( msg ) {
      if( window.console && window.console.log ) {
        window.console.log( "[debug] "+msg );
      }
    }

    function setup_tabs( tabs_el, opts ) {
      debug( "hello from setup_tabs" );
      var tabs = tabs_new( tabs_el, opts );
      var $tabs = $(tabs_el);
      
      // NB: attach widget to dom element
      // - use like $('#tabs').data( 'widget' ).select(2); etc.
      $tabs.data( 'widget', tabs );
      return tabs_el;
    }

    debug( 'add jquery fn tabify' );

    $.fn.tabify = function( opts ) {
        debug( "calling tabify" );
        return this.each( function( index, tabs_el ) {
          debug( "before setup_tabs["+ index +"]" );
          setup_tabs( tabs_el, opts );
          debug( "after setup_tabs["+ index +"]" );
        });
    };

}( jQuery ));


