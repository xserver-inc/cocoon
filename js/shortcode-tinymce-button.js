(function() {

    tinymce.PluginManager.add('rashortcodes', function( editor, url )
    {
    		//console.log(editor);
        var shortcodeValues = [];
        jQuery.each(shortcodes_button, function(i)
        {
            shortcodeValues.push({text: shortcodes_button[i]['code'], value:i, aaa:'aaa'});
        });

        editor.addButton('rashortcodes', {
            type: 'listbox',
            text: 'Shortcodes',
						icon				: false,
						fixedWidth  : true,
						onclick     : function(){editor.focus();},
            onselect: function(e) {
            //_v(e);
            _v(shortcodeValues);
            var selected_text = editor.selection.getContent();
            //_v(selected_text);
            var v = e.control.settings.text;
            tinyMCE.activeEditor.selection.setContent('[' + v + ']'+selected_text+'[/' + v + ']');
            },
            values: shortcodeValues
        });
    });
})();