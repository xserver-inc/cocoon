<?php //画像設定をデータベースに保存
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

//アイキャッチの表示
update_theme_option(OP_EYECATCH_VISIBLE);

//アイキャッチラベルの表示
update_theme_option(OP_EYECATCH_LABEL_VISIBLE);

//アイキャッチの中央寄せ
update_theme_option(OP_EYECATCH_CENTER_ENABLE);

//アイキャッチをカラム幅に引き伸ばす
update_theme_option(OP_EYECATCH_WIDTH_100_PERCENT_ENABLE);

//アイキャッチキャプションを表示する
update_theme_option(OP_EYECATCH_CAPTION_VISIBLE);

//Auto Post Thumbnail
update_theme_option(OP_AUTO_POST_THUMBNAIL_ENABLE);

//画像の枠線効果
update_theme_option(OP_IMAGE_WRAP_EFFECT);

//画像の拡大効果
update_theme_option(OP_IMAGE_ZOOM_EFFECT);

//本文中画像の中央寄せ
update_theme_option(OP_CONTENT_IMAGE_CENTER_ENABLE);

//サムネイル画像タイプ
update_theme_option(OP_THUMBNAIL_IMAGE_TYPE);

//Retinaディスプレイ
update_theme_option(OP_RETINA_THUMBNAIL_ENABLE);

//NO IMAGE画像
update_theme_option(OP_NO_IMAGE_URL);
//画像が設定された場合は生成
if (file_exists(get_no_image_file()) &&
   (!file_exists(get_no_image_320x180_file())
       || !file_exists(get_no_image_160x90_file())
       || !file_exists(get_no_image_120x68_file())
       || !file_exists(get_no_image_150x150_file()))
) {
  $image_editor = wp_get_image_editor(get_no_image_file());
  if ( !is_wp_error($image_editor) ) {
    $image_editor->resize(THUMB320WIDTH, THUMB320HEIGHT, true);
    $image_editor->save( get_no_image_320x180_file() );

    $image_editor->resize(THUMB160WIDTH, THUMB160HEIGHT, true);
    $image_editor->save( get_no_image_160x90_file() );

    $image_editor->resize(THUMB120WIDTH, THUMB120HEIGHT, true);
    $image_editor->save( get_no_image_120x68_file() );

    $image_editor->resize(THUMB150WIDTH, THUMB150HEIGHT, true);
    $image_editor->save( get_no_image_150x150_file() );
  }
}
