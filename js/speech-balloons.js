/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
(function() {
  tinymce.PluginManager.add('speech_balloons', function( editor, url )  {
		//console.log(editor);
    var dropdownValues = [];
    jQuery.each(speechBalloons, function(i)    {
        dropdownValues.push({
          text:   speechBalloons[i]['title'],
          value:  speechBalloons[i]['id'],
          before: speechBalloons[i]['before'],
          after:  speechBalloons[i]['after']
        });
    });

    editor.addButton('speech_balloons', {
      type: 'listbox',
      text: speechBalloonsTitle,
			icon				: false,
			fixedWidth  : true,
			onclick     : function(){editor.focus();},
      onselect: function(e) {
        var selectedText = editor.selection.getContent();
        if (!selectedText) {
          selectedText = speechBalloonsEmptyText;
        }
        var before = e.control.settings.before;
        var after  = e.control.settings.after ;
        tinyMCE.activeEditor.selection.setContent(before + selectedText + after + "\n\n");
      },
      values: dropdownValues
    });
  });
})();