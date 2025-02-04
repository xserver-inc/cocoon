<?php //タイトルからアイキャッ生成関係関数
/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

// タイトル整形関数
if ( !function_exists( 'sanitize_post_title' ) ):
function sanitize_post_title($post_title) {
  // 連続する空白を1つの空白に置き換える
  $post_title = preg_replace('/\s{2,}/u', ' ', $post_title);
  // 投稿タイトルが自動的にHTMLエンティティや特殊文字に変換されることへの対応
  $post_title = html_entity_decode($post_title);
  // 絵文字を除外する
  $post_title = preg_replace([
    '/[\x{1F600}-\x{1F64F}]/u', // 顔文字
    '/[\x{1F300}-\x{1F5FF}]/u', // その他のシンボル
    '/[\x{1F680}-\x{1F6FF}]/u', // 交通機関と地図記号
    '/[\x{1F700}-\x{1F77F}]/u', // アルケミー記号
    '/[\x{1F780}-\x{1F7FF}]/u', // 幾何学模様
    '/[\x{1F800}-\x{1F8FF}]/u', // 装飾用記号
    '/[\x{1F900}-\x{1F9FF}]/u', // 装飾用記号補助
    '/[\x{1FA00}-\x{1FA6F}]/u', // 装飾用記号補助
    '/[\x{1FA70}-\x{1FAFF}]/u', // 装飾用記号補助
    '/[\x{2600}-\x{26FF}]/u'    // その他のシンボル
  ], '', $post_title);
  return $post_title;
}
endif;

// 画像幅に合わせて各部分のサイズを変更
if ( !function_exists( 'get_dynamic_featured_image_size' ) ) :
function get_dynamic_featured_image_size($canvas_size, $parts_size) {
  return round(($parts_size / 1280) * $canvas_size);
}
endif;

// GDライブラリを用いた動的画像生成関数
if ( !function_exists( 'generate_dynamic_image' ) ):
function generate_dynamic_image($post_id, $new_image_path, $width, $height) {

  // 投稿タイトルと投稿者名を取得
  $post_title = get_the_title($post_id);

  // タイトルが「自動下書き」の場合は画像を生成しない
  if ($post_title === __( 'Auto Draft' )) {
    return false;
  }

  // タイトルの整形
  $post_title = sanitize_post_title($post_title);
  if (!$post_title) {
    return false;
  }

  // 投稿者ID取得
  $author_id = get_post_field('post_author', $post_id);
  // 投稿者の表示名の取得
  $author_name = get_the_author_meta('display_name', $author_id);
  // 投稿者のアバター画像を取得
  $avatar_url = get_the_author_upladed_avatar_url($author_id);


  // ベース画像の作成
  $image = imagecreatetruecolor($width, $height);

  // カラーの設定
  // 背景色（白）
  $background_color_code = apply_filters('featured_image_background_color_code', '#ffffff');
  list($r, $g, $b) = sscanf($background_color_code, "#%02x%02x%02x");
  $background_color = imagecolorallocate($image, $r, $g, $b);

  // テキスト色（ダークグレー）
  $text_color_code = apply_filters('featured_image_text_color_code', '#333333');
  list($r, $g, $b) = sscanf($text_color_code, "#%02x%02x%02x");
  $text_color = imagecolorallocate($image, $r, $g, $b);

  // ボーダー色(#a2d7dd)
  $border_color_code = apply_filters('featured_image_border_color_code', '#a2d7dd');
  list($r, $g, $b) = sscanf($border_color_code, "#%02x%02x%02x");
  $border_color = imagecolorallocate($image, $r, $g, $b);

  $border_width = get_dynamic_featured_image_size($width, 30);

  // 背景を塗りつぶす のボーダー部分を描画し、その内側を背景色で塗りつぶす
  imagefilledrectangle($image, 0, 0, $width, $height, $border_color);
  imagefilledrectangle($image, $border_width, $border_width, $width - $border_width, $height - $border_width, $background_color);

  // 四隅を丸くするための半径を設定
  $radius = $border_width;

  // 四隅を丸くする
  // 左上の角を丸くする
  imagefilledarc($image, $radius, $radius, $radius * 2, $radius * 2, 0, 90, $border_color, IMG_ARC_PIE);
  imagefilledarc($image, $radius * 2, $radius * 2, $radius * 2, $radius * 2, 180, 270, $background_color, IMG_ARC_PIE);
  // 右上の角を丸くする
  imagefilledarc($image, $width - $radius, $radius, $radius * 2, $radius * 2, 90, 180, $border_color, IMG_ARC_PIE);
  imagefilledarc($image, $width - $radius * 2, $radius * 2, $radius * 2, $radius * 2, 270, 360, $background_color, IMG_ARC_PIE);
  // 左下の角を丸くする
  imagefilledarc($image, $radius, $height - $radius, $radius * 2, $radius * 2, 270, 360, $border_color, IMG_ARC_PIE);
  imagefilledarc($image, $radius * 2, $height - $radius * 2, $radius * 2, $radius * 2, 90, 180, $background_color, IMG_ARC_PIE);
  // 右下の角を丸くする
  imagefilledarc($image, $width - $radius, $height - $radius, $radius * 2, $radius * 2, 180, 270, $border_color, IMG_ARC_PIE);
  imagefilledarc($image, $width - $radius * 2, $height - $radius * 2, $radius * 2, $radius * 2, 0, 90, $background_color, IMG_ARC_PIE);

  // 日本語フォントファイルのパスを定義
  $font_path = get_template_directory() . '/webfonts/googlefonts/NotoSansJP-Regular.ttf';
  if (!file_exists($font_path)) {
    error_log("Font file not found: " . $font_path);
    return;
  }

  // フォントサイズを設定
  $font_size = get_dynamic_featured_image_size($width, 48);
  // 省略記号を設定
  $ellipsis = '...';

  // 余白と最大幅を計算
  $margin = $width * 0.1;
  $max_width = $width - ($margin * 2);

  // タイトルを自動改行で描画
  if (file_exists($font_path)) {
    $lines = [];
    $current_line = '';
    // タイトルを単語または全角文字（ひらがな、カタカナ、漢字）ごとに分割し、改行を調整するための配列に変換
    $words = preg_split('/(?<=\p{Hiragana}|\p{Katakana}|\p{Han}|\s)|(?=\p{Hiragana}|\p{Katakana}|\p{Han}|\s)|(?<=\s)|(?=\s)|(?<=\p{P}(?<!-))|(?=\p{P}(?<!-))|(?<=-)(?=[^\p{L}])|(?<=[^\p{L}])(?=-)/u', $post_title, -1, PREG_SPLIT_NO_EMPTY);

    foreach ($words as $word) {
      // 仮に現在の行に追加した場合のテキストサイズを測定
      $box = imagettfbbox($font_size, 0, $font_path, $current_line . $word);
      $text_width = $box[2] - $box[0];

      // 行の幅が最大幅を超えた場合の処理
      if ($text_width > $max_width && !empty(trim($current_line))) {
        // はじめ括弧が行の最後に来る場合の処理
        if (preg_match('/^[\p{Ps}]/u', $word)) {
          $lines[] = trim($current_line);
          $current_line = $word;
        }
        // 閉じ括弧が行の先頭に来る場合の処理
        else if (preg_match('/^[\p{Pe}]/u', $word)) {
          $lines[] = trim($current_line) . mb_substr($word, 0, 1); // 前の行の最後に閉じ括弧一文字を追加
          $current_line = mb_substr($word, 1); // 残りの部分を次の行に設定
        }
        // ダブルクオート・シングルクォートの開きクォートが行の最後に来る場合の処理
        else if (preg_match('/^[‘“‚„‹«]/u', $word)) {
          $lines[] = trim($current_line);
          $current_line = $word;
        }
        // ダブルクオート・シングルクォートの閉じクォートが行の先頭に来る場合の処理
        else if (preg_match('/^[’”‘“›»]/u', $word)) {
          $lines[] = trim($current_line) . mb_substr($word, 0, 1); // 前の行の最後に記号一文字を追加
          $current_line = mb_substr($word, 1); // 残りの部分を次の行に設定
        }
        // 記号が行の先頭に来る場合の処理
        else if (preg_match('/^[\p{P}\p{S}]/u', $word)) {
          $lines[] = trim($current_line) . mb_substr($word, 0, 1); // 前の行の最後に記号一文字を追加
          $current_line = mb_substr($word, 1); // 残りの部分を次の行に設定
        } else {
          // 英単語が長すぎる場合の処理
          $box = imagettfbbox($font_size, 0, $font_path, str_repeat('A', 1)); // 1文字の幅を取得
          $char_width = $box[2] - $box[0];
          $max_chars_per_line = floor($max_width / $char_width); // 描画エリアに描画可能な半角英数字の数を計算

          if (strlen($word) > $max_chars_per_line) { // 描画エリアに収まらない場合は途中で改行
            $split_word = str_split($word, $max_chars_per_line); // 単語を最大文字数ごとに分割
            foreach ($split_word as $part) {
              $box = imagettfbbox($font_size, 0, $font_path, $current_line . $part); // テキストのバウンディングボックスを取得
              $text_width = $box[2] - $box[0]; // テキストの幅を計算
              // テキストの幅が最大幅を超え、現在の行が空でないかを確認
              if ($text_width > $max_width && !empty(trim($current_line))) {
                $lines[] = trim($current_line); // 現在の行を追加
                $current_line = $part; // 新しい行を開始
              } else {
                $current_line .= $part; // 現在の行に追加
              }
            }
          } else {
            $lines[] = trim($current_line); // 現在の行を追加
            $current_line = $word; // 新しい行を開始
          }
        }
      } else {
        $current_line .= $word;
        // 行の先頭に全角スペースがある場合は削除
        $current_line = preg_replace('/^\x{3000}/u', '', $current_line);
      }
    }

    // 最後の行を追加
    if (!empty(trim($current_line))) {
      $lines[] = trim($current_line);
    }

    // アバター画像の高さを考慮して最大行数を計算
    $line_space = get_dynamic_featured_image_size($width, 40);
    $avatar_top_space = get_dynamic_featured_image_size($width, $line_space + 1);
    $avatar_size = get_dynamic_featured_image_size($width, 82);
    $avatar_margin = $margin + $avatar_top_space; // アバター画像の余白を考慮
    $available_height = $height - $avatar_size - $avatar_margin;
    $max_row = floor($available_height / ($font_size + $avatar_top_space)); // 行間を含めた行の高さで割る

    // 行数が最大行数を超える場合の処理
    if (count($lines) > $max_row) {
      // 最大行数までの行を取得
      $lines = array_slice($lines, 0, $max_row);
      // 最後の行に省略記号を追加
      $last_line = $lines[$max_row - 1];
      $last_line_width = imagettfbbox($font_size, 0, $font_path, $last_line)[2] - imagettfbbox($font_size, 0, $font_path, $last_line)[0];
      $ellipsis_width = imagettfbbox($font_size, 0, $font_path, $ellipsis)[2] - imagettfbbox($font_size, 0, $font_path, $ellipsis)[0];

      // 省略記号を追加しても最大幅を超えないように調整
      while ($last_line_width + $ellipsis_width > $max_width && mb_strlen($last_line) > 0) {
        $last_line = mb_substr($last_line, 0, -1);
        $last_line_width = imagettfbbox($font_size, 0, $font_path, $last_line)[2] - imagettfbbox($font_size, 0, $font_path, $last_line)[0];
      }
      $lines[$max_row - 1] = $last_line . $ellipsis;
    }

    // 各行を描画
    $y = get_dynamic_featured_image_size($width, 150);
    foreach ($lines as $line) {
      imagettftext($image, $font_size, 0, $margin, $y, $text_color, $font_path, $line);
      $y += $font_size + $line_space; // 行間をさらに広く設定
    }

    if (empty($avatar_url)) {
      $avatar_url = get_avatar_url($author_id, ['size' => $avatar_size]); // WordPressデフォルトのアバター画像を取得
    }
    $avatar_path = url_to_local($avatar_url);
    if (!file_exists($avatar_path)) {
      $avatar_path = get_template_directory() . '/images/anony.png'; // 自前のサーバーにない場合、'anony.png'を使用
    }
    $avatar_image = @imagecreatefromstring(file_get_contents($avatar_path));

    if ($avatar_image !== false) {
      // アバター画像のサイズを取得
      $avatar_width = imagesx($avatar_image);
      $avatar_height = imagesy($avatar_image);

      // アバター画像の中心をトリミングするための座標を計算
      if ($avatar_width > $avatar_height) {
        $src_x = ($avatar_width - $avatar_height) / 2;
        $src_y = 0;
        $src_w = $avatar_height;
        $src_h = $avatar_height;
      } else {
        $src_x = 0;
        $src_y = ($avatar_height - $avatar_width) / 2;
        $src_w = $avatar_width;
        $src_h = $avatar_width;
      }

      // アバター画像を描画
      $avatar_x = $margin;
      $avatar_y = $height - $avatar_size - $margin + $line_space; // 下部に余白を持たせて配置
      imagecopyresampled($image, $avatar_image, $avatar_x, $avatar_y, $src_x, $src_y, $avatar_size, $avatar_size, $src_w, $src_h);
      imagedestroy($avatar_image);

      // アバター画像の内径に沿って円を描画
      $center_x = $avatar_x + ($avatar_size / 2);
      $center_y = $avatar_y + ($avatar_size / 2);
      $radius = $avatar_size / 2;

      // 円を描画（マスク処理がうまくいかなかったので、背景色の太い円でアバター画像を丸型にする）
      $circle_width = floor(($avatar_size / 2) * sqrt(2)); // 線の太さを設定
      // imagesetthickness($image, $circle_width); // 線の幅を30ピクセルに設定
      // imagearc($image, $center_x, $center_y, $avatar_size + $circle_width, $avatar_size + $circle_width, 0, 360, $circle_color);
      // imagesetthicknessで線の幅を設定すると綺麗な描けなかったのでforループを使用してひとつずつ描画する
      for ($i = 0; $i < $circle_width; $i++) {
        imagearc($image, $center_x, $center_y, $avatar_size + $i, $avatar_size + $i, 0, 360, $background_color);
      }

      // 投稿者名を描画エリアに収まるように省略する
      $author_name_font_size = get_dynamic_featured_image_size($width, 38);
      $max_author_name_width = $width - $avatar_x - $avatar_size - $border_width - $margin; // アバター画像の幅と余白を考慮
      $author_name_box = imagettfbbox($author_name_font_size, 0, $font_path, $author_name);
      $author_name_width = $author_name_box[2] - $author_name_box[0];

      // 投稿者名が最大幅を超える場合の処理
      if ($author_name_width > $max_author_name_width) {
        $ellipsis_width = imagettfbbox($author_name_font_size, 0, $font_path, $ellipsis)[2] - imagettfbbox($author_name_font_size, 0, $font_path, $ellipsis)[0]; // 省略記号の幅を計算
        // 減算して省略記号の幅を最大著者名幅から引く
        $max_author_name_width -= $ellipsis_width;

        // 投稿者名を省略して最大幅に収める
        for ($i = mb_strlen($author_name); $i > 0; $i--) {
          // 著者名を指定した長さに切り詰める
          $truncated_author_name = mb_substr($author_name, 0, $i);
          // 切り詰めた著者名の幅を計算する
          $truncated_author_name_width = imagettfbbox($author_name_font_size, 0, $font_path, $truncated_author_name)[2] - imagettfbbox($author_name_font_size, 0, $font_path, $truncated_author_name)[0];
          // 切り詰めた著者名の幅が最大幅以下か確認する
          if ($truncated_author_name_width <= $max_author_name_width) {
            // 著者名を省略記号付きで更新する
            $author_name = $truncated_author_name . $ellipsis;
            break;
          }
        }
      }

      // 投稿者名をアバター画像の上下中央に配置し、さらに余白を追加
      $author_text_y = $avatar_y + ($avatar_size / 2) + ($author_name_font_size / 3); // 6pxの余白を追加
      imagettftext($image, $author_name_font_size, 0, $avatar_x + $avatar_size + $border_width, $author_text_y, $text_color, $font_path, $author_name); // 余白を増やして30pxに設定
    }
  } else {
    // フォントファイルが見つからない場合の代替処理
    imagestring($image, 5, $margin, 150, $post_title, $text_color);
    imagestring($image, 4, $margin, $height - $margin, $author_name, $text_color);
  }

  // 画像をアップロードディレクトリに保存
  if (imagepng($image, $new_image_path)) {
    imagedestroy($image);
    return true;
  }

}
endif;

// 生成する画像ファイルのパスを取得
if ( !function_exists( 'get_dynamic_image_path' ) ):
function get_dynamic_image_path($post_id, $upload_path, $post_title_hash) {
  return trailingslashit($upload_path) . 'featured-image-' . $post_id . '-' . $post_title_hash . '.png';
}
endif;

// 投稿が保存または更新されたときに関数を実行するフック
add_action('save_post', 'generate_dynamic_featured_image');
if ( !function_exists( 'generate_dynamic_featured_image' ) ):
function generate_dynamic_featured_image($post_id) {
  // 自動保存やリビジョン、自動下書きの場合は処理を終了
  if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
    return false;
  }

  // 投稿が下書き、レビュー待ち、または公開以外のステータスの場合は処理を終了
  $post_status = get_post_status($post_id);
  if (!in_array($post_status, ['publish'])) {
    return false;
  }

  // 投稿タイプが post, page, カスタム投稿 以外の場合は処理を終了
  $post_type = get_post_type($post_id);
  $custom_post_types = get_post_types(['_builtin' => false]); // すべてのカスタム投稿タイプを取得
  if (!in_array($post_type, array_merge(['post', 'page'], $custom_post_types))) {
    return false;
  }

  // GDライブラリがインストールされていない場合は処理を終了
  if (!extension_loaded('gd')) {
    error_log('GD library does not exist.');
    return false;
  }

  // すでにアイキャッチ画像が設定されている場合は処理を終了
  $current_thumbnail_id = get_post_thumbnail_id($post_id);
  if ($current_thumbnail_id) {
    return false;
  }

  // 「タイトルからアイキャッチ生成をする」が有効でない場合は処理を終了
  $is_checked = isset($_POST['generate_featured_image_from_title']) && (intval($_POST['generate_featured_image_from_title']) === 1);
  if (!$is_checked) {
    return false;
  }

  // 投稿タイトルと投稿者名を取得
  $post_title = get_the_title($post_id);
  // タイトルの整形
  $post_title = sanitize_post_title($post_title);

  // 投稿者ID取得
  $author_id = get_post_field('post_author', $post_id);
  // 投稿者の表示名の取得
  $author_name = get_the_author_meta('display_name', $author_id);
  // 投稿者のアバター画像を取得
  $avatar_url = get_the_author_upladed_avatar_url($author_id);

  // 画像のサイズを設定
  $width = 1280;
  // Cocoon設定のサムネイル画像サイズに適した高さ
  $height = get_thumbnail_height($width); // デフォルトだと720

  // アイキャッチ画像のパスを定義
  $upload_path = get_theme_featured_images_path();
  $post_title_hash = md5($post_title . $avatar_url . $author_name . $width . 'x' . $height);
  $new_image_path = get_dynamic_image_path($post_id, $upload_path, $post_title_hash);

  // すでにメディアに同じファイルが登録されている場合は処理を終了
  $existing_attachment_id = attachment_url_to_postid($new_image_path);
  if ($existing_attachment_id) {
    set_post_thumbnail($post_id, $existing_attachment_id);
    return;
  }

  // アイキャッチ画像生成に成功した場合、
  if (generate_dynamic_image($post_id, $new_image_path, $width, $height)) {
    // 画像をWordPressメディアライブラリに登録
    $attachment = [
      'post_mime_type' => 'image/png',
      'post_title'     => sanitize_file_name(basename($new_image_path)),
      'post_content'   => '',
      'post_status'    => 'inherit',
    ];

    // 画像をWordPressのメディアライブラリに添付ファイルとして登録
    $attachment_id = wp_insert_attachment($attachment, $new_image_path, $post_id);
    require_once(ABSPATH . 'wp-admin/includes/image.php'); // メタデータ生成用のファイルを読み込む
    $attachment_data = wp_generate_attachment_metadata($attachment_id, $new_image_path); // 添付ファイルのメタデータを生成
    wp_update_attachment_metadata($attachment_id, $attachment_data); // メタデータを更新し、データベースに保存

    // 投稿のアイキャッチ画像として設定
    set_post_thumbnail($post_id, $attachment_id);



    // Twitterカードのサムネイルのアスペクト比に準じた高さ
    $width = 1200;
    $height = 630;
    // $height = floor($width * 630 / 1200);

    // SNS画像のパスを定義
    $upload_path = get_theme_sns_images_path();
    $new_image_path = get_dynamic_image_path($post_id, $upload_path, $post_title_hash);

    // SNS画像生成に成功した場合
    if (generate_dynamic_image($post_id, $new_image_path, $width, $height)) {
      return true;
    }
  }
}
endif;

// タイトルからアイキャッチを生成HTMLの作成
if ( !function_exists( 'generate_featured_image_from_title_custom_checkbox' ) ):
function generate_featured_image_from_title_custom_checkbox() {
  ?>
  <div id="featured-image-from-title" class="featured-image-from-title">
    <label>
      <input type="checkbox" id="generate_featured_image_from_title" name="generate_featured_image_from_title" value="1">
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
  // GDライブラリがインストールされていない場合は処理を終了
  if (!extension_loaded('gd')) {
    return;
  }

  // タイトルアイキャッチが有効でない時
  if (!is_featured_image_from_title_enable()) {
    return;
  }
  global $post;
  ?>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      // チェックボックスのHTMLを作成
      var checkboxHtml = `
        <?php generate_featured_image_from_title_custom_checkbox(); ?>
      `;

      // チェックボックスを挿入または削除する関数
      function manageCustomCheckbox() {
        var postThumbnailDiv = document.querySelector('#postimagediv .inside');
        var thumbnailImage = document.querySelector('#postimagediv .inside img'); // アイキャッチ画像の存在をチェック
        var checkboxWrapper = document.querySelector('#featured-image-from-title');
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
        var checkboxInput = document.querySelector('#featured-image-from-title input[type="checkbox"]');
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

/* 「アイキャッチ画像」メタボックスにチェックボックスを追加 */
add_action('admin_footer-post.php', 'add_custom_checkbox_below_featured_image');
add_action('admin_footer-post-new.php', 'add_custom_checkbox_below_featured_image');
if ( !function_exists( 'add_custom_checkbox_below_featured_image' ) ):
function add_custom_checkbox_below_featured_image() {
  // GDライブラリがインストールされていない場合は処理を終了
  if (!extension_loaded('gd')) {
    return;
  }

  // Gutenbergエディターを使用していない場合は処理を終了
  if (!use_gutenberg_editor()) {
    return;
  }

  // タイトルアイキャッチが有効でない時
  if (!is_featured_image_from_title_enable()) {
    return;
  }
  ?>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      /* チェックボックスのHTMLを作成 */
      var checkboxHtml = `
        <?php generate_featured_image_from_title_custom_checkbox(); ?>
      `;

      /* チェックボックスを挿入または削除する関数 */
      function manageCustomCheckbox() {
      var featuredImagePanel = document.querySelector('.editor-post-featured-image'); /* ブロックエディターのアイキャッチエリア */
      var actionsPanel = document.querySelector('.editor-post-featured-image__actions');
      var customCheckbox = document.querySelector('#featured-image-from-title');

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
          var checkbox = document.querySelector('#generate_featured_image_from_title');
          var hiddenInput = document.querySelector('#generate_featured_image_from_title_hide');
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
      var initialCheckbox = document.querySelector('#generate_featured_image_from_title');
      if (initialCheckbox) {
      toggleFeaturedImageContainer(initialCheckbox.checked);
      }

      /* 常に#featured-image-from-titleの存在を監視してチェックボックスを管理 */
      setInterval(function () {
      manageCustomCheckbox();
      }, 500); /* 500msごとにチェック */
    });
  </script>
  <?php

}
endif;

/* チェックボックスのnonceフィールドを追加 */
add_action('edit_form_after_title', 'add_featured_image_checkbox_nonce');
if ( !function_exists( 'add_featured_image_checkbox_nonce' ) ):
function add_featured_image_checkbox_nonce() {
  // GDライブラリがインストールされていない場合は処理を終了
  if (!extension_loaded('gd')) {
    return;
  }

  // Gutenbergエディターを使用していない場合は処理を終了
  if (!use_gutenberg_editor()) {
    return;
  }

  // タイトルアイキャッチが有効でない時
  if (!is_featured_image_from_title_enable()) {
    return;
  }

  // nonceフィールドを追加してセキュリティを確保
  wp_nonce_field('save_generate_featured', 'generate_featured_image_nonce');
  // チェックボックスの状態を保持するための隠しフィールドを追加
  echo '<input type="hidden" id="generate_featured_image_from_title_hide" name="generate_featured_image_from_title" value="0">';
}
endif;