/* A polyfill for browsers that don't support ligatures. */
/* The script tag referring to this file must be placed before the ending body tag. */

/* To provide support for elements dynamically added, this script adds
   method 'icomoonLiga' to the window object. You can pass element references to this method.
*/
(function () {
    'use strict';
    function supportsProperty(p) {
        var prefixes = ['Webkit', 'Moz', 'O', 'ms'],
            i,
            div = document.createElement('div'),
            ret = p in div.style;
        if (!ret) {
            p = p.charAt(0).toUpperCase() + p.substr(1);
            for (i = 0; i < prefixes.length; i += 1) {
                ret = prefixes[i] + p in div.style;
                if (ret) {
                    break;
                }
            }
        }
        return ret;
    }
    var icons;
    if (!supportsProperty('fontFeatureSettings')) {
        icons = {
            'google-plus': '&#xea8b;',
            'brand5': '&#xea8b;',
            'instagram': '&#xea92;',
            'brand12': '&#xea92;',
            'github': '&#xeab0;',
            'brand40': '&#xeab0;',
            'home3': '&#xe902;',
            'house3': '&#xe902;',
            'amazon': '&#xea87;',
            'brand': '&#xea87;',
            'linkedin2': '&#xeaca;',
            'brand65': '&#xeaca;',
            'codepen': '&#xeae8;',
            'brand81': '&#xeae8;',
            'bubble': '&#xe96b;',
            'comment': '&#xe96b;',
            'soundcloud': '&#xeac3;',
            'brand58': '&#xeac3;',
            'linkedin2': '&#xeacb;',
            'brand65': '&#xeacb;',
          '0': 0
        };
        delete icons['0'];
        window.icomoonLiga = function (els) {
            var classes,
                el,
                i,
                innerHTML,
                key;
            els = els || document.getElementsByTagName('*');
            if (!els.length) {
                els = [els];
            }
            for (i = 0; ; i += 1) {
                el = els[i];
                if (!el) {
                    break;
                }
                classes = el.className;
                if (/icon-/.test(classes)) {
                    innerHTML = el.innerHTML;
                    if (innerHTML && innerHTML.length > 1) {
                        for (key in icons) {
                            if (icons.hasOwnProperty(key)) {
                                innerHTML = innerHTML.replace(new RegExp(key, 'g'), icons[key]);
                            }
                        }
                        el.innerHTML = innerHTML;
                    }
                }
            }
        };
        window.icomoonLiga();
    }
}());
