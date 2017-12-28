(function() {
  tinymce.PluginManager.add('function_texts', function( editor, url )  {
    var dropdownValues = [];
    jQuery.each(functionTexts, function(i)    {
        dropdownValues.push({
          text:   functionTexts[i]['title'],
          value:  functionTexts[i]['id'],
          template:  functionTexts[i]['text']
        });
    });

    editor.addButton('function_texts', {
      type: 'listbox',
      text: functionTextsTitle,
			icon				: false,
			fixedWidth  : true,
			onclick     : function(){editor.focus();},
      onselect: function(e) {
        var template  = e.control.settings.template ;
        tinyMCE.activeEditor.selection.setContent(template + "\n\n");
      },
      values: dropdownValues
    });
  });
})();