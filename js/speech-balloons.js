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
        var selected_text = editor.selection.getContent();
        //_v(e);
        var before = e.control.settings.before;
        var after  = e.control.settings.after ;
        tinyMCE.activeEditor.selection.setContent(before + selected_text + after + "\n\n");
      },
      values: dropdownValues
    });
  });
})();