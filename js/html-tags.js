/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
(function() {
  tinymce.PluginManager.add('html_tags', function( editor, url )  {
		//console.log(editor);
    var dropdownValues = [];
    jQuery.each(htmlTags, function(i)    {
        dropdownValues.push({
          text:   htmlTags[i]['title'],
          value:  i,
          tag: htmlTags[i]['tag'],
          before: htmlTags[i]['before'],
          after:  htmlTags[i]['after']
        });
    });

    editor.addButton('html_tags', {
      type: 'listbox',
      text: htmlTagsTitle,
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