/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */

wp.domReady(function () {
    // add classes
    const addClasses = function () {
        // add body class
        jQuery('#editor .block-editor-writing-flow').addClass('body main article page-body ' + gbSettings['siteIconFont']);

        // add title class
        jQuery('#editor .editor-post-title__input').addClass('entry-title');
    };
    addClasses();

    // subscribe switch editor mode
    wp.data.subscribe(function (selector, listener) {
        let previousValue = selector();
        return function () {
            let selectedValue = selector();
            if (selectedValue !== previousValue) {
                previousValue = selectedValue;
                listener(selectedValue);
            }
        };
    }(function () {
        return wp.data.select('core/edit-post').getEditorMode();
    }, function () {
        setTimeout(addClasses, 1);
    }));

    // remove style
    const removeStyle = function (regexp, applyTo, index, keep, keepOriginal) {
        // TODO: consider media query
        jQuery('style').each(function () {
            const html = jQuery(this).html();

            // get all matched styles
            let m;
            const matches = [];
            while ((m = regexp.exec(html)) != null) {
                matches.push(m);
            }

            // if exists
            if (matches.length > 0) {
                let replaced = keepOriginal ? html : html.replace(regexp, '');
                matches.forEach(function (match) {
                    // keep some styles ( for child skin )
                    // e.g. font-family
                    let style = '';
                    match[index].replace(/\/\*[\s\S]+?\*\//g, '').trim().split(/\r\n|\r|\n/).forEach(function (item) {
                        const split = item.split(':');
                        if (split.length >= 2) {
                            if (!keep || jQuery.inArray(split[0], keep) >= 0) {
                                style += item.replace(/;$/, '') + ';';
                            }
                        }
                    });
                    if (style) {
                        replaced += ' ' + applyTo + ' {' + style + '}';
                    }
                });
                jQuery(this).html(replaced);
            }
        });
    };

    /** @var {{background: boolean, title: boolean}} cocoon_gutenberg_params */

    // remove style which applied to all elements ( e.g. body, * )
    // body, *
    // -> .editor-styles-wrapper, .editor-styles-wrapper *
    removeStyle(/\.editor-styles-wrapper(\s+\*)?\s*{([\s\S]+?)}/g, [
        '.editor-post-title__block .editor-post-title__input',
        'div.editor-block-list__block',
        'div.editor-block-list__block p',
    ].join(', '), 2, [
        'font-family',
        'line-height',
        // keep style names
    ], cocoon_gutenberg_params.background);

    if (cocoon_gutenberg_params.background) {
        // for background
        removeStyle(/\.editor-styles-wrapper(\s+\*|\.public-page)?\s*{([\s\S]+?)}/g, '.editor-styles-wrapper', 2, [
            'background',
            'background-image',
            'background-size',
            'background-repeat',
            'background-origin',
            'background-position',
            'background-position-x',
            'background-position-y',
            'background-attachment',
            'background-clip',
            'background-color',
            // keep style names
        ]);
    }

    if (cocoon_gutenberg_params.title) {
        // .article h1 -> title
        removeStyle(/\.editor-styles-wrapper\s+.article\s+h1\s*{([\s\S]+?)}/g, '.editor-post-title__block .editor-post-title__input.entry-title', 1);
    }

    jQuery('style').each(function () {
        jQuery(this).html(jQuery(this).html().replace(/main\.main/g, '.block-editor-writing-flow.main'));
    });
});

// (function($){
//   //タイマーを使ったあまり美しくない方法
//   //tiny_mce_before_initフック引数配列のbody_classがなかったもので。
//   //もしWordPressフックを使った方法や、もうちょっと綺麗なjQueryの書き方があればフォーラムで教えていただければ幸いです。
//   //https://wp-cocoon.com/community/cocoon-theme/
//   setInterval(function(){
//     $('#editor .block-editor-writing-flow').addClass('body main article page-body ' + gbSettings['siteIconFont']);
//   },1000);

// })(jQuery);
