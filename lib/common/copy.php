<!doctype html>
<html>
<head>
<meta name="robots" content="noindex,nofollow">
<title>COPY</title>
<style>textarea{width: 100%;}</style>
</head>
<body>
<textarea name="copy-text" id="copy-text" cols="30" rows="5"><?php
  // タイトルはHTMLエスケープ、URLはURL用エスケープで安全に出力する
  $title = isset($_GET['title']) ? esc_html($_GET['title']).' ' : '';
  $url = isset($_GET['url']) ? esc_url($_GET['url']) : '';
  echo $title;
  echo $url;
?></textarea>
</body>
</html>
