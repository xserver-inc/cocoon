<?php
//比較表
return [
	'title'         => '比較表',
	'content'       => "<!-- wp:group {\"className\":\"is-style-compare\"} -->\n<div class=\"wp-block-group is-style-compare\"><div class=\"wp-block-group__inner-container\"><!-- wp:cocoon-blocks/iconlist-box {\"title\":\"比較１\",\"icon\":\"list-check\",\"borderColor\":\"blue\",\"iconColor\":\"blue\"} -->\n<div class=\"wp-block-cocoon-blocks-iconlist-box iconlist-box blank-box list-check block-box has-border-color has-icon-color has-blue-border-color has-blue-icon-color\"><div class=\"iconlist-title\">比較１</div><!-- wp:list -->\n<ul><li>リスト</li></ul>\n<!-- /wp:list --></div>\n<!-- /wp:cocoon-blocks/iconlist-box -->\n\n<!-- wp:cocoon-blocks/iconlist-box {\"title\":\"比較２\",\"icon\":\"list-check\",\"borderColor\":\"red\",\"iconColor\":\"red\"} -->\n<div class=\"wp-block-cocoon-blocks-iconlist-box iconlist-box blank-box list-check block-box has-border-color has-icon-color has-red-border-color has-red-icon-color\"><div class=\"iconlist-title\">比較２</div><!-- wp:list -->\n<ul><li>リスト</li></ul>\n<!-- /wp:list --></div>\n<!-- /wp:cocoon-blocks/iconlist-box --></div></div>\n<!-- /wp:group -->",
	'description'   => 'グループブロックとアイコンリストブロックを組み合わせた比較表が作成できます。',
  'categories'   => ['silk'],
  'keywords'     => ['comparison', 'table', 'lists'],
	'viewportWidth' => 1000,
];
