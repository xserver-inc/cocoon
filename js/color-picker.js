(function( $ ) {
    var options = {
        defaultColor: false,
        change: function(event, ui){},
        clear: function() {},
        hide: true,
        palettes: true
    };
    $('.color-picker').wpColorPicker(options);
})( jQuery );