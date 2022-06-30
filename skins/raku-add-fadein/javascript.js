//ここにスキンで利用するJavaScriptを記入する

/*----------------------------------------------------
 メニューが被ってしまうのを回避
 ※「animation-fill-mode:both」を指定するとz-indexが正常に動作しないバグがあるようです
----------------------------------------------------*/
$('#navi-in').find('li, a').on('mouseenter', function() {
    if ($('#header-container').hasClass(('animation-done')) === false) {
        $('#header-container').addClass('animation-done');
    }
});

/*----------------------------------------------------
 fadein_type1
----------------------------------------------------*/
const fadein_type1 = [
    '#header-container',
    '#main',
    '#main > *',
    '#main .article > *',
    '#sidebar',
    '#sidebar > *',
];

$('.raku_fadein_type1').find(fadein_type1.join(',')).on('inview', function(event, isInView) {
    if (isInView) { $(this).stop().addClass('raku_fadeIn');} });

/*----------------------------------------------------
 fadein_type2
----------------------------------------------------*/
const fadein_type2 = [
    '#header-container',
    '#main',
    '#sidebar',
];

$('.raku_fadein_type2').find(fadein_type2.join(',')).on('inview', function(event, isInView) {
    if (isInView) { $(this).stop().addClass('raku_fadeIn');} });

$('.raku_fadein_type2').find('#main > *, #main .article > *, #sidebar > *').on('inview', function(event, isInView) {
    if (isInView) { $(this).stop().addClass('raku_fadeInUp');} });

/*----------------------------------------------------
 fadein_type3
----------------------------------------------------*/
const fadein_type3 = [
    '#header-container',
    '#main > *', 
    '#main .article > *',
    '#sidebar > *',
];

$('.raku_fadein_type3').find(fadein_type3.join(',')).on('inview', function(event, isInView) {
    if (isInView) { $(this).stop().addClass('raku_fadeIn');} });

$('.raku_fadein_type3').find('#main').on('inview', function(event, isInView) {
    if (isInView) { $(this).stop().addClass('raku_fadeInLeft');} });

$('.raku_fadein_type3').find('#sidebar').on('inview', function(event, isInView) {
    if (isInView) { $(this).stop().addClass('raku_fadeInRight');} });

/*----------------------------------------------------
 fadein_type4
----------------------------------------------------*/
const fadein_type4 = [
    '#main > *',
    '#main .article > *',
    '#sidebar > *',
];

$('.raku_fadein_type4').find(fadein_type4.join(',')).on('inview', function(event, isInView) {
    if (isInView) { $(this).stop().addClass('raku_fadeIn');} });

$('.raku_fadein_type4').find('#header-container').on('inview', function(event, isInView) {
    if (isInView) { $(this).stop().addClass('raku_fadeInDown');} });

$('.raku_fadein_type4').find('#main').on('inview', function(event, isInView) {
    if (isInView) { $(this).stop().addClass('raku_fadeInLeft');} });

$('.raku_fadein_type4').find('#sidebar').on('inview', function(event, isInView) {
    if (isInView) { $(this).stop().addClass('raku_fadeInRight');} });
