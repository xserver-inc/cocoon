<?php
//全幅内カラムに含まれる初期文言の翻訳
$content = strtr(
	"<!-- wp:group {\"align\":\"full\",\"backgroundColor\":\"grey\"} -->\n<div class=\"wp-block-group alignfull has-grey-background-color has-background\"><div class=\"wp-block-group__inner-container\"><!-- wp:columns -->\n<div class=\"wp-block-columns\"><!-- wp:column -->\n<div class=\"wp-block-column\"><!-- wp:group {\"className\":\"is-style-panel\",\"backgroundColor\":\"white\"} -->\n<div class=\"wp-block-group is-style-panel has-white-background-color has-background\"><div class=\"wp-block-group__inner-container\"><!-- wp:paragraph -->\n<p>カラム１</p>\n<!-- /wp:paragraph --></div></div>\n<!-- /wp:group --></div>\n<!-- /wp:column -->\n\n<!-- wp:column -->\n<div class=\"wp-block-column\"><!-- wp:group {\"className\":\"is-style-panel\",\"backgroundColor\":\"white\"} -->\n<div class=\"wp-block-group is-style-panel has-white-background-color has-background\"><div class=\"wp-block-group__inner-container\"><!-- wp:paragraph -->\n<p>カラム２</p>\n<!-- /wp:paragraph --></div></div>\n<!-- /wp:group --></div>\n<!-- /wp:column --></div>\n<!-- /wp:columns --></div></div>\n<!-- /wp:group -->",
	[
		'カラム１' => __( 'カラム１', THEME_NAME ),
		'カラム２' => __( 'カラム２', THEME_NAME ),
	]
);

return [
	'title'         => __( '全幅内カラム', THEME_NAME ),
	'content'       => $content,
	'description'   => __( '全幅設定のグループブロック内にカラムブロックを入れたパターンです。', THEME_NAME ),
  'categories'   => ['silk'],
  'keywords'     => ['fullwide', 'columns'],
	'viewportWidth' => 1000,
];
