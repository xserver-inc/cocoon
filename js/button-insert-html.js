/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
( function () {
  tinymce.create( 'tinymce.plugins.MyButtons', {
    init: function ( ed, url ) {
      // ボタン設定
      ed.addButton( 'button_insert_html', {
        title: insert_html_button_title,
        image: url + '/button-insert-html.png',
        cmd: 'button_insert_html_cmd',
      } );
      // ボタンの動作（TinyMCE ダイアログで HTML を入力させる）
      ed.addCommand( 'button_insert_html_cmd', function () {
        ed.windowManager.open( {
          title: insert_html_dialog_label,
          body: [
            {
              type: 'textbox',
              name: 'html',
              multiline: true,
              minWidth: 500,
              minHeight: 200,
              value: '',
            },
          ],
          // OK ボタン押下時のみ挿入（キャンセル時はコールバックが呼ばれない）
          onsubmit: function ( e ) {
            var raw_html = e.data.html;
            if ( raw_html ) {
              ed.execCommand( 'mceInsertContent', 0, raw_html );
            }
          },
        } );
      } );
    },
    createControl: function ( n, cm ) {
      return null;
    },
  } );
  /* Start the buttons */
  tinymce.PluginManager.add(
    'custom_button_script',
    tinymce.plugins.MyButtons
  );
} )();
