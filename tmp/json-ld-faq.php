<?php
//JSON-LDに関する記述
//https://developers.google.com/search/docs/data-types/articles
//https://schema.org/NewsArticle
//https://fantastech.net/review-snippet-customize

/**
 * Cocoon WordPress Theme
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */
if ( !defined( 'ABSPATH' ) ) exit;

$faqs = array();
$content = get_the_content();
$dl_res = preg_match_all('{<dl class="faq">.+?</dl>}s', $content, $dls);
if ($dl_res && isset($dls[0])) {
  foreach ($dls[0] as $dl) {
    // _v($dl);
    $dt_res = preg_match_all('{<dt.+?>.+?</dt>}s', $dl, $dts);
    $dd_res = preg_match_all('{<dd.+?>.+?</dd>}s', $dl, $dds);
    if ($dt_res && isset($dts[0]) && $dd_res && isset($dds[0])) {
      //質問を作成する
      $q = '';
      foreach ($dts[0] as $dt) {
        $q = preg_replace('{<div class="faq-question-label.+?</div>}s', '', $dt);
        $q = strip_tags($q);
        $q = str_replace("\n", '', $q);

        //回答を取得する
        $a = '';
        foreach ($dds[0] as $dd) {
          $a_res = preg_match('{<div class="faq-answer-content.+?>(.+?)</div>}s', $dd, $a);
          if ($a_res && isset($a[1])) {
            $a = strip_tags($a[1], '<h1><h2><h3><h4><h5><h6><br><ol><ul><li><a><p><b><strong><i><em>');
            $a = str_replace("\n", '', $a);
            // _v($a);
          }
          //FAQ JSONを作成する
          if ($q && $a) {
            $faqs[] ='
              {
                "@type": "Question",
                "name": '.json_encode($q, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES).',
                "acceptedAnswer": {
                  "@type": "Answer",
                  "text": '.json_encode($a, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES).'
                }
              }';
          }
        }
      }
    }
  }
}
// _v(implode(",", $faqs));
if (!empty($faqs)) {
 ?>
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@id":"#FAQContents",
  "@type": "FAQPage",
  "mainEntity": [
    <?php //FAQ JSONの結合
    echo implode(",", $faqs); ?>
  ]
}
</script>
<?php
}
