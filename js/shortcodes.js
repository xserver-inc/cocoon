/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
(function() {
  tinymce.PluginManager.add('shortcodes', function( editor, url )  {
		//console.log(editor);
    var dropdownValues = [];
    jQuery.each(shortcodes, function(i)    {
        dropdownValues.push({
          text:   shortcodes[i]['title'],
          value:  i,
          tag: shortcodes[i]['tag'],
          before: shortcodes[i]['before'],
          after:  shortcodes[i]['after']
        });
    });

    editor.addButton('shortcodes', {
      type: 'listbox',
      text: shortcodesTitle,
			icon				: false,
			fixedWidth  : true,
			onclick     : function(){editor.focus();},
      onselect: function(e) {
        var selectedText = editor.selection.getContent();
        var before = e.control.settings.before;
        var after  = e.control.settings.after ;
        var tag  = e.control.settings.tag ;
        var content = '';
        if (selectedText) {
          content = before + selectedText + after;
        } else {
          content = tag;
        }
        tinyMCE.activeEditor.selection.setContent(content);

      },
      values: dropdownValues
    });
  });
})();