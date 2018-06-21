/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
(function() {
  tinymce.PluginManager.add('item_rankings', function( editor, url )  {
    var dropdownValues = [];
    jQuery.each(itemRankings, function(i)    {
        dropdownValues.push({
          text:   itemRankings[i]['title'],
          value:  itemRankings[i]['id'],
          shrotecode:  itemRankings[i]['shrotecode']
        });
    });

    editor.addButton('item_rankings', {
      type: 'listbox',
      text: itemRankingsTitle,
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