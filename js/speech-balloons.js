(function() {
  tinymce.PluginManager.add('speech_bolloons', function( editor, url )  {
		//console.log(editor);
    var dropdownValues = [];
    jQuery.each(speech_balloons, function(i)    {
        dropdownValues.push({
          text:   speech_balloons[i]['title'],
          value:  speech_balloons[i]['id'],
          before: speech_balloons[i]['before'],
          after:  speech_balloons[i]['after']
        });
    });

    editor.addButton('speech_bolloons', {
      type: 'listbox',
      text: speech_balloons_title,
			icon				: false,
			fixedWidth  : true,
			onclick     : function(){editor.focus();},
      onselect: function(e) {
        var selectedText = editor.selection.getContent();
        if (!selectedText) {
          selectedText = speech_balloons_empty_text;
        }
        var before = e.control.settings.before;
        var after  = e.control.settings.after ;
        tinyMCE.activeEditor.selection.setContent(before + selectedText + after + "\n\n");
      },
      values: dropdownValues
    });
  });
})();