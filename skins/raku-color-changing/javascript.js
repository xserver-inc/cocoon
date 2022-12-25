//ここにスキンで利用するJavaScriptを記入する

/*----------------------------------------------------
 メニューが被ってしまうのを回避
 ※「animation-fill-mode:both」を指定するとz-indexが正常に動作しないバグがあるようです
----------------------------------------------------*/
let no = 0;
$('.widget-sidebar-title').each(function() {
    let skincolor_no = (no % 4) + 1; 
    $(this).addClass('skincolor' + skincolor_no.toString());
    no++;
});

