/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
(function() {
  function getDateID(){
    //Dateオブジェクトを利用
    var d = new Date();
    var year  = d.getFullYear();
    var month = d.getMonth() + 1;
    var month = ( month          < 10 ) ? '0' + month          : month;
    var day   = ( d.getDate()    < 10 ) ? '0' + d.getDate()    : d.getDate();
    var hour  = ( d.getHours()   < 10 ) ? '0' + d.getHours()   : d.getHours();
    var min   = ( d.getMinutes() < 10 ) ? '0' + d.getMinutes() : d.getMinutes();
    var sec   = ( d.getSeconds() < 10 ) ? '0' + d.getSeconds() : d.getSeconds();
    var dateID = '' + year + month + day + hour + min + sec;
    return dateID;
  }

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
        var dateID = getDateID();
        var content = content.replace(/COCOON_DATE_ID/g, dateID);
        tinyMCE.activeEditor.selection.setContent(content);

      },
      values: dropdownValues
    });
  });
})();
