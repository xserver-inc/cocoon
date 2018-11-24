<!doctype html>
<html>
<head>
<meta name="robots" content="noindex,nofollow">
<title>COPY</title>
<style>textarea{width: 100%;}</style>
</head>
<body>
<textarea name="copy-text" id="copy-text" cols="30" rows="5"><?php
  $title = isset($_GET['title']) ? strip_tags($_GET['title']).' ' : '';
  $url = isset($_GET['url']) ? strip_tags($_GET['url']) : '';
  echo $title;
  echo $url;
?></textarea>
</body>
</html>
