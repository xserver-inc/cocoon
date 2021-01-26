<?php
//全幅内カラム
return [
	'title'         => '全幅内カラム',
	'content'       => "<!-- wp:group {\"align\":\"full\",\"backgroundColor\":\"grey\"} -->\n<div class=\"wp-block-group alignfull has-grey-background-color has-background\"><div class=\"wp-block-group__inner-container\"><!-- wp:columns -->\n<div class=\"wp-block-columns\"><!-- wp:column -->\n<div class=\"wp-block-column\"><!-- wp:group {\"className\":\"is-style-panel\",\"backgroundColor\":\"white\"} -->\n<div class=\"wp-block-group is-style-panel has-white-background-color has-background\"><div class=\"wp-block-group__inner-container\"><!-- wp:paragraph -->\n<p>カラム１</p>\n<!-- /wp:paragraph --></div></div>\n<!-- /wp:group --></div>\n<!-- /wp:column -->\n\n<!-- wp:column -->\n<div class=\"wp-block-column\"><!-- wp:group {\"className\":\"is-style-panel\",\"backgroundColor\":\"white\"} -->\n<div class=\"wp-block-group is-style-panel has-white-background-color has-background\"><div class=\"wp-block-group__inner-container\"><!-- wp:paragraph -->\n<p>カラム２</p>\n<!-- /wp:paragraph --></div></div>\n<!-- /wp:group --></div>\n<!-- /wp:column --></div>\n<!-- /wp:columns --></div></div>\n<!-- /wp:group -->",
	'description'   => '全幅設定のグループブロック内にカラムブロックを入れたパターンです。',
  'categories'   => ['silk'],
  'keywords'     => ['fullwide', 'columns'],
	'viewportWidth' => 1000,
];
