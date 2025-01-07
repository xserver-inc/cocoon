<?php //タイトルからアイキャッ生成関係関数
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

// 投稿が保存または更新されたときに関数を実行するフック
add_action('save_post', 'generate_dynamic_featured_image');
if ( !function_exists( 'generate_dynamic_featured_image' ) ):
function generate_dynamic_featured_image($post_id) {
  // 自動保存やリビジョン、自動下書きの場合は処理を終了
  if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
    return;
  }

  // 投稿タイプが post, page, カスタム投稿 以外の場合は処理を終了
  $post_type = get_post_type($post_id);
  $custom_post_types = get_post_types(['_builtin' => false]); // すべてのカスタム投稿タイプを取得
  if (!in_array($post_type, array_merge(['post', 'page'], $custom_post_types))) {
    return;
  }

  // GDライブラリがインストールされていない場合は処理を終了
  if (!extension_loaded('gd')) {
    return;
  }

  // 投稿タイトルと著者名を取得
  $post_title = get_the_title($post_id);
  $author_id = get_post_field('post_author', $post_id);
  $author_name = get_the_author_meta('display_name', $author_id);
  // 著者のアバター画像を取得
  $avatar_url = get_the_author_upladed_avatar_url($author_id);

  // 画像のサイズを設定
  // $width = 1280;
  // $height = 720;
  $width = get_thumbnail_width(1280);
  $height = get_thumbnail_height($width);

  // タイトルが「自動下書き」の場合は画像を生成しない
  if ($post_title === __( 'Auto Draft' )) {
    return;
  }

  // アイキャッチ画像のパスを定義
  $upload_path = get_theme_featured_images_path();
  $post_title_hash = md5($post_title . $avatar_url . $author_name . $width . 'x' . $height);
  $new_image_path = trailingslashit($upload_path) . 'featured_image_' . $post_id . '_' . $width . 'x' . $height . $post_title_hash . '.png';

  // すでにアイキャッチ画像が設定されている場合は処理を終了
  $current_thumbnail_id = get_post_thumbnail_id($post_id);
  if ($current_thumbnail_id) {
    return;
  }

  // すでにメディアに同じファイルが登録されている場合は処理を終了
  $existing_attachment_id = attachment_url_to_postid($new_image_path);
  if ($existing_attachment_id) {
    set_post_thumbnail($post_id, $existing_attachment_id);
    return;
  }

  // 「タイトルからアイキャッチ生成をする」が有効でない場合は処理を終了
  $is_checked = isset($_POST['generate_featured_from_title']) && (intval($_POST['generate_featured_from_title']) == 1);
  if (!$is_checked) {
    return;
  }

  // ベース画像の作成
  $image = imagecreatetruecolor($width, $height);

  // カラーの設定
  $background_color = imagecolorallocate($image, 255, 255, 255); // 背景色（白）
  $text_color = imagecolorallocate($image, 51, 51, 51); // テキスト色（ダークグレー）
  $border_color = imagecolorallocate($image, 162, 215, 221); // 縁の色 (#a2d7dd)

  // 背景を塗りつぶす - 20pxのボーダー部分を描画し、その内側を背景色で塗りつぶす
  imagefilledrectangle($image, 0, 0, $width, $height, $border_color);
  imagefilledrectangle($image, 20, 20, $width - 20, $height - 20, $background_color);

  // 日本語フォントファイルのパスを定義（サーバーに配置する必要があります）
  $font_path = get_stylesheet_directory() . '/NotoSansJP-Regular.ttf';

  // フォントサイズを設定
  $font_size = 48;

  // 余白と最大幅を計算
  $margin = $width * 0.1;
  $max_width = $width - ($margin * 2);

  // タイトルを自動改行で描画
  if (file_exists($font_path)) {
    $lines = [];
    $current_line = '';

    // タイトルを単語または全角文字ごとに処理して改行を調整
    $words = preg_split('/(?=[\p{Script=Hiragana}\p{Script=Katakana}\p{Script=Han}\p{L}\p{N}]|\s+)/u', $post_title, -1, PREG_SPLIT_NO_EMPTY);

    foreach ($words as $word) {
      // 仮に現在の行に追加した場合のテキストサイズを測定
      $box = imagettfbbox($font_size, 0, $font_path, $current_line . $word);
      $text_width = $box[2] - $box[0];

      // 行の幅が最大幅を超えた場合の処理
      if ($text_width > $max_width && !empty(trim($current_line))) {
        // 直前の文字が記号の場合はその記号を次の行に移動
        if (preg_match('/[\p{P}\p{S}]$/u', $current_line)) {
          $current_line = preg_replace('/[\p{P}\p{S}]$/u', '', $current_line);
          $word = mb_substr($current_line, -1) . $word;
        }
        $lines[] = trim($current_line);
        $current_line = $word;
      } else {
        $current_line .= $word;
      }
    }
    // 最後の行を追加
    if (!empty(trim($current_line))) {
      $lines[] = trim($current_line);
    }

    // 最大5行に制限し、それ以降は省略
    if (count($lines) > 5) {
      $lines = array_slice($lines, 0, 5);
      $lines[4] = mb_substr($lines[4], 0, mb_strlen($lines[4]) - 3) . '...';
    }

    // 各行を描画
    $y = 150;
    foreach ($lines as $line) {
      imagettftext($image, $font_size, 0, $margin, $y, $text_color, $font_path, $line);
      $y += $font_size + 40; // 行間をさらに広く設定
    }

    if (empty($avatar_url)) {
      $avatar_url = get_avatar_url($author_id, ['size' => 64]); // WordPressデフォルトのアバター画像を取得
    }
    $avatar_path = url_to_local($avatar_url);
    $avatar_image = @imagecreatefromstring(file_get_contents($avatar_path));

    if ($avatar_image !== false) {
      // アバター画像を描画
      $avatar_size = 64;
      $avatar_x = $margin;
      $avatar_y = $height - $avatar_size - $margin + 40; // 下部に余白を持たせて配置
      imagecopyresampled($image, $avatar_image, $avatar_x, $avatar_y, 0, 0, $avatar_size, $avatar_size, imagesx($avatar_image), imagesy($avatar_image));
      imagedestroy($avatar_image);

      // 著者名をアバター画像の上下中央に配置し、さらに余白を追加（4px上に移動）
      $author_text_y = $avatar_y + ($avatar_size / 2) + ($font_size / 3) + 6; // 6pxの余白を追加
      imagettftext($image, $font_size - 6, 0, $avatar_x + $avatar_size + 30, $author_text_y, $text_color, $font_path, $author_name); // 余白を増やして30pxに設定
    }
  } else {
    // フォントファイルが見つからない場合の代替処理
    imagestring($image, 5, $margin, 150, $post_title, $text_color);
    imagestring($image, 4, $margin, $height - $margin, $author_name, $text_color);
  }

  // 画像をアップロードディレクトリに保存
  $image_path = $new_image_path;
  imagepng($image, $image_path);
  imagedestroy($image);

  // 画像をWordPressメディアライブラリに登録
  $attachment = [
    'post_mime_type' => 'image/png',
    'post_title'     => sanitize_file_name(basename($image_path)),
    'post_content'   => '',
    'post_status'    => 'inherit',
  ];

  // 画像をWordPressのメディアライブラリに添付ファイルとして登録
  $attachment_id = wp_insert_attachment($attachment, $image_path, $post_id);
  require_once(ABSPATH . 'wp-admin/includes/image.php'); // メタデータ生成用のファイルを読み込む
  $attachment_data = wp_generate_attachment_metadata($attachment_id, $image_path); // 添付ファイルのメタデータを生成
  wp_update_attachment_metadata($attachment_id, $attachment_data); // メタデータを更新し、データベースに保存

  // 投稿のアイキャッチ画像として設定
  set_post_thumbnail($post_id, $attachment_id);
}
endif;

// タイトルからアイキャッチを生成HTMLの作成
if ( !function_exists( 'generate_featured_from_title_custom_checkbox' ) ):
function generate_featured_from_title_custom_checkbox() {
  ?>
  <div id="custom-checkbox-wrapper" style="margin-top: 10px; padding: 5px 0;">
    <label>
      <input type="checkbox" id="generate_featured_from_title" name="generate_featured_from_title" value="1">
      <?php echo esc_js(__('タイトルからアイキャッチを生成する', THEME_NAME)); ?>
    </label>
  </div>
  <?php
}
endif;

// 「アイキャッチ画像」メタボックスにチェックボックスを追加
add_action('admin_footer-post.php', 'add_custom_checkbox_below_featured_image_meta_box');
add_action('admin_footer-post-new.php', 'add_custom_checkbox_below_featured_image_meta_box');
if ( !function_exists( 'add_custom_checkbox_below_featured_image_meta_box' ) ):
function add_custom_checkbox_below_featured_image_meta_box() {
  global $post;
  // $is_checked = get_post_meta($post->ID, '_generate_featured_from_title', true) ? 'checked' : '';
  ?>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      // チェックボックスのHTMLを作成
      var checkboxHtml = `
        <?php generate_featured_from_title_custom_checkbox(); ?>
      `;

      // チェックボックスを挿入または削除する関数
      function manageCustomCheckbox() {
        var postThumbnailDiv = document.querySelector('#postimagediv .inside');
        var thumbnailImage = document.querySelector('#postimagediv .inside img'); // アイキャッチ画像の存在をチェック
        var checkboxWrapper = document.querySelector('#custom-checkbox-wrapper');
        var checkboxInput = checkboxWrapper ? checkboxWrapper.querySelector('input[type="checkbox"]') : null;
        var setThumbnailLink = document.querySelector('#set-post-thumbnail');

        if (thumbnailImage) {
          // アイキャッチ画像が設定されている場合、チェックを無効化
          if (checkboxInput) {
            checkboxInput.checked = false; // チェックを外す
          }
          if (checkboxWrapper) {
            checkboxWrapper.remove();
          }
        } else {
          // アイキャッチ画像が削除された場合、チェックボックスを追加
          if (postThumbnailDiv && !checkboxWrapper) {
            postThumbnailDiv.insertAdjacentHTML('beforeend', checkboxHtml);
            attachCheckboxListener();
          }
        }

        // チェックボックスの状態に応じて「アイキャッチ画像を設定」リンクの状態を変更
        if (checkboxInput && setThumbnailLink) {
          if (checkboxInput.checked) {
            setThumbnailLink.style.pointerEvents = 'none';
            setThumbnailLink.style.opacity = '0.3';
          } else {
            setThumbnailLink.style.pointerEvents = 'auto';
            setThumbnailLink.style.opacity = '1';
          }
        }
      }

      // チェックボックスのイベントリスナーを設定
      function attachCheckboxListener() {
        var checkboxInput = document.querySelector('#custom-checkbox-wrapper input[type="checkbox"]');
        var setThumbnailLink = document.querySelector('#set-post-thumbnail');

        if (checkboxInput && setThumbnailLink) {
          checkboxInput.addEventListener('change', function () {
            if (checkboxInput.checked) {
              setThumbnailLink.style.pointerEvents = 'none';
              setThumbnailLink.style.opacity = '0.3';
            } else {
              setThumbnailLink.style.pointerEvents = 'auto';
              setThumbnailLink.style.opacity = '1';
            }
          });
        }
      }

      // 初期表示時にチェックボックスを管理
      manageCustomCheckbox();

      // DOMの変化を監視してチェックボックスを管理
      var observer = new MutationObserver(function () {
        manageCustomCheckbox();
      });

      // 「#postimagediv」の中身が変化したときに監視を開始
      var postThumbnailDiv = document.querySelector('#postimagediv');
      if (postThumbnailDiv) {
        observer.observe(postThumbnailDiv, { childList: true, subtree: true });
      }
    });
  </script>
  <?php
}
endif;

// add_action('save_post', 'save_checkbox_below_featured_image_meta_box');
// // チェックボックスの値を保存
// function save_checkbox_below_featured_image_meta_box($post_id) {
//     // 自動保存の場合はスキップ
//     if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
//         return;
//     }

//     // 投稿の編集権限を確認
//     if (!current_user_can('edit_post', $post_id)) {
//         return;
//     }

//     // チェックボックスの値を保存または削除
//     if (isset($_POST['generate_featured_from_title'])) {
//         // update_post_meta($post_id, '_generate_featured_from_title', '1');
//     } else {
//         delete_post_meta($post_id, '_generate_featured_from_title');
//     }
// }


/* 「アイキャッチ画像」メタボックスにチェックボックスを追加 */
add_action('admin_footer-post.php', 'add_custom_checkbox_below_featured_image');
add_action('admin_footer-post-new.php', 'add_custom_checkbox_below_featured_image');
if ( !function_exists( 'add_custom_checkbox_below_featured_image' ) ):
function add_custom_checkbox_below_featured_image() {
  if (!use_gutenberg_editor()) {
    return;
  }
  ?>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      /* チェックボックスのHTMLを作成 */
      var checkboxHtml = `
        <?php generate_featured_from_title_custom_checkbox(); ?>
      `;

      /* チェックボックスを挿入または削除する関数 */
      function manageCustomCheckbox() {
      var featuredImagePanel = document.querySelector('.editor-post-featured-image'); /* ブロックエディターのアイキャッチエリア */
      var actionsPanel = document.querySelector('.editor-post-featured-image__actions');
      var customCheckbox = document.querySelector('#custom-checkbox-wrapper');

      if (featuredImagePanel) {
        if (actionsPanel) {
        /* アクションパネルが存在する場合、チェックボックスを削除 */
        if (customCheckbox) {
          customCheckbox.remove();
        }
        } else {
        /* アクションパネルが存在しない場合、チェックボックスを追加 */
        if (!customCheckbox) {
          featuredImagePanel.insertAdjacentHTML('beforeend', checkboxHtml);

          /* チェックボックスの状態に応じてvalueを変更 */
          var checkbox = document.querySelector('#generate_featured_from_title');
          var hiddenInput = document.querySelector('#generate_featured_from_title_hide');
          if (checkbox && hiddenInput) {
          checkbox.addEventListener('change', function () {
            hiddenInput.value = checkbox.checked ? '1' : '0';
            toggleFeaturedImageContainer(checkbox.checked);
          });
          }
        }
        }
      }
      }

      /* .editor-post-featured-image__containerの表示を切り替える関数 */
      function toggleFeaturedImageContainer(isChecked) {
        var featuredImageContainer = document.querySelector('.editor-post-featured-image__container');
        if (featuredImageContainer) {
          if (isChecked) {
            featuredImageContainer.style.pointerEvents = 'none';
            featuredImageContainer.style.opacity = '0.3';
          } else {
            featuredImageContainer.style.pointerEvents = 'auto';
            featuredImageContainer.style.opacity = '1';
          }
        }
      }

      /* 初期表示時にチェックボックスを管理 */
      manageCustomCheckbox();

      /* 初期表示時にチェックボックスの状態に応じて表示を切り替え */
      var initialCheckbox = document.querySelector('#generate_featured_from_title');
      if (initialCheckbox) {
      toggleFeaturedImageContainer(initialCheckbox.checked);
      }

      /* 常に#custom-checkbox-wrapperの存在を監視してチェックボックスを管理 */
      setInterval(function () {
      manageCustomCheckbox();
      }, 500); /* 500msごとにチェック */
    });
  </script>
  <?php

}
endif;

/* チェックボックスのnonceフィールドを追加 */
add_action('edit_form_after_title', 'add_custom_checkbox_nonce');
if ( !function_exists( 'add_custom_checkbox_nonce' ) ):
function add_custom_checkbox_nonce() {
  // Gutenbergエディターを使用していない場合は処理を終了
  if (!use_gutenberg_editor()) {
    return;
  }
  // nonceフィールドを追加してセキュリティを確保
  wp_nonce_field('save_generate_featured', 'generate_featured_nonce');
  // チェックボックスの状態を保持するための隠しフィールドを追加
  echo '<input type="hidden" id="generate_featured_from_title_hide" name="generate_featured_from_title" value="0">';
}
endif;