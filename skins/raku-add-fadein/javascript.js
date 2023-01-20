//ここにスキンで利用するJavaScriptを記入する

/*----------------------------------------------------
 fadeIn
----------------------------------------------------*/
// fadein
const raku_fadein = [
    // raku_fadein_type1
    '.raku_fadein_type1 #header-container',
    '.raku_fadein_type1 #main',
    '.raku_fadein_type1 #main > *',
    '.raku_fadein_type1 #main .article > *',
    '.raku_fadein_type1 #sidebar',
    '.raku_fadein_type1 #sidebar > *',
    // raku_fadein_type2
    '.raku_fadein_type2 #header-container',
    '.raku_fadein_type2 #main',
    '.raku_fadein_type2 #sidebar',
    // raku_fadein_type3
    '.raku_fadein_type3 #header-container',
    '.raku_fadein_type3 #main > *', 
    '.raku_fadein_type3 #main .article > *',
    '.raku_fadein_type3 #sidebar > *',
    // raku_fadein_type4
    '.raku_fadein_type4 #main > *',
    '.raku_fadein_type4 #main .article > *',
    '.raku_fadein_type4 #sidebar > *',
];

const observer_fadein = new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
        if (entry.isIntersecting) {
            entry.target.classList.add('raku_fadeIn');
            observer_fadein.unobserve(entry.target);
        }
    });
});

const targets_fadein = document.querySelectorAll(raku_fadein.join(','));
targets_fadein.forEach((target) => {
    observer_fadein.observe(target);
});

/*----------------------------------------------------
 fadeInUp
----------------------------------------------------*/
const raku_fadein_up = [
    // raku_fadein_type2
    '.raku_fadein_type2 #main > *',
    '.raku_fadein_type2 #main .article > *',
    '.raku_fadein_type2 #sidebar > *',
];

const observer_raku_fadein_up = new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
        if (entry.isIntersecting) {
            entry.target.classList.add('raku_fadeInUp');
            observer_raku_fadein_up.unobserve(entry.target);
        }
    });
});

const targets_raku_fadein_up = document.querySelectorAll(raku_fadein_up.join(','));
targets_raku_fadein_up.forEach((target) => {
    observer_raku_fadein_up.observe(target);
});

/*----------------------------------------------------
 fadeInDown
----------------------------------------------------*/
const raku_fadein_down = [
    // raku_fadein_type4
    '.raku_fadein_type4 #header-container',
];

const observer_fadein_down = new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
        if (entry.isIntersecting) {
            entry.target.classList.add('raku_fadeInDown');
            observer_fadein_down.unobserve(entry.target);
        }
    });
});

const targets_fadein_down = document.querySelectorAll(raku_fadein_down.join(','));
targets_fadein_down.forEach((target) => {
    observer_fadein_down.observe(target);
});

/*----------------------------------------------------
 fadeInLeft
----------------------------------------------------*/
const raku_fadein_left = [
    // raku_fadein_type3
    '.raku_fadein_type3 #main',
    // raku_fadein_type4
    '.raku_fadein_type4 #main',
];

const observer_fadein_left = new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
        if (entry.isIntersecting) {
            entry.target.classList.add('raku_fadeInLeft');
            observer_fadein_left.unobserve(entry.target);
        }
    });
});

const targets_fadein_left = document.querySelectorAll(raku_fadein_left.join(','));
targets_fadein_left.forEach((target) => {
    observer_fadein_left.observe(target);
});

/*----------------------------------------------------
 fadeInRight
----------------------------------------------------*/
const raku_fadein_right = [
    // raku_fadein_type3
    '.raku_fadein_type3 #sidebar',
    // raku_fadein_type4
    '.raku_fadein_type4 #sidebar',
];

const observer_fadein_right = new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
        if (entry.isIntersecting) {
            entry.target.classList.add('raku_fadeInRight');
            observer_fadein_right.unobserve(entry.target);
        }
    });
});

const targets_fadein_right = document.querySelectorAll(raku_fadein_right.join(','));
targets_fadein_right.forEach((target) => {
    observer_fadein_right.observe(target);
});

