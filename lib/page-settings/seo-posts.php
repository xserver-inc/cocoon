<?php //SEO設定をデータベースに保存

//canonicalタグの追加
update_theme_option(OP_CANONICAL_TAG_ENABLE);

//分割ページにrel="next"/"prev"タグの追加
update_theme_option(OP_PREV_NEXT_ENABLE);

//カテゴリページの2ページ目以降をnoindexとする
update_theme_option(OP_PAGED_CATEGORY_PAGE_NOINDEX);

//タグページをnoindexとする
update_theme_option(OP_TAG_PAGE_NOINDEX);

//添付ファイルページをnoindexとする
update_theme_option(OP_ATTACHMENT_PAGE_NOINDEX);