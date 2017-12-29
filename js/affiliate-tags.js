(function() {
  tinymce.PluginManager.add('affiliate_tags', function( editor, url )  {
    var dropdownValues = [];
    jQuery.each(affiliateTags, function(i)    {
        dropdownValues.push({
          text:   affiliateTags[i]['title'],
          value:  affiliateTags[i]['id'],
          shrotecode:  affiliateTags[i]['shrotecode']
        });
    });

    editor.addButton('affiliate_tags', {
      type: 'listbox',
      text: affiliateTagsTitle,
			icon				: false,
			fixedWidth  : true,
			onclick     : function(){editor.focus();},
      onselect: function(e) {
        var shrotecode = e.control.settings.shrotecode;
        tinyMCE.activeEditor.selection.setContent(shrotecode);
      },
      values: dropdownValues
    });
  });
})();