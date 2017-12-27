(function() {


	tinymce.PluginManager.add('shortcodedrop', function(editor,url){
		var mlb = {};
		editor.addButton('shortcodedrop', {
			type    		: 'listbox',
			text				: listTitle,
			icon				: false,
			fixedWidth  : true,
			onclick     : function(){editor.focus();},//Set Focus of the current editor
			onselect 		: function(){

				var v = this.value();
				var clean_code   = decodeURI(v.substring(0,v.length-2));
				var close_code   = clean_code;
				var space = '';
				if((space = clean_code.indexOf(' '))>0){
					close_code = clean_code.substring(0,space); //get only the name of the shortcode to close it properly
				}
				var close_option = parseInt(v.substring(v.length - 1, v.length));

				var open = '[';
				var close = ']';
				var openclose = '[/';

				if(tinymce.activeEditor.selection.getContent() == ''){ //no content to wrap
					if(!close_option){ // auto close tag
						tinymce.activeEditor.selection.setContent( open + clean_code + close + openclose + close_code + close );
					}
					else {
						tinymce.activeEditor.selection.setContent( open + clean_code + close );
					}
				}
				else {// possibly wrap content
					if(!close_option){ //auto close tag - wrap content
						tinymce.activeEditor.selection.setContent(open + clean_code + close + tinyMCE.activeEditor.selection.getContent() + openclose + close_code + close);
					}
					else { // do not close tag - place content at end (just in case)
						tinymce.activeEditor.selection.setContent(open + clean_code + close + tinyMCE.activeEditor.selection.getContent());
					}
				}
			},
			values: btslb_shortcodes
	 });
	});
})();