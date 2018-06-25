<?php //画像設定をデータベースに保存
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */

//アイキャッチの表示
update_theme_option(OP_EYECATCH_VISIBLE);

//アイキャッチラベルの表示
update_theme_option(OP_EYECATCH_LABEL_VISIBLE);

//アイキャッチの中央寄せ
update_theme_option(OP_EYECATCH_CENTER_ENABLE);

//アイキャッチをカラム幅に引き伸ばす
update_theme_option(OP_EYECATCH_WIDTH_100_PERCENT_ENABLE);

//Auto Post Thumbnail
update_theme_option(OP_AUTO_POST_THUMBNAIL_ENABLE);

//画像の枠線効果
update_theme_option(OP_IMAGE_WRAP_EFFECT);

//画像の拡大効果
update_theme_option(OP_IMAGE_ZOOM_EFFECT);

//NO IMAGE画像
update_theme_option(OP_NO_IMAGE_URL);
//画像が設定された場合は生成
if (file_exists(get_no_image_file()) &&
   (!file_exists(get_no_image_320x180_file())
       || !file_exists(get_no_image_160x90_file())
       || !file_exists(get_no_image_150x150_file()))
) {
  $image_editor = wp_get_image_editor(get_no_image_file());
  if ( !is_wp_error($image_editor) ) {
    $image_editor->resize(320, 180, true);
    $image_editor->save( get_no_image_320x180_file() );

    $image_editor->resize(160, 90, true);
    $image_editor->save( get_no_image_160x90_file() );

    $image_editor->resize(150, 150, true);
    $image_editor->save( get_no_image_150x150_file() );
  }
}
// _v(get_no_image_file());
// // _v(get_no_image_url());
// _v(get_no_image_320x180_file());
// _v(get_no_image_160x90_file());
// _v(get_no_image_150x150_file());
// _v(get_no_image_320x180_url());
// _v(get_no_image_160x90_url());
// _v(get_no_image_150x150_url());