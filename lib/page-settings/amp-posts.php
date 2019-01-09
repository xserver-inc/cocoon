<?php //AMP設定用関数
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

//AMPの有効化
update_theme_option(OP_AMP_ENABLE);

//AMPロゴ
update_theme_option(OP_AMP_LOGO_IMAGE_URL);

//AMP画像の拡大効果
update_theme_option(OP_AMP_IMAGE_ZOOM_EFFECT);

//AMPバリデーションツール
update_theme_option(OP_AMP_VALIDATOR);

//インラインスタイルを取り除く
update_theme_option(OP_AMP_REMOVAL_INLINE_STYLE_ENABLE);

//インラインスタイル
update_theme_option(OP_AMP_INLINE_STYLE_ENABLE);

//スキンスタイルを有効
update_theme_option(OP_AMP_SKIN_STYLE_ENABLE);

//子テーマスタイルを有効
update_theme_option(OP_AMP_CHILD_THEME_STYLE_ENABLE);

//AMP除外カテゴリ
update_theme_option(OP_AMP_EXCLUDE_CATEGORY_IDS);


