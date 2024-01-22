<?php

// Based on <https://github.com/mecha-cms/extend.minify>
if ( !defined( 'ABSPATH' ) ) exit;

define('MINIFY_STRING', '"(?:[^"\\\]|\\\.)*"|\'(?:[^\'\\\]|\\\.)*\'|`(?:[^`\\\]|\\\.)*`');
define('MINIFY_COMMENT_CSS', '/\*[\s\S]*?\*/');
define('MINIFY_COMMENT_HTML', '<!\-{2}[\s\S]*?\-{2}>');
define('MINIFY_COMMENT_JS', '//[^\n]*');
define('MINIFY_PATTERN_JS', '/[^\n]+?/[gimuy]*');
// define('MINIFY_PATTERN_JS', '/([^\n]{2,}?|\w)/(/)?[gimuy]*');
define('MINIFY_HTML', '<[!/]?[a-zA-Z\d:.-]+[\s\S]*?>');
define('MINIFY_HTML_ENT', '&(?:[a-zA-Z\d]+|\#\d+|\#x[a-fA-F\d]+);');
define('MINIFY_HTML_KEEP', '<pre(?:\s[^<>]*?)?>[\s\S]*?</pre>|<code(?:\s[^<>]*?)?>[\s\S]*?</code>|<script(?:\s[^<>]*?)?>[\s\S]*?</script>|<style(?:\s[^<>]*?)?>[\s\S]*?</style>|<textarea(?:\s[^<>]*?)?>[\s\S]*?</textarea>');

// get URL
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || (!empty(($_SERVER['SERVER_PORT'])) && $_SERVER['SERVER_PORT'] === 443) ? 'https' : 'http') . '://';
$host = !empty($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : (!empty($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : "");
$url = $protocol . $host;

// escape character
define('X', "\x1A");

// normalize line–break(s)
function n($s) {
    return str_replace(array("\r\n", "\r"), "\n", $s);
}

// trim once
function t($a, $b) {
    if ($a && strpos($a, $b) === 0 && substr($a, -strlen($b)) === $b) {
        return substr(substr($a, strlen($b)), 0, -strlen($b));
    }
    return $a;
}

function fn_minify($pattern, $input) {
    $array = array();
    $res = preg_split('#(' . implode('|', $pattern) . ')#', $input, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
    if ($res) {
        $array = $res;
    }
    return $array;
}

function fn_minify_css($input, $comment = 0, $quote = 2) {
    if (!is_string($input) || !$input = n(trim($input))) return $input;
    $output = $prev = "";
    foreach (fn_minify(array(MINIFY_COMMENT_CSS, MINIFY_STRING), $input) as $part) {
        if (trim($part) === "") continue;
        if ($comment !== 1 && strpos($part, '/*') === 0 && substr($part, -2) === '*/') {
            $part2 = isset($part[2]) ? $part[2] : false;
            $res = $part2 ? strpos('*!', $part2) !== false : false;
            if (
                $comment === 2 && (
                    // Detect special comment(s) from the third character. It should be a `!` or `*` → `/*! keep */` or `/** keep */`
                    $res ||
                    // Detect license comment(s) from the content. It should contains character(s) like `@license`
                    stripos($part, '@licence') !== false || // noun
                    stripos($part, '@license') !== false || // verb
                    stripos($part, '@preserve') !== false
                )
            ) {
                $output .= $part;
            }
            continue;
        }
        if ($part[0] === '"' && substr($part, -1) === '"' || $part[0] === "'" && substr($part, -1) === "'") {
            // Remove quote(s) where possible …
            $q = $part[0];
            // Make sure URL does no contains `[ \n\t"']` character(s)
            $clean = t($part, $q); // Trim quote(s)
            $ok = strcspn($clean, " \n\t\"'") === strlen($clean);
            if (
                $quote !== 1 && (
                    // <https://www.w3.org/TR/CSS2/syndata.html#uri>
                    substr($prev, -4) === 'url(' && preg_match('#\burl\($#', $prev) && $ok ||
                    // <https://www.w3.org/TR/CSS2/syndata.html#characters>
                    substr($prev, -1) === '=' && preg_match('#^' . $q . '[a-zA-Z_][\w-]*?' . $q . '$#', $part)
                )
            ) {
                $part = $clean;
            }
            $output .= $part;
        } else {
            $output .= fn_minify_css_union($part);
        }
        $prev = $part;
    }
    return trim($output);
}

function fn_minify_css_union($input) {
    if (stripos($input, 'calc(') !== false) {
        // Keep important white–space(s) in `calc()`
        $input = preg_replace_callback('#\bcalc(\(([^\(\)]+|(?1))*\))#i', function($m) {
            return preg_replace('#\s+#', X, $m[0]);
        }, $input);
    }
    $input = preg_replace(array(
        // Fix case for `#foo<space>[bar="baz"]`, `#foo<space>*` and `#foo<space>:first-child` [^1]
        '#(?<=[\w])\s+(\*|\[|:[\w-]+)#',
        // Fix case for `[bar="baz"]<space>.foo`, `*<space>.foo`, `:nth-child(2)<space>.foo` and `@media<space>(foo: bar)<space>and<space>(baz: qux)` [^2]
        '#([*\]\)])\s+(?=[\w\#.])#', '#\b\s+\(#', '#\)\s+\b#',
        // Minify HEX color code … [^3]
        '#\#([a-f\d])\1([a-f\d])\2([a-f\d])\3\b#i',
        // Remove white–space(s) around punctuation(s) [^4]
        '#\s*([~!@*\(\)+=\{\}\[\]:;,>\/])\s*#',
        // Replace zero unit(s) with `0` [^5]
        // <https://www.w3.org/Style/Examples/007/units.en.html>
        '#\b(?<!\d\.)(?:0+\.)?0+(?:(?:cm|em|ex|in|mm|pc|pt|px|rem|vh|vmax|vmin|vw)\b)#',
        // Replace `0.6` with `.6` [^6]
        '#\b0+\.(\d+)#',
        // Replace `:0 0`, `:0 0 0` and `:0 0 0 0` with `:0` [^7]
        '#:(0\s+){0,3}0(?=[!,;\)\}]|$)#',
        // Replace `background(?:-position)?:(0|none)` with `background$1:0 0` [^8]
        '#\b(background(?:-position)?):(?:0|none)([;,\}])#i',
        // Replace `(border(?:-radius)?|outline):none` with `$1:0` [^9]
        '#\b(border(?:-radius)?|outline):none\b#i',
        // Remove empty selector(s) [^10]
        '#(^|[\{\}])(?:[^\{\}]+)\{\}#',
        // Remove the last semi–colon and replace multiple semi–colon(s) with a semi–colon [^11]
        '#;+([;\}])#',
        // Replace multiple white–space(s) with a space [^12]
        '#\s+#'
    ), array(
        // [^1]
        X . '$1',
        // [^2]
        '$1' . X, X . '(', ')' . X,
        // [^3]
        '#$1$2$3',
        // [^4]
        '$1',
        // [^5]
        '0',
        // [^6]
        '.$1',
        // [^7]
        ':0',
        // [^8]
        '$1:0 0$2',
        // [^9]
        '$1:0',
        // [^10]
        '$1',
        // [^11]
        '$1',
        // [^12]
        ' '
    ), $input);
    return trim(str_replace(X, ' ', $input));
}

function fn_minify_html($input, $comment = 2, $quote = 1) {
    if (!is_string($input) || !$input = n(trim($input))) return $input;
    $output = $prev = "";
    foreach (fn_minify(array(MINIFY_COMMENT_HTML, MINIFY_HTML_KEEP, MINIFY_HTML, MINIFY_HTML_ENT), $input) as $part) {
        // if (includes_string($part, '<pre') || includes_string($part, '<code')) {
        //     _v($part);
        // }
        if ($part === "\n") continue;
        if ($part !== ' ' && trim($part) === "" || $comment !== 1 && strpos($part, '<!--') === 0) {
            // Detect IE conditional comment(s) by its closing tag …
            if ($comment === 2 && substr($part, -12) === '<![endif]-->') {
                $output .= $part;
            }
            continue;
        }
        if ($part[0] === '<' && substr($part, -1) === '>') {
            $output .= $part;
            // //codeタグの場合は処理しない
            // if (includes_string($part, '<code')) {
            //     $output .= $part;
            // // } elseif ( includes_string($part, '<script') ) {
	        // //     //scriptタグの場合はjsの圧縮を行う
	        // //     $output .= minify_js($part);
            // }
            // else {
            //     // $output .= $part;
            //     $output .= fn_minify_html_union($part, $quote);
            // }
        } else if ($part[0] === '&' && substr($part, -1) === ';' && $part !== '&lt;' && $part !== '&gt;' && $part !== '&amp;') {
            $output .= html_entity_decode($part); // Evaluate HTML entit(y|ies)
        } else {
            $output .= preg_replace('#\s+#', ' ', $part);
        }
        $prev = $part;
    }

    $output = str_replace(' </', '</', $output);
    // Force space with `&#x0020;` and line–break with `&#x000A;`
    return str_ireplace(array('&#x0020;', '&#x20;', '&#x000A;', '&#xA;'), array(' ', ' ', "\n", "\n"), trim($output));
}

function fn_minify_html_union($input, $quote) {
    if (
        strpos($input, ' ') === false &&
        strpos($input, "\n") === false &&
        strpos($input, "\t") === false
    ) return $input;
    global $url;
    return preg_replace_callback('#<\s*([^\/\s]+)\s*(?:>|(\s[^<>]+?)\s*>)#', function($m) use($quote, $url) {
        if (isset($m[2])) {
            // // Minify inline CSS(s)
            // if (stripos($m[2], ' style=') !== false) {
            //     $m[2] = preg_replace_callback('#( style=)([\'"]?)(.*?)\2#i', function($m) {
            //         return $m[1] . $m[2] . fn_minify_css($m[3]) . $m[2];
            //     }, $m[2]);
            // }

            // // Minify URL(s)
            // if (strpos($m[2], '://') !== false) {
            //     $m[2] = str_replace(array(
            //         $url . '/',
            //         $url . '?',
            //         $url . '&',
            //         $url . '#',
            //         $url . '"',
            //         $url . "'"
            //     ), array(
            //         '/',
            //         '?',
            //         '&',
            //         '#',
            //         '/"',
            //         "/'"
            //     ), $m[2]);
            // }
            $a = 'a(sync|uto(focus|play))|c(hecked|ontrols)|d(efer|isabled)|hidden|ismap|loop|multiple|open|re(adonly|quired)|s((cop|elect)ed|pellcheck)';
            //$subject = $m[2];
            $subject = str_replace("\n", ' ', $m[2]);
            $a = '<' . $m[1] . preg_replace(array(
                // From `a="a"`, `a='a'`, `a="true"`, `a='true'`, `a=""` and `a=''` to `a` [^1]
                '#\s(' . $a . ')(?:=([\'"]?)(?:true|\1)?\2)#i',
                // Remove extra white–space(s) between HTML attribute(s) [^2]
                '#\s*([^\s=]+?)(=(?:\S+|([\'"]?).*?\3)|$)#',
                // From `<img />` to `<img/>` [^3]
                '#\s+\/$#'
            ), array(
                // [^1]
                ' $1',
                // [^2]
                ' $1$2',
                // [^3]
                '/'
            ), $subject) . '>';
            return $quote !== 1 ? fn_minify_html_union_attr($a) : $a;
        }
        return '<' . $m[1] . '>';
    }, $input);
}

function fn_minify_html_union_attr($input) {
    if (strpos($input, '=') === false) return $input;
    return preg_replace_callback('#=(' . MINIFY_STRING . ')#', function($m) {
        $q = $m[1][0];
        if (strpos($m[1], ' ') === false && preg_match('#^' . $q . '[a-zA-Z_][\w-]*?' . $q . '$#', $m[1])) {
            return '=' . t($m[1], $q);
        }
        return $m[0];
    }, $input);
}

function fn_minify_js($input, $comment = 2, $quote = 2) {
    if (!is_string($input) || !$input = n(trim($input))) return $input;
    $output = $prev = "";
    foreach (fn_minify(array(MINIFY_COMMENT_CSS, MINIFY_STRING, MINIFY_COMMENT_JS, MINIFY_PATTERN_JS), $input) as $part) {
        if (trim($part) === "") continue;
        if ($comment !== 1 && (
            strpos($part, '//') === 0 || // Remove inline comment(s)
            strpos($part, '/*') === 0 && substr($part, -2) === '*/'
        )) {
            $part2 = isset($part[2]) ? $part[2] : false;
            $res = $part2 ? strpos('*!', $part2) !== false : false;
            if (
                $comment === 2 && (
                    // Detect special comment(s) from the third character. It should be a `!` or `*` → `/*! keep */` or `/** keep */`
                    $res ||
                    // Detect license comment(s) from the content. It should contains character(s) like `@license`
                    stripos($part, '@licence') !== false || // noun
                    stripos($part, '@license') !== false || // verb
                    stripos($part, '@preserve') !== false
                )
            ) {
                $output .= $part;
            }
            continue;
        }
        if ($part[0] === '/' && (substr($part, -1) === '/' || preg_match('#\/[gimuy]*$#', $part))) {
            $output .= $part;
        } else if (
            $part[0] === '"' && substr($part, -1) === '"' ||
            $part[0] === "'" && substr($part, -1) === "'" ||
            $part[0] === '`' && substr($part, -1) === '`' // ES6
        ) {
            // TODO: Remove quote(s) where possible …
            $output .= $part;
        } else {
            $output .= fn_minify_js_union($part);
        }
        $prev = $part;
    }
    return $output;
}

function fn_minify_js_union($input) {
    return preg_replace(array(
        // Remove white–space(s) around punctuation(s) [^1]
        '#\s*([!%&*\(\)\-=+\[\]\{\}|;:,.<>?\/])\s*#',
        // Remove the last semi–colon and comma [^2]
        '#[;,]([\]\}])#',
        // Replace `true` with `!0` and `false` with `!1` [^3]
        '#\btrue\b#', '#\bfalse\b#', '#\b(return\s?)\s*\b#',
        // Replace `new Array(x)` with `[x]` … [^4]
        //'#\b(?:new\s+)?Array\((.*?)\)#', '#\b(?:new\s+)?Object\((.*?)\)#'
    ), array(
        // [^1]
        '$1',
        // [^2]
        '$1',
        // [^3]
        '!0', '!1', '$1',
        // [^4]
        //'[$1]', '{$1}'
    ), $input);
}


/**
 * Backward Compatibility
 * ----------------------
 */

// function minify_css(...$lot) {
//     return fn_minify_css(...$lot);
// }
use MatthiasMullie\Minify;
function minify_css(...$lot) {
    $css = '';
    foreach ($lot as $css_source) {
        $css .= $css_source;
    }
    //公開ページのみ縮小化
    if (!is_admin()) {
        $minifier = new Minify\CSS($css);
        $css = $minifier->minify();
    }
    return $css;
}


// function minify_html(...$lot) {
//     return fn_minify_html(...$lot);
// }
function minify_html($html) {
    // 改行やタブ、余分なスペースを削除
    $search = array(
        '/\>[^\S ]+/s',     // タグの後の空白を削除
        '/[^\S ]+\</s',     // タグの前の空白を削除
        // '/(\s)+/s',         // 連続する空白を一つに
        '/<!--(.|\s)*?-->/' // HTMLコメントを削除
    );

    $replace = array(
        '>',
        '<',
        // '\\1',
        ''
    );

    $html = preg_replace($search, $replace, $html);

    return $html;
}


// function minify_js(...$lot) {
//     return fn_minify_js(...$lot);
// }
function minify_js(...$lot) {
    $js = '';
    foreach ($lot as $js_source) {
        $js .= $js_source;
    }
    //公開ページのみ縮小化
    if (!is_admin()) {
        $minifier = new Minify\JS($js);
        $js = $minifier->minify();
    }
    return $js;
}
