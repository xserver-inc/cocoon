/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
(function() {
  tinymce.PluginManager.add('function_texts', function( editor, url )  {
    var dropdownValues = [];
    jQuery.each(functionTexts, function(i)    {
        dropdownValues.push({
          text:   functionTexts[i]['title'],
          value:  functionTexts[i]['id'],
          shrotecode:  functionTexts[i]['shrotecode']
        });
    });

    editor.addButton('function_texts', {
      type: 'listbox',
      text: functionTextsTitle,
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