<?php
/**
 * 同梱スキンの一覧用軽量サムネイル（360x225 WebP）を生成します。
 */

if ( PHP_SAPI !== 'cli' ) {
  exit( 1 );
}

$theme_root = dirname( __DIR__ );
$skins_dir  = $theme_root . '/skins';
$output_dir = $theme_root . '/images/skin-thumbs';
$target_w   = 360;
$target_h   = 225;
$quality    = 76;

if ( ! is_dir( $output_dir ) && ! mkdir( $output_dir, 0775, true ) ) {
  fwrite( STDERR, "出力ディレクトリを作成できません: {$output_dir}\n" );
  exit( 1 );
}

$use_imagick = extension_loaded( 'imagick' );
$use_gd      = extension_loaded( 'gd' ) && function_exists( 'imagewebp' );
if ( ! $use_imagick && ! $use_gd ) {
  fwrite( STDERR, "Imagick または WebP 対応の GD 拡張が必要です。\n" );
  exit( 1 );
}

// 対象スキンとScreenshot URIの収集
$targets = array();
$entries = scandir( $skins_dir );
sort( $entries );
foreach ( $entries as $entry ) {
  if ( $entry === '.' || $entry === '..' ) {
    continue;
  }
  $style_path = $skins_dir . '/' . $entry . '/style.css';
  if ( ! is_file( $style_path ) ) {
    continue;
  }
  $css = file_get_contents( $style_path );
  if ( $css === false ) {
    continue;
  }
  if ( ! preg_match( '/(Skin )?Name: *(.+)/i', $css ) ) {
    continue;
  }
  if ( preg_match( '/Screenshot URI: *(.+)/i', $css, $match ) ) {
    $uri = trim( $match[1] );
    if ( $uri !== '' ) {
      $targets[ $entry ] = $uri;
    }
  }
}

// --force指定時の既存サムネイル再生成オプション
$force = in_array( '--force', array_slice( $argv, 1 ), true );

// 外部画像のダウンロードと同一URLのキャッシュ再利用
$download_cache = array();
$curl_handle    = function_exists( 'curl_init' ) ? curl_init() : null;
$fetch_image    = static function ( $url ) use ( &$download_cache, $curl_handle ) {
  if ( array_key_exists( $url, $download_cache ) ) {
    return $download_cache[ $url ];
  }
  $binary = false;
  if ( $curl_handle ) {
    curl_setopt_array(
      $curl_handle,
      array(
        CURLOPT_URL            => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_MAXREDIRS      => 5,
        CURLOPT_CONNECTTIMEOUT => 8,
        CURLOPT_TIMEOUT        => 15,
        CURLOPT_USERAGENT      => 'Cocoon-Skin-Thumbnail-Generator/1.0',
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_SSL_VERIFYHOST => 2,
      )
    );
    $result = curl_exec( $curl_handle );
    $status = (int) curl_getinfo( $curl_handle, CURLINFO_RESPONSE_CODE );
    if ( $result !== false && $status >= 200 && $status < 400 ) {
      $binary = $result;
    }
  } else {
    $context = stream_context_create(
      array(
        'http' => array(
          'method'          => 'GET',
          'timeout'         => 15,
          'follow_location' => 1,
          'max_redirects'   => 5,
          'header'          => "User-Agent: Cocoon-Skin-Thumbnail-Generator/1.0\r\n",
        ),
        'ssl'  => array(
          'verify_peer'      => true,
          'verify_peer_name' => true,
        ),
      )
    );
    $binary = @file_get_contents( $url, false, $context );
  }
  $download_cache[ $url ] = $binary !== false ? $binary : null;
  // 外部サーバーへの負荷軽減用の短い待機
  usleep( 50000 );
  return $download_cache[ $url ];
};

// 上端中央揃えの16対10カバークロップとWebP変換
$convert_to_webp = static function ( $binary, $dest_path ) use ( $use_imagick, $use_gd, $target_w, $target_h, $quality ) {
  $target_ratio = $target_w / $target_h;
  if ( $use_imagick ) {
    try {
      $img = new Imagick();
      $img->readImageBlob( $binary );
      if ( $img->getNumberImages() > 1 ) {
        $img->setIteratorIndex( 0 );
        $first = $img->getImage();
        $img->clear();
        $img->destroy();
        $img = $first;
      }
      $img->setImageBackgroundColor( new ImagickPixel( 'white' ) );
      $img = $img->mergeImageLayers( Imagick::LAYERMETHOD_FLATTEN );
      $src_w = $img->getImageWidth();
      $src_h = $img->getImageHeight();
      if ( $src_w <= 0 || $src_h <= 0 ) {
        $img->destroy();
        return false;
      }
      $src_ratio = $src_w / $src_h;
      if ( $src_ratio > $target_ratio ) {
        $crop_h = $src_h;
        $crop_w = (int) round( $src_h * $target_ratio );
        $crop_x = (int) floor( ( $src_w - $crop_w ) / 2 );
        $crop_y = 0;
      } else {
        $crop_w = $src_w;
        $crop_h = (int) round( $src_w / $target_ratio );
        $crop_x = 0;
        $crop_y = 0;
      }
      $img->cropImage( max( 1, $crop_w ), max( 1, $crop_h ), $crop_x, $crop_y );
      $img->setImagePage( 0, 0, 0, 0 );
      $img->resizeImage( $target_w, $target_h, Imagick::FILTER_LANCZOS, 1, false );
      $img->stripImage();
      $img->setImageFormat( 'webp' );
      $img->setImageCompressionQuality( $quality );
      $img->setOption( 'webp:method', '4' );
      $ok = $img->writeImage( $dest_path );
      $img->destroy();
      if ( $ok ) {
        return true;
      }
    } catch ( Throwable $e ) {
      // Imagick失敗時はGDへのフォールバックへ移行
      if ( ! $use_gd ) {
        throw $e;
      }
    }
  }

  if ( ! $use_gd ) {
    return false;
  }

  $src = @imagecreatefromstring( $binary );
  if ( ! $src ) {
    return false;
  }
  $src_w = imagesx( $src );
  $src_h = imagesy( $src );
  $src_ratio = $src_w / $src_h;
  if ( $src_ratio > $target_ratio ) {
    $crop_h = $src_h;
    $crop_w = (int) round( $src_h * $target_ratio );
    $crop_x = (int) floor( ( $src_w - $crop_w ) / 2 );
    $crop_y = 0;
  } else {
    $crop_w = $src_w;
    $crop_h = (int) round( $src_w / $target_ratio );
    $crop_x = 0;
    $crop_y = 0;
  }
  $dst = imagecreatetruecolor( $target_w, $target_h );
  $white = imagecolorallocate( $dst, 255, 255, 255 );
  imagefill( $dst, 0, 0, $white );
  imagecopyresampled( $dst, $src, 0, 0, $crop_x, $crop_y, $target_w, $target_h, $crop_w, $crop_h );
  $ok = imagewebp( $dst, $dest_path, $quality );
  imagedestroy( $src );
  imagedestroy( $dst );
  return $ok;
};

$succeeded = 0;
$failed    = array();
$sizes     = array();

foreach ( $targets as $slug => $url ) {
  $dest = $output_dir . '/' . $slug . '.webp';
  if ( ! $force && is_file( $dest ) && filesize( $dest ) > 0 ) {
    $succeeded++;
    $sizes[ $slug ] = filesize( $dest );
    continue;
  }
  $binary = $fetch_image( $url );
  if ( $binary === null ) {
    $failed[] = "{$slug} (download failed: {$url})";
    continue;
  }
  try {
    $ok = $convert_to_webp( $binary, $dest );
  } catch ( Throwable $e ) {
    $ok = false;
    $failed[] = "{$slug} (convert error: {$e->getMessage()})";
    continue;
  }
  if ( ! $ok || ! is_file( $dest ) ) {
    $failed[] = "{$slug} (write failed)";
    continue;
  }
  $succeeded++;
  $sizes[ $slug ] = filesize( $dest );
}

if ( $curl_handle ) {
  curl_close( $curl_handle );
}

$total_bytes = array_sum( $sizes );
$avg_bytes   = $succeeded > 0 ? (int) round( $total_bytes / $succeeded ) : 0;
$min_bytes   = $succeeded > 0 ? min( $sizes ) : 0;
$max_bytes   = $succeeded > 0 ? max( $sizes ) : 0;

printf(
  "Generated: %d / %d thumbnails (%dx%d WebP q=%d)\n",
  $succeeded,
  count( $targets ),
  $target_w,
  $target_h,
  $quality
);
printf(
  "Total: %s bytes (%.2f KB / %.2f MB) | Avg: %.2f KB | Min: %.2f KB | Max: %.2f KB\n",
  number_format( $total_bytes ),
  $total_bytes / 1024,
  $total_bytes / ( 1024 * 1024 ),
  $avg_bytes / 1024,
  $min_bytes / 1024,
  $max_bytes / 1024
);
if ( ! empty( $failed ) ) {
  fwrite( STDERR, "Failed:\n - " . implode( "\n - ", $failed ) . "\n" );
  exit( 1 );
}
