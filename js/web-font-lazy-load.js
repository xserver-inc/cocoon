/////////////////////////////////
// WEBフォントの非同期読み込み
/////////////////////////////////
function loadWebFont(url)  {

  var webfont = url;
  var createLink = function (href) {
    var link = document.createElement('link');
    link.rel = 'stylesheet';
    link.href = href;
    document.head.appendChild(link);
  };
  var raf = requestAnimationFrame || mozRequestAnimationFrame || webkitRequestAnimationFrame || msRequestAnimationFrame;
  if (raf) {
    raf(function (){
      createLink(webfont);
    });
  } else {
    window.addEventListener('load', function(){
      createLink(webfont);
    });
  }
}
