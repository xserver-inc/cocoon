<?php //吹き出し入力フォーム
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit; ?>

<p><?php _e( '吹き出し用の設定です。', THEME_NAME ) ?></p>

<form name="form1" method="post" action="">
  <?php

  if (isset($_GET['id'])) {
    $action = 'edit';
    $id = isset($_GET['id']) ? intval($_GET['id']) : '';
    $record = get_speech_balloon($id);
    $title = $record->title;
    $name = $record->name;
    $icon = $record->icon;
    $style = $record->style;
    $position = $record->position;
    $iconstyle = $record->iconstyle;
    $visible = $record->visible;

    //吹き出しデモの表示

    require_once abspath(__FILE__).'demo.php';

  } else {
    $action = 'new';
    $id = '';
    $title = isset($_POST['title']) ? $_POST['title'] : '';
    $name = isset($_POST['name']) ? $_POST['name'] : '';
    $icon = isset($_POST['icon']) ? $_POST['icon'] : '';
    $style = isset($_POST['style']) ? $_POST['style'] : SBS_STANDARD;
    $position = isset($_POST['position']) ? $_POST['position'] : SBP_LEFT;
    $iconstyle = isset($_POST['iconstyle']) ? $_POST['iconstyle'] : SBIS_CIRCLE_BORDER;
    $visible = 1;
    //_v($visible);
  }?>

  <table class="form-table speech-balloon">
    <tbody>

      <tr>
        <th scope="row">
          <?php generate_label_tag('title', __( 'タイトル', THEME_NAME )); ?>
        </th>
        <td>
          <?php
          generate_textbox_tag('title', $title,  __('タイトル入力',THEME_NAME ));
          generate_tips_tag(__( '吹き出しのタイトルを入力してください。タイトルは管理画面（検索など）で利用されます', THEME_NAME ));
          ?>
        </td>
      </tr>

      <tr>
        <th scope="row">
          <?php generate_label_tag('name', __( '名前', THEME_NAME )); ?>
        </th>
        <td>
          <?php
          generate_textbox_tag('name', $name,  __('推奨：6文字以下',THEME_NAME ));
          generate_tips_tag(__( 'アイコン下に表示される名前を入力してください。※未入力でも可', THEME_NAME ));
          ?>
        </td>
      </tr>

      <tr>
        <th scope="row">
          <?php generate_label_tag('icon', __( 'アイコン画像', THEME_NAME )); ?>
        </th>
        <td>
          <?php
          generate_upload_image_tag('icon', $icon);
          generate_tips_tag(__( 'アイコンとなる画像を選択してください。160px以上の正方形画像を設定してください。', THEME_NAME ));
          ?>
        </td>
      </tr>

      <tr>
        <th scope="row">
          <?php generate_label_tag('style', __( '吹き出しスタイル', THEME_NAME )); ?>
        </th>
        <td>
          <?php
          $options = array(
            SBS_STANDARD => __( 'デフォルト', THEME_NAME ),
            SBS_FLAT => __( 'フラット', THEME_NAME ),
            SBS_LINE => __( 'LINE風', THEME_NAME ),
            SBS_THINK => __( '考え事', THEME_NAME ),
          );
          generate_selectbox_tag('style', $options, $style);
          generate_tips_tag(__( '吹き出しのスタイルを設定します。', THEME_NAME ));
          ?>
        </td>
      </tr>

      <tr>
        <th scope="row">
          <?php generate_label_tag('position', __( '人物位置', THEME_NAME )); ?>
        </th>
        <td>
          <?php
          $options = array(
            SBP_LEFT => __( '左', THEME_NAME ),
            SBP_RIGHT => __( '右', THEME_NAME ),
          );
          generate_selectbox_tag('position', $options, $position);
          generate_tips_tag(__( 'アイコンを表示するポジションを設定します。', THEME_NAME ));
          ?>
        </td>
      </tr>

      <tr>
        <th scope="row">
          <?php generate_label_tag('iconstyle', __( 'アイコンスタイル', THEME_NAME )); ?>
        </th>
        <td>
          <?php
          $options = array(
            SBIS_SQUARE_NONE   => __( '四角（枠線なし）', THEME_NAME ),
            SBIS_SQUARE_BORDER => __( '四角（枠線あり）', THEME_NAME ),
            SBIS_CIRCLE_NONE   => __( '丸（枠線なし）', THEME_NAME ),
            SBIS_CIRCLE_BORDER => __( '丸（枠線あり）', THEME_NAME ),
          );
          generate_selectbox_tag('iconstyle', $options, $iconstyle);
          generate_tips_tag(__( 'アイコンの形や枠線を指定します。', THEME_NAME ));
          ?>
        </td>
      </tr>

      <tr>
        <th scope="row">
          <?php generate_label_tag('visible', __( 'TinyMCE', THEME_NAME )); ?>
        </th>
        <td>
          <?php
          generate_checkbox_tag('visible' , $visible, __( 'エディターのリストに表示', THEME_NAME ));
          generate_tips_tag(__( 'エディターのドロップダウンリストに表示しなくて良い場合は、無効にしてください。', THEME_NAME ));
          ?>
        </td>
      </tr>

    </tbody>
  </table>
  <input type="hidden" name="action" value="<?php echo $action; ?>">
  <input type="hidden" name="id" value="<?php echo $id; ?>">
  <input type="hidden" name="<?php echo HIDDEN_FIELD_NAME; ?>" value="<?php echo wp_create_nonce('speech-balloon');?>">
  <?php submit_button(__( '保存', THEME_NAME )); ?>
</form>

<p class="alert"><?php _e( 'ブロックエディターの「吹き出しブロック」で利用後、設定を変更すると、ブロックエディター上で再編集できなくなります。', THEME_NAME ) ?></p>
