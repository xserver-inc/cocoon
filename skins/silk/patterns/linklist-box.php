<?php
//リンクリストボックス
return [
	'title'         => 'リンクリストボックス',
	'content'       => "<!-- wp:cocoon-blocks/tab-caption-box-1 {\"content\":\"関連リンク\",\"icon\":\"fab-file-text\",\"borderColor\":\"black\"} -->\n<div class=\"wp-block-cocoon-blocks-tab-caption-box-1 tab-caption-box block-box has-border-color has-black-border-color\"><div class=\"tab-caption-box-label block-box-label box-label fab-file-text\"><span class=\"tab-caption-box-label-text block-box-label-text box-label-text\">関連リンク</span></div><div class=\"tab-caption-box-content block-box-content box-content\"><!-- wp:list {\"className\":\"is-style-link\"} -->\n<ul class=\"is-style-link\"><li><a href=\"https://wp-cocoon.com/\" data-type=\"URL\" data-id=\"https://wp-cocoon.com/\" target=\"_blank\" rel=\"noreferrer noopener\">テキストリンク</a></li></ul>\n<!-- /wp:list --></div></div>\n<!-- /wp:cocoon-blocks/tab-caption-box-1 -->",
	'description'   => 'タブ見出しボックスブロック内にリンクリストを入れたパターンです。',
  'categories'   => ['silk'],
  'keywords'     => ['link', 'list', 'tab', 'box'],
	'viewportWidth' => 1000,
];
