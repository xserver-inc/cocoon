/**
 * Cocoon WordPress Theme
 * @author: technote-space
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 * @description「Passive Event Listener を使用してサイトでのスクロール パフォーマンスを向上させる」への対応 (https://developers.google.com/web/tools/lighthouse/audits/passive-event-listeners?hl=ja)
 */
(function () {
    // https://github.com/WICG/EventListenerOptions/blob/gh-pages/explainer.md#feature-detection
    let supportsPassive = false;
    try {
        let opts = Object.defineProperty({}, 'passive', {
            get: function () {
                supportsPassive = true;
            }
        });
        window.addEventListener("testPassive", null, opts);
        window.removeEventListener("testPassive", null, opts);
    } catch (e) {
    }

    // https://stackoverflow.com/questions/36675693/eventtarget-interface-in-safari
    const target = window.EventTarget || Element;
    const originalAddEventListener = target.prototype.addEventListener;
    target.prototype.addEventListener = function (type, listener, options) {
        if ('wheel' === type || 'mousewheel' === type || 'touchstart' === type || 'touchmove' === type) {
            if (supportsPassive) {
                options = options || {};
                options.passive = true;
            }
        }
        originalAddEventListener.call(this, type, listener, options);
    };
})();
